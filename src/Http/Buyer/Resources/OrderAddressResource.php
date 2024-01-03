<?php

namespace RedJasmine\Order\Http\Buyer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RedJasmine\Support\Http\Resources\WithCollectionResource;

/** @mixin \RedJasmine\Order\Models\OrderAddress */
class OrderAddressResource extends JsonResource
{
    use WithCollectionResource;

    public function toArray(Request $request) : array
    {
        return [
            'contacts'     => $this->contacts,
            'mobile'       => $this->mobile,
            'country'      => $this->country,
            'province'     => $this->province,
            'city'         => $this->city,
            'district'     => $this->district,
            'street'       => $this->street,
            'country_id'   => $this->country_id,
            'province_id'  => $this->province_id,
            'city_id'      => $this->city_id,
            'district_id'  => $this->district_id,
            'street_id'    => $this->street_id,
            'address'      => $this->address,
            'zip_code'     => $this->zip_code,
            'long'         => $this->long,
            'lat'          => $this->lat,
            'extends'      => $this->extends,
            'full_address' => $this->full_address,
        ];
    }
}
