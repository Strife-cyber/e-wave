<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        // Generate a random token
        $plainToken = Str::random(40);
        $key = env('REGISTRATION_TOKEN_KEY');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedToken = openssl_encrypt($plainToken, 'aes-256-cbc', $key, 0, $iv);
        $encryptedToken = base64_encode($encryptedToken.'::'.$iv);

        // Store the encrypted token with a 15-minute expiration
        Token::create([
            'token' => $encryptedToken,
            'type' => 'registration',
            'expires_at' => Carbon::now()->addMinutes(15),
        ]);

        return Inertia::render('auth/Register', [
            'token' => $plainToken,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $provider = $request->has('emailVerified') && ! $request->has('password') ? 'social' : 'email';

        // Conditional validation for email uniqueness
        $emailRules = ['required', 'string', 'lowercase', 'email', 'max:255'];
        if ($provider === 'email') {
            $emailRules[] = 'unique:'.User::class;
        }

        $request->validate([
            'token' => 'required|string',
            'displayName' => 'required_without:name|string|max:255',
            'name' => 'required_without:displayName|string|max:255',
            'photoUrl' => 'nullable|string',
            'email' => $emailRules,
            'password' => ['required_without:emailVerified', 'confirmed', Rules\Password::defaults()],
            'emailVerified' => 'nullable|boolean',
        ]);

        // Validate the token
        $key = env('REGISTRATION_TOKEN_KEY');
        $plainToken = $request->token;
        $tokens = Token::where('type', 'registration')
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

        // Check if the user exists for social login
        if ($provider === 'social') {
            $existingUser = User::where('email', $request->email)
                ->where('provider', 'social')
                ->first();
            if ($existingUser) {
                // Log in the existing user
                Auth::login($existingUser);
                $validToken->delete();

                return to_route('dashboard');
            }
        }

        // Create new user
        $user = User::create([
            'name' => $request->get('name') ?? $request->get('displayName'),
            'email' => $request->email,
            'password' => Hash::make($request->password ?? Str::random()),
            'role' => 'student',
            'email_verified_at' => $request->emailVerified ? now() : null,
            'profile' => $request->photoUrl,
            'provider' => $provider,
        ]);

        // Clean up
        $validToken->delete();

        // Trigger registration event and log in
        event(new Registered($user));
        Auth::login($user);

        return to_route('dashboard');
    }
}
