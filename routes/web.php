<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RealtorController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\TaskNoteController;
use App\Http\Controllers\OfficeNoteController;
use App\Http\Controllers\OfficeVisitController;
use App\Http\Controllers\RealtorCommunicationLogController;
use App\Http\Controllers\RealtorNoteController;
use App\Http\Controllers\RealtorSequenceController;
use App\Http\Controllers\RealtorSequenceStepNoteController;
use App\Http\Controllers\ReportController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/user/{id}/upload-avatar', [UserController::class, 'uploadAvatar'])->name('user.upload-avatar');
    /**
     * Handle Dashboard
     */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/realtor', RealtorController::class);
    Route::resource('/office', OfficeController::class);
    Route::resource('/sequence', SequenceController::class);
    Route::resource('/sequence-step-note', RealtorSequenceStepNoteController::class);
    Route::resource('/realtor-note', RealtorNoteController::class);
    Route::resource('/office-note', OfficeNoteController::class);
    Route::resource('/office-visit', OfficeVisitController::class);
    Route::resource('task', TaskController::class);
    Route::resource('/settings', SettingController::class);
    Route::resource('/user', UserController::class);
    Route::resource('/task-note', TaskNoteController::class);
    Route::resource('/realtor-sequence', RealtorSequenceController::class);
    Route::resource('/communication-log', RealtorCommunicationLogController::class);
    Route::resource('/reports', ReportController::class);

    Route::post('/realtor/{id}/upload-avatar', [RealtorController::class, 'uploadAvatar'])->name('realtor.upload-avatar');
    Route::post('/api/realtor/previous-inspections', [RealtorController::class, 'fetchPreviousInspections']);
    Route::post('/office/{id}/upload-image', [OfficeController::class, 'uploadImage'])->name('office.upload-image');

    /**
     * Import offices
     */
    Route::post('office/import', [OfficeController::class, 'import'])->name('offices.import');

    /**
     * Generate Reports
     */
    Route::post('/reports/generate-today', [ReportController::class, 'generateTodaysReport'])->name('reports.generate.today');
    Route::post('/reports/generate-report', [ReportController::class, 'generateReportByDateRange'])->name('reports.generate-report');
    Route::post('/reports/{report}/sync', [ReportController::class, 'syncReport'])->name('reports.sync');

});

require __DIR__.'/auth.php';
