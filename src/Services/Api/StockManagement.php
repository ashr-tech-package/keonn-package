<?php

namespace Ashr\Keonn\Services\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
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
    public function uploadStock(
        array $data,
        string $shop,
        array $optionals = []
    ): Response
    {
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as $dt) {
                $this->validateStock($dt);
            }
        } else {
            $this->validateStock($data);
        }

        if ($optionals && isset($optionals['type'])) {
            $this->validateStockType($optionals);
        }

        if (!Storage::disk('local')->exists('keonn/stock')) {
            Storage::disk('local')->makeDirectory('keonn/stock');
        }

        $path = storage_path('app/keonn/stock');
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
     * Download stock by shop and inventory code
     *
     * @param string $shop
     * @param string $reportType
     * @param array $optionals
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function downloadStock(
        string $shop,
        string $reportType = 'json',
        array $optionals = []
    ): Response
    {
        if ($optionals && isset($optionals['type'])) {
            $this->validateStockType($optionals);
        }

        if ($optionals && isset($optionals['mode'])) {
            $validator = Validator::make($optionals, [
                'mode' => 'required|in:sku,epc'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }

        $url = '/advancloud/import/stock/download';
        if ($optionals && isset($optionals['inventory_code'])) {
            $url = '/advancloud/import/stock/download/' . $optionals['inventory_code'];
            unset($optionals['inventory_code']);
        }

        $parameters = [
            'token' => $this->config['app_mode'],
            'shop' => $shop,
            'reporttype' => $reportType,
        ];

        $parameters = array_merge($parameters, $optionals);

        return $this->request
            ->asForm()
            ->post($url, $parameters)
            ->throw();
    }

    /**
     * Download stock by inventory code
     *
     * @param string $code
     * @param string $shop
     * @param string $reportType
     * @param array $optionals
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function downloadStockByInventoryCode(
        string $code,
        string $shop,
        string $reportType = 'json',
        array $optionals = []
    ): Response
    {
        $optionals['inventory_code'] = $code;

        return $this->downloadStock($shop, $reportType, $optionals);
    }

    /**
     * Check status stock
     *
     * @return Response
     * @throws RequestException
     */
    public function statusStock(): Response
    {
        return $this->request
            ->asForm()
            ->post('/advancloud/import/stock/status', [
                'token' => $this->config['app_mode']
            ])
            ->throw();
    }

    /**
     * @param string $shop
     * @param array $optionals
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function searchStock(string $shop, array $optionals = []): Response
    {
        if ($optionals && isset($optionals['type'])) {
            $this->validateStockType($optionals);
        }

        $parameters = [
            'token' => $this->config['app_mode'],
            'shop' => $shop,
        ];

        $parameters = array_merge($parameters, $optionals);

        return $this->request
            ->asForm()
            ->post('/advancloud/import/stock/search', $parameters)
            ->throw();
    }

    /**
     * Remove uploaded stock
     *
     * @param string $code
     * @param string $shop
     * @return Response
     * @throws RequestException
     */
    public function removeStock(string $code, string $shop): Response
    {
        return $this->request
            ->asForm()
            ->post('/advancloud/import/stock/remove/' . $code, [
                'token' => $this->config['app_mode'],
                'shop' => $shop
            ])
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

    /**
     * Validate stock type
     *
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    public function validateStockType(array $data): void
    {
        $validator = Validator::make($data, [
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
