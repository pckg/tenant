<?php

namespace Pckg\Tenant\Middleware;

use Pckg\Tenant\Service\TenantManager;

class InitTenant
{
    public TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        if (!server('HTTP_HOST')) {
            throw new \Exception('Tenants are not available in console, yet');
        }

        $uuid = $this->tenantManager->resolveUuid();

        if (!$uuid) {
            return;
            // throw new \Exception('Tenant not detected');
        }

        // require user to be logged in with tenant account? redirect to root/homepage?
        $this->tenantManager->applyConfigForUuid($uuid);
    }
}
