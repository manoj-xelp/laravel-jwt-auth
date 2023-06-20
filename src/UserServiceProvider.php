<?php

namespace URS\usersregistration;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $filename=$this->migrationExists('create_users_table');
        if ($filename === false) {
            $this->publishes([
                __DIR__.'/migrations/create_users_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_users_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/migrations/create_users_table.php' => database_path('migrations/' .$filename),
            ], 'migrations');
        }
        
        $this->publishes([
            __DIR__.'/User.php' => app_path('Models/User.php'),
            __DIR__.'/UserController.php' => app_path('Http/Controllers/UserController.php'),
            __DIR__.'/routes/9/api.php' => app_path('./../routes/api.php'),
            __DIR__.'/SendMails.php' => app_path('Mail/SendMails.php'),
            __DIR__.'/ValidateUserMiddleware.php' => app_path('Http/Middleware/ValidateUserMiddleware.php'),
            __DIR__.'/verify.blade.php' => app_path('./../resources/views/verify.blade.php'),
        ],'above8');
    }

    protected function migrationExists($mgr)
    {
        $path = database_path('migrations/');
        $files = scandir($path);
        $pos = false;$filename='';
        foreach ($files as &$value) {
            $pos = strpos($value, $mgr);
            if($pos !== false){
                $filename=$value;  
            };
        } 
        if($filename){
            return $filename;
        }else {
            return false;
        }
    }
}
