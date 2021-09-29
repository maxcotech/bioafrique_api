<?php
namespace App\Traits;

trait HasResourceStatus{
    use StringFormatter;
    /**
     * status:
     * 0 = inactive 
     * 1 = active 
     * 2 = in review 
     * 4 = blacklisted
     */
    protected $status_list = [
        'inactive' => 0,
        'active' => 1,
        'in_review' => 2,
        'blacklisted' => 4
    ];

    public function getActiveStatusId(){
        return $this->status_list['active'];
    }
    public function getInStatusId(){
        return $this->status_list['inactive'];
    }
    public function getInReviewStatusId(){
        return $this->status_list['in_review'];
    }
    public function getBlacklistedStatusId(){
        return $this->status_list['blacklisted'];
    }

    public function getResourceStatusText($status){
        $text = "N/A";
        foreach($this->status_list as $key => $value){
            if($value == $status){
                $text = $key;
            }
        }
        return $this->fromSnakeToCamelCase($text);
    }

}
    