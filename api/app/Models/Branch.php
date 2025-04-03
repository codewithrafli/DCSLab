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
