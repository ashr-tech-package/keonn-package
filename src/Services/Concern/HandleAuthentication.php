<?php

namespace Ashr\Keonn\Services\Concern;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * @property PendingRequest $request
 * @property array $config
 */
trait HandleAuthentication
{
    /**
     * Return list of credential value from the config.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return Arr::only($this->config, [
            'username',
            'password',
            'grant_type',
            'client_id',
        ]);
    }

    /**
     * Get auth token if token is expired then create new by re-login
     *
     * @return mixed
     */
    public function getToken(): mixed
    {
        return Cache::remember('keonn_auth_token', 3600, function () {
            return $this->login();
        });
    }

    /**
     * Login to keonn ouath
     *
     * @return string
     * @throws RequestException
     */
    public function login(): string
    {
        $response = Http::asForm()->post($this->config['base_url'] . '/advancloud/oauth/token', $this->getCredentials())
            ->throw()
            ->json();

        return data_get($response, 'access_token', '');
    }
}