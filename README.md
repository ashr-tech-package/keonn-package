# ASHR KEONN PACKAGE

**ASHR KEONN PACKAGE** is package contains function to connect with KEONN web service (AdvanCloud).

## Instalation

Install this package via composer

```bash
composer require ashr/keonn
```
> Packagist: [https://packagist.org/packages/ashr/keonn](https://packagist.org/packages/ashr/keonn)

* Add ```\Ashr\Keonn\ServiceProvider::class``` to config/app.php
* Publish config by run this command ```php artisan vendor:publish --tag=ashr-keonn```

## How to use
* Make sure the configurations is present in your .env file. To use this package you need to fill this fields in .env: 
```bash
KEONN_APP_MODE=
KEONN_BASE_URL=
KEONN_USERNAME=
KEONN_PASSWORD=
KEONN_STORAGE_DRIVER=sftp or webdav
KEONN_SFTP_HOST=
KEONN_SFTP_USERNAME=
KEONN_SFTP_PASSWORD=
KEONN_SFTP_PORT=
KEONN_WEBDAV_BASEURL=
KEONN_WEBDAV_USERNAME=
KEONN_WEBDAV_PASSWORD=
KEONN_WEBDAV_PORT=
```

Fill the storage field base on the driver you choose, for now available to use webdav or sftp

### Data Management

* Create Product
```
public function createProduct()
{
    $data[] = [
        'itemtype' => 'product',
        'productid' => '806160',
        'name' => 'Lorem Ipsum Dummy Text',
        'price' => 0,
        'images[0]' => '301405.png'
    ];

    try {
        return KeonnApi::createProduct($data);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

* Update Product
```
public function updateProduct()
{
    $data[] = [
        'itemtype' => 'product',
        'productid' => '806160',
        'name' => 'Lorem Ipsum Dummy Text',
        'price' => 100,
        'oldPrice' => 0,
        'images[0]' => '301405232781.png',
        'images[1]' => '301405232782.png',
    ];

    try {
        return KeonnApi::updateProduct($data, false);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

* Delete Product
```
public function deleteProduct()
{
    $data[] = [
        'itemtype' => 'product',
        'productid' => '806160',
        'name' => 'Lorem Ipsum Dummy Text'
    ];

    try {
        return KeonnApi::deleteProduct($data, false);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

* Clone Part: With this function you can clone data between apps example copy data product from pro to pre. 
```
public function clonePart()
{
    try {
        return KeonnApi::clonePart('pro', 'pre', 'data');
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

* Delete Part: Bulk delete part of application.
```
public function deletePart()
{
    try {
        return KeonnApi::deletePart('data');
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

### Inventory
* Upload stock
```
public function uploadStock(Request $request)
{
    $data = $request->data;
    $extra = json_encode($request->extra ?? []);

    try {
        return KeonnApi::uploadStock($data, $request->shop, [
            'type' => $request->type,
            'description' => 'Testing From Api Call',
            'extra' => $extra,
        ]);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

* Download Stock
```
public function downloadStock(Request $request)
{
    try {
        return KeonnApi::downloadStock($request->shop, 'json', [
            'mode' => 'sku',
            'type' => $request->type,
        ]);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

### Entity
* Create or Update Shop:
```
public function createOrUpdateShop(Request $request)
{
    try {
        return KeonnApi::createOrUpdateShop($request->data);
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```
Example shop data:
```
{
 "data":
    {
        "code": "R034",
        "area": "BCN",
        "name": "TOG 1234"
    }
}
```

* Get Data Shop:
```
public function getShop()
{
    try {
        return KeonnApi::getShop();
    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
```

### Report
* Get Report: this is example get report stock
```
public function report(Request $request)
{
    try {
        return KeonnApi::report('stock');
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
}
```

* For more open ```\Ashr\Keonn\Support\Facades\KeonnApi``` to know the available function



