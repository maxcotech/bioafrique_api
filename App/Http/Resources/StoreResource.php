<?php

namespace App\Http\Resources;

use App\Traits\HasResourceStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    use HasResourceStatus;
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
            'owner_id' => $this->user_id,
            'store_name' => $this->store_name,
            'store_logo' => $this->store_logo,
            'store_slug' => $this->store_slug,
            'country_id' => $this->country_id,
            'store_address' => $this->store_address,
            'store_email' => $this->store_email,
            'store_telephone' => $this->store_telephone,
            'store_status' => $this->store_status,
            'state' => isset($this->state)? $this->state->state_name:null,
            'city' => isset($this->city)? $this->city->city_name:null,
            'store_status_text' => $this->getResourceStatusTextById($this->store_status)
        ];
    }
}
