<?php

namespace Ashr\Keonn\Support\Facades;

use Ashr\Keonn\Services\KeonnApi as Service;
use Illuminate\Support\Facades\Facade;

/**
 * @method static createProduct(array $data)
 * @method static updateProduct(array $data, bool $allFields = false)
 * @method static deleteProduct(array $data)
 * @method static createOrUpdateProduct(array $data, bool $allFields = false)
 * @method static deletePart(string $part)
 * @method static clonePart(string $part)
 * @method static uploadFile(mixed $file, string $fileName)
 * @method static deleteFile(string $fileName)
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