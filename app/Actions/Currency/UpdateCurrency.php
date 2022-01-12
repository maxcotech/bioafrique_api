<?php

namespace App\Actions\Currency;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Currency;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class UpdateCurrency extends Action
{
    use HasRoles;
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'id' => 'required|integer|exists:currencies,id',
            'country_id' => 'required|integer|exists:countries,id',
            'currency_name' => ['required','string',Rule::unique('currencies','currency_name')->where(function($query){
                $query->where('country_id',$this->request->country_id)->where('id',"!=",$this->request->id);
            })],
            'currency_code' => ['required','string',Rule::unique('currencies','currency_code')->where(function($query){
                $query->where('country_id',$this->request->country_id)->where('id',"!=",$this->request->id);
            })],
            'currency_symbol' => ['required','string',Rule::unique('currencies','currency_sym')->where(function($query){
                $query->where('country_id',$this->request->country_id)->where('id',"!=",$this->request->id);
            })],
            'base_rate' => 'required|numeric'
        ]);
        return $this->valResult($val);
    }

    protected function onUpdateCurrency(){
        $data = $this->request->all(['country_id','currency_name','currency_code','base_rate']);
        $data['currency_sym'] = $this->request->currency_symbol;
        Currency::where('id',$this->request->id)
        ->update($data);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            if($this->isSuperAdmin()){
                $this->onUpdateCurrency();
                return $this->successMessage('Currency updated successfully');
            }
            return $this->notAuthorized('You are not authorized.');

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
