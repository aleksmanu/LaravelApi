<?php

namespace App\Modules\Common\Traits;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TestsAuthenticatedRoute
{
    /**
     * @param $user
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    protected function apiAs($user, $method, $uri, array $data = [], array $headers = [])
    {
        $token = auth()->login($user);

        $headers = array_merge([
            'Authorization' => 'Bearer ' . $token,
        ], $headers);

        return $this->api($method, $uri, $data, $headers);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    protected function api($method, $uri, array $data = [], array $headers = [])
    {
        return $this->json($method, $uri, $data, $headers);
    }
}
