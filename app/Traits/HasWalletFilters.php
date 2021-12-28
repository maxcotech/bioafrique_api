<?php
namespace App\Traits;

use Carbon\Carbon;

trait HasWalletFilters{
   protected function filterSelectByDate($query){
      $in_range = $this->request->query('in_range',0);
      $start_date = $this->request->query('start_date',null);
      $end_date = $this->request->query('end_date',null);
      if($in_range == 1 && isset($start_date)){
         $cstart_date = new Carbon($start_date);
         $cend_date = (isset($end_date))? new Carbon($end_date): now();
         $query = $query->whereBetween('created_at',[
            $cstart_date->toDateString()." 00:00:00",
            $cend_date->toDateString()." 00:00:00"
         ]);
      } else {
         if(isset($start_date)){
            $cstart_date = new Carbon($start_date);
            $query = $query->whereDate('created_at',$cstart_date->toDateString());
         }
      }
      return $query;
   }

   protected function filterSelectByLedgerType($query){
      $ledger_type = $this->request->query('ledger_type',null);
      if(isset($ledger_type)){
         $query = $query->where('ledger_type',$ledger_type);
      }
      return $query;
   }

   

}
   