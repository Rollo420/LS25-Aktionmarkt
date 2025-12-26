<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--compress : Gzip the SQL dump} {--path=backups : Storage path under storage/app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup and store it in storage/app/backups';

    public function handle(): int
    {
        $driver = config('database.default');
        $path = $this->option('path') ?: 'backups';
        $timestamp = Carbon::now()->format('Ymd_His');
        $database = env('DB_DATABASE', 'database');
        $filename = sprintf('%s_%s.sql', $timestamp, $this->sanitizeFilename($database));
        $fullpath = storage_path('app/'.$path.'/'.$filename);

        if (!is_dir(dirname($fullpath))) {
            if (!mkdir(dirname($fullpath), 0755, true) && !is_dir(dirname($fullpath))) {
                $this->error('Could not create backup directory: '.dirname($fullpath));
                return 1;
            }
        }

        if ($driver === 'mysql') {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');
            $db = env('DB_DATABASE');

            $envPart = $pass !== null ? 'MYSQL_PWD='.escapeshellarg($pass).' ' : '';
            $cmd = $envPart.'mysqldump --single-transaction --quick --lock-tables=false -h '.escapeshellarg($host).' -P '.escapeshellarg($port).' -u '.escapeshellarg($user).' '.escapeshellarg($db).' > '.escapeshellarg($fullpath);

            $this->info('Running mysqldump...');
            exec($cmd, $output, $return);
            if ($return !== 0) {
                $this->error('mysqldump failed. Ensure `mysqldump` is available and credentials are correct.');
                return 1;
            }
        } elseif ($driver === 'pgsql') {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '5432');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');
            $db = env('DB_DATABASE');

            $envPart = $pass !== null ? 'PGPASSWORD='.escapeshellarg($pass).' ' : '';
            $cmd = $envPart.'pg_dump -h '.escapeshellarg($host).' -p '.escapeshellarg($port).' -U '.escapeshellarg($user).' -F p -f '.escapeshellarg($fullpath).' '.escapeshellarg($db);

            $this->info('Running pg_dump...');
            exec($cmd, $output, $return);
            if ($return !== 0) {
                $this->error('pg_dump failed. Ensure `pg_dump` is available and credentials are correct.');
                return 1;
            }
        } elseif ($driver === 'sqlite') {
            $databasePath = database_path(env('DB_DATABASE', 'database.sqlite'));
            if (!file_exists($databasePath)) {
                $this->error('SQLite database file not found: '.$databasePath);
                return 1;
            }
            if (!copy($databasePath, $fullpath)) {
                $this->error('Failed to copy sqlite database file.');
                return 1;
            }
        } else {
            $this->error('Unsupported database driver: '.$driver);
            return 1;
        }

        if ($this->option('compress')) {
            $this->info('Compressing dump...');
            $data = file_get_contents($fullpath);
            if ($data === false) {
                $this->error('Failed to read dump for compression.');
                return 1;
            }
            $gz = gzencode($data, 9);
            if ($gz === false) {
                $this->error('Compression failed.');
                return 1;
            }
            file_put_contents($fullpath.'.gz', $gz);
            unlink($fullpath);
            $fullpath .= '.gz';
        }

        $this->info('Backup saved to: storage/app/'.trim($path, '/').'/'.basename($fullpath));
        return 0;
    }

    private function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $name);
    }
}
