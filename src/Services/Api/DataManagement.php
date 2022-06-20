<?php

namespace Ashr\Keonn\Services\Api;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait DataManagement
{
    /**
     * Create new product
     *
     * @param array $data
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function createProduct(array $data): Response
    {
        return $this->interactWithApiProduct($data);
    }

    /**
     * Update product
     *
     * @param array $data
     * @param bool $allFields
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function updateProduct(array $data, bool $allFields = false): Response
    {
        return $this->interactWithApiProduct(
            $data,
            'import',
            'excel',
            $allFields,
            true
        );
    }

    /**
     * Delete product
     *
     * @param array $data
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function deleteProduct(array $data): Response
    {
        return $this->interactWithApiProduct(
            $data,
            'delete',
        );
    }

    /**
     * Create new product or update if record exists
     *
     * @throws RequestException
     * @throws ValidationException
     */
    public function createOrUpdateProduct(array $data, bool $allFields = false): Response
    {
        return $this->interactWithApiProduct(
            $data,
            'import',
            'excel',
            $allFields
        );
    }

    /**
     * @throws RequestException
     * @throws ValidationException
     */
    public function interactWithApiProduct(
        array $data,
        string $operation = 'import',
        string $reportType = 'excel',
        bool $allFields = false,
        bool $updateOnly = false,
    ): Response
    {
        $validator = Validator::make($data, [
            'itemtype' => 'required',
            'productid' => 'required',
            'skuid' => ['required_if:itemtype,==,skucolor', 'required_if:itemtype,==,sku'],
            'code' => ['required_if:itemtype,==,skucolor', 'required_if:itemtype,==,sku'],
            'name' => 'required',
            'category' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $path = storage_path('app');
        $fileName = sprintf('%s_%s_%s.csv', 'product', time(), rand(10000, 100000000));
        $filePath = sprintf('%s/%s', $path, $fileName);
        $file = fopen($filePath, 'w');
        fputcsv($file, array_keys($data));
        fputcsv($file, array_values($data));

        fclose($file);

        return $this->request
            ->attach('file', file_get_contents($filePath), 'product.csv')
            ->post('/advancloud/import/upload', [
                'token' => $this->config['app_mode'],
                'allfields' => $allFields,
                'updateonly' => $updateOnly,
                'operation' => $operation,
                'reporttype' => $reportType,
            ])
            ->throw();
    }

    /**
     * Removing data from applications
     * Parts: configuration, data, resources
     *
     * @param string $part
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function deletePart(string $part): Response
    {
        $data = ['part' => $part];
        $validator = Validator::make($data, [
            'part' => 'required|in:configuration,data,resources'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->request
            ->asForm()
            ->post('/advancloud/import/app/remove', [
                'token' => $this->config['app_mode'],
                'parts' => $part
            ])
            ->throw();
    }

    /**
     * Clone data between app mode example production to development
     * Parts: configuration, data, resources
     *
     * @param string $from
     * @param string $to
     * @param string $part
     * @return Response
     * @throws RequestException
     * @throws ValidationException
     */
    public function clonePart(string $from, string $to, string $part): Response
    {
        $data = [
            'part' => $part,
            'from' => $from,
            'to' => $to
        ];

        $validator = Validator::make($data, [
            'part' => 'required|in:configuration,data,resources',
            'from' => 'required',
            'to' => 'required'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->request
            ->asForm()
            ->post('/advancloud/import/app/clone', [
                'tokenfrom' => $from,
                'tokento' => $to,
                'parts' => $part
            ])
            ->throw();
    }
}