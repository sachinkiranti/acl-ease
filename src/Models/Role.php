<?php

namespace SachinKiranti\AclEase\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'status', 'parent_id',
    ];

    /**
     * Roles belongs to many permissions
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany( config('acl-ease.models.permission', Permission::class) )->withTimestamps();
    }

    /**
     * Roles belongs to many users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany( config('acl-ease.models.user', User::class) );
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function inRole(string $permission): bool
    {
        if ($permission) {
            return $this->permissions->contains('slug', $permission);
        }
        return !! $permission->intersect($this->permissions)->count();
    }

    /**
     * Check whether the role contains the provided permission or not
     *
     * @param int $permission
     * @return boolean
     *
     */
    public function containsPermission($permission): bool
    {
        return $this->permissions->contains($permission);
    }

    /**
     * Assign the permissions to given role
     *
     * @param array $permissions
     * @return mixed
     */
    public function assignPermission(array $permissions): mixed
    {
        return $this->permissions()->sync($permissions);
    }

}
