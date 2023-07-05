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
use App\Mail\SendMails;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Block;
use App\Models\Follower;
use App\Http\Requests\CreateUser;
use App\Http\Requests\UserLogin;
use App\Http\Requests\VerifyUser;
use App\Http\Requests\UserInfo;

class UserController extends Controller
{
    //global variable to access contants
    private $constants;
    private $user_profile_base_url;

    public function __construct()
    {
        $this->constants = Config::get('constants');
        $this->user_profile_base_url = $this->constants['SERVER_URL'].$this->constants['USER_PROFILE_PATH'];
    }

    public function create(CreateUser $request)
    {
        try{
            $requestObject = $request->all();
            $rules = [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6',
                'dial_code'=> 'string',
                'mobile_number' => 'numeric',
                'date_of_birth' => 'date',
                'source' => 'required|string'
            ];
            $validator = Validator::make($requestObject, $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->messages()->first()], 400);
            }
    
            $created_user = User::create([
                "name" => $request->first_name.' '.$request->last_name,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "dial_code" => $request->dial_code,
                "mobile_number" => $request->mobile_number,
                "about_me" => $request->about_me,
                "date_of_birth" => $request->date_of_birth,
                "verification_code" => $this->generateToken(65)
            ]);
    
            if ($created_user) {
                $activity_store=ActivityLog::create([
                    "user_id" => $created_user->id,
                    "activity" => "signup",
                    "source" => $request->source
                ]);
    
                $this->sendEmailVerificationMail($created_user,$request->source);
                //$token = Auth::login($created_user);
                return response()->json(['success' => true, 'user_id'=>$created_user->id], 200);
            } else {
                return response()->json(['success' => false, 'error' => 'Looks like there is an issue on our side, we are working on fixing it.'], 500);
            }
        }catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    function generateToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    public function sendEmailVerificationMail($user_details,$source)
    {
        try {
            $user_email = $user_details->email;
            //$link = url("/api/verify?token=" . $user_details->verification_code);
            $link = env('APP_URL')."/api/v1/auth/verify_email?email=".$user_email."&source=".$source."&type=verify&token=".$user_details->verification_code;
            $mail_details = array('email' => $user_email, "link" => $link);

            $subject='Confirm your email and start exploring right away!';
            $view='verify';
            $cc_mail=[];
            $bcc_mail=[];
            Mail::to($user_email)->queue(new SendMails($mail_details,$subject,$view,$cc_mail,$bcc_mail));

        } catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function login(UserLogin $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'source' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $credentials = $request->only('email', 'password');
    
            $token = Auth::attempt($credentials);
    
            $user_details = User::where('email', $request->email)->select()->first();
            if ($user_details) {
    
                // Check deactivation
                if ($user_details->status == -1) {
                    return response()->json(['success' => false, 'error' => "Account Deactivated"], 400);
                }
    
                if ($user_details->status == 0) {
                    
                    if (!$user_details->verification_code) {
                        $user_details->verification_code = generateToken(65);
                        $user_details->save();
                    }
                    $this->sendEmailVerificationMail($user_details,$request->source);
                    return response()->json(['success' => false, 'error' => "Email not verified"], 403);
                }
                
                if (!$token ) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
    
                $verified = Hash::check($request->password, $user_details->password);
                if ($verified) {
                    $activity_store=ActivityLog::create([
                        "user_id" => $user_details->id,
                        "activity" => "login",
                        "source" => $request->source
                    ]);
                    return $this->createNewToken($token);
                }
                
            }
            return response()->json(['success' => false, 'error' => "Looks like you haven't registered yet, please sign up to continue."], 400);
        }catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    protected function createNewToken($token){
        $user=User::where('id',auth()->user()->id)->first();
        $user->update(['auth_token'=>$token]);
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 1444,
            'user' => $user
        ],200);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function verifyUser(VerifyUser $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'token' => 'required|string',
                'source' => 'required|string|in:dashboard,app'
            ]);
            if ($validator->fails()) {
                return view('useractioninfo')->with(["type" => "VALIDATION_ERROR"]);
            }

            $check=User::where('email',$request->email)->first();
            if(!$check){
                return view('useractioninfo')->with(["type" => "USER_NOT_FOUND"]);
            }
            if($check->status == 1 || $check->verification_code == NULL){
                return view('useractioninfo')->with(["type" => "ALREADY_ACTIVATED"]);
            }
            if($check->verification_code != $request->token){
                return view('useractioninfo')->with(["type" => "USER_NOT_FOUND"]);
            }

            $update = User::where('verification_code', $request->token)->update(["status" => 1]);
            if($update){
                User::where('verification_code', $request->token)->update(["verification_code" => null]);
                $activity_store=ActivityLog::create([
                    "user_id" => $check->id,
                    "activity" => "verify_email",
                    "source" => $request->source
                ]);
                return view('useractioninfo')->with(["type" => "VERIFY_SUCCESS"]);
            }else{
                return view('useractioninfo')->with(["type" => "SERVER_ERROR"]);
            }
    
        }catch(\Exception $error) {
            Log::error($error);
            return view('useractioninfo')->with(["type" => "SERVER_ERROR"]);
        }
    }

    public function welcomessss(Request $request)
    {
        
        return view('welcome');
    }

    public function getUserInfo(UserInfo $request)
    {
        try{
            /* This feature was working fine suddenly, in future if the code breaks use this

            //Merging both form and http request, 
            //since the form doesn't take request from http replaced request via middleware
            $http_request=\Request::all();
            $request=$request->merge([
                "logged_in_user" => $http_request['logged_in_user']
            ]);
            */
            $id=$request->user_id;

            //check if you have blocked
            $block = Block::where('blocker_id', $request->logged_in_user->id)->where('blocked_id',$id)->first();
            //check if you have been blocked
            $blocked = Block::where('blocker_id', $id)->where('blocked_id',$request->logged_in_user->id)->first();
            if($block){
                $user_details = User::where('id', $id)->select('id', 'name', 'email', 'phone_number', 'username', 'status')->first();
                $user_details->blocked = true;
                //return response()->json(['success' => true, 'user_details' => $user_details, 'user_profile_base_url' => getUrl(Config::get('constants')['USER_PROFILE_PATH'])], 200);
                return response()->json(['success' => true, 'message'=>'You have blocked this user. Unblock to view'], 200);
            }
            if($blocked){
                $user_details = User::where('id', $id)->select('id', 'name', 'email', 'phone_number', 'username', 'status')->first();
                $user_details->blocked = true;
                //return response()->json(['success' => true, 'user_details' => $user_details, 'user_profile_base_url' => getUrl(Config::get('constants')['USER_PROFILE_PATH'])], 200);
                return response()->json(['success' => true, 'message' => "You have been blocked by this user"], 200);
            }
            $user_details = $this->getUserById($id, $request->logged_in_user->id);
            unset($user_details['auth_token']);
            $user_details->blocked = false;
            if ($user_details) {
                return response()->json(['success' => true, 'user_details' => $user_details, 'user_profile_base_url' => $this->user_profile_base_url], 200);
            } else {
                return response()->json(['success' => false, 'error' => "User doesn't exist."], 400);
            }

        }catch (\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }   
    }

    public function getUserById($id, $logged_in_user_id)
    {
        $user_details = User::where('id', $id)->select()->withCount(['followings', 'followers'])->first();

        $following = false;
        if (!$user_details) {
            return;
        }

        if ($user_details->followers_count > 0) {
            $follower_details = Follower::where('leader_id', $id)->where('follower_id', $logged_in_user_id)->get();
            if (count($follower_details) > 0) {
                $following = true;
            }
        }
        $user_details->following = $following;
        return $user_details;
    }


}
