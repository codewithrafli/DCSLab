<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CustomerGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'ulid' => $this->ulid,
            'company' => new CompanyResource($this->company),
            'code' => $this->code,
            'name' => $this->name,
            'max_open_invoice' => $this->max_open_invoice,
            'max_outstanding_invoice' => $this->max_outstanding_invoice,
            'max_invoice_age' => $this->max_invoice_age,
            'payment_term_type' => $this->payment_term_type,
            'payment_term' => $this->payment_term,
            'selling_point' => $this->selling_point,
            'selling_point_multiple' => $this->selling_point_multiple,
            'sell_at_cost' => $this->sell_at_cost,
            'price_markup_percent' => $this->price_markup_percent,
            'price_markup_nominal' => $this->price_markup_nominal,
            'price_markdown_percent' => $this->price_markdown_percent,
            'price_markdown_nominal' => $this->price_markdown_nominal,
            'round_on' => $this->round_on,
            'round_digit' => $this->round_digit,
            'remarks' => $this->remarks,
        ];
    }
}
