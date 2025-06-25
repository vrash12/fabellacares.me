<?php

namespace App\Providers;

use App\Models\Token;
use App\Models\OpdForm;                 // ① add
use App\Observers\TokenObserver;
use Illuminate\Support\Facades\View;    // ② add
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register() { /* … */ }

    public function boot(): void
    {
        // Existing observer
        Token::observe(TokenObserver::class);

        // ③  View composer for the encoder sidebar
        View::composer('layouts.encoder', function ($view) {
            $sidebarForms = OpdForm::orderBy('name')->get(['id', 'name', 'form_no']);
            $view->with('sidebarForms', $sidebarForms);
        });
    }
}
