<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill;

use Illuminate\Support\HtmlString;

class Quill
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function styles(): HtmlString
    {
        $theme = $this->config['theme'] ?? 'snow';
        $version = $this->config['cdn']['version'] ?? '2.0.2';
        $useCdn = $this->config['cdn']['enabled'] ?? true;

        $styles = [];

        if ($useCdn) {
            // Quill core styles from CDN
            $styles[] = sprintf(
                '<link href="https://cdn.jsdelivr.net/npm/quill@%s/dist/quill.%s.css" rel="stylesheet">',
                $version,
                $theme
            );
        } else {
            // Local styles
            $styles[] = sprintf(
                '<link href="%s" rel="stylesheet">',
                asset('vendor/quill/css/quill.css')
            );
        }

        // Custom styles
        $styles[] = sprintf(
            '<link href="%s" rel="stylesheet">',
            asset('vendor/quill/css/laravel-quill.css')
        );

        return new HtmlString(implode("\n", $styles));
    }

    public function scripts(): HtmlString
    {
        $version = $this->config['cdn']['version'] ?? '2.0.2';
        $useCdn = $this->config['cdn']['enabled'] ?? true;

        $scripts = [];

        if ($useCdn) {
            // Quill from CDN
            $scripts[] = sprintf(
                '<script src="https://cdn.jsdelivr.net/npm/quill@%s/dist/quill.js"></script>',
                $version
            );
        } else {
            // Local Quill
            $scripts[] = '<script src="' . asset('vendor/quill/js/quill.min.js') . '"></script>';
        }

        // Laravel Quill integration script
        $scripts[] = '<script src="' . asset('vendor/quill/js/laravel-quill.js') . '"></script>';

        return new HtmlString(implode("\n", $scripts));
    }

    public function deltaToHtml(array|string $delta): string
    {
        if (is_string($delta)) {
            $delta = json_decode($delta, true);
        }

        if (!isset($delta['ops'])) {
            return '';
        }

        return $this->renderOps($delta['ops']);
    }

    public function htmlToDelta(string $html): array
    {
        // Basic HTML to Delta conversion
        // For complex conversions, consider using a JavaScript-based solution
        return [
            'ops' => [
                ['insert' => strip_tags($html) . "\n"]
            ]
        ];
    }

    protected function renderOps(array $ops): string
    {
        $html = '';

        foreach ($ops as $op) {
            if (!isset($op['insert'])) {
                continue;
            }

            $insert = $op['insert'];
            $attributes = $op['attributes'] ?? [];

            if (is_array($insert)) {
                // Handle embeds (images, videos, etc.)
                $html .= $this->renderEmbed($insert, $attributes);
            } else {
                // Handle text
                $html .= $this->renderText($insert, $attributes);
            }
        }

        return $html;
    }

    protected function renderText(string $text, array $attributes): string
    {
        $html = e($text);

        // Apply inline formats
        if (isset($attributes['bold'])) {
            $html = "<strong>{$html}</strong>";
        }
        if (isset($attributes['italic'])) {
            $html = "<em>{$html}</em>";
        }
        if (isset($attributes['underline'])) {
            $html = "<u>{$html}</u>";
        }
        if (isset($attributes['strike'])) {
            $html = "<s>{$html}</s>";
        }
        if (isset($attributes['code'])) {
            $html = "<code>{$html}</code>";
        }
        if (isset($attributes['link'])) {
            $html = sprintf('<a href="%s">%s</a>', e($attributes['link']), $html);
        }
        if (isset($attributes['script'])) {
            $tag = $attributes['script'] === 'super' ? 'sup' : 'sub';
            $html = "<{$tag}>{$html}</{$tag}>";
        }

        // Handle block formats (headers, lists, etc.)
        if (isset($attributes['header'])) {
            $level = $attributes['header'];
            $html = "<h{$level}>{$html}</h{$level}>";
        }
        if (isset($attributes['blockquote'])) {
            $html = "<blockquote>{$html}</blockquote>";
        }
        if (isset($attributes['code-block'])) {
            $html = "<pre><code>{$html}</code></pre>";
        }
        if (isset($attributes['list'])) {
            $tag = $attributes['list'] === 'ordered' ? 'ol' : 'ul';
            $html = "<{$tag}><li>{$html}</li></{$tag}>";
        }

        // Convert newlines to <br> or <p>
        $html = str_replace("\n", "<br>", $html);

        return $html;
    }

    protected function renderEmbed(array $embed, array $attributes): string
    {
        if (isset($embed['image'])) {
            $src = e($embed['image']);
            $alt = $attributes['alt'] ?? '';
            $width = isset($attributes['width']) ? ' width="' . e($attributes['width']) . '"' : '';
            $height = isset($attributes['height']) ? ' height="' . e($attributes['height']) . '"' : '';
            return sprintf('<img src="%s" alt="%s"%s%s>', $src, e($alt), $width, $height);
        }

        if (isset($embed['video'])) {
            $src = e($embed['video']);
            return sprintf(
                '<iframe class="ql-video" src="%s" frameborder="0" allowfullscreen></iframe>',
                $src
            );
        }

        if (isset($embed['formula'])) {
            return sprintf('<span class="ql-formula">%s</span>', e($embed['formula']));
        }

        return '';
    }

    public function sanitize(string $html): string
    {
        $allowed = $this->config['sanitize']['allowed_tags'] ?? [
            'p', 'br', 'strong', 'em', 'u', 's', 'a', 'img', 'video', 'iframe',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre', 'code',
            'ul', 'ol', 'li', 'span', 'sub', 'sup'
        ];

        return strip_tags($html, array_map(fn($tag) => "<{$tag}>", $allowed));
    }

    public function getConfig(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key, $default);
    }
}
