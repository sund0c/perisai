<?php

// ===================================================================
// ******************SSO MIdleware***************************
// This handles the authentication process with the SSO server.
// Customized for the application to manage user sessions and roles Balikom.info
// By Wahyu Sudiatmika
// ===================================================================

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class SSOBrokerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sessionid = session()->getId();
        if (!Auth::check()) {
            if(!session('UserIsAuthenticated')){
                session(['urlToRedirect'=>$request->url()]);
                return redirect('authenticateToSSO');
            }
            $UserService = new UserService();
            $sso_session = Session::get('authUserData');
            if (!$sso_session) {
                return redirect('authenticateToSSO');
            }
            if ($UserService->isAlreadyExist($sso_session->user)) {
                $user = $UserService->whereSsoUserId($sso_session->user->id);
                Auth::loginUsingId($user->id);
                Session::getFacadeRoot()->setId($sessionid);
            }
        }
        return $next($request);
    }

}
