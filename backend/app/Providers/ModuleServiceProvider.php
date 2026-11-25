<?php

declare(strict_types=1);

namespace App\Providers;

use App\Shared\Modules\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $registry = new ModuleRegistry;

        foreach ($registry->modules() as $module) {
            if (class_exists($module->provider())) {
                $this->app->register($module->provider());
            }
        }
    }

    public function boot(): void {}
}
