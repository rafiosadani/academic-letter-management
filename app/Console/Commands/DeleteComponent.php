<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DeleteComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'component:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a Laravel Blade component';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = str_replace('\\', '/', $this->argument('name'));

        // Path class component (dukung nested folder)
        $classPath = app_path('View/Components/' . $name . '.php');

        // Konversi path blade ke kebab-case + nested folder
        $bladePath = collect(explode('/', $name))
            ->map(fn ($part) => Str::kebab(Str::snake($part)))
            ->implode('/');

        $viewPath = resource_path("views/components/{$bladePath}.blade.php");

        $deleted = false;

        if (file_exists($classPath)) {
            unlink($classPath);
            $this->info("Deleted class: $classPath");
            $deleted = true;
        }

        if (file_exists($viewPath)) {
            unlink($viewPath);
            $this->info("Deleted view:  $viewPath");
            $deleted = true;
        }

        if (!$deleted) {
            $this->warn("Component '{$name}' not found.");
        } else {
            $this->info("Component '{$name}' deleted successfully.");
        }
    }
}
