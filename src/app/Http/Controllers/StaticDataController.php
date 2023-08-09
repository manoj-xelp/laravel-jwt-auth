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
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Http\Requests\UploadFile;
use App\Http\Requests\UploadMultiFiles;
use App\Http\Requests\GetCountries;
use App\Http\Requests\GetStates;
use App\Http\Requests\GetCities;

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
        $this->city_base_url = $this->constants['SERVER_URL'].$this->constants['CITY_PATH'];
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

    public function getCountries(GetCountries $request)
    {
        try{
            $key=$request->key;

            $countries = Country::when($key, function($query,$key){
                return $query->where('name','LIKE','%'.$key.'%');
            })->get(['id','name']);

            if(count($countries) > 0){
                return response()->json(['success' => true,'countries' => $countries], 200);
            }else{
                return response()->json(['success' => false,'message' => 'No data found.'], 400);
            }

        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function getStates(GetStates $request)
    {
        try{
            $key=$request->key;
            $country_id=$request->country_id;

            $states = State::when($key, function($query,$key){
                return $query->where('name','LIKE','%'.$key.'%');
            })->when($country_id, function($query,$country_id){
                return $query->where('country_id',$country_id);
            })
            ->get(['id','name','country_id']);

            if(count($states) > 0){
                return response()->json(['success' => true,'states' => $states], 200);
            }else{
                return response()->json(['success' => false,'message' => 'No data found.'], 400);
            }

        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function getCities(GetCities $request)
    {
        try{
            $key=$request->key;
            $country_id=$request->country_id;
            $state_id=$request->state_id;
            $status=$request->status;

            if($status == "all"){
                $status = [0,1];
            }else if($status == "active"){
                $status = [1];
            }else if($status == "added"){
                $status = [1,-1];
            }else{
                $status = [];
            }

            $cities = City::when($key, function($query,$key){
                return $query->where('name','LIKE','%'.$key.'%');
            })->when($country_id, function($query,$country_id){
                return $query->where('country_id',$country_id);
            })->when($state_id, function($query,$state_id){
                return $query->where('state_id',$state_id);
            })->when($status, function($query,$status){
                return $query->whereIn('status',$status);
            })
            ->get(['id','name','country_id','state_id','image']);

            if(count($cities) > 0){
                return response()->json(['success' => true,'cities' => $cities, 'city_base_url'=> $this->city_base_url], 200);
            }else{
                return response()->json(['success' => false,'message' => 'No data found.'], 400);
            }

        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    
}
