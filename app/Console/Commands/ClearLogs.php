<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLogs extends Command
{
    // Define the command signature
    protected $signature = 'logs:clear';

    // Command description
    protected $description = 'Clear the application logs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Path to the log files
        $logFilePath = storage_path('logs/laravel.log');

        if (File::exists($logFilePath)) {
            File::put($logFilePath, '');  // Clear the log file
            $this->info('Logs have been cleared!');
        } else {
            $this->info('Log file does not exist.');
        }
    }
}