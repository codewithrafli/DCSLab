<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'date',
        'customer_id',
        'customer_address_id',
        'shipping_date',
        'remarks',
        'is_has_invoice',
        'is_sent',
        'total',
        'global_discount_rate',
        'global_discount_fixed',
        'grand_total',
        'down_payment',
        'down_payment_due_days',
        'down_payment_applied',
        'down_payment_remaining',
        'is_down_payment_paid_off',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function saleOrderProductUnits()
    {
        return $this->hasMany(SaleOrderProductUnit::class);
    }

    public function saleOrderDownPayments()
    {
        return $this->hasMany(SaleOrderDownPayment::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
