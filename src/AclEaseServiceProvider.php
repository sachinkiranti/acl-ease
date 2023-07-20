<?php

/**
 * AclEase Service Provider
 *
 * @author    Sachin Kiranti <sachinkiranti@gmail.com>
 * @copyright 2023 Sachin Kiranti (https://www.raisachin.com.np)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/sachinkiranti/acl-ease
 */

namespace SachinKiranti\AclEase;

use Illuminate\Support\ServiceProvider;
use SachinKiranti\AclEase\Commands\GeneratePermission;
use SachinKiranti\AclEase\Middleware\HasAccessMiddleware;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AclEaseServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function boot(): void
    {
        $this->commands([
            GeneratePermission::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/acl-ease.php' => config_path('acl-ease.php'),
        ], 'acl-ease');


        $this->publishes([
            __DIR__.'/../database/migrations/create_acl_ease_tables.php.stub' => $this->getMigration(),
        ], 'acl-ease-migrations');

        // Middleware can be used access:admin,user
        $this->app['router']->aliasMiddleware( config('acl-ease.middleware_alias'), HasAccessMiddleware::class );

        // Register the permissions
        (new PermissionRegistrar( $this->app['files'] ))();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/acl-ease.php', 'acl-ease'
        );
    }

    protected function getMigration(): string
    {
        $migrationFullPath = $this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . date('Y_m_d_His');
        return $migrationFullPath . '_' . 'create_acl_ease_tables.php';
    }

}
