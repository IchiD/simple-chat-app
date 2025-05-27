<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
  /**
   * 管理画面ダッシュボード
   */
  public function dashboard()
  {
    $admin = Auth::guard('admin')->user();

    // 統計情報
    $userCount = User::count();
    $adminCount = Admin::count();

    // チャットルーム数
    $chatRoomCount = Conversation::count();

    // 本日送信されたメッセージ数
    $todayMessagesCount = Message::whereDate('sent_at', date('Y-m-d'))->count();

    // 本日のアクティブユーザー数（本日メッセージを送信したユーザー）
    $todayActiveUsersCount = Message::whereDate('sent_at', date('Y-m-d'))
      ->distinct('sender_id')
      ->count('sender_id');

    return view('admin.dashboard', compact(
      'admin',
      'userCount',
      'adminCount',
      'chatRoomCount',
      'todayMessagesCount',
      'todayActiveUsersCount'
    ));
  }

  /**
   * ユーザー管理画面
   */
  public function users()
  {
    $admin = Auth::guard('admin')->user();
    $users = User::paginate(20);

    return view('admin.users.index', compact('admin', 'users'));
  }

  /**
   * アドミン管理画面（スーパーアドミンのみ）
   */
  public function admins()
  {
    $admin = Auth::guard('admin')->user();

    if (!$admin->isSuperAdmin()) {
      abort(403, 'アクセス権限がありません。');
    }

    $admins = Admin::paginate(20);

    return view('admin.admins.index', compact('admin', 'admins'));
  }

  /**
   * アドミン作成（スーパーアドミンのみ）
   */
  public function createAdmin(Request $request)
  {
    $admin = Auth::guard('admin')->user();

    if (!$admin->isSuperAdmin()) {
      abort(403, 'アクセス権限がありません。');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:admins',
      'password' => 'required|string|min:8',
      'role' => 'required|in:admin,super_admin',
    ]);

    Admin::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
    ]);

    return redirect()->route('admin.admins')->with('success', 'アドミンが作成されました。');
  }
}
