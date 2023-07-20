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

class AclEaseServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/acl-ease.php' => config_path('acl-ease.php'),
        ], 'acl-ease');
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

}
