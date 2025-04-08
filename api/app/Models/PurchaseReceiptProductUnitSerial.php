<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReceiptProductUnitSerial extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'purchase_receipt_id',
        'purchase_receipt_product_unit_id',
        'serial',
    ];

    protected $casts = [

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchaseReceipt()
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function purchaseReceiptProductUnit()
    {
        return $this->belongsTo(PurchaseReceiptProductUnit::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
