<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Mortogo321\LaravelQuill\Facades\Quill;

class Viewer extends Component
{
    public string $html;

    public function __construct(
        public ?string $content = null,
        public ?array $delta = null,
        public bool $sanitize = true,
        public ?string $class = null,
    ) {
        // Convert delta to HTML if provided
        if ($this->delta !== null) {
            $this->html = Quill::deltaToHtml($this->delta);
        } elseif ($this->content !== null) {
            // Check if content is JSON (delta format)
            $decoded = json_decode($this->content, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['ops'])) {
                $this->html = Quill::deltaToHtml($decoded);
            } else {
                $this->html = $this->content;
            }
        } else {
            $this->html = '';
        }

        // Sanitize if enabled
        if ($this->sanitize && config('quill.sanitize.enabled', true)) {
            $this->html = Quill::sanitize($this->html);
        }
    }

    public function render(): View
    {
        return view('quill::components.viewer');
    }
}
