<?php

namespace Modules\WhatsApp\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class WhatsAppServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'WhatsApp';

    protected string $nameLower = 'whatsapp';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->registerWhatsAppBindings();
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\WhatsApp\Console\Commands\WhatsAppHealthCheckCommand::class,
            \Modules\WhatsApp\Console\Commands\ConversationGarbageCollectCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('whatsapp:health-check')->everyTenMinutes();
            $schedule->command('whatsapp:gc-conversations')->daily();
        });
    }

    /**
     * Register AI-ready bindings.
     *
     * Conversation handlers are registered here in priority order.
     * To swap a structured flow for an AI-powered one, change the binding
     * in this method only — no other code changes required.
     */
    protected function registerWhatsAppBindings(): void
    {
        // Singleton services
        $this->app->singleton(\Modules\WhatsApp\Services\EvolutionApiClient::class);
        $this->app->singleton(\Modules\WhatsApp\Services\SlotFinderService::class);

        // Handler chain — order matters (first match wins)
        $this->app->singleton(
            \Modules\WhatsApp\Services\ConversationStateMachine::class,
            function ($app) {
                return new \Modules\WhatsApp\Services\ConversationStateMachine([
                    $app->make(\Modules\WhatsApp\Flows\ConfirmationFlow::class),  // 1. Pending confirmations
                    $app->make(\Modules\WhatsApp\Flows\RescheduleFlow::class),    // 2. Reschedule requests
                    $app->make(\Modules\WhatsApp\Flows\OnboardingFlow::class),    // 3. New patients
                    $app->make(\Modules\WhatsApp\Flows\BookingFlow::class),       // 4. Booking intent
                    $app->make(\Modules\WhatsApp\Flows\HelpFlow::class),          // 5. Fallback
                ]);
            }
        );
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
