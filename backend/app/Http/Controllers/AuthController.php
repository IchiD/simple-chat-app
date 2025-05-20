<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Mail\PreRegistrationEmail;
use App\Services\AuthService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
  protected $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }
  /**
   * 仮登録処理
   */
  public function register(RegisterRequest $request)
  {
    // バリデーション済みデータを取得
    $data = $request->validated();

    Log::info('仮登録の試行を開始しました', [
      'email' => $data['email'],
      'ip'    => $request->ip(),
    ]);

    $result = $this->authService->register($data);

    if ($result['status'] === 'error') {
      Log::warning('仮登録処理でエラーが発生しました', ['email' => $data['email']]);
      return response()->json($result, 422);
    }

    Log::info('仮登録処理が正常に完了しました', ['email' => $data['email']]);
    return response()->json($result, 201);
  }

  /**
   * メール認証リンクによる本登録処理
   */
  public function verifyEmail(VerifyEmailRequest $request)
  {
    $data = $request->validated();
    $token = $data['token'];

    Log::info('メール認証の試行を開始しました', [
      'token' => $token,
      'ip'    => $request->ip(),
    ]);

    $result = $this->authService->verifyEmail($token, $request->ip());

    if ($result['status'] === 'error') {
      // エラーの場合はレスポンスを返す
      Log::warning('メール認証に失敗しました', [
        'error_type' => $result['error_type'],
        'ip'         => $request->ip(),
      ]);
      // エラーコードは必要に応じて調整してください
      return response()->json($result, 400);
    }

    Log::info('アクセストークンを発行しました', ['email' => $result['email']]);

    return response()->json($result, 200);
  }

  /**
   * ログイン処理
   */
  public function login(LoginRequest $request)
  {
    Log::info('ログインの試行を開始しました', [
      'email' => $request->email,
      'ip'    => $request->ip(),
    ]);

    // サービス層に処理を委譲
    $result = $this->authService->login(
      $request->email,
      $request->password,
      $request->ip()
    );

    // エラーの場合
    if ($result['status'] === 'error') {
      return response()->json($result, 401);
    }

    Log::info('ログインに成功しました', [
      'email' => $result['email'],
      'ip'    => $request->ip(),
    ]);

    return response()->json($result, 200);
  }

  /**
   * ログアウト処理
   */
  public function logout(Request $request)
  {
    Log::info('ログアウトの試行を開始しました', [
      'email' => $request->user()->email,
      'ip'    => $request->ip(),
    ]);

    try {
      $request->user()->currentAccessToken()->delete();
      Log::info('ログアウトに成功しました', [
        'email' => $request->user()->email,
        'ip'    => $request->ip(),
      ]);
      return response()->json([
        'message' => 'ログアウトしました。',
      ], 200);
    } catch (\Exception $e) {
      Log::error('ログアウトに失敗しました', [
        'email' => $request->user()->email,
        'ip'    => $request->ip(),
        'error' => $e->getMessage(),
      ]);
      return response()->json([
        'message' => 'ログアウト処理中にエラーが発生しました。',
      ], 500);
    }
  }

  /**
   * パスワード再設定リンク送信
   */
  public function sendResetLinkEmail(PasswordResetEmailRequest $request)
  {
    $email = $request->input('email');
    $ip = $request->ip();

    $result = $this->authService->sendResetLinkEmail($email, $ip);

    if ($result['status'] === 'success') {
      return response()->json($result, 200);
    }

    if ($result['status'] === 'error') {
      if ($result['error_type'] === 'invalid_user') {
        return response()->json($result, 404);
      }
    }

    return response()->json($result, 500);
  }


  /**
   * パスワードリセット処理
   */
  public function resetPassword(ResetPasswordRequest $request)
  {
    Log::info('【コントローラー】パスワードリセットの試行を開始しました', [
      'email' => $request->input('email'),
      'ip'    => $request->ip(),
    ]);

    $data = $request->only('email', 'password', 'password_confirmation', 'token');

    // サービス層に処理を委譲
    $result = $this->authService->resetPassword($data);

    if ($result['status'] === 'success') {
      return response()->json($result, 200);
    }

    return response()->json($result, 500);
  }

  /**
   * 現在のログインユーザー情報を取得
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getCurrentUser(Request $request)
  {
    $user = $request->user();

    // 必要な情報のみを返す
    return response()->json([
      'id' => $user->id,
      'friend_id' => $user->friend_id,
      'name' => $user->name,
      'email' => $user->email,
      'avatar' => $user->avatar,
      'bio' => $user->bio,
      'last_active_at' => $user->last_active_at,
      'created_at' => $user->created_at,
    ]);
  }

  /**
   * ユーザー名を更新
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateName(Request $request)
  {
    $user = $request->user();

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $newName = $request->input('name');

    try {
      $user->name = $newName;
      $user->save();

      Log::info('ユーザー名が更新されました', ['user_id' => $user->id, 'new_name' => $newName]);

      return response()->json([
        'message' => 'ユーザー名が正常に更新されました。',
        'user' => [
          'id' => $user->id,
          'friend_id' => $user->friend_id,
          'name' => $user->name,
          'email' => $user->email,
          'avatar' => $user->avatar,
          'bio' => $user->bio,
          'last_active_at' => $user->last_active_at,
          'created_at' => $user->created_at,
        ]
      ], 200);
    } catch (\Exception $e) {
      Log::error('ユーザー名の更新中にエラーが発生しました', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
      ]);
      return response()->json(['message' => 'ユーザー名の更新中にエラーが発生しました。'], 500);
    }
  }
}
