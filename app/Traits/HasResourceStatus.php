<?php
namespace App\Traits;

trait HasResourceStatus{
    /*
        resource_status: 0 = not approved 1 = approved, 
        2 = in draft 4 = blacklisted
    */

    protected $status_list = [
        'inactive' => 0,
        'active' => 1,
        'in_review' => 2,
        'blacklisted' => 4
    ];

    public function isResourceActive($type){
        return $this->getResourceActiveId() == $type;
    }

    public function isResourceInactive($type){
        return $this->getResourceInactiveId() == $type;
    }

    public function isResourceInReview($type){
        return $this->getResourceInReviewId() == $type;
    }

    public function isResourceBlacklisted($type){
        return $this->getResourceBlacklistedId() == $type;
    }

    public function getResourceActiveId(){
        return $this->status_list['active'];
    }

    public function getResourceInactiveId(){
        return $this->status_list['inactive'];
    }

    public function getResourceInReviewId(){
        return $this->status_list['in_review'];
    }

    public function getResourceBlacklistedId(){
        return $this->status_list['blacklisted'];
    }

}
    