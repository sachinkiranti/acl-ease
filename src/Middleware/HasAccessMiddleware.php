<?php

namespace SachinKiranti\AclEase\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class HasAccessMiddleware
{

    public function handle($request, Closure $next)
    {
        $roles = array_slice(func_get_args(), 2);

        if (empty($roles)) {
            $roles = (array) config('acl-ease.default_role');
        }

        abort_if(
            auth()->check() && $request->user()->doesNotHaveRole($roles),
            Response::HTTP_FORBIDDEN
        );

        return $next($request);
    }

}
