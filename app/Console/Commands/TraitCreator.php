<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TraitCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {actionClass}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'a command to create traits';

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
            if(!file_exists(base_path().'/app/Traits/'.$str_array[0])){
                mkdir(base_path().'/app/Traits/'.$str_array[0]);
            }
            $namespace="App\Traits\\".$str_array[0];
            $mod_file_name=$str_array[1];
        }
        else{
            $mod_file_name=$file_class_name;
            $namespace="App\Traits";
        }
        $file=fopen(base_path()."/app/Traits/".$file_class_name.".php",'w');
        $new_file_content=
"<?php
namespace $namespace;

trait $mod_file_name{


}
    ";
        fwrite($file,$new_file_content);
        $this->info('trait  created successfully.');
    }
}
