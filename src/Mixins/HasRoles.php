<?php

namespace SachinKiranti\AclEase\Mixins;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany( config('acl-ease.model') );
    }

    /**
     * @param array $role
     * @return array
     */
    public function assignRole(array $role): array
    {
        return $this->roles()->sync($role);
    }

    /**
     * Return true if user has given roles
     *
     * @usage $user->hasRole('super-admin','user')
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function havePermission(string $permission): bool
    {
        $roles = auth()->user()->load('roles.permissions')->roles;

        foreach($roles as $role)
        {
            if($role->name === \Foundation\Lib\Role::DEFAULT_ROLE)
            {
                return true;
            }
            return $role->inRole($permission);
        }
    }

    public function hasPermission($permission): bool
    {
        return $this->permissions->where('slug', $permission->slug)->exists();
    }

    public function doesNotHaveRole(...$roles): bool
    {
        $roles = Arr::flatten($roles);

        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return false;
            }
        }
        return true;
    }

}
