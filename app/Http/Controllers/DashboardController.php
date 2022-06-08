<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\ClassStudentPivot;
use App\Models\School;
use App\Models\SubjectTeacherPivot;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
        $user = Auth::user();
        // $user = User::where('user_type', 'student')->first();
        // $user = User::where('user_type', 'teacher')->first();
        // $user = User::where('user_type', 'parent')->first();

        $shouldShowDashboardHeading = false;

        if ($request->userId) {
            /** @var \App\Models\User */
            $user = User::find($request->userId);
            $shouldShowDashboardHeading = true;
        }

        /** @var \App\Models\School */
        $school = $user->school;

        $academicYearsWithTerms = $school->getAcademicYearsWithTerms();

        $term = $school->getTerm($request);

        $defaultProps = [
            'user' => $user,
            'shouldShowDashboardHeading' => $shouldShowDashboardHeading,
            'school' => $school,
            'academicYearsWithTerms' => $academicYearsWithTerms,
            'term' => $term,
            'showTerm' => true,
            'noticeBoardMessages' => $school
                ->noticeBoard()
                ->where('term_id', $term->id)
                ->latest()
                ->get()
                ->groupBy(function ($item) {
                    return "{$item->created_at->format(
                        'l\, jS F Y',
                    )} ...({$item->created_at->diffForHumans()})";
                }),
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
                # code...
                break;
        }

        return Inertia::render(
            "Dashboard/{$component}",
            //order is important so any repeated keys in $props will overwrite the keys in $defaultProps
            array_merge($defaultProps, $props),
        );
    }
}
