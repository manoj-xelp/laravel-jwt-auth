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
use App\Models\Block;
use App\Models\Follower;
use App\Http\Requests\CreateUser;
use App\Http\Requests\UserLogin;
use App\Http\Requests\VerifyUser;
use App\Http\Requests\UserInfo;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\UploadFile;
use App\Http\Requests\ForgetPassword;
use App\Http\Requests\UserLogout;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\ChangePassword;

class UserController extends Controller
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

    public function create(CreateUser $request)
    {
        try{    
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
                "verification_code" => generateToken(65)
            ]);
    
            if ($created_user) {
                $activity_store=ActivityLog::create([
                    "user_id" => $created_user->id,
                    "activity" => "signup",
                    "source" => $request->source
                ]);
    
                $this->sendEmailVerificationMail($created_user,$request->source);
                //$token = Auth::login($created_user);
                return response()->json(['success' => true, 'message'=>'Thanks for signing up. Your account has been created. Verification link has been sent to registered email.','user_id'=>$created_user->id], 200);
            } else {
                return response()->json(['success' => false, 'error' => 'Looks like there is an issue on our side, we are working on fixing it.'], 500);
            }
        }catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function sendEmailVerificationMail($user_details,$source)
    {
        try {
            $user_email = $user_details->email;
            //$link = url("/api/verify?token=" . $user_details->verification_code);
            $link = env('SERVER_URL')."/api/v1/auth/verify_email?email=".$user_email."&source=".$source."&type=verify&token=".$user_details->verification_code;
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
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ],200);
    }

    public function refresh(Request $request) {
        return $this->createNewToken(Auth::refresh());
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

    public function updateUser(UpdateUser $request)
    {
        try{
            $update=User::where('id',$request->logged_in_user->id)
                ->update([
                    "name"=> $request->first_name.' '.$request->last_name,
                    "first_name"=> $request->first_name,
                    "last_name"=> $request->last_name,
                    "username" => strtolower($request->username),
                    "dial_code"=> $request->dial_code,
                    "mobile_number"=> $request->mobile_number,
                    "date_of_birth"=> $request->date_of_birth,
                    "about_me"=> $request->about_me,
                    "profile_pic"=> $request->profile_pic
                ]);
            
            if($update){
                return response()->json(['success' => true, 'message'=>'Your account details have been saved.', 'user_details' => $update, 'user_profile_base_url' => $this->user_profile_base_url], 200);
            }else{
                return response()->json(['success' => false, 'error' => 'Something went wrong! Please try again later.'], 502);
            }
            
        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function uploadSingleFile(UploadFile $request)
    {
        try{
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

    public function logout(UserLogout $request)
    {
        try{
            $user = User::where('id', $request->logged_in_user->id)->first();
            $user->auth_token = null;
            $user->save();

            $activity_store=ActivityLog::create([
                "user_id" => $user->id,
                "activity" => "logout",
                "source" => $request->source
            ]);

            Auth::logout();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ], 200);

        }catch(\Exception $error){
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Internal server error.'], 500);
        }
    }

    public function forgotPassword(ForgetPassword $request)
    {
        try {
            $verification_token = generateToken(65);
            $user_details  = User::where('email', $request->email)->select()->first();
            if ($user_details) {
                
                // Check activation
                if ($user_details->status == 0) {
                    if (!$user_details->verification_code) {
                        $user_details->verification_code = generateToken(65);
                        $user_details->save();
                    }
                    $this->sendEmailVerificationMail($user_details,$request->source);
                    return response()->json(['success' => false, 'error' => "Before you can reset your password, please verify your email address."], 400);
                }
                
                // Use old token
                if (isset($user_details->verification_code) && $user_details->verification_code != "") {
                    $verification_token = $user_details->verification_code;
                }
                
                // Check deactivation
                if ($user_details->status == -1) {
                    return response()->json(['success' => false, 'error' => "Account Deactivated."], 400);
                }
                $user_email = $user_details->email;
                $user_details->verification_code = $verification_token;
                $user_details->save();

                $link = $this->reset_password_url."?type=resetpassword&source=".$request->source."&token=".$verification_token;
                $mail_details = array('name' => $user_details->name, "link" => $link);
                $subject="Reset your password";
                $view='forgotpassword';
                $cc=[];
                $bcc=[];

                Mail::to($user_email)->queue(new SendMails($mail_details,$subject,$view,$cc,$bcc));
                $activity_store=ActivityLog::create([
                    "user_id" => $user_details->id,
                    "activity" => "forgot_password",
                    "source" => $request->source
                ]);
                return response()->json(['success' => true, 'message' => 'Password reset link successfully sent to your email.'], 200);
            } else {
                return response()->json(['success' => false, 'error' => 'Invalid Email Provided.'], 400);
            }
        } catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Looks like there is an issue on our side, we are working on fixing it.'], 500);
        }
    }

    public function resetPasswordValidation(ResetPassword $request)
    {
        try {
            $user_details  = User::where('verification_code', $request->token)->first();
            if ($user_details) {
                return view('resetpassword', ['token' => $request->token, 'source' => $request->source]);
            } else {
                return view('useractioninfo')->with(["type" => "VALIDATION_ERROR"]);
            }
        } catch (\Exception $error) {
            Log::error($error);
            return view('useractioninfo')->with(["type" => "SERVER_ERROR"]);
        }
    }

    public function resetPassword(ResetPassword $request)
    {
        try {
            $credentials = $request->all();
            $rules = [
                'token' => 'required|string',
                'source' => 'required|in:dashboard,app',
                'password' => 'required|min:8|confirmed|max:20|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
                'password_confirmation' => 'required|min:8|max:20',
            ];
            $validator = Validator::make($credentials, $rules);
            if ($validator->fails()) {
                return view('useractioninfo')->with(["type" => "VALIDATION_ERROR"]);
            }

            $user_details  = User::where('verification_code', $request->token)->first();
            if ($user_details) {
                $user_details->password = Hash::make($request->password);
                $user_details->verification_code = null;
                $user_details->save();
                $details = [
                    "type" => "PASSWORD_CHANGED",
                    "email" => $user_details->email,
                    "dateTime" => Carbon::now('Asia/Kolkata')->toDayDateTimeString()
                ];

                $mail = $user_details->email;
                $mail_details=$details;

                $subject="Your password has been changed";
                $view='useractioninfo';
                $cc_mail=[];
                $bcc_mail=[];
                Mail::to($mail)->queue(new SendMails($mail_details,$subject,$view,$cc_mail,$bcc_mail));
                
                $activity_store=ActivityLog::create([
                    "user_id" => $user_details->id,
                    "activity" => "reset_password",
                    "source" => $request->source
                ]);

                return view('useractioninfo',$details);

            } else {
                return view('useractioninfo')->with(["type" => "PASSWORD_NOT_CHANGED"]);
            }
        } catch (\Exception $error) {
            Log::error($error);
            return view('useractioninfo')->with(["type" => "SERVER_ERROR"]);
        }
    }

    public function changePassword(ChangePassword $request)
    {
        try{
            return response()->json(['success' => false, 'message' => 'Inprogress'], 404);
        } catch (\Exception $error) {
            Log::error($error);
            return response()->json(['success' => false, 'error' => 'Looks like there is an issue on our side, we are working on fixing it.'], 500);
        }
    }

}
