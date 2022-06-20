<?php

namespace Ashr\Keonn\Services\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait StockManagement
{
    /**
     * Upload stock
     *
     * @param array $data
     * @param string $shop
     * @param array $optionals
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function uploadStock(array $data, string $shop, array $optionals = []): Response
    {
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as $dt) {
                $this->validateStock($dt);
            }
        } else {
            $this->validateStock($data);
        }

        if ($optionals && $optionals['type']) {
            $validator = Validator::make($optionals, [
                'type' => 'required|in:UPLOAD,RETURN,PURCHASE,PICKING,ASN,REFERENCE'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }

        $path = storage_path('app');
        $fileName = sprintf('%s_%s_%s.csv', 'stock', time(), rand(10000, 1000000000));
        $filePath = sprintf('%s/%s', $path, $fileName);
        $file = fopen($filePath, 'w');
        if (isset($data[0]) && is_array($data[0])) {
            fputcsv($file, array_keys($data[0]));
            foreach ($data as $dt) {
                fputcsv($file, array_values($dt));
            }
        } else {
            fputcsv($file, array_keys($data));
            fputcsv($file, array_values($data));
        }

        fclose($file);

        $parameters = [
            'token' => $this->config['app_mode'],
            'shop' => $shop
        ];

        $parameters = array_merge($parameters, $optionals);

        return $this->request
            ->attach('file', file_get_contents($filePath), 'stock.csv')
            ->post('/advancloud/import/stock/upload', $parameters)
            ->throw();
    }

    /**
     * Validate stock data
     *
     * @throws ValidationException
     */
    public function validateStock(array $data): void
    {
        $validator = Validator::make($data, [
            'code' => 'required',
            'stock' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}