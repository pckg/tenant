<?php

namespace Pckg\Tenant\Middleware;

use Pckg\Auth\Controller\Auth;
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

        if (in_array('tag:internal', $tags)) {
            return $next();
        }

        $controller = router()->get('controller');
        if ($controller === Auth::class) {
            return $next();
        }

        $identifier = config('pckg.tenant.identifier');
        if (!$identifier) {
            throw new \Exception('Tenant not loaded');
        }

        $auth = auth();

        //$auth->useProvider('frontend');
        //$auth->setSecureCookiePrefix(config('pckg.tenant.identifier', null));

        $isLoggedIn = $auth->isLoggedIn();
        $user = $auth->user();
        if (!$user) {
            trigger(RequireTenant::class . '.user.unauthenticated');

            throw new Unauthorized();
        }

        trigger(RequireTenant::class . '.validateUser', ['user' => $user]);

        return $next();
    }
}
