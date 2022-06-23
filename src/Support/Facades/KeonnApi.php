<?php

namespace Ashr\Keonn\Support\Facades;

use Ashr\Keonn\Services\KeonnApi as Service;
use Illuminate\Support\Facades\Facade;

/**
 * @method static createProduct(array $data)
 * @method static updateProduct(array $data, bool $allFields = false)
 * @method static deleteProduct(array $data)
 * @method static createOrUpdateProduct(array $data, bool $allFields = false)
 * @method static getProduct(array $data, string $reportType = 'json')
 * @method static deletePart(string $part)
 * @method static clonePart(string $from, string $to, string $part)
 * @method static uploadFile(mixed $file, string $fileName)
 * @method static deleteFile(string $fileName)
 * @method static getFilePath(string $fileName)
 * @method static uploadStock(array $data, string $shop, array $optionals = [])
 * @method static downloadStock(string $shop, string $reportType = 'json', array $optionals = [])
 * @method static downloadStockByInventoryCode(string $code, string $shop, string $reportType = 'json', array $optionals = [])
 * @method static statusStock()
 * @method static searchStock(string $shop)
 * @method static removeStock(string $code, string $shop)
 * @method static report(string $reportCode, string $reportType = 'json', array $optionals = [])
 * @method static createOrUpdateShop(array $data, array $optionals = [])
 * @method static getShop(string $reportType = 'json', array $optionals = [])
 */
class KeonnApi extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}