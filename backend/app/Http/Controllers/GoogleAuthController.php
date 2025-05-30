<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class GoogleAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Redirect to Google authentication page
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return redirect('http://localhost:3000/auth/login?error=google_redirect_failed');
        }
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();
            
            // Find existing user by email or Google ID
            $user = User::where('email', $googleUser->getEmail())
                       ->orWhere('google_id', $googleUser->getId())
                       ->first();

            if ($user) {
                // Update existing user with Google info if not already set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'social_type' => 'google',
                    ]);
                }
            } else {
                // Create new user with Google info
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'social_type' => 'google',
                    'email_verified_at' => now(),
                    'friend_id' => $this->generateUniqueFriendId(),
                    'status' => 'active',
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            return redirect("http://localhost:3000/auth/callback?token={$token}&user=" . urlencode(json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'is_admin' => $user->is_admin,
            ])));

        } catch (Exception $e) {
            Log::error('Google callback error: ' . $e->getMessage());
            return redirect('http://localhost:3000/auth/login?error=google_auth_failed');
        }
    }

    /**
     * Generate a unique friend ID
     */
    private function generateUniqueFriendId()
    {
        do {
            $friendId = mt_rand(100000, 999999);
        } while (User::where('friend_id', $friendId)->exists());

        return $friendId;
    }
}