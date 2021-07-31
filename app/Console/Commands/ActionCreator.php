<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ActionCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {actionClass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'a command to create action classes that holds app business logics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //create an action class file
        $file_class_name=$this->argument('actionClass');
        $mod_file_name="";
        $namespace="";
        if(strpos($file_class_name,'/')){
            $str_array=explode('/',$file_class_name);
            if(!file_exists(base_path().'/app/Actions/'.$str_array[0])){
                mkdir(base_path().'/app/Actions/'.$str_array[0]);
            }
            $namespace="App\Actions\\".$str_array[0];
            $mod_file_name=$str_array[1];
        }
        else{
            $mod_file_name=$file_class_name;
            $namespace="App\Actions";
        }
        $file=fopen(base_path()."/app/Actions/".$file_class_name.".php",'w');

        $e='$e';
        $new_file_content=
"<?php
namespace $namespace;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;

class $mod_file_name extends Action{
    protected \$request;
    public function __construct(Request \$request){
       \$this->request=\$request;
    }
    public function execute(){
       try{
          //
       }
       catch(\Exception \$e){
          return \$this->internalError(\$e->getMessage());
       }
    }

}
    ";
        fwrite($file,$new_file_content);
        $this->info('Action class created successfully');
    }
}
