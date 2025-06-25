<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\{
    ChangePasswordController,
    DepartmentController,
    EncoderFollowUpOpdFormController,
    EncoderHighRiskOpdFormController,
    EncoderOpdFormController,
    EncoderOpdbFormController,
    FollowUpOpdFormController,
    HighRiskOpdFormController,
    HomeController,
    InternalConsultationController,
    InternalMedicineTriageController,
    ObGynTriageController,
    ObOpdFormController,
    PatientRecordController,
    PatientTrendController,
    PatientVisitController,
    PediaTriageController,
    QueueController,
    ReportController,
    TeensTriageController,
    UserController,
    WorkScheduleController,
    OpdFormController,
    OpdSubmissionController,
    PatientTriageFormController
};

/* --------------------------------------------------------------------------
 | 1.  Authentication & Root Redirect
 -------------------------------------------------------------------------- */
Auth::routes();
Route::get('/', fn () => redirect()->route('login'));
Route::get('patients/export-excel', [PatientRecordController::class, 'exportExcel'])
     ->name('patients.exportExcel');

Route::get('patients/export-pdf', [PatientRecordController::class, 'exportPdf'])
     ->name('patients.exportPdf');

/* --------------------------------------------------------------------------
 | 2.  Public Queue Pages (no auth)
 -------------------------------------------------------------------------- */
Route::get('/queues/general', [QueueController::class, 'selectGeneral'])->name('queue.general');
Route::get('/queues/select',  [QueueController::class, 'selectQueue'])  ->name('queue.queue_select');
Route::get('/queues/{queue}/departments', [QueueController::class, 'selectDepartment'])->name('queue.department_select');
Route::get('/queues/{queue}/display',     [QueueController::class, 'display'])->name('queue.display');
Route::get('/queues/{queue}/status',      [QueueController::class, 'status']) ->name('queue.status');
Route::middleware(['auth','role:admin,encoder,patient'])
     ->get('opd_submissions/{submission}/export.pdf',
           [OpdSubmissionController::class,'exportPdf'])
     ->name('opd_submissions.export.pdf');
     Route::get('patients/export', [PatientRecordController::class, 'exportAllExcel'])
     ->name('patients.exportAll');


/* --------------------------------------------------------------------------
 | 3.  Global AJAX / Utility (auth mixed)
 -------------------------------------------------------------------------- */
Route::get('patients/search', [PatientRecordController::class, 'search'])
     ->middleware(['auth', 'role:admin,encoder'])
     ->name('patients.search');

/* --------------------------------------------------------------------------
 | 4.  Patient-Scoped Queue Actions (auth)
 -------------------------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::get ('patients/{patient}/queue',                 [QueueController::class, 'forPatient'])      ->name('patients.queue.index');
    Route::post('patients/{patient}/queue',                 [QueueController::class, 'patientStore'])    ->name('patients.queue.store');
    Route::get ('patients/{patient}/queue/{token}/print',   [QueueController::class, 'forPatientPrint']) ->name('patients.queue.print');
Route::post('queue/{queue}/issue', [QueueController::class, 'issue'])
     ->name('queue.issue')
     ->whereNumber('queue');

    Route::get ('opd_submissions/{submission}', [OpdSubmissionController::class, 'show'])->name('opd_submissions.show');
    Route::get ('patients/{patient}/visits',           [PatientVisitController::class, 'index'])->name('patients.visits.index');
    Route::get ('patients/{patient}/visits/{visit}',   [PatientVisitController::class, 'show']) ->name('patients.visits.show');
    
});

/* --------------------------------------------------------------------------
 | 5.  Internal Medicine Consultation (auth)
 -------------------------------------------------------------------------- */
Route::middleware(['auth'])
      ->prefix('consult/internal')
      ->name('consult.internal.')
      ->group(function () {
    Route::get('/',                 [InternalConsultationController::class, 'index'])->name('index');
    Route::get('create',            [InternalConsultationController::class, 'create'])->name('create');
    Route::post('/',                [InternalConsultationController::class, 'store'])->name('store');
    Route::get('{submission}',      [InternalConsultationController::class, 'show'])->name('show');
    Route::get('{submission}/edit', [InternalConsultationController::class, 'edit'])->name('edit');
    Route::put('{submission}',      [InternalConsultationController::class, 'update'])->name('update');
    Route::delete('{submission}',   [InternalConsultationController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->name('triage.teens.')->prefix('triage/teens')->group(function () {
    Route::get('/',        [TeensTriageController::class,'index'])->name('index');
    Route::get('/create',  [TeensTriageController::class,'create'])->name('create');
    Route::post('/',       [TeensTriageController::class,'store'])->name('store');
    Route::get('/{teen}',  [TeensTriageController::class,'show'])->name('show');
    Route::get('/{teen}/edit',[TeensTriageController::class,'edit'])->name('edit');

    // ← Add this PUT route:
    Route::put('/{teen}',  [TeensTriageController::class,'update'])->name('update');

    Route::delete('/{teen}',[TeensTriageController::class,'destroy'])->name('destroy');
});
Route::middleware('auth','role:admin,encoder')->group(function () {
    Route::resource('triage/internal', InternalMedicineTriageController::class)->names('triage.internal');
    Route::resource('triage/pedia',    PediaTriageController::class)          ->names('triage.pedia');
   
    Route::resource('triage/obgyn',    ObGynTriageController::class)          ->names('triage.obgyn');
});


// Patient-scoped Triage
Route::middleware('auth')
     ->prefix('opd_forms/triage')
     ->name('opd_forms.triage.')
     ->group(function(){
         Route::get('/',                  [PatientTriageFormController::class,'index'])   ->name('index');
         Route::get('create',             [PatientTriageFormController::class,'create'])  ->name('create');
         Route::post('/',                 [PatientTriageFormController::class,'store'])   ->name('store');
         Route::get('{triageForm}',       [PatientTriageFormController::class,'show'])    ->name('show');
         Route::get('{triageForm}/edit',  [PatientTriageFormController::class,'edit'])    ->name('edit');
         Route::put('{triageForm}',       [PatientTriageFormController::class,'update'])  ->name('update');
         Route::delete('{triageForm}',    [PatientTriageFormController::class,'destroy']) ->name('destroy');
});

/* --------------------------------------------------------------------------
 | 7.  OB-OPD Form Templates (auth)
 -------------------------------------------------------------------------- */
Route::middleware('auth')
     ->prefix('ob-opd-forms')
     ->name('ob-opd-forms.')
     ->group(function () {
         Route::get   ('/',                [ObOpdFormController::class, 'index'])->name('index');
         Route::get   ('create',           [ObOpdFormController::class, 'create'])->name('create');
         Route::post  ('/',                [ObOpdFormController::class, 'store'])->name('store');
         Route::get   ('{submission}',     [ObOpdFormController::class, 'show'])->name('show');
         Route::get   ('{submission}/edit', [ObOpdFormController::class, 'edit'])->name('edit');
         Route::put   ('{submission}',     [ObOpdFormController::class, 'update'])->name('update');
         Route::delete('{submission}',     [ObOpdFormController::class, 'destroy'])->name('destroy');
     });

/* --------------------------------------------------------------------------
 | 8.  Follow-Up OPD Forms (auth)
 -------------------------------------------------------------------------- */
Route::middleware('auth')->name('follow-up-opd-forms.')->group(function () {
    Route::get ('follow-up-opd-forms',                 [FollowUpOpdFormController::class, 'index'])->name('index');
    Route::get ('follow-up-opd-forms/create',          [FollowUpOpdFormController::class, 'create'])->name('create');
    Route::post('follow-up-opd-forms',                 [FollowUpOpdFormController::class, 'store'])->name('store');
    Route::get ('follow-up-opd-forms/{submission}',    [FollowUpOpdFormController::class, 'show'])->name('show');
    Route::get ('follow-up-opd-forms/{submission}/edit',[FollowUpOpdFormController::class, 'edit'])->name('edit');
    Route::put ('follow-up-opd-forms/{submission}',    [FollowUpOpdFormController::class, 'update'])->name('update');
    Route::delete('follow-up-opd-forms/{submission}',  [FollowUpOpdFormController::class, 'destroy'])->name('destroy');
});

/* --------------------------------------------------------------------------
 | 9.  Queue Admin & Encoder Utilities (auth)
 -------------------------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    /* quick delete-list workflow */
    Route::get   ('/admin/queue/delete-select',             [QueueController::class, 'deleteSelect'])->name('queue.delete.select');
    Route::get   ('/admin/queue/{queue}/delete',            [QueueController::class, 'deleteList'])  ->name('queue.delete.list');
    Route::delete('/admin/queue/{queue}/delete/{token}',    [QueueController::class, 'deleteToken']) ->name('queue.delete.token');

    /* counter reset & print receipt */
    Route::patch ('queue/{queue}/reset',                    [QueueController::class, 'resetCounter'])->name('queue.reset');
    Route::get   ('queue/{token}/print',                    [QueueController::class, 'printReceipt'])->name('queue.tokens.print');
Route::get('/tokens/{token}/print',       
    [QueueController::class, 'printReceipt']
)->name('queue.print');
    /* encoder home */
    Route::get('/encoder', [QueueController::class, 'encoderIndex'])->name('encoder.index');
});

/* --------------------------------------------------------------------------
 |10. Admin-Only Block
 -------------------------------------------------------------------------- */
Route::middleware(['auth', 'role:admin'])->group(function () {

    /* Dashboard & summary */
    Route::get('/home',                      [HomeController::class, 'index'])->name('home');
    Route::get('queues/summary',             [QueueController::class, 'summary'])->name('queue.summary');

    /* Departments CRUD */
    Route::resource('departments', DepartmentController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    /* Queue Admin Controls */
    Route::get   ('/queue',                                 [QueueController::class, 'index'])->name('queue.index');
    Route::post  ('/queues/{queue}/tokens',                 [QueueController::class, 'store'])->name('queue.store');
    Route::get   ('/queues/{queue}/tokens/{token}/edit',    [QueueController::class, 'edit'])->name('queue.tokens.edit');
    Route::patch ('/queues/{queue}/tokens/{token}',         [QueueController::class, 'update'])->name('queue.tokens.update');
    Route::delete('/queues/{queue}/tokens/{token}',         [QueueController::class, 'destroy'])->name('queue.tokens.destroy');
    Route::patch ('/queues/{queue}/serve-next-admin',       [QueueController::class, 'serveNextAdmin'])->name('queue.serveNext.admin');
    Route::post  ('/queues/{queue}/route/{child}',          [QueueController::class, 'routeToChild'])->name('queue.route');
    Route::get   ('/queues/{queue}/display/admin',          [QueueController::class, 'adminDisplay'])->name('queue.admin_display');

    /* Users & Patients */
    Route::resource('users',    UserController::class);
    Route::resource('patients', PatientRecordController::class);
    Route::get('patients/{patient}/export.xlsx', [PatientRecordController::class, 'exportExcel'])->name('patients.export.excel');
    Route::get('patients/{patient}/export.pdf',  [PatientRecordController::class, 'exportPdf']) ->name('patients.export.pdf');

    /* OPD form templates */
    Route::resource('opd_forms', OpdFormController::class);
    Route::get('opd_forms/{opd_form}/fill', [OpdFormController::class,'fill'])
     ->name('opd_forms.fill');

// (Bonus) handle the POST when they submit
Route::post('opd_forms/{opd_form}/fill', [OpdFormController::class,'submit'])
     ->name('opd_forms.submit');
    Route::get('opd_forms/{opd_form}/export.pdf', [OpdFormController::class,'exportPdf'])->name('opd_forms.export.pdf');

    /* High-Risk OPD forms */
    Route::prefix('high-risk-opd-forms')->name('high-risk-opd-forms.')->group(function () {
        Route::get('/',            [HighRiskOpdFormController::class, 'index'])->name('index');
        Route::get('create',       [HighRiskOpdFormController::class, 'create'])->name('create');
        Route::post('/',           [HighRiskOpdFormController::class, 'store'])->name('store');
        Route::get('{submission}', [HighRiskOpdFormController::class, 'show'])->name('show');
        Route::get('{submission}/edit',[HighRiskOpdFormController::class, 'edit'])->name('edit');
        Route::put('{submission}', [HighRiskOpdFormController::class, 'update'])->name('update');
        Route::delete('{submission}', [HighRiskOpdFormController::class, 'destroy'])->name('destroy');
    });

    /* Patient Trends */
    Route::prefix('trends')->name('trends.')->group(function () {
        Route::get('/',         [PatientTrendController::class, 'index'])->name('index');
        Route::post('/request', [PatientTrendController::class, 'requestNew'])->name('request');
        Route::get('/excel',    [PatientTrendController::class, 'exportExcel'])->name('excel');
        Route::get('/pdf',      [PatientTrendController::class, 'exportPdf'])->name('pdf');
    });

    /* Reports */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',          [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/excel',     [ReportController::class, 'exportExcel'])->name('excel');
        Route::get('/pdf',       [ReportController::class, 'exportPdf'])->name('pdf');
        Route::get('/verify',    [ReportController::class, 'verify'])->name('verify');
   Route::get('/served-tokens',         
    [ReportController::class, 'servedTokenHistory'])
    ->name('servedtokens');  

        /* Served-token history exports */
        Route::get('/excel/tokens', [ReportController::class, 'exportExcel'])->name('tokens.excel');
        Route::get('/pdf/tokens',   [ReportController::class, 'exportPdf'])  ->name('tokens.pdf');
           Route::get('/excel/schedules', [ReportController::class, 'exportSchedulesExcel'])
         ->name('schedules.excel');
    Route::get('/pdf/schedules',   [ReportController::class, 'exportSchedulesPdf'])
         ->name('schedules.pdf');
    });

    /* Work Schedules */
    Route::resource('schedules', WorkScheduleController::class);
    Route::get('/schedules/{schedule}/show', [WorkScheduleController::class, 'show'])->name('schedules.show');
});

/* --------------------------------------------------------------------------
 |11. Encoder-Only Block (role:encoder)
 -------------------------------------------------------------------------- */
Route::middleware(['auth', 'role:encoder'])->prefix('encoder')->as('encoder.')->group(function () {

    Route::resource('opd/high-risk', HighRiskOpdFormController::class, [
        'as'         => 'opd',
        'parameters' => ['high-risk' => 'id'],
    ]);

    Route::resource('opd/follow-up', FollowUpOpdFormController::class, [
        'as'         => 'opd',
        'parameters' => ['follow-up' => 'id'],
    ]);

    Route::resource('opd/ob', ObOpdFormController::class, [
        'as'         => 'opd',
        'parameters' => ['ob' => 'id'],
    ]);
});

/* --------------------------------------------------------------------------
 |12. Shared Auth Routes (password change, history, etc.)
 -------------------------------------------------------------------------- */
Route::middleware('auth')->group(function () {
    Route::get ('password/change',        [ChangePasswordController::class, 'show'])  ->name('password.change');
    Route::post('password/change',        [ChangePasswordController::class, 'update'])->name('password.change.update');
    Route::get ('queue/history',          [QueueController::class, 'history'])        ->name('queue.history');
});
