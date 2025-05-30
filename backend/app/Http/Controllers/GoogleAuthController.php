<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
  protected $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  /**
   * Googleの認証ページにリダイレクト
   */
  public function redirectToGoogle()
  {
    try {
      Log::info('Google認証へのリダイレクトを開始');
      return Socialite::driver('google')->redirect();
    } catch (\Exception $e) {
      Log::error('Google認証リダイレクトでエラーが発生しました', [
        'error' => $e->getMessage()
      ]);
      return response()->json([
        'status' => 'error',
        'message' => 'Google認証サービスに接続できませんでした。'
      ], 500);
    }
  }

  /**
   * Googleからのコールバック処理
   */
  public function handleGoogleCallback(Request $request)
  {
    try {
      Log::info('Googleコールバック処理を開始', [
        'query_params' => $request->query()
      ]);

      // Googleからユーザー情報を取得
      $googleUser = Socialite::driver('google')->user();

      Log::info('Googleユーザー情報を取得しました', [
        'google_id' => $googleUser->id,
        'email' => $googleUser->email,
        'name' => $googleUser->name
      ]);

      // 既存ユーザーをメールアドレスとGoogle IDで検索
      $user = User::where('email', $googleUser->email)
        ->orWhere('google_id', $googleUser->id)
        ->first();

      if ($user) {
        // 既存ユーザーの場合
        Log::info('既存ユーザーが見つかりました', ['user_id' => $user->id]);

        // 削除されたアカウントの場合
        if ($user->isDeleted()) {
          Log::warning('削除されたアカウントでのGoogle認証試行', [
            'user_id' => $user->id,
            'email' => $user->email
          ]);
          return $this->redirectWithError('このアカウントは削除されています。');
        }

        // バンされたアカウントの場合
        if ($user->isBanned()) {
          Log::warning('バンされたアカウントでのGoogle認証試行', [
            'user_id' => $user->id,
            'email' => $user->email
          ]);
          return $this->redirectWithError('このアカウントは利用停止されています。');
        }

        // Google IDが設定されていない場合は設定
        if (!$user->google_id) {
          $user->update([
            'google_id' => $googleUser->id,
            'avatar' => $googleUser->avatar,
            'social_type' => 'google'
          ]);
          Log::info('既存ユーザーにGoogle ID を設定しました', ['user_id' => $user->id]);
        }

        // メール認証が完了していない場合は自動認証
        if (!$user->is_verified) {
          $user->update([
            'is_verified' => true,
            'email_verified_at' => Carbon::now()
          ]);
          Log::info('Google認証によりメール認証を完了しました', ['user_id' => $user->id]);
        }
      } else {
        // 新規ユーザーの場合
        Log::info('新規ユーザーを作成します', ['email' => $googleUser->email]);

        $user = User::create([
          'name' => $googleUser->name,
          'email' => $googleUser->email,
          'google_id' => $googleUser->id,
          'avatar' => $googleUser->avatar,
          'social_type' => 'google',
          'is_verified' => true,
          'email_verified_at' => Carbon::now(),
          'password' => Hash::make(Str::random(32)), // ランダムパスワード
        ]);

        Log::info('新規ユーザーが作成されました', ['user_id' => $user->id]);
      }

      // Sanctumトークンを発行
      $tokenResult = $user->createToken('authToken');
      $tokenResult->accessToken->update([
        'expires_at' => Carbon::now()->addDay(),
      ]);

      Log::info('Google認証が完了しました', [
        'user_id' => $user->id,
        'email' => $user->email
      ]);

      // フロントエンドにトークンを渡してリダイレクト
      return $this->redirectWithSuccess($tokenResult->plainTextToken, $user);
    } catch (\Exception $e) {
      Log::error('Google認証コールバック処理でエラーが発生しました', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return $this->redirectWithError('Google認証処理中にエラーが発生しました。');
    }
  }

  /**
   * エラー時のフロントエンドリダイレクト
   */
  private function redirectWithError(string $message)
  {
    $encodedMessage = urlencode($message);
    return redirect("http://localhost:3000/auth/login?error={$encodedMessage}");
  }

  /**
   * 成功時のフロントエンドリダイレクト
   */
  private function redirectWithSuccess(string $token, User $user)
  {
    $encodedToken = urlencode($token);
    $userData = [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'friend_id' => $user->friend_id,
      'google_id' => $user->google_id,
      'avatar' => $user->avatar,
      'social_type' => $user->social_type,
    ];
    $encodedUserData = urlencode(json_encode($userData));

    $redirectUrl = "http://localhost:3000/auth/google/callback?token={$encodedToken}&user={$encodedUserData}";

    Log::info('フロントエンドにリダイレクトします', [
      'redirect_url' => $redirectUrl,
      'user_data' => $userData,
      'token_length' => strlen($token)
    ]);

    return redirect($redirectUrl);
  }
}
