<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\PatientRecordController;
use App\Http\Controllers\OpdFormController;
use App\Http\Controllers\OpdSubmissionController;
use App\Http\Controllers\FollowUpOpdFormController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PatientTrendController;
use App\Http\Controllers\TeensTriageController;
use App\Http\Controllers\EncoderOpdFormController;
use App\Http\Controllers\EncoderFollowUpOpdFormController;
use App\Http\Controllers\EncoderHighRiskOpdFormController;
use App\Http\Controllers\EncoderOpdbFormController;
use App\Http\Controllers\ObOpdFormController;
use App\Http\Controllers\ObGynTriageController;
use App\Http\Controllers\InternalMedicineTriageController;
use App\Http\Controllers\PediaTriageController;
use App\Http\Controllers\InternalConsultationController;

use App\Http\Controllers\PatientVisitController;

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HighRiskOpdFormController;

// 1) Authentication & root redirect
Auth::routes();
Route::get('/', fn() => redirect()->route('login'));
// 5) Public “General” queue and window selection
Route::get('/queues/general',    [QueueController::class,'selectGeneral'])
     ->name('queue.general');
Route::get('queues/summary', [QueueController::class,'summary'])
     ->name('queue.summary')
     ->middleware(['auth','role:admin']);

// 3) AJAX patient‐search for Select2 (admin & encoder)
Route::get('patients/search', [PatientRecordController::class, 'search'])
     ->middleware('auth','role:admin,encoder')
     ->name('patients.search');

Route::middleware('auth')->group(function () {

    // View queues that belong to this patient
    Route::get(
        'patients/{patient}/queue',
        [QueueController::class, 'forPatient']
    )->name('patients.queue.index');

    // Create a token for the patient in a queue
    Route::post(
        'patients/{patient}/queue',
        [QueueController::class, 'patientStore']
    )->name('patients.queue.store');

    // Print that specific token
    Route::get(
        'patients/{patient}/queue/{token}/print',
        [QueueController::class, 'forPatientPrint']
    )->name('patients.queue.print');
});


// 4) OB-OPD form templates (admin only)
Route::middleware('auth')->prefix('ob-opd/forms')->group(function () {
    Route::get('/',                [ObOpdFormController::class,'index'])
         ->name('ob-opd-forms.index');
    Route::get('create',           [ObOpdFormController::class,'create'])
         ->name('ob-opd-forms.create');
    Route::post('/',               [ObOpdFormController::class,'store'])
         ->name('ob-opd-forms.store');
    Route::get('{submission}',     [ObOpdFormController::class,'show'])
         ->name('ob-opd-forms.show');
    Route::get('{submission}/edit',[ObOpdFormController::class,'edit'])
         ->name('ob-opd-forms.edit');
    Route::put('{submission}',     [ObOpdFormController::class,'update'])
         ->name('ob-opd-forms.update');
    Route::delete('{submission}',  [ObOpdFormController::class,'destroy'])
         ->name('ob-opd-forms.destroy');
});

Route::middleware('auth')
     ->prefix('consult/internal')
     ->name('consult.internal.')
     ->group(function () {
         Route::get('/',            [InternalConsultationController::class, 'index'])  ->name('index');
         Route::get('create',       [InternalConsultationController::class, 'create']) ->name('create');
         Route::post('/',           [InternalConsultationController::class, 'store'])  ->name('store');
         Route::get('{submission}', [InternalConsultationController::class, 'show'])   ->name('show');
         Route::get('{submission}/edit',
                                 [InternalConsultationController::class, 'edit'])   ->name('edit');
         Route::put('{submission}', [InternalConsultationController::class, 'update']) ->name('update');
         Route::delete('{submission}',
                                 [InternalConsultationController::class, 'destroy'])->name('destroy');
     });
Route::resource('triage/internal', InternalMedicineTriageController::class)
     ->names([
         'index'   => 'triage.internal.index',
         'create'  => 'triage.internal.create',
         'store'   => 'triage.internal.store',
         'show'    => 'triage.internal.show',
         'edit'    => 'triage.internal.edit',
         'update'  => 'triage.internal.update',
         'destroy' => 'triage.internal.destroy',
     ]);
// ── Internal Medicine Triage ───────────────────────────────────────────────
Route::middleware('auth')
     ->prefix('triage/internal')
     ->name('triage.internal.')
     ->group(function () {
         Route::get('/',                            [InternalMedicineTriageController::class,'index'])->name('index');
         Route::get('create',                       [InternalMedicineTriageController::class,'create'])->name('create');
         Route::post('/',                           [InternalMedicineTriageController::class,'store'])->name('store');
         Route::get('{submission}',                 [InternalMedicineTriageController::class,'show'])->name('show');
         Route::get('{submission}/edit',            [InternalMedicineTriageController::class,'edit'])->name('edit');
         Route::put('{submission}',                 [InternalMedicineTriageController::class,'update'])->name('update');
         Route::delete('{submission}',              [InternalMedicineTriageController::class,'destroy'])->name('destroy');
     });

// ── Pedia Triage ───────────────────────────────────────────────────────────
Route::middleware('auth')
     ->prefix('triage/pedia')
     ->name('triage.pedia.')
     ->group(function () {
         Route::get('/',                            [PediaTriageController::class,'index'])->name('index');
         Route::get('create',                       [PediaTriageController::class,'create'])->name('create');
         Route::post('/',                           [PediaTriageController::class,'store'])->name('store');
         Route::get('{submission}',                 [PediaTriageController::class,'show'])->name('show');
         Route::get('{submission}/edit',            [PediaTriageController::class,'edit'])->name('edit');
         Route::put('{submission}',                 [PediaTriageController::class,'update'])->name('update');
         Route::delete('{submission}',              [PediaTriageController::class,'destroy'])->name('destroy');
     });

// ── Teens Triage ────────────────────────────────────────────────────────────
Route::middleware('auth')
     ->prefix('triage/teens')
     ->name('triage.teens.')
     ->group(function () {
         Route::get('/',                            [TeensTriageController::class,'index'])->name('index');
         Route::get('create',                       [TeensTriageController::class,'create'])->name('create');
         Route::post('/',                           [TeensTriageController::class,'store'])->name('store');
         Route::get('{submission}',                 [TeensTriageController::class,'show'])->name('show');
         Route::get('{submission}/edit',            [TeensTriageController::class,'edit'])->name('edit');
         Route::put('{submission}',                 [TeensTriageController::class,'update'])->name('update');
         Route::delete('{submission}',              [TeensTriageController::class,'destroy'])->name('destroy');
     });

     Route::middleware('auth')
     ->prefix('triage/obgyn')
     ->name('triage.obgyn.')
     ->group(function () {
         Route::get('/',                [ObGynTriageController::class,'index'])->name('index');
         Route::get('create',           [ObGynTriageController::class,'create'])->name('create');
         Route::post('/',               [ObGynTriageController::class,'store'])->name('store');
         Route::get('{submission}',     [ObGynTriageController::class,'show'])->name('show');
         Route::get('{submission}/edit',[ObGynTriageController::class,'edit'])->name('edit');
         Route::put('{submission}',     [ObGynTriageController::class,'update'])->name('update');
         Route::delete('{submission}',  [ObGynTriageController::class,'destroy'])->name('destroy');
     });


Route::middleware('auth')->group(function () {
    // Show “New Follow-Up Record” (OPD-F-08)
    Route::get('patients/{patient}/queue/{token}/print', [QueueController::class,'forPatientPrint'])
     ->middleware('auth')
     ->name('patients.queue.print');
  Route::get('follow-up-opd-forms',                             [FollowUpOpdFormController::class, 'index'])
         ->name('follow-up-opd-forms.index');
    Route::get('follow-up-opd-forms/create',                      [FollowUpOpdFormController::class, 'create'])
         ->name('follow-up-opd-forms.create');
    Route::post('follow-up-opd-forms',                             [FollowUpOpdFormController::class, 'store'])
         ->name('follow-up-opd-forms.store');
    // Notice we now use {submission} instead of {form}
    Route::get('follow-up-opd-forms/{submission}',                [FollowUpOpdFormController::class, 'show'])
         ->name('follow-up-opd-forms.show');
    Route::get('follow-up-opd-forms/{submission}/edit',           [FollowUpOpdFormController::class, 'edit'])
         ->name('follow-up-opd-forms.edit');
    Route::put('follow-up-opd-forms/{submission}',                [FollowUpOpdFormController::class, 'update'])
         ->name('follow-up-opd-forms.update');
    Route::delete('follow-up-opd-forms/{submission}',             [FollowUpOpdFormController::class, 'destroy'])
         ->name('follow-up-opd-forms.destroy');
});



Route::get('/queues/select',     [QueueController::class,'selectQueue'])
     ->name('queue.queue_select');
 Route::get('/admin/queue/delete‐select', [QueueController::class, 'deleteSelect'])
         ->name('queue.delete.select');

    // 2) After choosing a queue, show that queue’s pending tokens (with delete buttons):
    Route::get('/admin/queue/{queue}/delete', [QueueController::class, 'deleteList'])
         ->name('queue.delete.list');

    // 3) Finally, delete a single token from that queue:
    Route::delete('/admin/queue/{queue}/delete/{token}', [QueueController::class, 'deleteToken'])
         ->name('queue.delete.token');

Route::get('/queues/{queue}/departments', [QueueController::class,'selectDepartment'])
     ->name('queue.department_select');

Route::get('/queues/{queue}/display', [QueueController::class,'display'])
     ->name('queue.display');
Route::get('/queues/{queue}/status',  [QueueController::class,'status'])
     ->name('queue.status');

  
     Route::middleware('auth')->group(function () {
    Route::resource('triage/obgyn', ObGynTriageController::class)
         ->names([
             'index'   => 'triage.obgyn.index',
             'create'  => 'triage.obgyn.create',
             'store'   => 'triage.obgyn.store',
             'show'    => 'triage.obgyn.show',
             'edit'    => 'triage.obgyn.edit',
             'update'  => 'triage.obgyn.update',
             'destroy' => 'triage.obgyn.destroy',
         ]);
});
     Route::middleware('auth')->group(function () {
    Route::resource('triage/teens', TeensTriageController::class)
         ->names([
             'index'   => 'triage.teens.index',
             'create'  => 'triage.teens.create',
             'store'   => 'triage.teens.store',
             'show'    => 'triage.teens.show',
             'edit'    => 'triage.teens.edit',
             'update'  => 'triage.teens.update',
             'destroy' => 'triage.teens.destroy',
         ]);
});
     Route::middleware('auth')->group(function () {
    Route::resource('triage/pedia', PediaTriageController::class)
         ->names([
             'index'   => 'triage.pedia.index',
             'create'  => 'triage.pedia.create',
             'store'   => 'triage.pedia.store',
             'show'    => 'triage.pedia.show',
             'edit'    => 'triage.pedia.edit',
             'update'  => 'triage.pedia.update',
             'destroy' => 'triage.pedia.destroy',
         ]);
});
// 6) Shared authenticated routes
Route::middleware('auth')->group(function () {
    // OPD submission detail (any role)
    Route::get('opd_submissions/{submission}', [OpdSubmissionController::class,'show'])
         ->name('opd_submissions.show');
Route::get ('patients/{patient}/visits',
        [PatientVisitController::class,'index'])
        ->name('patients.visits.index');

         Route::get('patients/{patient}/visits', [
        \App\Http\Controllers\PatientVisitController::class,
        'index'
    ])->name('patients.visits.index');

    Route::get('patients/{patient}/visits/{visit}', [
        \App\Http\Controllers\PatientVisitController::class,
        'show'
    ])->name('patients.visits.show');

    Route::get('patients/{patient}/queue', [
        \App\Http\Controllers\QueueController::class,
        'forPatient'
    ])->name('patients.queue.index');
    

 Route::middleware('auth')
     ->get('patients/{patient}/queue/{token}/print', [
         \App\Http\Controllers\QueueController::class,
         'forPatientPrint'
     ])
     ->name('patients.queue.print');
    Route::post(
    'patients/{patient}/queue',
    [\App\Http\Controllers\QueueController::class, 'patientStore']
)->middleware('auth')->name('patients.queue.store');

    Route::get ('patients/{patient}/visits/{visit}',
        [PatientVisitController::class,'show'])
        ->name('patients.visits.show');
Route::post(
    'patients/{patient}/queue',
    [QueueController::class, 'patientStore']
)->name('patients.queue.store');
    // Password change
    Route::get('password/change', [ChangePasswordController::class,'show'])
         ->name('password.change');
    Route::post('password/change', [ChangePasswordController::class,'update'])
         ->name('password.change.update');

    // Queue history
    Route::get('queue/history', [QueueController::class,'history'])
         ->name('queue.history');

    // 7) Admin‐only routes
    Route::middleware('role:admin')->group(function () {
        // Dashboard
        Route::get('/home', [HomeController::class, 'index'])
             ->name('home');
             Route::get('queues/summary', [QueueController::class,'summary'])
     ->name('queue.summary');

        // Departments CRUD
        Route::resource('departments', DepartmentController::class)
             ->only(['index','create','store','edit','update','destroy']);

        // Queue Admin Controls
        Route::get('/queue', [QueueController::class,'index'])
             ->name('queue.index');

        // Live Admin Display for a single queue
        Route::get('/queues/{queue}/display/admin', [QueueController::class,'adminDisplay'])
             ->name('queue.admin_display');

        // Add a new token under a queue
        Route::post('/queues/{queue}/tokens', [QueueController::class,'store'])
             ->name('queue.store');

        // Edit a token
        Route::get('/queues/{queue}/tokens/{token}/edit', [QueueController::class,'edit'])
             ->name('queue.tokens.edit');
        Route::patch('/queues/{queue}/tokens/{token}', [QueueController::class,'update'])
             ->name('queue.tokens.update');
        Route::delete('/queues/{queue}/tokens/{token}', [QueueController::class,'destroy'])
             ->name('queue.tokens.destroy');

        // Serve‐next for a queue
        Route::patch('/queues/{queue}/serve-next-admin', [QueueController::class,'serveNextAdmin'])
             ->name('queue.serveNext.admin');
 Route::patch('/queues/{queue}/reset-counter',
        [QueueController::class,'resetCounter']
    )->name('queue.reset_counter');
        // Route from parent → child
        Route::post('/queues/{queue}/route/{child}', [QueueController::class,'routeToChild'])
             ->name('queue.route');

        // Users & Patients CRUD + exports
        Route::resource('users', UserController::class);
        Route::resource('patients', PatientRecordController::class);
        Route::get('patients/{patient}/export.xlsx', [PatientRecordController::class,'exportExcel'])
             ->name('patients.export.excel');
        Route::get('patients/{patient}/export.pdf',   [PatientRecordController::class,'exportPdf'])
             ->name('patients.export.pdf');

        // OPD form templates (admin)
        Route::resource('opd_forms', OpdFormController::class);
        Route::get('opd_forms/{opd_form}/export.pdf', [OpdFormController::class,'exportPdf'])
             ->name('opd_forms.export.pdf');

        // Patient trends (charts, ARIMA/LSTM/Ensemble)
        Route::prefix('trends')->name('trends.')->group(function () {
            Route::get('/',         [PatientTrendController::class, 'index'])->name('index');
            Route::post('/request', [PatientTrendController::class, 'requestNew'])->name('request');
            Route::get('/excel',    [PatientTrendController::class, 'exportExcel'])->name('excel');
            Route::get('/pdf',      [PatientTrendController::class, 'exportPdf'])->name('pdf');
        });

        // Reports (filter, generate, export)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',         [ReportController::class, 'index'])->name('index');
            Route::post('/generate',[ReportController::class, 'generate'])->name('generate');
            Route::get('/excel',    [ReportController::class, 'exportExcel'])->name('excel');
            Route::get('/pdf',      [ReportController::class, 'exportPdf'])->name('pdf');
            Route::get('/verify',   [ReportController::class, 'verify'])->name('verify');
        });
        Route::get('/reports/excel/tokens',  [ReportController::class,'exportExcel'])
     ->name('reports.tokens.excel');

Route::get('/reports/pdf/tokens',    [ReportController::class,'exportPdf'])
     ->name('reports.tokens.pdf');


        // Work schedules
        Route::resource('schedules', WorkScheduleController::class);
   
   Route::get('/schedules/{schedule}/show', [WorkScheduleController::class,'show'])
     ->name('schedules.show');



        // High-Risk OPD forms (admin)
        Route::prefix('high-risk-opd-forms')
             ->name('high-risk-opd-forms.')
             ->group(function(){
            Route::get('/',            [HighRiskOpdFormController::class,'index'])->name('index');
            Route::get('create',       [HighRiskOpdFormController::class,'create'])->name('create');
            Route::post('/',           [HighRiskOpdFormController::class,'store'])->name('store');
            Route::get('{submission}', [HighRiskOpdFormController::class,'show'])->name('show');
            Route::get('{submission}/edit',[HighRiskOpdFormController::class,'edit'])->name('edit');
            Route::put('{submission}', [HighRiskOpdFormController::class,'update'])->name('update');
            Route::delete('{submission}', [HighRiskOpdFormController::class,'destroy'])->name('destroy');
        });

        // OB-OPD forms (resource, except destroy)
        Route::resource('ob-opd-forms', ObOpdFormController::class)
             ->except(['destroy']);

        // Fill & submit any OPD template (admin, encoder, patient)
        Route::get('opd_forms/{form}/fill',   [OpdFormController::class,'fill'])
             ->middleware('role:admin,encoder,patient')
             ->name('opd_forms.fill');
        Route::post('opd_forms/{form}/submit',[OpdFormController::class,'submit'])
             ->middleware('role:admin,encoder,patient')
             ->name('opd_forms.submit');

        // Encoder “add patient token” route
        Route::post('/queue/{department}/add', [QueueController::class,'encoderStore'])
             ->name('queue.encoder.store');
    });

 Route::middleware(['auth', 'role:encoder'])
    ->prefix('encoder')
    ->as('encoder.')
    ->group(function () {

        // OPD-F-06: High-Risk OPD forms
        Route::resource('opd/high-risk', HighRiskOpdFormController::class, [
            'as'         => 'opd',
            'parameters' => ['high-risk' => 'id'],
        ]);

        // OPD-F-05: Follow-Up OPD forms
        Route::resource('opd/follow-up', FollowUpOpdFormController::class, [
            'as'         => 'opd',
            'parameters' => ['follow-up' => 'id'],
        ]);

        // OPD-F-07: OB OPD forms
        Route::resource('opd/ob', ObOpdFormController::class, [
            'as'         => 'opd',
            'parameters' => ['ob' => 'id'],
        ]);

    });

    // 9) Fill & submit any OPD template (admin, encoder, patient)
    Route::get('opd_forms/{form}/fill',   [OpdFormController::class,'fill'])
         ->middleware('role:admin,encoder,patient')
         ->name('opd_forms.fill');
    Route::post('opd_forms/{form}/submit',[OpdFormController::class,'submit'])
         ->middleware('role:admin,encoder,patient')
         ->name('opd_forms.submit');
});

// 10) Public “select window” page (duplicate of above if needed)
Route::get('/queues/select', [QueueController::class,'selectQueue'])
     ->name('queue.queue_select');

// 11) Encoder “home” route (example)
Route::get('/encoder', [QueueController::class, 'encoderIndex'])
     ->name('encoder.index');

Route::patch(
    'queue/{queue}/reset',
    [QueueController::class, 'resetCounter']
)->name('queue.reset');

Route::get('queue/{token}/print', [QueueController::class, 'printReceipt'])
     ->middleware('auth')
     ->name('queue.tokens.print');
