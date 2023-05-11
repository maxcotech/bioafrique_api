<?php
namespace App\Actions\Admin;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\SuperAdminPreference;
use Illuminate\Support\Facades\DB;

class GetPreferences extends Action{
    protected $request;
    public function __construct(Request $request){
        $this->request=$request;
    }

    protected function initRecord(){
        DB::transaction(function(){
            foreach(SuperAdminPreference::initData as $item){
                if(!SuperAdminPreference::where('preference_key',$item['key'])->exists()){
                    SuperAdminPreference::create([
                        'preference_key' => $item['key'],
                        'preference_value' => $item['init_value']
                    ]);
                }
            }
        });
    }

    protected function onGetPreferences(){
        return SuperAdminPreference::all();
    }
    
    public function execute(){
        try{
            $this->initRecord();
            $data = $this->onGetPreferences();
            return $this->successWithData($data);
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    