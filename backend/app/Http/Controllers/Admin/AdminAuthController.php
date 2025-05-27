<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthController extends Controller
{
  /**
   * ログイン画面を表示
   */
  public function showLoginForm()
  {
    return view('admin.auth.login');
  }

  /**
   * ログイン処理
   */
  public function login(Request $request)
  {
    // レート制限（5回/分）
    $key = 'admin_login_attempts:' . $request->ip();
    if ($this->hasTooManyLoginAttempts($request)) {
      return back()->withErrors([
        'email' => '試行回数が上限を超えました。しばらく時間をおいてから再試行してください。'
      ]);
    }

    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
      $request->session()->regenerate();

      return redirect()->intended(route('admin.dashboard'));
    }

    // ログイン失敗時のレート制限記録
    $this->incrementLoginAttempts($request);

    throw ValidationException::withMessages([
      'email' => 'メールアドレスまたはパスワードが正しくありません。',
    ]);
  }

  /**
   * ログアウト処理
   */
  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
  }

  /**
   * ログイン試行回数制限チェック
   */
  protected function hasTooManyLoginAttempts(Request $request)
  {
    return RateLimiter::tooManyAttempts(
      'admin_login:' . $request->ip(),
      5 // 5回まで
    );
  }

  /**
   * ログイン試行回数を記録
   */
  protected function incrementLoginAttempts(Request $request)
  {
    RateLimiter::hit(
      'admin_login:' . $request->ip(),
      60 // 60秒
    );
  }
}
