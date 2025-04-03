<?php

namespace App\Models;

use App\Traits\BootableModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderProductUnit extends Model
{
    use BootableModel;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
	    'branch_id',
	    'purchase_order_id',

	    'qty',
	    'product_id',
	    'product_unit_id',
	    'product_unit_amount_per_unit',
	    'product_unit_amount_total',
	    'product_unit_initial_price',
	    'product_unit_discount_rate1',
	    'product_unit_discount_rate2',
	    'product_unit_discount_rate3',
	    'product_unit_discount_rate4',
	    'product_unit_discount_rate5',
	    'product_unit_discount_fixed1',
	    'product_unit_discount_fixed2',
	    'product_unit_discount_fixed3',
	    'product_unit_discount_fixed4',
	    'product_unit_discount_fixed5',
	    'product_unit_net_price',
	    'product_unit_subtotal',
	    'product_unit_subtotal_discount_rate',
	    'product_unit_subtotal_discount_fixed',
	    'product_unit_total',
	    'product_unit_global_discount_rate',
	    'product_unit_global_discount_fixed',
	    'product_unit_grand_total',

	    'product_is_taxable',
	    'product_vat_rate',
	    'product_price_include_vat',
	    'product_vat_base',
	    'product_vat',

	    'product_unit_final_price',
	    'product_final_price_base_unit',
    ];

    protected $casts = [

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('code', 'like', '%'.$search.'%')
            ->orWhere('remarks', 'like', '%'.$search.'%');
    }
}
