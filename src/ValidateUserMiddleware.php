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
        $token = App('request')->header('token');
        $stored_token = User::where('auth_token', $token)->select()->first();
        if ($token && $stored_token) {
            $request->logged_in_user = $stored_token;
            return $next($request);
        } else {
            return response()->json(['success' => false, 'error' => "Unauthorized user."], 401);
        }
    }
}
