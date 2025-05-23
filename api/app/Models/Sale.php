<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'date',
        'due_days',
        'warehouse_id',
        'customer_id',
        'delivery_note_reference',

        'tax_invoice_number',
        'tax_invoice_vat_base',
        'tax_invoice_vat',
        'return_tax_invoice_number',
        'return_tax_invoice_vat_base',
        'return_tax_invoice_vat',

        'remarks',
        'is_posted',

        'total',
        'global_discount_rate',
        'global_discount_fixed',
        'additional_cost',
        'rounding',
        'grand_total',

        'return_total',
        'return_global_discount_rate',
        'return_global_discount_fixed',
        'return_rounding',
        'return_grand_total',

        'amount_due',
        'amount_paid_by_sale_order_down_payment',
        'amount_paid_by_sale_return',
        'amount_paid_before_invoice',
        'amount_paid_on_invoice',
        'amount_paid_after_invoice',
        'amount_paid_total',
        'amount_due',

        'is_paid_off',
        'is_valid',
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleProductUnits()
    {
        return $this->hasMany(SaleProductUnit::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
