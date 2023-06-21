<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\SendMails;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class UserController extends Controller
{
    
    public function create(Request $request)
    {

        $requestObject = $request->all();
        $rules = [
            'name' => 'max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'dial_code'=> 'numeric',
            'mobile_number' => 'numeric',
            'date_of_birth' => 'date'
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
            "username" => $request->uname,
            "dial_code" => $request->dial_code,
            "mobile_number" => $request->phone_number,
            "about_me" => $request->about_me,
            "date_of_birth" => $request->date_of_birth,
            "verification_code" => $this->generateToken(65)
        ]);

        if ($created_user) {
            $this->sendEmailVerificationMail($created_user);
            $token = Auth::login($created_user);
            return response()->json(['success' => true, 'user_id'=>$created_user->id], 200);
        } else {
            return response()->json(['success' => false, 'error' => 'Looks like there is an issue on our side, we are working on fixing it.'], 500);
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

    public function sendEmailVerificationMail($user_details)
    {
        try {
            $user_email = $user_details->email;
            //$link = url("/api/verify?token=" . $user_details->verification_code);
            $link = env('APP_URL')."/redirect.php?type=verify&params=".urlencode('token='.$user_details->verification_code);
            $mail_details = array('email' => $user_email, "link" => $link);

            $subject='Confirm your email and start exploring right away!';
            $view='verify';
            $cc_mail=[];
            $bcc_mail=[];
            Mail::to($user_email)->queue(new SendMails($mail_details,$subject,$view,$cc_mail,$bcc_mail));

        } catch (\Exception $error) {

            throw $error;
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
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
                $this->sendEmailVerificationMail($user_details);
                return response()->json(['success' => false, 'error' => "Email not verified"], 403);
            }
            
            if (!$token ) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $verified = Hash::check($request->password, $user_details->password);
            if ($verified) {
                return $this->createNewToken($token);
            }
            
        }

        return response()->json(['success' => false, 'error' => "Looks like you haven't registered yet, please sign up to continue."], 400);
    }

    protected function createNewToken($token){
        $user=User::where('id',auth()->user()->id)->first();
        $user->update(['auth_token'=>$token]);
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 2,
            'user' => $user
        ],200);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function verifyUser(Request $request)
    {
        $updated_row = User::where('verification_code', $request->token)->update(["status" => 1]);
        if ($updated_row) {
            $data = User::where('verification_code', $request->token)->first();
            if(!empty($data)){
                User::where('verification_code', $request->token)->update(["verification_code" => null]);
                return response()->json(['success' => true, 'message' => "User Email verified successfully"], 200);

            }else{
                return response()->json(['success' => false, 'error' => 'User not found.'], 400);
            }
        } else {
            return "Link expired";
        }
    }

    public function welcomessss(Request $request)
    {
        
        return view('welcome');
    }
}
