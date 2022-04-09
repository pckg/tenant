<?php

namespace Pckg\Tenant\Service;

use Pckg\Framework\Config;
use Pckg\Framework\Exception;
use Pckg\Tenant\IdentifierMethod\Header;
use Pckg\Tenant\IdentifierMethod\HostMapper;
use Pckg\Tenant\IdentifierMethod\HttpReferer;
use Pckg\Tenant\IdentifierMethod\UrlPrefix;
use Ramsey\Uuid\Uuid;

class TenantManager
{

    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function resolveUuid(): ?string
    {
        // @T00D00 - detect tenant switch?
        return collect([
            Header::class,
            UrlPrefix::class,
            HttpReferer::class,
            HostMapper::class,
        ])->realReduce(function ($identifer, $i, $existing) {
            if ($existing) {
                return $existing;
            }

            $object = new $identifer(request());

            return $object->can() ? $object->get() : null;
        }, null);
    }

    public function applyConfigForUuid(string $uuid)
    {
        // must be in one of 3 forms: uuid, a-z string of length < 20
        if (mb_strtolower($uuid) !== $uuid || (!Uuid::isValid($uuid) && !preg_match('/^[a-z0-9]+$/', $uuid))) {
            throw new \Exception('Invalid tenant - ' . $uuid);
        }

        try {
            message('Initializing tenant: ' . $uuid);

            $path = config('pckg.tenant.path', path('storage') . '_tenants');
            $file = $path . '/' . $uuid . '.json';

            if (!file_exists($file)) {
                throw new Exception('Tenant config is not available - ' . $uuid);
            }

            $tenantConfig = json_decode(file_get_contents($file), true);
            if (!$tenantConfig) {
                throw new \Exception('Invalid tenant config - ' . $uuid);
            }

            message('Applying tenant config');
            $this->config->apply($tenantConfig);

            /**
             * Change cookie prefix.
             */
            $identifier = config('identifier');
            $this->config->set('pckg.auth.cookiePrefix', $identifier);
            $this->config->set('pckg.tenant.identifier', $identifier);
        } catch (\Throwable $e) {
            $message = 'Error initializing tenant ' . $uuid . ':' . exception($e);
            error_log($message);
            throw $e;
        }
    }
}
