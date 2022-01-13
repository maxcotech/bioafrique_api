<?php

namespace App\Actions\Currency;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\Currency;
use App\Traits\HasRoles;
use Illuminate\Validation\Rule;

class CreateCurrency extends Action
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
            'country_id' => 'required|integer|exists:countries,id',
            'currency_name' => ['required','string',Rule::unique('currencies','currency_name')->where(function($query){
                $query->where('country_id',$this->request->country_id);
            })],
            'currency_code' => ['required','string',Rule::unique('currencies','currency_code')->where(function($query){
                $query->where('country_id',$this->request->country_id);
            })],
            'currency_symbol' => ['required','string',Rule::unique('currencies','currency_sym')->where(function($query){
                $query->where('country_id',$this->request->country_id);
            })],
            'base_rate' => 'required|numeric'
        ]);
        return $this->valResult($val);
    }

    protected function onCreateCurrency(){
        $data = $this->request->all(['currency_name','country_id','currency_code','base_rate']);
        $data['currency_sym'] = $this->request->currency_symbol;
        $data['is_base_currency'] = 0;
        Currency::create($data);
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            if($this->isSuperAdmin()){
                $this->onCreateCurrency();
                return $this->successMessage('Currency created successfully');
            }
            return $this->notAuthorized('You are not authorized.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
