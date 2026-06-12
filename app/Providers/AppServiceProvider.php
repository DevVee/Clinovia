<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\Patient;
use App\Observers\AppointmentObserver;
use App\Observers\ConsultationObserver;
use App\Observers\MedicineObserver;
use App\Observers\PatientObserver;
use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PatientRepositoryInterface::class,
            PatientRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS for all generated URLs when running behind Render's
        // load balancer. Without this, route() returns http:// URLs because
        // the internal container connection is plain HTTP — even though the
        // browser is talking to https://clinovia.onrender.com.
        if (str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        Patient::observe(PatientObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Consultation::observe(ConsultationObserver::class);
        Medicine::observe(MedicineObserver::class);
    }
}
