<?php

namespace Pckg\Tenant\IdentifierMethod;

class Header
{
    public function getHttpHeader()
    {
        return config('pckg.tenant.header', 'X-Pckg-Tenant-Id');
    }

    public function can()
    {
        $header = $this->getHttpHeader();
        return $header && !!request()->header($header);
    }

    public function get()
    {
        return request()->header($this->getHttpHeader());
    }
}
