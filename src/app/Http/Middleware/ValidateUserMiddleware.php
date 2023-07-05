<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

use Illuminate\Http\Request;

class ValidateUserMiddleware 

{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = App('request')->header('access_token');
        $stored_token = User::where('auth_token', $token)->first();
        
        if ($token && $stored_token) {
            $request_array=$request->all();

            $request_array['logged_in_user'] = $stored_token;
            $request->replace($request_array);
            return $next($request);
        } else {
            return response()->json(['success' => false, 'error' => "Unauthorized user."], 401);
        }
    }
}
