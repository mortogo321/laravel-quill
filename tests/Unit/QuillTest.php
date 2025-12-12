<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Tests\Unit;

use Mortogo321\LaravelQuill\Quill;
use Mortogo321\LaravelQuill\Tests\TestCase;

class QuillTest extends TestCase
{
    protected Quill $quill;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quill = new Quill(config('quill'));
    }

    public function test_can_convert_delta_to_html(): void
    {
        $delta = [
            'ops' => [
                ['insert' => 'Hello '],
                ['insert' => 'World', 'attributes' => ['bold' => true]],
                ['insert' => "\n"],
            ]
        ];

        $html = $this->quill->deltaToHtml($delta);

        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('<strong>World</strong>', $html);
    }

    public function test_can_convert_delta_json_to_html(): void
    {
        $deltaJson = '{"ops":[{"insert":"Test content\\n"}]}';

        $html = $this->quill->deltaToHtml($deltaJson);

        $this->assertStringContainsString('Test content', $html);
    }

    public function test_can_convert_delta_with_link(): void
    {
        $delta = [
            'ops' => [
                ['insert' => 'Click here', 'attributes' => ['link' => 'https://example.com']],
                ['insert' => "\n"],
            ]
        ];

        $html = $this->quill->deltaToHtml($delta);

        $this->assertStringContainsString('href="https://example.com"', $html);
        $this->assertStringContainsString('Click here', $html);
    }

    public function test_can_convert_delta_with_image(): void
    {
        $delta = [
            'ops' => [
                ['insert' => ['image' => 'https://example.com/image.jpg']],
                ['insert' => "\n"],
            ]
        ];

        $html = $this->quill->deltaToHtml($delta);

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('src="https://example.com/image.jpg"', $html);
    }

    public function test_sanitize_removes_disallowed_tags(): void
    {
        $html = '<p>Hello</p><script>alert("xss")</script><b>World</b>';

        $sanitized = $this->quill->sanitize($html);

        $this->assertStringContainsString('<p>Hello</p>', $sanitized);
        $this->assertStringContainsString('<b>World</b>', $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function test_returns_empty_string_for_invalid_delta(): void
    {
        $html = $this->quill->deltaToHtml(['invalid' => 'data']);

        $this->assertEmpty($html);
    }

    public function test_styles_method_returns_html_string(): void
    {
        $styles = $this->quill->styles();

        $this->assertStringContainsString('quill', (string) $styles);
        $this->assertStringContainsString('stylesheet', (string) $styles);
    }

    public function test_scripts_method_returns_html_string(): void
    {
        $scripts = $this->quill->scripts();

        $this->assertStringContainsString('quill', (string) $scripts);
        $this->assertStringContainsString('script', (string) $scripts);
    }

    public function test_can_get_config(): void
    {
        $theme = $this->quill->getConfig('theme');

        $this->assertEquals('snow', $theme);
    }

    public function test_can_get_full_config(): void
    {
        $config = $this->quill->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('theme', $config);
    }
}
