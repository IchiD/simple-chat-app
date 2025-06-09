<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DropConversationsTable extends Command
{
  protected $signature = 'conversations:drop-table {--force : Skip confirmation}';
  protected $description = 'Drop the old conversations table after Phase 2 migration';

  public function handle()
  {
    $this->info('Checking conversations table status...');

    if (!Schema::hasTable('conversations')) {
      $this->info('✅ Conversations table does not exist - already dropped');
      return 0;
    }

    // Show current table count
    $conversationsCount = DB::table('conversations')->count();
    $groupsCount = DB::table('groups')->count();
    $chatRoomsCount = DB::table('chat_rooms')->count();

    $this->info("Current state:");
    $this->info("  - conversations table: {$conversationsCount} records");
    $this->info("  - groups table: {$groupsCount} records");
    $this->info("  - chat_rooms table: {$chatRoomsCount} records");

    // Verify that Phase 2 migration was successful
    if ($groupsCount === 0 || $chatRoomsCount === 0) {
      $this->error('❌ Phase 2 migration appears incomplete. Cannot drop conversations table.');
      $this->error('Please ensure groups and chat_rooms tables have data before proceeding.');
      return 1;
    }

    if (!$this->option('force')) {
      $this->warn('🚨 This will permanently delete the conversations table.');
      $this->warn('Make sure you have a backup and Phase 2 migration is complete.');

      if (!$this->confirm('Are you sure you want to drop the conversations table?')) {
        $this->info('Operation cancelled.');
        return 0;
      }
    }

    try {
      $this->info('Dropping conversations table...');
      Schema::dropIfExists('conversations');

      $this->info('✅ Successfully dropped conversations table');
      $this->info('Phase 2 migration cleanup complete!');

      return 0;
    } catch (\Exception $e) {
      $this->error('❌ Failed to drop conversations table: ' . $e->getMessage());
      return 1;
    }
  }
}
