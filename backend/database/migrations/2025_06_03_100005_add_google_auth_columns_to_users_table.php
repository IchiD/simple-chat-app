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
      if (!Schema::hasColumn('users', 'google_id')) {
        $table->string('google_id')->nullable()->after('email');
      }
      if (!Schema::hasColumn('users', 'avatar')) {
        $table->string('avatar')->nullable()->after('google_id');
      }
      if (!Schema::hasColumn('users', 'social_type')) {
        $table->string('social_type')->nullable()->after('avatar');
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $columnsToCheck = ['google_id', 'avatar', 'social_type'];
      foreach ($columnsToCheck as $column) {
        if (Schema::hasColumn('users', $column)) {
          $table->dropColumn($column);
        }
      }
    });
  }
};
