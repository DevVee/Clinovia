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
        Patient::observe(PatientObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Consultation::observe(ConsultationObserver::class);
        Medicine::observe(MedicineObserver::class);
    }
}
