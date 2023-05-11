<?php
namespace App\Actions\Currency;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Currency;
use App\Traits\HasRoles;

class DeleteCurrency extends Action{
    use HasRoles;
    protected $request;
    protected $currency_id;
    public function __construct(Request $request,$currency_id){
        $this->request=$request;
        $this->currency_id = $currency_id;
    }

    protected function onDelete(){
        Currency::where('id',$this->currency_id)->delete();
    }
    
    public function execute(){
        try{
            if($this->isSuperAdmin()){
                $this->onDelete();
                return $this->successMessage('Currency deleted successfully');
            } else {
                return $this->notAuthorized('You are not authorized.');
            }
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    