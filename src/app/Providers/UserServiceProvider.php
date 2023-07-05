<?php

namespace App\Providers;

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
        //Publish Migrations*******
        $filename=$this->migrationExists('create_users_table');
        if ($filename === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_users_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_users_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_users_table.php' => database_path('migrations/' .$filename),
            ], 'migrations');
        }

        $filename1=$this->migrationExists('create_activity_logs_table');
        if ($filename1 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_activity_logs_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_activity_logs_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_activity_logs_table.php' => database_path('migrations/' . $filename1),
            ], 'migrations');
        }

        $filename2=$this->migrationExists('create_blocks_table');
        if ($filename2 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_blocks_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_blocks_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_blocks_table.php' => database_path('migrations/' . $filename2),
            ], 'migrations');
        }

        $filename3=$this->migrationExists('create_followers_table');
        if ($filename2 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_followers_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_followers_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_followers_table.php' => database_path('migrations/' . $filename3),
            ], 'migrations');
        }

        //Publish Views*****
        $this->publishes([
            __DIR__.'/resources/views/verify.blade.php' => app_path('./../resources/views/verify.blade.php'),
            __DIR__.'/resources/views/useractioninfo.blade.php' => app_path('./../resources/views/useractioninfo.blade.php'),
        ],'above8');

        //Publish Routes*****
        $this->publishes([
            __DIR__.'/routes/10/api.php' => app_path('./../routes/api.php'),
        ],'above8');

        //Publish Configs*****
        $this->publishes([
            __DIR__.'/config/constants.php' => app_path('./../config/constants.php'),
        ],'above8');

        //Publish Controllers****
        $this->publishes([
            __DIR__.'/app/Http/Controllers/UserController.php' => app_path('Http/Controllers/UserController.php'),
        ],'above8');

        //Publish Middlewares****
        $this->publishes([
            __DIR__.'/app/Http/Middleware/ValidateUserMiddleware.php' => app_path('Http/Middleware/ValidateUserMiddleware.php'),
        ],'above8');

        //Publish Requests****
        $this->publishes([
            __DIR__.'/app/Http/Requests/CreateUser.php' => app_path('Http/Requests/CreateUser.php'),
            __DIR__.'/app/Http/Requests/UserInfo.php' => app_path('Http/Requests/UserInfo.php'),
            __DIR__.'/app/Http/Requests/UserLogin.php' => app_path('Http/Requests/UserLogin.php'),
            __DIR__.'/app/Http/Requests/VerifyUser.php' => app_path('Http/Requests/VerifyUser.php'),
        ],'above8');

        //Publish Models*****
        $this->publishes([
            __DIR__.'/app/Models/User.php' => app_path('Models/User.php'),
            __DIR__.'/app/Models/ActivityLog.php' => app_path('Models/ActivityLog.php'),
            __DIR__.'/app/Models/Block.php' => app_path('Models/Block.php'),
            __DIR__.'/app/Models/Follower.php' => app_path('Models/Follower.php'),
        ],'above8');

        //Publish Mails****
        $this->publishes([
            __DIR__.'/app/Mail/SendMails.php' => app_path('Mail/SendMails.php'),
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
