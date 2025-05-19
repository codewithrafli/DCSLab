<?php

namespace App\Models;

use App\Enums\ProductType;
use App\Enums\RecordStatus;
use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'is_manufacturer_sku',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'is_taxable',
        'vat_rate',
        'is_price_include_vat',
        'is_use_serial_number',
        'is_expirable',
        'point',
        'remarks',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_manufacturer_sku' => 'boolean',
            'is_taxable' => 'boolean',
            'vat_rate' => 'decimal:8',
            'is_price_include_vat' => 'boolean',
            'is_use_serial_number' => 'boolean',
            'is_expirable' => 'boolean',
            'has_expiry_date' => 'boolean',
            'type' => ProductType::class,
            'status' => RecordStatus::class,
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withTrashed();
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function purchaseReturnProductUnits()
    {
        return $this->hasMany(PurchaseReturnProductUnit::class);
    }

    public function purchaseReceiptProductUnits()
    {
        return $this->hasMany(PurchaseReceiptProductUnit::class);
    }

    public function stockTransferProductUnits()
    {
        return $this->hasMany(StockTransferProductUnit::class);
    }

    public function saleOrderProductUnits()
    {
        return $this->hasMany(SaleOrderProductUnit::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
