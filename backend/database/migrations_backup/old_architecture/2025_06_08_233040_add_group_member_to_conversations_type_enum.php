<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    DB::statement("ALTER TABLE conversations MODIFY COLUMN type ENUM('direct', 'group', 'group_member') DEFAULT 'direct'");
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    DB::statement("ALTER TABLE conversations MODIFY COLUMN type ENUM('direct', 'group') DEFAULT 'direct'");
  }
};
