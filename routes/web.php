<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\ClassModelController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\ClassStudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AddAsChildRequestController;
use App\Http\Controllers\AddAsParentRequestController;
use App\Http\Controllers\JoinSchoolRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FilePondController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/join-school-request/form', [JoinSchoolRequestController::class, 'form'])->name('join_school_request.form');
    Route::post('/join-school-request/send-request', [JoinSchoolRequestController::class, 'sendRequest'])->name('join_school_request.send_request');
    Route::post('/join-school-request/accept-request', [JoinSchoolRequestController::class, 'acceptRequest'])->name('join_school_request.accept_request');
    Route::post('/join-school-request/decline-request', [JoinSchoolRequestController::class, 'declineRequest'])->name('join_school_request.decline_request');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/academic-years/form', [AcademicYearController::class, 'form'])->name('academic_years.form');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic_years.store');
    Route::patch('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic_years.update');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic_years.destroy');

    Route::get('/dashboard/terms/form', [TermController::class, 'form'])->name('terms.form');
    Route::get('/academic-years/{academicYear}/terms', [TermController::class, 'terms'])->name('academic_years.terms');
    Route::post('/academic-years/{academicYear}/terms', [TermController::class, 'store'])->name('academic_years.terms.store');
    Route::patch('/terms/{term}', [TermController::class, 'update'])->name('terms.update');
    Route::delete('/terms/{term}', [TermController::class, 'destroy'])->name('terms.destroy');

    Route::get('/dashboard/classes/form', [ClassModelController::class, 'form'])->name('classes.form');
    Route::get('/classes', [ClassModelController::class, 'index'])->name('classes.index');
    Route::get('/classes/{classModel}', [ClassModelController::class, 'show'])->name('classes.show');
    Route::post('/classes', [ClassModelController::class, 'store'])->name('classes.store');
    Route::patch('/classes/{classModel}', [ClassModelController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{classModel}', [ClassModelController::class, 'destroy'])->name('classes.destroy');

    Route::get('/dashboard/notice-board/create', [NoticeBoardController::class, 'create'])->name('notice_board.create');
    Route::get('/dashboard/notice-board', [NoticeBoardController::class, 'index'])->name('notice_board.index');
    Route::post('/notice-board', [NoticeBoardController::class, 'store'])->name('notice_board.store');

    Route::get('/dashboard/class-teacher/form', [ClassTeacherController::class, 'form'])->name('class_teacher.form');
    Route::post('/class-teacher', [ClassTeacherController::class, 'store'])->name('class_teacher.store');
    Route::delete('/class-teacher/{classTeacher}', [ClassTeacherController::class, 'destroy'])->name('class_teacher.destroy');

    Route::get('/dashboard/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/{userType}', [UserController::class, 'index'])->name('users.index');
    Route::patch('/change-user-type', [UserController::class, 'changeUserType'])->name('change_user_type');
    Route::get('/change-password', [UserController::class, 'changePasswordForm'])->name('change_password_form');
    Route::patch('/change-password', [UserController::class, 'changePassword'])->name('change_password');

    Route::get('/dashboard/remove-children/form', [UserController::class, 'removeChildrenForm'])->name('remove_children.form');
    Route::delete('/parent-student/{parentStudent}', [UserController::class, 'deleteChild'])->name('parent_student.destroy');

    Route::get('/dashboard/class-student/form', [ClassStudentController::class, 'form'])->name('class_student.form');
    Route::get('/dashboard/class-student/join-class/form', [ClassStudentController::class, 'joinClassForm'])->name('class_student.join_class.form');
    Route::post('/class-student', [ClassStudentController::class, 'store'])->name('class_student.store');
    Route::get('/class-student/{classModel}/{academicYear}/students', [ClassStudentController::class, 'students'])->name('class_student.students');
    Route::delete('/class-student/{classStudent}', [ClassStudentController::class, 'destroy'])->name('class_student.destroy');

    Route::get('/dashboard/grades/form', [GradeController::class, 'form'])->name('grades.form');
    Route::get('/grades/{classModel}/{term}/{subjectName}', [GradeController::class, 'getStudentsWithGrades'])->name('grades.students_with_grades');
    Route::patch('/grades/upsert', [GradeController::class, 'upsert'])->name('grades.upsert');

    Route::get('/dashboard/add-as-child-request/form', [AddAsChildRequestController::class, 'form'])->name('add_as_child_request.form');
    Route::post('/add-as-child-request/send-request', [AddAsChildRequestController::class, 'sendRequest'])->name('add_as_child_request.send_request');
    Route::post('/add-as-child-request/accept-request', [AddAsChildRequestController::class, 'acceptRequest'])->name('add_as_child_request.accept_request');
    Route::post('/add-as-child-request/decline-request', [AddAsChildRequestController::class, 'declineRequest'])->name('add_as_child_request.decline_request');

    Route::get('/dashboard/add-as-parent-request/form', [AddAsParentRequestController::class, 'form'])->name('add_as_parent_request.form');
    Route::post('/add-as-parent-request/send-request', [AddAsParentRequestController::class, 'sendRequest'])->name('add_as_parent_request.send_request');
    Route::post('/add-as-parent-request/accept-request', [AddAsParentRequestController::class, 'acceptRequest'])->name('add_as_parent_request.accept_request');
    Route::post('/add-as-parent-request/decline-request', [AddAsParentRequestController::class, 'declineRequest'])->name('add_as_parent_request.decline_request');

    Route::get('/dashboard/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/dashboard/view-school-notifications', [NotificationController::class, 'viewSchoolNotifications'])->name('notifications.view_school_notifications');

    Route::get('/phpinfo', function(Request $request) {
        if (Auth::user()->email === 'calvinelikem@gmail.com') phpinfo();
    });
});

Route::controller(FilePondController::class)->group(function () {
    Route::post('/filepond/process', 'process');
    Route::delete('/filepond/revert', 'revert');

    Route::middleware(['auth'])->group(function () {   
        Route::get('/filepond/load/{model}/{id}', 'load');
        Route::delete('/filepond/remove/{model}/{id}', 'remove');
    });
});

require __DIR__.'/auth.php';
