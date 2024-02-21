<?php

namespace RedJasmine\Order\DataTransferObjects;

use RedJasmine\Support\DataTransferObjects\Data;

class OrderAddressDTO extends Data
{

    public string  $contacts;
    public string  $mobile;
    public ?string $country;
    public ?string $province;
    public ?string $city;
    public ?string $district;
    public ?string $street;
    public ?string $address;
    public ?string $zip_code;
    public ?string $lon;
    public ?int    $lat;
    public ?int    $countryId;
    public ?int    $provinceId;
    public ?int    $cityId;
    public ?int    $districtId;
    public ?int    $streetId;
    public ?array  $extends;

}
