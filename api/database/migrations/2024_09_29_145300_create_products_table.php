<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->ulid();

            $table->foreignId('company_id')->references('id')->on('companies');
            $table->string('code');
            $table->boolean('is_factory_code');
            $table->foreignId('category_id')->references('id')->on('product_categories');
            $table->foreignId('brand_id')->nullable()->references('id')->on('brands');
            $table->string('name');
            $table->string('slug');
            $table->boolean('taxable_supply')->default(false);
            $table->integer('standard_rated_supply', 30, 8)->default(0);
            $table->boolean('price_include_vat')->default(false);
            $table->integer('point')->default(0);
            $table->boolean('use_serial_number')->default(false);
            $table->boolean('has_expiry_date')->default(false);
            $table->integer('type');
            $table->string('remarks')->nullable();
            $table->integer('status');

            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->default(0);
            $table->unsignedBigInteger('deleted_by')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
