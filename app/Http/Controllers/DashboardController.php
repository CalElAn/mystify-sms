<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show school's dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User */
        $authUser = Auth::user();

        if ($authUser->user_type !== 'parent' && is_null($authUser->school)) {
            return redirect()->route('join_school_request.form');
        }

        if (
            $authUser->user_type === 'student' &&
            $authUser->classes
                ->where('school_id', $authUser->school->id)
                ->isEmpty()
        ) {
            return redirect()->route('class_student.join_class.form');
        }

        $user = $authUser;

        if ($request->userId) {
            /** @var \App\Models\User */
            $user = User::find($request->userId);
        }

        if (
            $user->user_type === 'student' &&
            $user->classes->where('school_id', $user->school->id)->isEmpty()
        ) {
            return redirect()
                ->back()
                ->with(
                    'warning',
                    'Selected student has to join a class before his/her dashboard can be viewed',
                );
        }

        /** @var \App\Models\School */
        $school = $user->school;

        $academicYearsWithTerms = $school?->getAcademicYearsWithTerms();

        $term = $school?->getTerm($request);

        $defaultProps = [
            'user' => $user,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $term,
            'noticeBoardMessages' => $school?->getNoticeBoardMessages(
                $term->id,
            ),
            'notifications' => $authUser->getNotifications(),
        ];

        switch ($user->user_type) {
            case 'headteacher':
                $this->authorize('viewHeadteacherDashboard', $user);
                $component = 'Headteacher';
                $props = $user->getPropsForHeadteacherDashboard($term);
                break;

            case 'student':
                $this->authorize('viewStudentDashboard', $user);
                $component = 'Student';
                $props = $user->getPropsForStudentDashboard($term);
                break;

            case 'teacher':
                $this->authorize('viewTeacherDashboard', $user);
                $component = 'Teacher';
                $props = $user->getPropsForTeacherDashboard($term);
                break;

            case 'parent':
                $this->authorize('viewParentDashboard', $user);
                $component = 'Parent';
                $props = $user->getPropsForParentDashboard();
                break;

            default:
                abort(404);
                break;
        }

        return Inertia::render(
            "Dashboard/{$component}",
            //order is important so any repeated keys in $props will overwrite the keys in $defaultProps
            array_merge($defaultProps, $props),
        );
    }
}
