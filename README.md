# PHP_Laravel12_Generate_Barcode

A complete Laravel 12 application demonstrating barcode generation and product management using the `milon/barcode` package. This project includes full CRUD functionality, barcode image generation, download support, and a responsive Bootstrap-based user interface.

---

## Features

* Product CRUD (Create, Read, Update, Delete)
* Barcode generation using `milon/barcode`
* Support for multiple barcode formats (CODE 128, EAN13, UPC-A, etc.)
* Barcode image preview and download
* Barcode regeneration option
* Sample data seeder for testing
* Responsive Bootstrap UI
* Form validation and error handling

---

## Requirements

* PHP 8.1 or higher
* Laravel 12
* Composer
* MySQL or compatible database

---

## Installation

### 1. Create Laravel Project

```bash
composer create-project laravel/laravel laravel-barcode
cd laravel-barcode
```

### 2. Install Barcode Package

```bash
composer require milon/barcode
php artisan vendor:publish --provider="Milon\Barcode\BarcodeServiceProvider"
```

### 3. Configure Barcode Package

Edit `config/barcode.php` if needed:

```php
return [
    'store_path' => public_path('/'),
];
```

---

## Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_barcode
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## Database Setup

### Create Products Table

```bash
php artisan make:migration create_products_table
```

Migration structure:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('sku')->unique();
    $table->decimal('price', 8, 2);
    $table->text('description')->nullable();
    $table->string('barcode')->nullable();
    $table->timestamps();
});
```

Run migrations:

```bash
php artisan migrate
```

---

## Model

`app/Models/Product.php`

```php
class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'price',
        'description',
        'barcode'
    ];
}
```

---

## Routes

`routes/web.php`

```php
Route::resource('products', ProductController::class);
Route::get('/product/{id}/barcode-image', [ProductController::class, 'barcodeImage'])
    ->name('product.barcode.image');
```

---

## Controller Logic

Key functionalities implemented in `ProductController`:

* Product listing and management
* Automatic barcode generation
* Barcode regeneration
* Barcode image rendering
* Secure validation

Barcode generation example:

```php
DNS1D::getBarcodePNG($code, 'C128');
```

---

## Views

Views are located in:

```
resources/views/products/
```

Included views:

* index.blade.php
* create.blade.php
* show.blade.php
* edit.blade.php

Each view is built using Bootstrap 5 and fully responsive.

---

## Seeder

Sample data seeder for testing:

```bash
php artisan make:seeder ProductSeeder
php artisan db:seed --class=ProductSeeder
```

Seeder inserts demo products with pre-generated barcodes.

---

## Running the Application

```bash
php artisan serve
```

Open in browser:

```
http://localhost:8000/products
```

---

## Supported Barcode Types

The following barcode formats are supported:

* CODE 128 (C128, C128A, C128B)
* EAN 13
* UPC-A
* CODABAR
* PHARMACODE

Example:

```php
DNS1D::getBarcodePNG($code, 'EAN13');
```

---

## Project Structure

```
laravel-barcode/
├── app/
│   ├── Http/Controllers/ProductController.php
│   └── Models/Product.php
├── config/
│   └── barcode.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/products/
├── routes/web.php
└── public/
```

---

## Troubleshooting

* Run `composer dump-autoload` if classes are not found
* Ensure GD extension is enabled in PHP
* Clear cache if UI changes are not reflected

```bash
php artisan optimize:clear
```
## Images

<img width="1712" height="788" alt="image" src="https://github.com/user-attachments/assets/da7a8ff9-75d5-4604-b49e-87eeb8543ffa" />

<img width="1182" height="802" alt="image" src="https://github.com/user-attachments/assets/a6d2c6dd-6e89-4b9b-8e67-33baf855d753" />

<img width="1271" height="868" alt="image" src="https://github.com/user-attachments/assets/9d29b604-2dd3-44cf-83f2-23eced118e92" />

<img width="728" height="434" alt="image" src="https://github.com/user-attachments/assets/fb408fc8-5281-40de-ab4c-5c3e8c16dd54" />
T License.
