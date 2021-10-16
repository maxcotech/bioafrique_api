<?php
namespace App\Traits;

trait HasResourceStatus{
    /*
        resource_status: 0 = not approved 1 = approved, 2 = in review
        3 = in draft 4 = blacklisted
    */

    protected $resource_status_list = [
        'inactive' => 0,
        'active' => 1,
        'in_review' => 2,
        'in_draft' => 3,
        'blacklisted' => 4
    ];

    protected function getResourceStatusTextById($status){
        foreach($this->resource_status_list as $key => $value){
            if($value == $status){
                return $key;
            }
        }
        return "N/A";
    }

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
        return $this->resource_status_list['active'];
    }

    public function getResourceInactiveId(){
        return $this->resource_status_list['inactive'];
    }

    public function getResourceInReviewId(){
        return $this->resource_status_list['in_review'];
    }

    public function getResourceBlacklistedId(){
        return $this->resource_status_list['blacklisted'];
    }

    public function getResourceInDraftId(){
        return $this->resource_status_list['in_draft'];
    }

}
    