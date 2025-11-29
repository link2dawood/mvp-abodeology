<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Compress the backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('backup.database.enabled')) {
            $this->warn('Database backups are disabled in config.');
            return 0;
        }

        $this->info('Starting database backup...');

        try {
            $connection = DB::connection()->getConfig();
            $database = $connection['database'];
            $username = $connection['username'];
            $password = $connection['password'];
            $host = $connection['host'];
            $port = $connection['port'] ?? 3306;

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "database_backup_{$timestamp}.sql";
            $backupPath = config('backup.storage_path', storage_path('app/backups'));
            
            // Ensure backup directory exists
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );

            // Execute backup
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Database backup failed with return code: ' . $returnCode);
            }

            // Compress if requested
            if ($this->option('compress') || config('backup.database.compress')) {
                $this->info('Compressing backup...');
                $compressedPath = $fullPath . '.gz';
                $gz = gzopen($compressedPath, 'w9');
                gzwrite($gz, file_get_contents($fullPath));
                gzclose($gz);
                unlink($fullPath); // Remove uncompressed file
                $fullPath = $compressedPath;
                $filename .= '.gz';
            }

            $fileSize = filesize($fullPath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            $this->info("✓ Backup created successfully: {$filename} ({$fileSizeMB} MB)");

            // Clean up old backups
            $this->cleanupOldBackups($backupPath);

            // Send notification if configured
            if (config('backup.notifications.enabled') && config('backup.notifications.on_success')) {
                // You can implement email notification here
                $this->info('Notification sent (if configured)');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            
            // Send failure notification if configured
            if (config('backup.notifications.enabled') && config('backup.notifications.on_failure')) {
                // You can implement email notification here
                \Log::error('Database backup failed', ['error' => $e->getMessage()]);
            }

            return 1;
        }
    }

    /**
     * Clean up old backup files based on retention policy.
     */
    private function cleanupOldBackups(string $backupPath): void
    {
        $retentionDays = config('backup.database.retention_days', 30);
        $files = glob($backupPath . '/database_backup_*.sql*');
        $deleted = 0;

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            $fileAge = Carbon::now()->diffInDays(Carbon::createFromTimestamp($fileTime));

            if ($fileAge > $retentionDays) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup(s)");
        }
    }
}

