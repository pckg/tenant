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
            throw new \Exception('Tenant not loaded');
        }

        //trigger(RequireTenant::class . '.validateUser');
        $user = auth()->user();
        if (!$user) {
            response()->redirect('/?reason=uauthenticated&tenant=' . $identifier);
            throw new Unauthorized();
        } else if (!$user->isSuperadmin()) {
            response()->redirect('/?reason=unauthorized&tenant=' . $identifier);
            throw new Unauthorized('Only superadmins can access this page');
        }

        return $next();
    }
}
