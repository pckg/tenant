<?php

namespace Pckg\Tenant\Middleware;

use Pckg\Framework\Exception\Unauthorized;

class RequireTenant
{
    /**
     * @throws \Throwable
     */
    public function execute(callable $next)
    {
        if (!server('HTTP_HOST')) {
            throw new \Exception('Tenants are not available in console, yet');
        }

        $tags = router()->get('tags');
        if (in_array('tenant:skip', $tags)) {
            return $next();
        }

        $identifier = config('pckg.tenant.identifier');
        if (!$identifier) {
            //response()->redirect('/');
            throw new \Exception('Tenant not loaded');
        }

        $user = auth()->user();
        if (!$user) {
            //throw new Unauthorized();
        }

        if (false && !$user->isSuperadmin()) {
            //throw new Unauthorized('Only superadmins can access this page');
        }

        return $next();
    }
}
