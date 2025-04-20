<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\SplFileInfo;

class GenerateModelResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate controllers, factories, seeders, policies, and form requests for all Eloquent models';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $modelsPath = app_path('Models');
        $modelFiles = File::allFiles($modelsPath);

        foreach ($modelFiles as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $modelName = $file->getBasename('.php');
            $relativePath = $file->getRelativePath();

            $modelClass = $this->getModelClass($file);

            if (!$this->isValidModel($modelClass)) {
                $this->error("Skipping $modelName: Not a valid Eloquent model.");
                continue;
            }

            $this->generateResources($modelClass, $relativePath, $modelName);
        }

        $this->newLine();
        $this->info('All resources generated successfully!');
        $this->line('Note: Don\'t forget to register your policies in AuthServiceProvider.');
    }

    /**
     * Generate all resources for a given model
     */
    protected function generateResources(string $modelClass, string $relativePath, string $modelName): void
    {
        $this->generateFactory($modelClass, $modelName);
        $this->generateSeeder($modelClass, $relativePath, $modelName);
        $this->generatePolicy($modelClass, $relativePath, $modelName);
        $this->generateRequests($relativePath, $modelName);
        $this->generateController($modelClass, $relativePath, $modelName);
    }

    /**
     * Generate controller with resource methods
     */
    protected function generateController(string $modelClass, string $relativePath, string $modelName): void
    {
        $controllerPath = $relativePath
            ? "$relativePath/{$modelName}Controller"
            : "{$modelName}Controller";

        $this->call('make:controller', [
            'name' => $controllerPath,
            '--resource' => true,
            '--model' => $modelClass,
        ]);

        $this->updateControllerRequests($relativePath, $modelName);
    }

    /**
     * Update controller to use custom form requests
     */
    protected function updateControllerRequests(string $relativePath, string $modelName): void
    {
        $storeRequest = $this->getRequestClass($relativePath, $modelName, 'Store');
        $updateRequest = $this->getRequestClass($relativePath, $modelName, 'Update');

        $controllerPath = app_path(
            "Http/Controllers/" .
            ($relativePath ? "$relativePath/" : "") .
            "{$modelName}Controller.php"
        );

        if (!File::exists($controllerPath)) return;

        $content = File::get($controllerPath);

        // Add use statements
        $content = str_replace(
            'use Illuminate\Http\Request;',
            "use Illuminate\Http\Request;\nuse $storeRequest;\nuse $updateRequest;",
            $content
        );

        // Update method parameters
        $content = preg_replace([
            '/public function store\(Request \$request\)/',
            '/public function update\(Request \$request/'
        ], [
            "public function store($storeRequest \$request)",
            "public function update($updateRequest \$request"
        ], $content);

        File::put($controllerPath, $content);
    }

    /**
     * Generate model factory
     */
    protected function generateFactory(string $modelClass, string $modelName): void
    {
        $this->call('make:factory', [
            'name' => "{$modelName}Factory",
            '--model' => $modelClass,
        ]);
    }

    /**
     * Generate database seeder with factory usage
     */
    protected function generateSeeder(string $modelClass, string $relativePath, string $modelName): void
    {
        $seederName = $relativePath
            ? "$relativePath/{$modelName}Seeder"
            : "{$modelName}Seeder";

        $this->call('make:seeder', [
            'name' => $seederName,
        ]);

        $this->updateSeederContent($modelClass, $seederName);
    }

    /**
     * Add factory code to seeder
     */
    protected function updateSeederContent(string $modelClass, string $seederName): void
    {
        $seederPath = database_path('seeders/'.str_replace('/', '/', $seederName).'.php');

        if (!File::exists($seederPath)) return;

        $content = File::get($seederPath);
        $newContent = preg_replace(
            '/public function run\(\): void\s*\{[^}]*}/',
            "public function run(): void\n    {\n        $modelClass::factory()->count(10)->create();\n    }",
            $content
        );

        File::put($seederPath, $newContent);
    }

    /**
     * Generate authorization policy
     */
    protected function generatePolicy(string $modelClass, string $relativePath, string $modelName): void
    {
        $policyName = $relativePath
            ? "$relativePath/{$modelName}Policy"
            : "{$modelName}Policy";

        $this->call('make:policy', [
            'name' => $policyName,
            '--model' => $modelClass,
        ]);
    }

    /**
     * Generate form requests
     */
    protected function generateRequests(string $relativePath, string $modelName): void
    {
        $this->call('make:request', [
            'name' => $this->getRequestPath($relativePath, $modelName, 'Store'),
        ]);

        $this->call('make:request', [
            'name' => $this->getRequestPath($relativePath, $modelName, 'Update'),
        ]);
    }

    /**
     * Get fully qualified model class name
     */
    protected function getModelClass(SplFileInfo $file): string
    {
        $relativePath = $file->getRelativePath();
        $namespace = $relativePath
            ? 'App\\Models\\'.str_replace('/', '\\', $relativePath)
            : 'App\\Models';

        return "$namespace\\{$file->getBasename('.php')}";
    }

    /**
     * Verify class is a valid Eloquent model
     */
    protected function isValidModel(string $modelClass): bool
    {
        return class_exists($modelClass) && is_subclass_of($modelClass, Model::class);
    }

    /**
     * Generate request class path
     */
    protected function getRequestPath(?string $path, string $model, string $type): string
    {
        return $path
            ? "$path/$model{$type}Request"
            : "$model{$type}Request";
    }

    /**
     * Generate fully qualified request class name
     */
    protected function getRequestClass(?string $path, string $model, string $type): string
    {
        $namespace = $path ? '\\'.str_replace('/', '\\', $path) : '';
        return "App\\Http\\Requests$namespace\\$model{$type}Request";
    }
}
