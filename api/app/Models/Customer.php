<?php

namespace App\Models;

use App\Enums\PaymentTermType;
use App\Enums\RecordStatus;
use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'customer_group_id',
        'user_id',
        'code',
        'is_member',
        'name',
        'zone',
        'max_open_invoice',
        'max_outstanding_invoice',
        'max_invoice_age',
        'payment_term_type',
        'payment_term',
        'taxable_enterprise',
        'tax_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'is_member' => 'boolean',
        'payment_term_type' => PaymentTermType::class,
        'taxable_enterprise' => 'boolean',
        'status' => RecordStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class)->withTrashed();
    }

    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('zone', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
