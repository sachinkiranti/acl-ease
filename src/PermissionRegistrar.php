<?php

namespace SachinKiranti\AclEase;

use Illuminate\Support\Facades\Gate;
use Illuminate\Filesystem\Filesystem;
use SachinKiranti\AclEase\Commands\GeneratePermission;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class PermissionRegistrar
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @throws FileNotFoundException
     */
    public function __invoke(): void
    {
        $permissionListFile = app()->basePath('bootstrap') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . GeneratePermission::PERMISSION_LIST_FILE_NAME;

        if ($this->filesystem->exists($permissionListFile)) {
            $permissions = $this->filesystem->getRequire($permissionListFile);

            collect($permissions)->each(function ($permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            });
        }
    }

}
