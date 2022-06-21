<?php

namespace Ashr\Keonn\Services;

use Ashr\Keonn\Services\Api\DataManagement;
use Ashr\Keonn\Services\Api\Media;
use Ashr\Keonn\Services\Api\Report;
use Ashr\Keonn\Services\Api\StockManagement;
use Ashr\Keonn\Services\Concern\HandleAuthentication;
use BadMethodCallException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Traits\Macroable;

class KeonnApi
{
    use Macroable {
        __call as macroCall;
    }
    use HandleAuthentication;
    use DataManagement;
    use StockManagement;
    use Report;
    use Media;

    public static array $escapedMethods = [
        'createRequestInstance',
        'login',
        'interactWithApiProduct',
        'validateStock',
    ];

    public static array $storageMethods = [
        'uploadFile',
        'deleteFile',
        'getFile',
    ];

    /**
     * Instance of \Illuminate\Http\Client\PendingRequest to make the request.
     *
     * @var PendingRequest
     */
    protected PendingRequest $request;

    /**
     * Create a new instance class.
     *
     * @param array $config
     * @return void
     */
    public function __construct(protected array $config)
    {
        $this->createRequestInstance();
    }

    /**
     * Create Laravel HTTP client request instance.
     *
     * @return void
     */
    public function createRequestInstance(): void
    {
        $this->request = Http::baseUrl($this->config['base_url'])
            ->withToken($this->getToken());
    }


    public function sendRequestToKeonn(string $method, array $parameters = [])
    {
        return $this->{$method}(...$parameters);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (in_array($method, static::$escapedMethods, true)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s is in the escaped method list.', static::class, $method
            ));
        }

        if (in_array($method, static::$storageMethods, true)) {
            return $this->{$method}(...$parameters);
        }

        return $this->sendRequestToKeonn($method, $parameters);
    }
}