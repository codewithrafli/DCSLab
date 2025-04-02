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
        'is_factory_code',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'taxable_supply',
        'standard_rated_supply',
        'price_include_vat',
        'point',
        'use_serial_number',
        'has_expiry_date',
        'type',
        'remarks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_factory_code' => 'boolean',
            'taxable_supply' => 'boolean',
            'standard_rated_supply' => 'decimal:8',
            'price_include_vat' => 'boolean',
            'use_serial_number' => 'boolean',
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

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
