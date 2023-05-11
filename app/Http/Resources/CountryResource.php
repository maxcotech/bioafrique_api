<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'country_name' => $this->country_name,
            'country_code' => $this->country_code,
            'country_logo' => $this->country_logo,
            'country_tel_code' => $this->country_tel_code
        ];
    }
}
