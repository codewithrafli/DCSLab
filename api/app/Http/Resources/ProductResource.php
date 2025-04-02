<?php

namespace App\Http\Resources;

use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'ulid' => $this->ulid,
            'company' => new CompanyResource($this->company),
            'code' => $this->code,
            'product_category' => new ProductCategoryResource($this->productCategory),
            'brand' => new BrandResource($this->brand),
            'name' => $this->name,
            'product_type' => $this->product_type,
            'taxable_supply' => $this->taxable_supply,
            'standard_rated_supply' => $this->standard_rated_supply,
            'price_include_vat' => $this->price_include_vat,
            'point' => $this->point,
            'use_serial_number' => $this->use_serial_number,
            'has_expiry_date' => $this->has_expiry_date,
            'status' => $this->setStatus($this->status, $this->deleted_at),
            'remarks' => $this->remarks,
        ];
    }

    private function setStatus($status, $deleted_at)
    {
        if (! is_null($deleted_at)) {
            return RecordStatus::DELETED->name;
        } else {
            return $status->name;
        }
    }
}
