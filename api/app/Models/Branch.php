<?php

namespace App\Models;

use App\Enums\RecordStatus;
use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'address',
        'city',
        'contact',
        'is_main',
        'remarks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'status' => RecordStatus::class,
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function purchaseProductUnitSerials()
    {
        return $this->hasMany(PurchaseProductUnitSerial::class);
    }

    public function purchaseAdditionalCosts()
    {
        return $this->hasMany(PurchaseAdditionalCost::class);
    }

    public function purchaseReturnProductUnits()
    {
        return $this->hasMany(PurchaseReturnProductUnit::class);
    }

    public function purchaseReturnProductUnitSerials()
    {
        return $this->hasMany(PurchaseReturnProductUnitSerial::class);
    }

    public function purchaseReturnAdditionalCosts()
    {
        return $this->hasMany(PurchaseReturnAdditionalCost::class);
    }

    public function purchasePayments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function purchaseReceipts()
    {
        return $this->hasMany(PurchaseReceipt::class);
    }

    public function purchaseReceiptProductUnits()
    {
        return $this->hasMany(PurchaseReceiptProductUnit::class);
    }

    public function purchaseReceiptProductUnitSerials()
    {
        return $this->hasMany(PurchaseReceiptProductUnitSerial::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('company', fn ($query) => $query->search($search))
            ->orWhere('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->orWhere('city', 'like', '%'.$search.'%')
            ->orWhere('contact', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
