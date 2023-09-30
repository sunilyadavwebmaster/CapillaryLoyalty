<?php

namespace App\functionClass;

class CommonFunction
{
    static function getClientParams($auth)
    {

        return [
            'headers' => ['content-type' => 'application/json'],
            'auth' => [
                $auth->app_key,
                $auth->client_secret_key
            ],
            'base_uri' =>rtrim($auth->base_url,"/")."/",
            'http_errors'=>false
        ];
    }
}