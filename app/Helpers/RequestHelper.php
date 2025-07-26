<?php

if (!function_exists('custom_route')) {
    function custom_route($name, $parameters = [], $absolute = true)
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return app('https_url')->route($name, $parameters);
        }

        return app('url')->route($name, $parameters, $absolute);
    }
}
