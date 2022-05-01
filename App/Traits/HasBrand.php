<?php
namespace App\Traits;

trait HasBrand{
    use HasRoles,HasResourceStatus;

    protected function getBrandDefaultStatus(){
        $user = request()->user();
        $user_type = $user->user_type;
        if($this->isStoreOwner($user_type) || $this->isStoreStaff($user_type)){
           return $this->getResourceInReviewId();
        } else if($this->isSuperAdmin($user_type)){
           return $this->getResourceActiveId();
        } else {
           throw new \Exception('Could not determine the category of your profile.');
        }
     }

}
    