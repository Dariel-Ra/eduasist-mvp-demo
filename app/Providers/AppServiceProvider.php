<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Course;
use App\Models\Guardian;
use App\Models\GuardianStudent;
use App\Models\Student;
use App\Models\Teacher;
use App\Observers\CourseObserver;
use App\Observers\GuardianObserver;
use App\Observers\GuardianStudentObserver;
use App\Observers\StudentObserver;
use App\Observers\TeacherObserver;
use App\Policies\CoursePolicy;
use App\Policies\GuardianStudentPolicy;
use App\Policies\GuardianPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        Guardian::class => GuardianPolicy::class,
        GuardianStudent::class => GuardianStudentPolicy::class,
        Student::class => StudentPolicy::class,
        Teacher::class => TeacherPolicy::class,

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
        Course::observe(CourseObserver::class);
        Guardian::observe(GuardianObserver::class);
        GuardianStudent::observe(GuardianStudentObserver::class);
        Teacher::observe(TeacherObserver::class);
        Student::observe(StudentObserver::class);
 
        // Registrar las policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
