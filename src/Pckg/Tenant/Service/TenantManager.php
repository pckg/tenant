<?php

namespace Pckg\Tenant\Service;

use Pckg\Collection;
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

    public function getHandlers(): Collection
    {
        return collect([
            HostMapper::class,
            UrlPrefix::class,
            Header::class,
            HttpReferer::class,
        ]);
    }

    public function resolveUuid(): ?string
    {
        $uuids = $this->getHandlers()
            ->map(fn($handler) => new $handler(request()))
            ->filter(fn($handler) => $handler->can())
            ->map(fn($handler) => $handler->get())
            ->removeEmpty()
            ->unique();

        if (!$uuids->has()) {
            return null;
        }

        if ($uuids->count() !== 1) {
            error_log('Multiple tenants resolved ' . $uuids->toJSON());
            throw new Exception('Multiple tenants resolved ' . $uuids->toJSON());
        }

        $valid = $uuids->filter(fn($value) => mb_strtolower($value) === $value
            && (Uuid::isValid($value) || preg_match('/^[a-z0-9]+$/', $value)));

        if ($valid->count() !== 1) {
            error_log('Invalid tenants detected ' . $uuids->toJSON());
            throw new Exception('Invalid tenants detected ' . $uuids->toJSON());
        }

        return $uuids->first();
    }

    public function applyConfigForUuid(string $uuid)
    {
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
