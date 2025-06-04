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
use App\Http\Controllers\EncoderOpdFormController;
use App\Http\Controllers\EncoderFollowUpOpdFormController;
use App\Http\Controllers\EncoderHighRiskOpdFormController;
use App\Http\Controllers\EncoderOpdbFormController;
use App\Http\Controllers\ObOpdFormController;
use App\Http\Controllers\PatientVisitController;

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HighRiskOpdFormController;

// 1) Authentication & root redirect
Auth::routes();
Route::get('/', fn() => redirect()->route('login'));

// 2) Print a queue token (requires authentication)
Route::get('queue/{token}/print', [QueueController::class, 'printReceipt'])
     ->middleware('auth')
     ->name('queue.tokens.print');

// 3) AJAX patient‐search for Select2 (admin & encoder)
Route::get('patients/search', [PatientRecordController::class, 'search'])
     ->middleware('auth','role:admin,encoder')
     ->name('patients.search');

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

Route::middleware('auth')->group(function () {
    // Show “New Follow-Up Record” (OPD-F-08)
    Route::get('follow-up-opd-forms/create', [FollowUpOpdFormController::class, 'create'])
         ->name('follow-up-opd-forms.create');

    // Handle the POST from that form
    Route::post('follow-up-opd-forms', [FollowUpOpdFormController::class, 'store'])
         ->name('follow-up-opd-forms.store');
         Route::get('follow-up-opd-forms', [FollowUpOpdFormController::class, 'index'])
     ->name('follow-up-opd-forms.index');
     Route::get('follow-up-opd-forms/{form}', [FollowUpOpdFormController::class, 'show'])
      ->name('follow-up-opd-forms.show');
     Route::get('follow-up-opd-forms/{form}/edit', [FollowUpOpdFormController::class, 'edit'])
          ->name('follow-up-opd-forms.edit');
     Route::put('follow-up-opd-forms/{form}', [FollowUpOpdFormController::class, 'update'])
          ->name('follow-up-opd-forms.update');
     Route::delete('follow-up-opd-forms/{form}', [FollowUpOpdFormController::class, 'destroy'])
          ->name('follow-up-opd-forms.destroy');
});

// 5) Public “General” queue and window selection
Route::get('/queues/general',    [QueueController::class,'selectGeneral'])
     ->name('queue.general_select');

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

    Route::get ('patients/{patient}/visits/{visit}',
        [PatientVisitController::class,'show'])
        ->name('patients.visits.show');
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

    // 8) Encoder-only OPD form routes
    Route::middleware(['auth','role:encoder'])
         ->prefix('encoder/opd_forms')
         ->name('encoder.opd_forms.')
         ->group(function () {

        // 8.1) Main OPD forms (index/create/show/edit/update/destroy)
        Route::get('/',                        [EncoderOpdFormController::class, 'index'])
             ->name('index');
        Route::get('create',                   [EncoderOpdFormController::class, 'create'])
             ->name('create');
        Route::post('/',                       [EncoderOpdFormController::class, 'store'])
             ->name('store');
        Route::get('{submission}',             [EncoderOpdFormController::class, 'show'])
             ->name('show');
        Route::get('{submission}/edit',        [EncoderOpdFormController::class, 'edit'])
             ->name('edit');
        Route::put('{submission}',             [EncoderOpdFormController::class, 'update'])
             ->name('update');
        Route::delete('{submission}',          [EncoderOpdFormController::class, 'destroy'])
             ->name('destroy');

        // 8.2) Follow-Up OPD forms (nested under /encoder/opd_forms/follow_up)
        Route::get('follow_up',                [EncoderFollowUpOpdFormController::class, 'index'])
             ->name('follow_up.index');
        Route::get('follow_up/create',         [EncoderFollowUpOpdFormController::class, 'create'])
             ->name('follow_up.create');
        Route::post('follow_up',               [EncoderFollowUpOpdFormController::class, 'store'])
             ->name('follow_up.store');
        Route::get('follow_up/{followup}',     [EncoderFollowUpOpdFormController::class, 'show'])
             ->name('follow_up.show');
        Route::get('follow_up/{followup}/edit',[EncoderFollowUpOpdFormController::class, 'edit'])
             ->name('follow_up.edit');
        Route::put('follow_up/{followup}',     [EncoderFollowUpOpdFormController::class, 'update'])
             ->name('follow_up.update');
        Route::delete('follow_up/{followup}',  [EncoderFollowUpOpdFormController::class, 'destroy'])
             ->name('follow_up.destroy');

        // 8.3) High-Risk OPD forms (nested under /encoder/opd_forms/high_risk)
        Route::get('high_risk',                [EncoderHighRiskOpdFormController::class, 'index'])
             ->name('high_risk.index');
        Route::get('high_risk/create',         [EncoderHighRiskOpdFormController::class, 'create'])
             ->name('high_risk.create');
        Route::post('high_risk',               [EncoderHighRiskOpdFormController::class, 'store'])
             ->name('high_risk.store');
        Route::get('high_risk/{highrisk}',     [EncoderHighRiskOpdFormController::class, 'show'])
             ->name('high_risk.show');
        Route::get('high_risk/{highrisk}/edit',[EncoderHighRiskOpdFormController::class, 'edit'])
             ->name('high_risk.edit');
        Route::put('high_risk/{highrisk}',     [EncoderHighRiskOpdFormController::class, 'update'])
             ->name('high_risk.update');
        Route::delete('high_risk/{highrisk}',  [EncoderHighRiskOpdFormController::class, 'destroy'])
             ->name('high_risk.destroy');

        // 8.4) OPDB forms (nested under /encoder/opd_forms/opdb)
        Route::get('opdb',                     [EncoderOpdbFormController::class, 'index'])
             ->name('opdb.index');
        Route::get('opdb/create',              [EncoderOpdbFormController::class, 'create'])
             ->name('opdb.create');
        Route::post('opdb',                    [EncoderOpdbFormController::class, 'store'])
             ->name('opdb.store');
        Route::get('opdb/{opdb}',              [EncoderOpdbFormController::class, 'show'])
             ->name('opdb.show');
        // If OPDB is read‐only for encoders, omit edit/update/destroy; otherwise uncomment:
        // Route::get('opdb/{opdb}/edit',   [EncoderOpdbFormController::class, 'edit'])->name('opdb.edit');
        // Route::put('opdb/{opdb}',        [EncoderOpdbFormController::class, 'update'])->name('opdb.update');
        // Route::delete('opdb/{opdb}',     [EncoderOpdbFormController::class, 'destroy'])->name('opdb.destroy');
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