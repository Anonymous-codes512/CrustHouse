<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all models in the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelsPath = app_path('Models');
        if (!is_dir($modelsPath)) {
            $this->info('No models directory found.');
            return 0;
        }

        $modelFiles = File::allFiles($modelsPath);

        $models = array_map(function ($file) {
            return 'App\\Models\\' . $file->getFilenameWithoutExtension();
        }, $modelFiles);

        if (empty($models)) {
            $this->info('No models found.');
        } else {
            $this->info('List of models:');
            foreach ($models as $model) {
                $this->line($model);
            }
        }

        return 0;
    }
}
