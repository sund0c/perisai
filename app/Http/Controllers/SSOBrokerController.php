<?php

// ===================================================================
// ******************SSO Broker Controller***************************
// This controller handles the authentication process with the SSO server.
// Customized for the application to manage user sessions and roles Balikom.info
// By Wahyu Sudiatmika
// ===================================================================


namespace App\Http\Controllers;

use Session;
use App\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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



            /* utk liat saja */



            $payloadArr = json_decode(json_encode($payload), true);
            Log::info('[SSO] Decoded payload', [
                'keys'    => array_keys($payloadArr),
                // hati2: kalau mengandung data sensitif, lebih baik komentari baris berikut
                'payload' => $payloadArr,
            ]);



            /* */


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
        $sessionId = $request->sessionId ?? session()->getId();

        if ($sessionId) Session::getHandler()->destroy($sessionId);
        session()->flush();
        session()->invalidate();

        // TODO: not sure if this is the correct way to handle logout
        // SSO have its session.
        // When logout in this website and SSO still logged in (it's session active),
        // it still login in automatically in this website. I want it redirected.
        return redirect(($this->ssoDomain . '/logout'));
    }

    private function abortWithMessage(string $message)
    {
        abort(403, $message);
    }
}
