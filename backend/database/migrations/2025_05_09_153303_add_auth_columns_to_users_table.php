<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      // 認証関連のカラムを追加
      $table->boolean('is_verified')->default(false)->after('password');
      $table->string('email_verification_token')->nullable()->after('is_verified');
      $table->timestamp('token_expires_at')->nullable()->after('email_verification_token');

      // ユーザーのプロフィール情報用のカラム（必要に応じて）
      $table->string('avatar')->nullable()->after('token_expires_at');
      $table->text('bio')->nullable()->after('avatar');
      $table->timestamp('last_active_at')->nullable()->after('bio');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      // 追加したカラムを削除
      $table->dropColumn([
        'is_verified',
        'email_verification_token',
        'token_expires_at',
        'avatar',
        'bio',
        'last_active_at'
      ]);
    });
  }
};
