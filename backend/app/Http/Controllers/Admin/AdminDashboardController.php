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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    // システム状態チェック
    $systemStatus = $this->checkSystemStatus();

    return view('admin.dashboard', compact(
      'admin',
      'userCount',
      'adminCount',
      'chatRoomCount',
      'todayMessagesCount',
      'todayActiveUsersCount',
      'systemStatus'
    ));
  }

  /**
   * ユーザー管理画面
   */
  public function users(Request $request)
  {
    $admin = Auth::guard('admin')->user();
    
    // クエリビルダーを初期化
    $query = User::query();
    
    // 検索機能
    if ($search = $request->get('search')) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', '%' . $search . '%')
          ->orWhere('email', 'LIKE', '%' . $search . '%')
          ->orWhere('friend_id', 'LIKE', '%' . $search . '%');
      });
    }
    
    // 認証状態フィルター
    if ($status = $request->get('status')) {
      if ($status === 'verified') {
        $query->where('is_verified', true);
      } elseif ($status === 'unverified') {
        $query->where('is_verified', false);
      }
    }
    
    // 並び順
    $sort = $request->get('sort', 'created_at_desc');
    switch ($sort) {
      case 'created_at_asc':
        $query->orderBy('created_at', 'asc');
        break;
      case 'name_asc':
        $query->orderBy('name', 'asc');
        break;
      case 'name_desc':
        $query->orderBy('name', 'desc');
        break;
      case 'created_at_desc':
      default:
        $query->orderBy('created_at', 'desc');
        break;
    }
    
    // ページネーション実行
    $users = $query->paginate(20);
    
    // 検索パラメータをページネーションに追加
    $users->appends($request->query());

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

  /**
   * システム状態をチェック
   */
  private function checkSystemStatus()
  {
    $status = [];

    // データベース接続とサイズチェック
    try {
      DB::connection()->getPdo();
      $dbSize = $this->getDatabaseSize();
      $status['database'] = [
        'name' => 'データベース',
        'status' => 'success',
        'message' => sprintf(
          '接続正常 | サイズ: %s | テーブル数: %d',
          $dbSize['formatted_size'],
          $dbSize['table_count']
        )
      ];
    } catch (\Exception $e) {
      $status['database'] = [
        'name' => 'データベース',
        'status' => 'error',
        'message' => '接続に失敗しました'
      ];
    }

    // Laravelストレージディレクトリ監視
    $storageInfo = $this->getStorageUsage();
    $status['app_storage'] = [
      'name' => 'アプリストレージ',
      'status' => $storageInfo['status'],
      'message' => sprintf(
        '総使用量: %s | ファイル数: %d',
        $storageInfo['total_size'],
        $storageInfo['file_count']
      )
    ];

    // ログファイル監視
    $logInfo = $this->getLogFileInfo();
    $status['logs'] = [
      'name' => 'ログファイル',
      'status' => $logInfo['status'],
      'message' => sprintf(
        '総サイズ: %s | エラー: %d件 | 最新: %s',
        $logInfo['total_size'],
        $logInfo['error_count'],
        $logInfo['last_modified']
      )
    ];

    // メモリ使用量チェック
    $memoryUsage = memory_get_usage(true);
    $memoryPeak = memory_get_peak_usage(true);
    $memoryLimit = $this->parseSize(ini_get('memory_limit'));
    if ($memoryLimit > 0) {
      $memoryPercent = ($memoryPeak / $memoryLimit) * 100;
      $status['memory'] = [
        'name' => 'メモリ使用量',
        'status' => $memoryPercent > 85 ? 'error' : ($memoryPercent > 70 ? 'warning' : 'success'),
        'message' => sprintf(
          '現在: %s | ピーク: %s (%.1f%%)',
          $this->formatBytes($memoryUsage),
          $this->formatBytes($memoryPeak),
          $memoryPercent
        )
      ];
    } else {
      $status['memory'] = [
        'name' => 'メモリ使用量',
        'status' => 'success',
        'message' => sprintf(
          '現在: %s | ピーク: %s',
          $this->formatBytes($memoryUsage),
          $this->formatBytes($memoryPeak)
        )
      ];
    }

    // Laravelキャッシュ監視
    $cacheInfo = $this->getCacheInfo();
    $status['cache'] = [
      'name' => 'キャッシュ',
      'status' => $cacheInfo['status'],
      'message' => $cacheInfo['message']
    ];

    // レスポンス時間とデータベースクエリチェック
    $performanceInfo = $this->getPerformanceInfo();
    $status['performance'] = [
      'name' => 'パフォーマンス',
      'status' => $performanceInfo['status'],
      'message' => $performanceInfo['message']
    ];

    return $status;
  }

  /**
   * バイト数を人間が読みやすい形式に変換
   */
  private function formatBytes($bytes, $precision = 2)
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
  }

  /**
   * メモリサイズ文字列を数値に変換
   */
  private function parseSize($size)
  {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);

    if ($unit) {
      return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
      return round($size);
    }
  }

  /**
   * データベースサイズを取得
   */
  private function getDatabaseSize()
  {
    try {
      // テーブル数を取得
      $tables = DB::select("SHOW TABLES");
      $tableCount = count($tables);

      // データベースサイズを取得
      $dbName = config('database.connections.mysql.database');
      $sizeQuery = "SELECT 
                      ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS db_size_mb
                    FROM information_schema.tables 
                    WHERE table_schema = ?";

      $result = DB::select($sizeQuery, [$dbName]);
      $sizeMb = $result[0]->db_size_mb ?? 0;

      return [
        'table_count' => $tableCount,
        'size_bytes' => $sizeMb * 1024 * 1024,
        'formatted_size' => $this->formatBytes($sizeMb * 1024 * 1024)
      ];
    } catch (\Exception $e) {
      return [
        'table_count' => 0,
        'size_bytes' => 0,
        'formatted_size' => '不明'
      ];
    }
  }

  /**
   * Laravelストレージの使用量を取得
   */
  private function getStorageUsage()
  {
    try {
      $storagePath = storage_path();
      $totalSize = $this->getDirectorySize($storagePath);
      $fileCount = $this->countFiles($storagePath);

      // 100MB以上で警告、500MB以上でエラー
      $status = 'success';
      if ($totalSize > 500 * 1024 * 1024) {
        $status = 'error';
      } elseif ($totalSize > 100 * 1024 * 1024) {
        $status = 'warning';
      }

      return [
        'total_size' => $this->formatBytes($totalSize),
        'file_count' => $fileCount,
        'status' => $status
      ];
    } catch (\Exception $e) {
      return [
        'total_size' => '取得失敗',
        'file_count' => 0,
        'status' => 'warning'
      ];
    }
  }

  /**
   * ログファイル情報を取得
   */
  private function getLogFileInfo()
  {
    try {
      $logsPath = storage_path('logs');
      $totalSize = $this->getDirectorySize($logsPath);
      $errorCount = $this->countRecentErrors(storage_path('logs/laravel.log'));

      // 最新のログファイルの更新日時
      $latestLog = '';
      if (file_exists(storage_path('logs/laravel.log'))) {
        $latestLog = date('m/d H:i', filemtime(storage_path('logs/laravel.log')));
      } else {
        $latestLog = 'なし';
      }

      // 10MB以上で警告、50MB以上でエラー、または過去24時間でエラー5件以上
      $status = 'success';
      if ($totalSize > 50 * 1024 * 1024 || $errorCount > 10) {
        $status = 'error';
      } elseif ($totalSize > 10 * 1024 * 1024 || $errorCount > 5) {
        $status = 'warning';
      }

      return [
        'total_size' => $this->formatBytes($totalSize),
        'error_count' => $errorCount,
        'last_modified' => $latestLog,
        'status' => $status
      ];
    } catch (\Exception $e) {
      return [
        'total_size' => '取得失敗',
        'error_count' => 0,
        'last_modified' => '不明',
        'status' => 'warning'
      ];
    }
  }

  /**
   * キャッシュ情報を取得
   */
  private function getCacheInfo()
  {
    try {
      // ファイルキャッシュのサイズ
      $cachePath = storage_path('framework/cache');
      $cacheSize = 0;
      $cacheFiles = 0;

      if (is_dir($cachePath)) {
        $cacheSize = $this->getDirectorySize($cachePath);
        $cacheFiles = $this->countFiles($cachePath);
      }

      // キャッシュテスト
      $testKey = 'system_status_test_' . time();
      try {
        cache()->put($testKey, 'test', 60);
        $testResult = cache()->get($testKey);
        cache()->forget($testKey);
        $cacheWorking = ($testResult === 'test');
      } catch (\Exception $e) {
        $cacheWorking = false;
      }

      $status = $cacheWorking ? 'success' : 'error';
      $message = sprintf(
        '%s | サイズ: %s | ファイル数: %d',
        $cacheWorking ? '動作正常' : 'キャッシュエラー',
        $this->formatBytes($cacheSize),
        $cacheFiles
      );

      return [
        'status' => $status,
        'message' => $message
      ];
    } catch (\Exception $e) {
      return [
        'status' => 'warning',
        'message' => 'キャッシュ情報取得に失敗'
      ];
    }
  }

  /**
   * パフォーマンス情報を取得
   */
  private function getPerformanceInfo()
  {
    try {
      // データベースレスポンス時間
      $startTime = microtime(true);
      DB::select('SELECT 1');
      $dbResponseTime = (microtime(true) - $startTime) * 1000;

      // アクティブなデータベース接続数（概算）
      $activeConnections = 1; // 現在の接続

      // PHPのOPcache状況
      $opcacheEnabled = function_exists('opcache_get_status');
      $opcacheInfo = '';
      if ($opcacheEnabled) {
        $opcacheStatus = opcache_get_status();
        $opcacheInfo = $opcacheStatus ? '有効' : '無効';
      } else {
        $opcacheInfo = '未サポート';
      }

      // ステータス判定
      $status = 'success';
      if ($dbResponseTime > 1000) {
        $status = 'error';
      } elseif ($dbResponseTime > 300) {
        $status = 'warning';
      }

      $message = sprintf(
        'DB応答: %.1fms | 接続数: %d | OPcache: %s',
        $dbResponseTime,
        $activeConnections,
        $opcacheInfo
      );

      return [
        'status' => $status,
        'message' => $message
      ];
    } catch (\Exception $e) {
      return [
        'status' => 'error',
        'message' => 'パフォーマンス測定に失敗'
      ];
    }
  }

  /**
   * ディレクトリサイズを再帰的に計算
   */
  private function getDirectorySize($directory)
  {
    $size = 0;
    if (is_dir($directory)) {
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
        if ($file->isFile()) {
          $size += $file->getSize();
        }
      }
    }
    return $size;
  }

  /**
   * ディレクトリ内のファイル数をカウント
   */
  private function countFiles($directory)
  {
    $count = 0;
    if (is_dir($directory)) {
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
        if ($file->isFile()) {
          $count++;
        }
      }
    }
    return $count;
  }

  /**
   * 過去24時間のエラー数をカウント
   */
  private function countRecentErrors($logFile)
  {
    try {
      if (!file_exists($logFile)) {
        return 0;
      }

      $handle = fopen($logFile, 'r');
      if (!$handle) return 0;

      $errorCount = 0;
      $yesterday = Carbon::now()->subDay();

      while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'error') !== false) {
          // ログの日時を抽出して24時間以内かチェック
          if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            $logTime = Carbon::parse($matches[1]);
            if ($logTime->gt($yesterday)) {
              $errorCount++;
            }
          }
        }
      }

      fclose($handle);
      return $errorCount;
    } catch (\Exception $e) {
      return 0;
    }
  }
}
