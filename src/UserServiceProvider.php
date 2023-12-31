<?php

namespace snosborn\laraveljwtauth;

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
                __DIR__.'/database/migrations/create_users_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_1_create_users_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_users_table.php' => database_path('migrations/' .$filename),
            ], 'migrations');
        }

        $filename1=$this->migrationExists('create_activity_logs_table');
        if ($filename1 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_activity_logs_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_2_create_activity_logs_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_activity_logs_table.php' => database_path('migrations/' . $filename1),
            ], 'migrations');
        }

        $filename2=$this->migrationExists('create_blocks_table');
        if ($filename2 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_blocks_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_3_create_blocks_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_blocks_table.php' => database_path('migrations/' . $filename2),
            ], 'migrations');
        }

        $filename3=$this->migrationExists('create_followers_table');
        if ($filename3 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_followers_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_4_create_followers_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_followers_table.php' => database_path('migrations/' . $filename3),
            ], 'migrations');
        }

        $filename4=$this->migrationExists('create_countries_table');
        if ($filename4 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_countries_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_5_create_countries_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_countries_table.php' => database_path('migrations/' . $filename4),
            ], 'migrations');
        }

        $filename5=$this->migrationExists('create_states_table');
        if ($filename5 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_states_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_6_create_states_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_states_table.php' => database_path('migrations/' . $filename5),
            ], 'migrations');
        }

        $filename6=$this->migrationExists('create_cities_table');
        if ($filename6 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_cities_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_7_create_cities_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_cities_table.php' => database_path('migrations/' . $filename6),
            ], 'migrations');
        }

        $filename7=$this->migrationExists('create_user_addresses_table');
        if ($filename7 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_user_addresses_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_8_create_user_addresses_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_user_addresses_table.php' => database_path('migrations/' . $filename7),
            ], 'migrations');
        }

        $filename8=$this->migrationExists('create_jobs_table');
        if ($filename8 === false) {
            $this->publishes([
                __DIR__.'/database/migrations/create_jobs_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_9_create_jobs_table.php'),
            ], 'migrations');
        }else {
            $this->publishes([
                __DIR__.'/database/migrations/create_jobs_table.php' => database_path('migrations/' . $filename8),
            ], 'migrations');
        }


        //Publish Seeders*****
        $this->publishes([
            __DIR__.'/database/seeders/CountryStateCitySeeder.php' => database_path('seeders/CountryStateCitySeeder.php'),
        ],'seeders');

        //Publish Views*****
        $this->publishes([
            __DIR__.'/resources/views/verify.blade.php' => app_path('./../resources/views/verify.blade.php'),
            __DIR__.'/resources/views/useractioninfo.blade.php' => app_path('./../resources/views/useractioninfo.blade.php'),
            __DIR__.'/resources/views/forgotpassword.blade.php' => app_path('./../resources/views/forgotpassword.blade.php'),
            __DIR__.'/resources/views/resetpassword.blade.php' => app_path('./../resources/views/resetpassword.blade.php'),
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
            __DIR__.'/app/Http/Controllers/StaticDataController.php' => app_path('Http/Controllers/StaticDataController.php'),
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
            __DIR__.'/app/Http/Requests/UpdateUser.php' => app_path('Http/Requests/UpdateUser.php'),
            __DIR__.'/app/Http/Requests/UploadFile.php' => app_path('Http/Requests/UploadFile.php'),
            __DIR__.'/app/Http/Requests/ForgetPassword.php' => app_path('Http/Requests/ForgetPassword.php'),
            __DIR__.'/app/Http/Requests/UserLogout.php' => app_path('Http/Requests/UserLogout.php'),
            __DIR__.'/app/Http/Requests/ResetPassword.php' => app_path('Http/Requests/ResetPassword.php'),
            __DIR__.'/app/Http/Requests/ChangePassword.php' => app_path('Http/Requests/ChangePassword.php'),
            __DIR__.'/app/Http/Requests/UploadMultiFiles.php' => app_path('Http/Requests/UploadMultiFiles.php'),
            __DIR__.'/app/Http/Requests/GetCountries.php' => app_path('Http/Requests/GetCountries.php'),
            __DIR__.'/app/Http/Requests/GetStates.php' => app_path('Http/Requests/GetStates.php'),
            __DIR__.'/app/Http/Requests/GetCities.php' => app_path('Http/Requests/GetCities.php'),
        ],'above8');

        //Publish Models*****
        $this->publishes([
            __DIR__.'/app/Models/User.php' => app_path('Models/User.php'),
            __DIR__.'/app/Models/ActivityLog.php' => app_path('Models/ActivityLog.php'),
            __DIR__.'/app/Models/Block.php' => app_path('Models/Block.php'),
            __DIR__.'/app/Models/Follower.php' => app_path('Models/Follower.php'),
            __DIR__.'/app/Models/Country.php' => app_path('Models/Country.php'),
            __DIR__.'/app/Models/State.php' => app_path('Models/State.php'),
            __DIR__.'/app/Models/City.php' => app_path('Models/City.php'),
            __DIR__.'/app/Models/UserAddress.php' => app_path('Models/UserAddress.php'),
        ],'above8');

        //Publish Mails****
        $this->publishes([
            __DIR__.'/app/Mail/SendMails.php' => app_path('Mail/SendMails.php'),
        ],'above8');

        //Publish Assets****
        $this->publishes([
            __DIR__.'/assets' => public_path('assets/'),
        ],'above8');
        
        //Publish Helpers****
        $this->publishes([
            __DIR__.'/app/Helpers' => app_path('Helpers/'),
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
