<?php

namespace Pckg\Tenant\IdentifierMethod;

use Pckg\Framework\Request;

class HttpReferer
{

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function can(): bool
    {
        return strpos($this->request->server('HTTP_REFERER', null), 'https://' . $this->request->server('HTTP_HOST') . '/@') === 0;
    }

    public function get(): string
    {
        return substr(explode('/', $this->request->server('HTTP_REFERER'))[3], 1);
    }
}
