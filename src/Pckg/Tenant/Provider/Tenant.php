<?php

namespace Pckg\Tenant\Provider;

use Pckg\Framework\Provider;
use Pckg\Tenant\Middleware\RequireTenant;

class Tenant extends Provider
{
    public function middlewares()
    {
        return [
            RequireTenant::class,
        ];
    }
}