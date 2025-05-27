<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessLog;
use Carbon\Carbon;

class AccessLogMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next)
  {
    // 特定のルートやリクエストはアクセスログから除外
    $excludedPaths = [
      'check-ip',
      'admin/logout',
      '_debugbar',
      'telescope',
      'favicon.ico',
    ];

    $shouldLog = true;
    foreach ($excludedPaths as $excludedPath) {
      if (str_contains($request->path(), $excludedPath)) {
        $shouldLog = false;
        break;
      }
    }

    // APIやAssetリクエストも除外（必要に応じて）
    if (str_starts_with($request->path(), 'api/') && $request->method() !== 'GET') {
      $shouldLog = false;
    }

    if ($shouldLog) {
      try {
        AccessLog::create([
          'ip_address' => $request->ip(),
          'user_agent' => $request->userAgent(),
          'url' => $request->fullUrl(),
          'method' => $request->method(),
          'user_id' => Auth::check() ? Auth::id() : null,
          'accessed_at' => Carbon::now(),
        ]);
      } catch (\Exception $e) {
        // ログ記録に失敗しても処理は継続
        \Log::error('Access log recording failed: ' . $e->getMessage());
      }
    }

    return $next($request);
  }
}
