<?php

namespace Ashr\Keonn\Services\Concern;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

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
        return Cache::remember('keonn_auth_token', $this->config['keonn_token_expired_time'], function () {
            return $this->login();
        });
    }

    /**
     * Clear token when request not authenticated
     *
     * @return void
     */
    public function clearToken(): void
    {
        Cache::forget('keonn_auth_token');
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

    /**
     * Register closure on the request instance to handle login re-attempt process
     * when the given response is unauthorized.
     *
     * @param  int  $times
     * @param  int  $sleep
     * @param  bool  $throw
     * @return void
     */
    protected function reattemptLoginWhenUnauthorized(int $times, int $sleep = 0, bool $throw = true): void
    {
        $this->request->retry($times, $sleep, function (Throwable $exception, PendingRequest $request) {
            if (! $exception instanceof RequestException || $exception->getCode() !== HttpResponse::HTTP_UNAUTHORIZED) {
                return false;
            }

            $this->clearToken();
            $this->getToken();

            return true;
        }, $throw);
    }
}