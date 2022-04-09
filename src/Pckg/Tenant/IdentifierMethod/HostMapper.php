<?php

namespace Pckg\Tenant\IdentifierMethod;

use Pckg\Framework\Request;

class HostMapper
{

    protected Request $request;

    /**
     * @var string[]
     */
    protected array $hosts;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->hosts = [
            // 'admin.comms.local:8082' => null, // multi-tenant access, should have /@identifier
            'gnpdev.admin.comms.local:8082' => 'gnpdev',
        ];
    }

    public function can(): bool
    {
        return isset($this->hosts[request()->host()]);
    }

    public function get(): string
    {
        return $this->hosts[request()->host()];
    }
}
