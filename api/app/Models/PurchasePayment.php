<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePayment extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'purchase_id',
        'code',
        'date',
        'cash_account_id',
        'amount',
        'remarks',
    ];

    protected $casts = [

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class)->withTrashed();
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
