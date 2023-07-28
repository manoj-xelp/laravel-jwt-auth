<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Mail\SendMails;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\ActivityLog;
use App\Http\Requests\UploadFile;
use App\Http\Requests\UploadMultiFiles;

class StaticDataController extends Controller
{
    //global variable to access contants
    private $constants;
    private $user_profile_base_url;
    private $reset_password_url;

    public function __construct()
    {
        $this->constants = Config::get('constants');
        $this->user_profile_base_url = $this->constants['SERVER_URL'].$this->constants['USER_PROFILE_PATH'];
        $this->reset_password_url = $this->constants['RESET_PASSWORD_URL'];
    }

    public function uploadSingleFile(UploadFile $request)
    {
        try{
            /*There is an issue with laravel request if the mime type doesn't match
             with request rules. It's returning the html response instead of 
             validation error.*/  
            $upload_path = $this->constants['UNDEFINED_PATH'];
            $type = $request->type;
            if($type == "USER_PROFILE"){
                $upload_path = $this->constants['USER_PROFILE_PATH'];
            }
            if ($request->hasFile('file')) {
                if ($request->file('file')->isValid()) {
                    $file_name = fileUpload($type, $request->file('file'), $upload_path);
                    if(isset($file_name) && $file_name != -1){
                        return response()->json(['success' => true, 'message'=>'Uploaded successfully.', 'file_name' => $file_name, 'base_url' => $this->constants['SERVER_URL'].$upload_path], 200);
                    }else{
                        return response()->json(['success' => false, 'error' => 'Something went wrong! Please try again later.'], 500);
                    }
                }
            }else{
                return response()->json(['success' => false, 'error' => 'File is required to upload.'], 400);
            }
        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function uploadMultiFiles(UploadMultiFiles $request)
    {
        try{  
            /*There is an issue with laravel request if the mime type doesn't match
             with request rules. It's returning the html response instead of 
             validation error.*/  
            $upload_path = $this->constants['UNDEFINED_PATH'];
            $type = $request->type;
            if($type == "USER_PROFILE"){
                $upload_path = $this->constants['USER_PROFILE_PATH'];
            }
            if ($request->hasFile('files')) {
                $file_names=[];
                $files=$request->file('files');
                foreach($files as $file){
                    $file_name = fileUpload($type, $file, $upload_path);
                    if(isset($file_name) && $file_name != -1){
                        array_push($file_names, $file_name);
                    }
                }
                return response()->json(['success' => true, 'message'=>'Uploaded successfully.', 'file_names' => $file_names, 'base_url' => $this->constants['SERVER_URL'].$upload_path], 200);
            }else{
                return response()->json(['success' => false, 'error' => 'File is required to upload.'], 400);
            }
        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    
}
