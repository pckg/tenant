<?php

namespace Pckg\Tenant\IdentifierMethod;

use Pckg\Framework\Request;

class UrlPrefix
{

    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function can(): bool
    {
        $url = $this->request->getUrl();

        return substr($url, 0, 2) === '/@' && strlen($url) > 2;
    }

    public function get(): string
    {
        return substr(explode('/', $this->request->getUrl())[1], 1);
    }
}
