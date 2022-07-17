<?php

namespace App\Http\Resources;

use App\Traits\HasRoles;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use HasRoles;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'telephone_code' => $this->telephone_code,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'user_type_text' => $this->getRoleTextById($this->user_type),
            'account_status' => $this->account_status,
            'permissions' => ($this->isSuperAdmin())? $this->getAllPermissionNames() : $this->permissions()->pluck("name")
        ];
        return $data;
    }


}
