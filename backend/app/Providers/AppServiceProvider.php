<?php

namespace App\Providers;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
use App\Events\ProfileUpdated;
use App\Listeners\ProcessLog;
use App\Mail\Transport\ResendTransport;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Internship;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Events\PublicStatsUpdated;
use App\Events\DashboardUpdated;
use App\Policies\LogPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('mail.manager', function ($manager) {
            $manager->extend('resend_custom', function () {
                return new ResendTransport(
                    config('services.resend.key')
                );
            });

            return $manager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for cURL error 77 on local Windows environment
        if (config('app.env') === 'local' && file_exists(storage_path('cacert.pem'))) {
            ini_set('curl.cainfo', storage_path('cacert.pem'));
            ini_set('openssl.cafile', storage_path('cacert.pem'));
        }

        // Force HTTPS in production (crucial for Railway and Google OAuth)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // ELITE STANDARDS: N+1 Prevention
        Model::preventLazyLoading(! app()->isProduction());

        // Implicitly grant "Super Admin" role all permissions
        // This works in the gate-checked permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole(UserRole::SUPER_ADMIN->value) ? true : null;
        });

        Gate::policy(ActivityLog::class, LogPolicy::class);
        Gate::policy(AuditLog::class, LogPolicy::class);
        Gate::policy(SecurityEvent::class, LogPolicy::class);

        Event::listen(
            ProfileUpdated::class,
            ProcessLog::class
        );

        // Register Gates based on Permissions Enum
        foreach (PermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function ($user) use ($permission) {
                return $user->hasPermissionTo($permission->value);
            });
        }

        Gate::define('use-ai', function ($user) {
            return $user->email_verified_at !== null && $user->is_active;
        });

        Gate::define('view-notification-channel', function ($user, $id) {
            return (int) $user->id === (int) $id;
        });

        // Broadcast public stats
        $statsModels = [User::class, Company::class, Internship::class, Application::class];
        foreach ($statsModels as $model) {
            $model::saved(fn () => event(new PublicStatsUpdated()));
            $model::deleted(fn () => event(new PublicStatsUpdated()));
        }

        // Broadcast role-specific dashboard updates
        $dispatchDashboardUpdates = function ($model) {
            $channels = ['admins.online'];

            if ($model instanceof Application) {
                $channels[] = 'App.Models.User.' . $model->user_id;
                
                if ($model->internship) {
                    $channels[] = 'company.' . $model->internship->company_id;
                    if ($model->internship->mentor_id) {
                        $channels[] = 'mentor.' . $model->internship->mentor_id;
                    }
                }
            } elseif ($model instanceof Internship) {
                $channels[] = 'company.' . $model->company_id;
                if ($model->mentor_id) {
                    $channels[] = 'mentor.' . $model->mentor_id;
                }
            } elseif ($model instanceof Company) {
                $channels[] = 'company.' . $model->id;
            }

            event(new DashboardUpdated(array_unique($channels)));
        };

        foreach ($statsModels as $model) {
            $model::saved($dispatchDashboardUpdates);
            $model::deleted($dispatchDashboardUpdates);
        }
    }
}
