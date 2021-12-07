<?php
namespace App\Traits;

trait HasDataProcessing{
    protected function getReviewSummary($data,$rating_key){
        $output = [
            'count-0' => 0,'count-1' => 0,'count-2' => 0,'count-3' => 0,'count-4' => 0,'count-5' => 0
        ];
        if(!is_object($data) && !is_array($data)) return $output;
        $data = json_decode(json_encode($data),true);
        if(count($data) == 0) return $output;
        foreach($data as $row){
            $output["count-".strval($row[$rating_key])] += 1;
        }
        return $output;
    }

    protected function getReviewAverage($data, $rating_key){
        $data = json_decode(json_encode($data),true);
        $data_length = count($data);
        if($data_length == 0) return 0;
        $total_rating = 0;
        foreach($data as $row){
            $total_rating += $row[$rating_key];
        }
        return $total_rating / $data_length;
    }

}
    