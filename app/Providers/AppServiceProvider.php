<?php

namespace App\Providers;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Observers\GuardianObserver;
use App\Observers\StudentObserver;
use App\Observers\TeacherObserver;
use App\Policies\GuardianPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Teacher::class => TeacherPolicy::class,
        Guardian::class => GuardianPolicy::class,
        Student::class => StudentPolicy::class,
    ];

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
        // Registrar Observers
        Teacher::observe(TeacherObserver::class);
        Guardian::observe(GuardianObserver::class);
        Student::observe(StudentObserver::class);

        // Registrar las policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
