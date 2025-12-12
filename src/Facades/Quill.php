<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString styles()
 * @method static \Illuminate\Support\HtmlString scripts()
 * @method static string deltaToHtml(array|string $delta)
 * @method static array htmlToDelta(string $html)
 * @method static string sanitize(string $html)
 * @method static mixed getConfig(string $key = null, mixed $default = null)
 *
 * @see \Mortogo321\LaravelQuill\Quill
 */
class Quill extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'quill';
    }
}
