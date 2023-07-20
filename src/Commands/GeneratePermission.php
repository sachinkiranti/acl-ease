<?php

namespace SachinKiranti\AclEase\Commands;

use Exception;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository as Config;

class GeneratePermission extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'acl:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Route-Name-Based Permissions for Laravel.';

    /**
     * The FileSystem instance
     *
     * @var FileSystem $file
     */
    private FileSystem $file;

    /**
     * The Config repository instance
     *
     * @var Config $config
     */
    private Config $config;

    /**
     * The Config repository instance
     *
     * @var Router $router
     */
    private Router $router;

    # This file name will be used to cache the generated permissions
    const PERMISSION_LIST_FILE_NAME = 'acl-ease.php';

    /**
     * GeneratePermission constructor.
     *
     * @param Filesystem $file
     * @param Config $config
     * @param Router $router
     */
    public function __construct( Filesystem $file, Config $config, Router $router )
    {
        parent::__construct();
        $this->file = $file;
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $this->info('Automatically Generating Permissions Based on Route Names ... !!!');

        $permissions = $this->cachePermissions($this->retrievePermissions());
        $this->savePermissions($permissions);

        $this->info('Permission Generation Complete! Automatically Created Permissions Using Route Names.');
    }

    /**
     * Cache and return permissions
     *
     * @param array $permissions
     * @return array
     * @throws Exception
     */
    private function cachePermissions(array $permissions): array
    {
        $cachePath = base_path('bootstrap') . DIRECTORY_SEPARATOR . 'cache';

        if ( ! $this->file->exists($cachePath) ) {
            $this->file->makeDirectory($cachePath, 0755, true, true);
        }

        $file = $cachePath . DIRECTORY_SEPARATOR . self::PERMISSION_LIST_FILE_NAME;

        if (! is_writable(dirname($file))) {
            throw new Exception('The '.dirname($file).' directory must be present and writable.');
        }

        $this->file->replace($file, '<?php'. PHP_EOL . PHP_EOL .'return ' . var_export($permissions, true).';');

        return $permissions;
    }

    /**
     * Save permissions
     *
     * @param array $permissions
     */
    private function savePermissions(array $permissions): void
    {
        foreach ($permissions as $permission) :

            $description = $this->resolvePermissionDescription($permission);

            app($this->config->get('acl-ease.models.permission'))->updateOrCreate(
                [ 'slug' => $permission, ],
                [ 'name' => $description, 'slug' => $permission, 'description' => $description, ]
            );

        endforeach;
    }

    /**
     * Return resolved permission slug
     *
     * @param string $route
     * @return string
     */
    private function resolvePermissionSlug(string $route): string
    {
        return str_replace($this->getBlackListedCharacters(), $this->getGlue(), $route);
    }

    /**
     * Return resolved permission description
     *
     * @param string $route
     * @return string
     */
    private function resolvePermissionDescription(string $route): string
    {
        return ucwords(str_replace($this->getGlue(), ' ', $route));
    }

    /**
     * Return permissions
     *
     * @return array
     */
    private function retrievePermissions(): array
    {
        $permissions = [];

        $ignoredRoutes = $this->getIgnoredRoutes();

        foreach ($this->router->getRoutes() as $route) :

            if (!in_array($route->getName(), $ignoredRoutes)) {
                if ($route->getName()) {
                    $permissions[] = $this->resolvePermissionSlug($route->getName());
                }
            }

        endforeach;

        return $permissions;
    }

    private function getGlue(): string
    {
        return $this->config->get('acl-ease.glue', '_');
    }

    /**
     * Return ignored routes
     *
     * @return array
     */
    private function getIgnoredRoutes(): array
    {
        return $this->config->get('acl-ease.ignore_routes', []);
    }

    /**
     * Return black listed characters
     *
     * This characters will be replaced using constant glue while generating permission
     *
     * @return array
     */
    private function getBlackListedCharacters(): array
    {
        return $this->config->get('acl-ease.black_listed_characters', [ '.', ]);
    }

    private function disableRoutes(): void
    {
        $this->config->set('app.environment', 'production');
    }

}
