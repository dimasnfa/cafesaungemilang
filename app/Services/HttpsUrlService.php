<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;

class HttpsUrlService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.https_url');
    }

    public function route(string $name, $params = [])
    {
        $url = URL::route($name, $params);
        return preg_replace('/^https?:\/\/[^\/]+/', $this->baseUrl, $url);

    }
}