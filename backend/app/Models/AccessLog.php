<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AccessLog extends Model
{
  protected $fillable = [
    'ip_address',
    'user_agent',
    'url',
    'method',
    'user_id',
    'accessed_at',
  ];

  protected $casts = [
    'accessed_at' => 'datetime',
  ];

  /**
   * アクセスしたユーザー（ログインしている場合）
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * 今日のアクセス数を取得
   */
  public static function getTodayCount(): int
  {
    return self::whereDate('accessed_at', Carbon::today())->count();
  }

  /**
   * 指定期間のアクセス数を取得
   */
  public static function getCountByDateRange(Carbon $startDate, Carbon $endDate): int
  {
    return self::whereBetween('accessed_at', [$startDate, $endDate])->count();
  }

  /**
   * ユニークアクセス数を取得（IPアドレスベース）
   */
  public static function getUniqueAccessCount(?Carbon $date = null): int
  {
    $query = self::query();

    if ($date) {
      $query->whereDate('accessed_at', $date);
    } else {
      $query->whereDate('accessed_at', Carbon::today());
    }

    return $query->distinct('ip_address')->count('ip_address');
  }
}
