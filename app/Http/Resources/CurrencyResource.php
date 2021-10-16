<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'currency_name' => $this->currency_name,
            'currency_code' => $this->currency_code,
            'currency_sym' => $this->currency_sym,
            'base_rate' => $this->base_rate
        ];
    }
}
