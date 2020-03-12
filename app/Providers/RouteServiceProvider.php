<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('domain', '[a-z0-9.\-]+');
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapWebRoutes($router);

        $this->mapApiRoutes($router);

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace, 'middleware' => ['web', 'hasTeam'],
        ], function ($router) {
            require base_path('routes/web.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/url.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/source.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/media.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/content.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/domains.php');
        });
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => ['web'],
            'prefix' => 'api/v0.1/',
        ], function ($router) {
            require base_path('routes/cat.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapApiRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => 'api',
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}
