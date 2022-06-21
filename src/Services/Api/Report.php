<?php

namespace Ashr\Keonn\Services\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

trait Report
{
    /**
     * @param string $reportCode
     * @param string $reportType
     * @param array $optionals
     * @return Response
     * @throws RequestException
     */
    public function report(
        string $reportCode,
        string $reportType = 'json',
        array $optionals = []
    ): Response
    {
        $parameters = [
            'token' => $this->config['app_mode'],
            'report' => $reportCode,
            'reporttype' => $reportType,
        ];

        $parameters = array_merge($parameters, $optionals);

        return $this->request
            ->asForm()
            ->post('/advancloud/appreportgeneration/download', $parameters)
            ->throw();
    }
}