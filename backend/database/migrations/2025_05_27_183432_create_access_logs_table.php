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
    Schema::create('access_logs', function (Blueprint $table) {
      $table->id();
      $table->string('ip_address')->nullable();
      $table->string('user_agent')->nullable();
      $table->string('url');
      $table->string('method')->default('GET');
      $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
      $table->timestamp('accessed_at')->useCurrent();
      $table->timestamps();

      // インデックスを追加
      $table->index(['accessed_at']);
      $table->index(['user_id', 'accessed_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('access_logs');
  }
};
