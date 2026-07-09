<?php

namespace App\Providers;

use App\Models\WebsiteText;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        View::composer('layouts.partials.public-footer', function ($view) {
            $view->with('texts', WebsiteText::whereIn('group', ['footer', 'contact'])->pluck('value', 'key'));
        });

        View::composer('layouts.partials.whatsapp-button', function ($view) {
            $view->with('texts', WebsiteText::where('group', 'whatsapp')->pluck('value', 'key'));
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        // MySQL < 5.7.7 / MariaDB < 10.2.2 (common on shared hosting) cap index keys at
        // 767 bytes, and utf8mb4 uses 4 bytes/char — a default VARCHAR(255) unique index
        // exceeds that. Capping the default string length avoids the "key too long" error.
        Schema::defaultStringLength(191);

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
