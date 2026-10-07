<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(10)->after('price');
            $table->string('barcode_type', 30)->default('C128')->after('barcode');
            $table->text('qr_data')->nullable()->after('barcode_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'barcode_type', 'qr_data']);
        });
    }
};
