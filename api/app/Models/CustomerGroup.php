<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'max_open_invoice',
        'max_outstanding_invoice',
        'max_invoice_age',
        'payment_term_type',
        'payment_term',
        'selling_point',
        'selling_point_multiple',
        'sell_at_cost',
        'price_markup_percent',
        'price_markup_nominal',
        'price_markdown_percent',
        'price_markdown_nominal',
        'round_on',
        'round_digit',
        'remarks',
    ];

    protected $casts = [
        'max_outstanding_invoice' => 'decimal:8',
        'selling_point' => 'decimal:8',
        'selling_point_multiple' => 'decimal:8',
        'price_markup_percent' => 'decimal:8',
        'price_markup_nominal' => 'decimal:8',
        'price_markdown_percent' => 'decimal:8',
        'price_markdown_nominal' => 'decimal:8',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('name', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
