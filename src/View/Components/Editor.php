<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Editor extends Component
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public ?string $value = null,
        public ?string $placeholder = null,
        public string $theme = 'snow',
        public string|array|null $toolbar = null,
        public bool $readOnly = false,
        public ?string $height = null,
        public bool $required = false,
        public ?string $uploadUrl = null,
        public array $formats = [],
        public array $modules = [],
        public bool $debug = false,
    ) {
        $this->id = $id ?? 'quill-' . $name;
        $this->placeholder = $placeholder ?? config('quill.placeholder', 'Write something...');
        $this->theme = $theme ?: config('quill.theme', 'snow');

        // Handle toolbar presets
        if (is_string($this->toolbar) && config("quill.toolbars.{$this->toolbar}")) {
            $this->toolbar = config("quill.toolbars.{$this->toolbar}");
        } elseif ($this->toolbar === null) {
            $this->toolbar = config('quill.toolbar');
        }

        // Set upload URL for image handling
        if ($this->uploadUrl === null && config('quill.uploads.enabled')) {
            $this->uploadUrl = route('quill.upload');
        }
    }

    public function render(): View
    {
        return view('quill::components.editor');
    }

    public function getConfig(): array
    {
        $config = [
            'theme' => $this->theme,
            'placeholder' => $this->placeholder,
            'readOnly' => $this->readOnly,
            'debug' => $this->debug ? 'info' : false,
        ];

        // Add toolbar
        if ($this->toolbar !== false) {
            $config['modules']['toolbar'] = $this->toolbar;
        }

        // Merge additional modules
        if (!empty($this->modules)) {
            $config['modules'] = array_merge($config['modules'] ?? [], $this->modules);
        }

        // Add formats if specified
        if (!empty($this->formats)) {
            $config['formats'] = $this->formats;
        }

        return $config;
    }

    public function getConfigJson(): string
    {
        return json_encode($this->getConfig());
    }
}
