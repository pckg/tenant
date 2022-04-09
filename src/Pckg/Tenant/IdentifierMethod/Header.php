<?php

namespace Pckg\Tenant\IdentifierMethod;

class Header
{

    public function can()
    {
        return !!request()->header('X-Comms-Store-Uuid');
    }

    public function get()
    {
        return request()->header('X-Comms-Store-Uuid');
    }
}
