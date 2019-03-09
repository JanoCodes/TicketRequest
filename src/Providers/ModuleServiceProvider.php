<?php
/**
 * Jano Ticketing System
 * Copyright (C) 2016-2019 Andrew Ying and other contributors.
 *
 * This file is part of Jano Ticketing System.
 *
 * Jano Ticketing System is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * v3.0 supplemented by additional permissions and terms as published at
 * COPYING.md.
 *
 * Jano Ticketing System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

namespace Jano\Modules\TicketRequest\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Binding of implementations to abstracts.
     *
     * @var array
     */
    protected $implementations = [
        \Jano\Modules\TicketRequest\Contracts\RequestContract::class =>
            \Jano\Modules\TicketRequest\Repositories\RequestRepository::class
    ];

    /**
     * Array of commands available for the module
     *
     * @var array
     */
    protected $commands = [
        \Jano\Modules\TicketRequest\Console\OpenTicketRequests::class,
        \Jano\Modules\TicketRequest\Console\CloseTicketRequests::class
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerBindings();
        $this->registerCommands();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /*
     * Registers the repositories used for the application.
     *
     * @return void
     */
    protected function registerBindings()
    {
        foreach ($this->implementations as $abstract => $implementation) {
            $this->app->bind($abstract, function ($app) use ($implementation) {
                return new $implementation();
            });
        }
    }

    /**
     * Register console commands
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->commands($this->commands);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('ticket-request.php'),
        ], 'config');
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'ticket-request');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/ticket-request');

        $sourcePath = __DIR__.'/../../resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/ticket-request';
        }, \Config::get('view.paths')), [$sourcePath]), 'ticket-request');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/ticket-request');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'ticket-request');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../../resources/lang', 'ticket-request');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../../database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
