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
            'is_manufacturer_sku' => $this->is_manufacturer_sku,
            'product_category' => new ProductCategoryResource($this->productCategory),
            'brand' => new BrandResource($this->brand),
            'name' => $this->name,
            'slug' => $this->slug,
            'is_taxable' => $this->is_taxable,
            'vat_rate' => $this->vat_rate,
            'is_price_include_vat' => $this->is_price_include_vat,
            'is_use_serial_number' => $this->is_use_serial_number,
            'is_expirable' => $this->is_expirable,
            'point' => $this->point,
            'remarks' => $this->remarks,
            'product_type' => $this->product_type,
            'status' => $this->setStatus($this->status, $this->deleted_at),
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
