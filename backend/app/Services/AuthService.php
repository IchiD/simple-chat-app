<?php

namespace App\Services;

use App\Models\User;
use App\Mail\PreRegistrationEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Carbon\Carbon;


class AuthService extends BaseService
{
  /**
   * ユーザー登録（仮登録）処理
   *
   * @param array $data バリデーション済みの登録データ
   * @return array 登録結果とメッセージ
   */
  public function register(array $data): array
  {
    // トランザクション開始
    DB::beginTransaction();
    try {
      $user = User::where('email', $data['email'])->first();

      if ($user) {
        if ($user->is_verified) {
          DB::rollBack();
          return $this->errorResponse('already_registered', 'このメールアドレスは既に登録されています。');
        }
        // 仮登録状態の場合は入力内容で上書き更新する
        $user->updateProvisionalRegistration([
          'password' => Hash::make($data['password']),
          'name'     => $data['name'],
        ]);
      } else {
        // ユーザーが存在しない場合は新規作成する
        $user = User::create([
          'email'       => $data['email'],
          'password'    => Hash::make($data['password']),
          'name'        => $data['name'],
          'is_verified' => false,
        ]);
      }

      // 認証用メールの送信を非同期実施
      Mail::to($user->email)->queue(new PreRegistrationEmail($user));

      // throw new \Exception('テスト用例外: メール送信をスキップ');

      DB::commit();
    } catch (\Exception $ex) {
      DB::rollBack();
      Log::error('登録処理中にエラーが発生しました: ' . $ex->getMessage(), [
        'email' => $data['email']
      ]);
      return $this->errorResponse('registration_failed', '登録処理に失敗しました。');
    }

    return [
      'status'  => 'success',
      'message' => '仮登録完了。確認メールを送信しました。メール内の認証リンクをクリックして本登録を完了してください。'
    ];
  }

  /**
   * メール認証処理を行い、成功ならアクセストークンを返す。
   *
   * @param string $token  リクエストから受け取ったトークン
   * @param string $ip     リクエスト元のIPアドレス（ログ用）
   * @return array         成功時は 'status' => 'success'、失敗時は 'status' => 'error' とともにエラーメッセージを返す
   */
  public function verifyEmail(string $token, string $ip): array
  {
    // トークンが空の場合
    if (!$token) {
      return $this->errorResponse('token_missing', '無効な認証リンクです。');
    }

    // ユーザーをトークンから検索
    $user = User::where('email_verification_token', $token)->first();
    if (!$user) {
      return $this->errorResponse('token_invalid', '無効な認証リンクです。（該当するユーザーが見つかりません）');
    }

    // トークンの有効期限チェック
    if (Carbon::now()->greaterThan($user->token_expires_at)) {
      return [
        'status'     => 'error',
        'error_type' => 'token_expired',
        'message'    => '認証リンクの有効期限が切れています。お手数ですが、再度登録を行なってください。',
      ];
    }

    // すでに認証済みの場合
    if ($user->is_verified) {
      return [
        'status'     => 'error',
        'error_type' => 'already_verified',
        'message'    => '既に登録済みです。',
      ];
    }

    // ユーザー情報を更新して本登録とする
    $user->update([
      'is_verified'       => true,
      'email_verified_at' => Carbon::now(),
    ]);

    // Sanctum を利用してアクセストークンを発行
    $tokenResult = $user->createToken('authToken');
    $tokenResult->accessToken->update([
      'expires_at' => Carbon::now()->addDay(),
    ]);

    return [
      'status'       => 'success',
      'message'      => '認証が完了しました。自動ログインします。',
      'access_token' => $tokenResult->plainTextToken,
      'token_type'   => 'Bearer',
      'email'        => $user->email,
    ];
  }

  /**
   * ログイン処理を実行。
   *
   * @param string $email    入力されたメールアドレス
   * @param string $password 入力されたパスワード
   * @param string $ip       リクエスト元のIPアドレス（ログ用）
   * @return array           成功時は 'status' => 'success'、失敗時は 'status' => 'error' とともにエラーメッセージを返す
   */
  public function login(string $email, string $password, string $ip): array
  {
    // ユーザーの検索
    $user = User::where('email', $email)->first();

    if (!$user || !Hash::check($password, $user->password)) {
      return [
        'status'     => 'error',
        'error_type' => 'invalid_credentials',
        'message'    => 'メールアドレスまたはパスワードが正しくありません。',
      ];
    }

    if (!$user->is_verified) {
      return [
        'status'     => 'error',
        'error_type' => 'not_verified',
        'message'    => 'メール認証がお済みでないようです。登録メールアドレスに送られたメールをご確認ください。',
      ];
    }

    // アクセストークンの発行
    $tokenResult = $user->createToken('authToken');
    $tokenResult->accessToken->update([
      'expires_at' => Carbon::now()->addDay(),
    ]);

    return [
      'status'       => 'success',
      'message'      => 'ログインに成功しました。',
      'access_token' => $tokenResult->plainTextToken,
      'token_type'   => 'Bearer',
      'email'        => $user->email,
    ];
  }

  /**
   * パスワード再設定リンクの送信処理
   *
   * @param string $email  リクエストされたメールアドレス
   * @param string $ip     リクエスト元のIPアドレス（ログ出力用）
   * @return array         処理結果とメッセージ（エラーの場合は error_type も含む）
   */
  public function sendResetLinkEmail(string $email, string $ip): array
  {
    Log::info('パスワード再設定リンクの要求を受け付けました', [
      'email' => $email,
      'ip'    => $ip,
    ]);

    $status = Password::sendResetLink(['email' => $email]);
    Log::debug('パスワード再設定リンク送信ステータス: ' . $status);

    if ($status === Password::RESET_LINK_SENT) {
      Log::info('パスワード再設定リンクの送信に成功しました', [
        'email' => $email,
      ]);
      return [
        'status'  => 'success',
        'message' => __('パスワード再設定用リンクを ' . $email . ' に送信しました。'),
      ];
    }

    if ($status === Password::INVALID_USER) {
      Log::warning('パスワード再設定リンクの送信に失敗しました（ユーザーが見つかりません）', [
        'email'  => $email,
        'status' => $status,
      ]);
      return [
        'status'     => 'error',
        'error_type' => 'invalid_user',
        'message'    => __('このメールアドレスは登録されていません。'),
      ];
    }

    if ($status === Password::RESET_THROTTLED) {
      Log::warning('パスワード再設定リンクの送信に失敗しました（スロットリング）', [
        'email'  => $email,
        'status' => $status,
      ]);
      return [
        'status'     => 'error',
        'error_type' => 'throttled',
        'message'    => __('送信が連続して行われました。しばらく待ってから再度お試しください。'),
      ];
    }

    Log::warning('パスワード再設定リンクの送信に失敗しました', [
      'email'  => $email,
      'status' => $status,
    ]);
    return $this->errorResponse('send_failed', 'パスワード再設定リンクの送信に失敗しました。');
  }

  /**
   * パスワードリセット処理を実行
   *
   * @param array $data ['email', 'password', 'password_confirmation', 'token']
   * @return array 成功時は 'status' => 'success'、失敗時は 'status' => 'error' とエラーメッセージを返す
   */
  public function resetPassword(array $data): array
  {
    Log::info('パスワードリセットの試行を開始しました', [
      'email' => $data['email'],
    ]);

    DB::beginTransaction();
    try {
      $status = Password::reset(
        $data,
        function ($user, $password) {
          $user->forceFill([
            'password' => Hash::make($password),
          ])->save();

          // Remember Token の更新
          $user->setRememberToken(Str::random(60));

          // throw new \Exception('テスト例外');

          // パスワード再設定完了イベントの発火（リスナーがメール送信などを非同期で処理する場合は、その設定も有効）
          event(new PasswordReset($user));
        }
      );

      if ($status === Password::PASSWORD_RESET) {
        DB::commit();
        Log::info('パスワードリセットに成功しました', [
          'email' => $data['email'],
        ]);
        return $this->successResponse('パスワードを新しく設定しました。');
      } else {
        DB::rollBack();
        Log::warning('パスワードリセットに失敗しました', [
          'email'  => $data['email'],
          'status' => $status,
        ]);
        return $this->errorResponse('password_reset_failed', __($status));
      }
    } catch (\Exception $ex) {
      DB::rollBack();
      Log::error('パスワードリセット中にエラーが発生しました: ' . $ex->getMessage(), [
        'email' => $data['email'],
      ]);
      return $this->errorResponse('password_reset_failed', 'パスワードリセットに失敗しました。');
    }
  }
}
