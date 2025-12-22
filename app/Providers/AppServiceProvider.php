<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Application;
use App\Models\Config;

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
     * Add configs macro to Application
     */
    private function addConfigsMacro(): void
    {
        Application::macro('configs', function () {
            return Config::query();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add configs macro to Application
        $this->addConfigsMacro();

        // Set application locale based on user preference or session
        $this->setAppLocale();
    }

    /**
     * Set the application locale based on user authentication or session
     */
    private function setAppLocale(): void
    {
        $locale = null;
        
        // Check if user is authenticated and has a locale preference
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        }
        
        // Fallback to session locale if no user locale
        if (!$locale && Session::has('locale')) {
            $locale = Session::get('locale');
        }
        
        // Fallback to default locale if nothing is set
        if (!$locale) {
            $locale = config('app.locale', 'de');
        }
        
        // Validate the locale (only allow supported languages)
        $allowedLocales = ['de', 'en', 'nl'];
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'de'; // Default to German if invalid
        }
        
        // Set the application locale
        app()->setLocale($locale);
    }
}
