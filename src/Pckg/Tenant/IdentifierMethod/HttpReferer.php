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
        $url = $this->request->url();

        // don't accept on tenant url
        if (substr($url, 0, 2) === '/@') {
            return false;
        }

        $referer = $this->request->server('HTTP_REFERER', null);
        // require referer
        if (!$referer) {
            return false;
        }

        $host = $this->request->server('HTTP_HOST');
        $startsWithTenant = strpos($referer, 'https://' . $host . '/@') === 0;

        // accept only from tenant urls (on non-tenant url)
        if (!$startsWithTenant) {
            return false;
        }

        $firstPart = explode('?', explode('/', $url)[1] ?? '')[0];

        return strlen($firstPart) > 2;
    }

    public function get(): string
    {
        return explode('?', substr(explode('/', $this->request->server('HTTP_REFERER'))[3], 1))[0];
    }
}
