# Laravel Quill

A Laravel package for seamless integration with **Quill 2.x** WYSIWYG editor.

[![Latest Stable Version](https://poser.pugx.org/mortogo321/laravel-quill/v/stable)](https://packagist.org/packages/mortogo321/laravel-quill)
[![Total Downloads](https://poser.pugx.org/mortogo321/laravel-quill/downloads)](https://packagist.org/packages/mortogo321/laravel-quill)
[![License](https://poser.pugx.org/mortogo321/laravel-quill/license)](https://packagist.org/packages/mortogo321/laravel-quill)
[![PHP Version Require](https://poser.pugx.org/mortogo321/laravel-quill/require/php)](https://packagist.org/packages/mortogo321/laravel-quill)

## Features

- Full support for Quill 2.x
- Blade components for easy integration
- Image upload handling with storage support
- Delta format conversion utilities
- Content sanitization
- Validation rules
- Configurable toolbar presets
- Dark mode support
- CDN or local assets

## Requirements

- PHP 8.2+
- Laravel 10.x, 11.x, or 12.x

## Installation

Install the package via Composer:

```bash
composer require mortogo321/laravel-quill
```

Publish the assets and configuration:

```bash
# Publish everything
php artisan vendor:publish --tag=quill

# Or publish individually
php artisan vendor:publish --tag=quill-config
php artisan vendor:publish --tag=quill-assets
php artisan vendor:publish --tag=quill-views
```

## Quick Start

### 1. Include Assets

Add the styles and scripts to your layout:

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- Include Quill styles -->
    @quillStyles
</head>
<body>
    <!-- Your content -->

    <!-- Include Quill scripts (before closing body tag) -->
    @quillScripts
</body>
</html>
```

### 2. Use the Editor Component

```blade
<form method="POST" action="/posts">
    @csrf

    <x-quill-editor
        name="content"
        :value="old('content', $post->content ?? '')"
        placeholder="Write your post content..."
    />

    <button type="submit">Save</button>
</form>
```

### 3. Display Content

```blade
<x-quill-viewer :content="$post->content" />
```

## Configuration

The configuration file is located at `config/quill.php`. Key options include:

### Theme

```php
'theme' => 'snow', // or 'bubble'
```

### CDN Settings

```php
'cdn' => [
    'enabled' => true,
    'version' => '2.0.2',
],
```

### Toolbar Presets

Three built-in presets: `minimal`, `basic`, `full`

```blade
<x-quill-editor name="content" toolbar="minimal" />
<x-quill-editor name="content" toolbar="basic" />
<x-quill-editor name="content" toolbar="full" />
```

### Custom Toolbar

```blade
<x-quill-editor
    name="content"
    :toolbar="['bold', 'italic', 'link']"
/>
```

### Image Uploads

Configure upload settings in `config/quill.php`:

```php
'uploads' => [
    'enabled' => true,
    'disk' => 'public',
    'path' => 'quill-uploads',
    'max_size' => 2048, // KB
    'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
],
```

## Editor Component

### Available Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Form input name |
| `id` | string | auto | Editor element ID |
| `value` | string | null | Initial content (Delta JSON or HTML) |
| `placeholder` | string | config | Placeholder text |
| `theme` | string | 'snow' | Quill theme ('snow' or 'bubble') |
| `toolbar` | string/array | config | Toolbar preset or custom config |
| `read-only` | bool | false | Read-only mode |
| `height` | string | '300px' | Editor height |
| `required` | bool | false | HTML5 required attribute |
| `upload-url` | string | auto | Custom upload endpoint |
| `formats` | array | [] | Allowed formats |
| `modules` | array | [] | Additional Quill modules |
| `debug` | bool | false | Enable debug mode |

### Examples

**Basic Editor:**

```blade
<x-quill-editor name="content" />
```

**With Initial Content:**

```blade
<x-quill-editor name="content" :value="$post->content" />
```

**Read-Only:**

```blade
<x-quill-editor name="content" :value="$content" read-only />
```

**Custom Height:**

```blade
<x-quill-editor name="content" height="500px" />
```

**Bubble Theme:**

```blade
<x-quill-editor name="content" theme="bubble" />
```

## Viewer Component

### Available Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `content` | string | null | Content (Delta JSON or HTML) |
| `delta` | array | null | Delta object directly |
| `sanitize` | bool | true | Sanitize HTML output |
| `class` | string | null | Additional CSS classes |

### Examples

**Display Delta Content:**

```blade
<x-quill-viewer :content="$post->content" />
```

**Display with Custom Class:**

```blade
<x-quill-viewer :content="$post->content" class="prose lg:prose-xl" />
```

**Without Sanitization:**

```blade
<x-quill-viewer :content="$post->content" :sanitize="false" />
```

## Validation Rules

### QuillContent

Validate Quill content with length constraints:

```php
use Mortogo321\LaravelQuill\Rules\QuillContent;

$request->validate([
    'content' => ['required', new QuillContent(minLength: 10, maxLength: 5000)],
]);

// Using static methods
$request->validate([
    'content' => [QuillContent::required()],
    'summary' => [QuillContent::max(500)],
    'bio' => [QuillContent::between(50, 1000)],
]);
```

### QuillDelta

Validate Delta structure and allowed formats:

```php
use Mortogo321\LaravelQuill\Rules\QuillDelta;

$request->validate([
    'content' => [new QuillDelta()],
]);

// Restrict formats
$request->validate([
    'content' => [QuillDelta::onlyFormats(['bold', 'italic', 'link'])],
]);

// Disallow media
$request->validate([
    'content' => [QuillDelta::withoutImages()],
    'comment' => [QuillDelta::plainTextOnly()],
]);
```

## Facade Methods

The `Quill` facade provides utility methods:

```php
use Mortogo321\LaravelQuill\Facades\Quill;

// Convert Delta to HTML
$html = Quill::deltaToHtml($deltaJson);

// Convert HTML to Delta
$delta = Quill::htmlToDelta($html);

// Sanitize HTML
$clean = Quill::sanitize($html);

// Get configuration
$config = Quill::getConfig('theme');
```

## JavaScript API

The `LaravelQuill` object is available globally:

```javascript
// Initialize all editors
LaravelQuill.init();

// Get an editor instance
const quill = LaravelQuill.getEditor('quill-content');

// Get content
const delta = LaravelQuill.getContents('quill-content');
const html = LaravelQuill.getHTML('quill-content');

// Set content
LaravelQuill.setContents('quill-content', delta);
LaravelQuill.setContents('quill-content', '<p>HTML content</p>');

// Enable/disable
LaravelQuill.setEnabled('quill-content', false);

// Focus/blur
LaravelQuill.focus('quill-content');
LaravelQuill.blur('quill-content');
```

### Events

```javascript
document.getElementById('quill-content').addEventListener('quill-init', (e) => {
    console.log('Editor initialized', e.detail.quill);
});

document.getElementById('quill-content').addEventListener('quill-change', (e) => {
    console.log('Content changed', e.detail.delta, e.detail.html);
});
```

## Customization

### Custom Upload Handler

Override the upload endpoint:

```blade
<x-quill-editor
    name="content"
    upload-url="{{ route('custom.upload') }}"
/>
```

### Custom Styling

Publish the CSS and modify `public/vendor/quill/css/laravel-quill.css`, or use CSS variables:

```css
.quill-editor-wrapper {
    --quill-editor-height: 400px;
    --quill-border-color: #e2e8f0;
    --quill-border-radius: 8px;
    --quill-focus-color: #3b82f6;
    --quill-toolbar-bg: #f8fafc;
}
```

## Environment Variables

```env
QUILL_THEME=snow
QUILL_CDN_ENABLED=true
QUILL_VERSION=2.0.2
QUILL_UPLOAD_DISK=public
QUILL_UPLOAD_PATH=quill-uploads
QUILL_MAX_UPLOAD_SIZE=2048
```

## Security

The package includes built-in content sanitization. Configure allowed tags and attributes in `config/quill.php`:

```php
'sanitize' => [
    'enabled' => true,
    'allowed_tags' => ['p', 'br', 'strong', 'em', ...],
    'allowed_attributes' => [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'class'],
        // ...
    ],
],
```

## License

MIT License. See [LICENSE](LICENSE) for more information.

## Credits

- [Mor](https://github.com/mortogo321)
- [Quill](https://quilljs.com/)
