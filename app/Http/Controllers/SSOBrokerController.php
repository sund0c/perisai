<?php

// ===================================================================
// ******************SSO Broker Controller***************************
// This controller handles the authentication process with the SSO server.
// Customized for the application to manage user sessions and roles Balikom.info
// By Wahyu Sudiatmika
// ===================================================================


namespace App\Http\Controllers;

use App\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\UserService;

class SSOBrokerController extends Controller
{
    private string $ssoServerLink, $logoutLink, $ssoDomain, $protocol;
    protected UserService $UserService;

    public function __construct(UserService $UserService)
    {
        $this->ssoDomain     = config('app.sso_domain');
        $this->ssoServerLink = $this->ssoDomain . '/authBroker/';
        $this->protocol      = request()->secure() ? 'https' : 'http';
        $this->logoutLink    = "{$this->protocol}://{$_SERVER['HTTP_HOST']}/exit";
        $this->UserService   = $UserService;
    }

    public function authenticateToSSO(Request $request)
    {
        if ($token = $request->authData) return $this->handleSSOResponse($token);
        if (session()->has('authUserData')) return response()->json(['authenticated' => true]);
        return $this->redirectToSSO();
    }

    private function handleSSOResponse(string $token)
    {
        try {
            $res = json_decode((new Client())->post($this->ssoDomain . '/api/v1/auth/jwt/verify', [
                'form_params' => ['token' => $token]
            ])->getBody());

            if (empty($res->status) || empty($res->data)) return $this->abortWithMessage('Invalid JWT String data!');

            $JWT = new JWT();
            $JWT->setJWTString($res->data);

            if (!$JWT->decodeJWT()) return $this->abortWithMessage('Invalid JWT data!');

            $payload = $JWT->getPayloadJWT();
            if (empty($payload->roles)) return redirect()->route('not-authorized')->with('error', 'Anda tidak memiliki akses ke aplikasi ini');
            if (session()->getId() !== $payload->sessionRequest) return $this->abortWithMessage('Invalid browser session!');

            session([
                'UserIsAuthenticated' => 1,
                'authUserData'        => $payload,
                'defaultRole'         => $payload->roles[0],
                'sso_user_id'         => $payload->user->id
            ]);

            return redirect($payload->urlToRedirect ?? '/');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return redirect()->to($this->ssoDomain);
        }
    }

    private function redirectToSSO()
    {
        $JWT = new JWT();
        $JWT->setPayloadJWT([
            'redirect'       => "{$this->protocol}://{$_SERVER['HTTP_HOST']}/authData",
            'urlToRedirect'  => session('urlToRedirect'),
            'logoutLink'     => $this->logoutLink,
            'kode_broker'    => config('app.broker_code'),
            'sessionRequest' => session()->getId(),
        ]);
        $JWT->encodeJWT();

        return redirect($this->ssoServerLink . $JWT->getJWTString());
    }

    public function logout(Request $request)
    {
        if ($request->sessionId) \Session::getHandler()->destroy($request->sessionId);
        session()->flush();
        return response()->json(['message' => 'Logged out successfully']);
    }

    private function abortWithMessage(string $message)
    {
        abort(403, $message);
    }
}
