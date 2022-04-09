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
        $this->hosts = config('pckg.tenant.hosts', []);
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
