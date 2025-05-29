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
    Schema::table('conversations', function (Blueprint $table) {
      $table->timestamp('user_deleted_at')->nullable()->comment('ユーザーによる削除日時')->after('deleted_by');
      $table->string('user_deleted_reason')->nullable()->comment('ユーザー削除理由')->after('user_deleted_at');
      $table->bigInteger('user_deleted_by')->unsigned()->nullable()->comment('削除を実行したユーザーID')->after('user_deleted_reason');

      // 外部キー制約
      $table->foreign('user_deleted_by')->references('id')->on('users')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('conversations', function (Blueprint $table) {
      $table->dropForeign(['user_deleted_by']);
      $table->dropColumn(['user_deleted_at', 'user_deleted_reason', 'user_deleted_by']);
    });
  }
};
