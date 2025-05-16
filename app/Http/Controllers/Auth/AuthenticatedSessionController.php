<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        $token = $request->query('token') ?? \Illuminate\Support\Str::random(40);
        $key = env('REGISTRATION_TOKEN_KEY');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedToken = openssl_encrypt($token, 'aes-256-cbc', $key, 0, $iv);
        $encryptedToken = base64_encode($encryptedToken.'::'.$iv);

        Token::create([
            'token' => $encryptedToken,
            'type' => 'registration',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
            'token' => $token,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Optional token validation
        if ($request->has('token')) {
            $key = env('REGISTRATION_TOKEN_KEY');
            $plainToken = $request->token;
            $tokens = Token::where('type', 'login')
                ->where('expires_at', '>', Carbon::now())
                ->get();

            $validToken = null;
            foreach ($tokens as $token) {
                [$encryptedToken, $iv] = explode('::', base64_decode($token->token));
                $decryptedToken = openssl_decrypt($encryptedToken, 'aes-256-cbc', $key, 0, $iv);
                if ($decryptedToken === $plainToken) {
                    $validToken = $token;
                    break;
                }
            }

            if (! $validToken) {
                return redirect()->back()->withErrors(['token' => 'Invalid or expired token']);
            }
            $validToken->delete();
        }

        // Handle social login users
        if ($user && $user->provider === 'social') {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Handle email/password login
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
