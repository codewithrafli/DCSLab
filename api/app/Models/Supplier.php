<?php

namespace App\Models;

use App\Enums\RecordStatus;
use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'code',
        'name',
        'address',
        'city',
        'payment_term_type',
        'payment_term',
        'taxable_enterprise',
        'tax_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'taxable_enterprise' => 'boolean',
        'status' => RecordStatus::class,
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('address', 'like', '%'.$search.'%')
            ->orWhere('city', 'like', '%'.$search.'%')
            ->orWhere('payment_term_type', 'like', '%'.$search.'%')
            ->orWhere('payment_term', 'like', '%'.$search.'%')
            ->orWhere('tax_id', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
