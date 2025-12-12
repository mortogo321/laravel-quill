<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Tests;

use Mortogo321\LaravelQuill\QuillServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            QuillServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Quill' => \Mortogo321\LaravelQuill\Facades\Quill::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('quill.theme', 'snow');
        $app['config']->set('quill.cdn.enabled', true);
        $app['config']->set('quill.cdn.version', '2.0.2');
    }
}
