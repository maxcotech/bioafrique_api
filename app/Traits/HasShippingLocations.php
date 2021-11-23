<?php
namespace App\Traits;

use App\Models\City;
use App\Models\State;

trait HasShippingLocations{
    protected function getStateModelByName(){
        $state = null;
        if($this->request->input('state',null) != null && $this->request->input('country_id') != null){
           $state = State::where('state_name',$this->request->state)
           ->where('country_id',$this->request->country_id)->first();
        }
        return $state;
     }
  
     protected function getCityModelByName($state_id){
        $city = null;
        if($this->request->input('city',null) != null && isset($state_id)){
           $city = City::where('city_name',$this->request->city)
           ->where('state_id',$state_id)->first();
        }
        return $city;
     }

}
    