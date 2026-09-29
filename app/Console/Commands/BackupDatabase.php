<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Creates a backup of the SQLite database';

    public function handle()
    {
        $dbPath = database_path('database.sqlite');
        
        if (!file_exists($dbPath)) {
            $this->error('Database file not found.');
            return;
        }

        $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = storage_path('app/backups/' . $backupName);

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        copy($dbPath, $backupPath);

        $this->info("Database backup created successfully: {$backupName}");
    }
}
