<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mortogo321\LaravelQuill\View\Components\Editor;
use Mortogo321\LaravelQuill\View\Components\Viewer;

class QuillServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/quill.php', 'quill');

        $this->app->singleton('quill', function ($app) {
            return new Quill($app['config']->get('quill'));
        });
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerComponents();
        $this->registerBladeDirectives();
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/quill.php' => config_path('quill.php'),
            ], 'quill-config');

            // Publish assets
            $this->publishes([
                __DIR__ . '/../resources/js' => public_path('vendor/quill/js'),
                __DIR__ . '/../resources/css' => public_path('vendor/quill/css'),
            ], 'quill-assets');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/quill'),
            ], 'quill-views');

            // Publish all
            $this->publishes([
                __DIR__ . '/../config/quill.php' => config_path('quill.php'),
                __DIR__ . '/../resources/js' => public_path('vendor/quill/js'),
                __DIR__ . '/../resources/css' => public_path('vendor/quill/css'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/quill'),
            ], 'quill');
        }
    }

    protected function registerRoutes(): void
    {
        if (config('quill.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'quill');
    }

    protected function registerComponents(): void
    {
        Blade::component('quill-editor', Editor::class);
        Blade::component('quill-viewer', Viewer::class);
    }

    protected function registerBladeDirectives(): void
    {
        Blade::directive('quillStyles', function () {
            return "<?php echo app('quill')->styles(); ?>";
        });

        Blade::directive('quillScripts', function () {
            return "<?php echo app('quill')->scripts(); ?>";
        });
    }
}
