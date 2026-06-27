<?php

namespace Modules\Classes\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Classes\Models\ClassModel;
use Modules\Classes\Policies\ClassPolicy;

class ClassesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->loadMigrations();
        $this->loadRoutes();
        $this->registerPolicies();
        $this->registerLivewireComponents();
        $this->loadViews();
        $this->loadTranslations();
    }

    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }

    private function registerPolicies(): void
    {
        Gate::policy(ClassModel::class, ClassPolicy::class);
    }

    private function registerLivewireComponents(): void
    {
        \Livewire\Livewire::component('classes.class-list', \Modules\Classes\Livewire\ClassListComponent::class);
        \Livewire\Livewire::component('classes.room-list', \Modules\Classes\Livewire\RoomListComponent::class);
        \Livewire\Livewire::component('classes.class-assignment', \Modules\Classes\Livewire\ClassAssignmentComponent::class);
        \Livewire\Livewire::component('classes.timetable', \Modules\Classes\Livewire\TimetableComponent::class);
        \Livewire\Livewire::component('classes.dashboard', \Modules\Classes\Livewire\DashboardComponent::class);
    }

    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'classes');
    }

    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'classes');
    }
}
