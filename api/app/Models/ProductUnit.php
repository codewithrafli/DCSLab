<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'product_id',
        'unit_id',
        'code',
        'is_base',
        'conversion_value',
        'is_primary_unit',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_base' => 'boolean',
            'conversion_value' => 'decimal',
            'is_primary_unit' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withTrashed();
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
            ->orWhere('conversion_value', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
