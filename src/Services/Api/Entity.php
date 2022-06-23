<?php

namespace Ashr\Keonn\Services\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait Entity
{
    /**
     * Create or update shop
     *
     * @param array $data
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function createOrUpdateShop(array $data): Response
    {
        if (isset($data[0]) && is_array($data[0])) {
            foreach ($data as $dt) {
                $this->validateShop($dt);
            }
        } else {
            $this->validateShop($data);
        }

        if (!Storage::disk('local')->exists('keonn/shop')) {
            Storage::disk('local')->makeDirectory('keonn/shop');
        }

        $path = storage_path('app/keonn/shop');
        $fileName = sprintf('%s_%s_%s.csv', 'shop', time(), rand(10000, 1000000000));
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

        return $this->request
            ->attach('file', file_get_contents($filePath), 'shop.csv')
            ->post('/advancloud/import/entity/upload', [
                'type' => 'Shop',
                'token' => $this->config['app_mode'],
            ])
            ->throw();
    }

    /**
     * Validate shop data
     *
     * @throws ValidationException
     */
    public function validateShop(array $data): void
    {
        $validator = Validator::make($data, [
            'code' => 'required',
            'area' => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}