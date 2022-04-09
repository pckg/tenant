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
        return substr($this->request->getUrl(), 0, 2) === '/@';
    }

    public function get(): string
    {
        $url = $this->request->getUrl();
        $secondPos = strpos($url, '/', 1);
        $length = ($secondPos ? $secondPos : strlen($url)) - 2;
        $uuid = substr($url, 2, $length);
        $newUrl = substr($url, strlen($uuid) + 2);
        //request()->setUrl($newUrl);
        //server()->set('REQUEST_URI', $newUrl);
        message('Rewrote to', $newUrl);
        return $uuid;
    }
}
