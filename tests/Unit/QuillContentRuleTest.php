<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Tests\Unit;

use Mortogo321\LaravelQuill\Rules\QuillContent;
use Mortogo321\LaravelQuill\Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class QuillContentRuleTest extends TestCase
{
    public function test_validates_valid_quill_content(): void
    {
        $delta = json_encode([
            'ops' => [
                ['insert' => "Hello World\n"]
            ]
        ]);

        $validator = Validator::make(
            ['content' => $delta],
            ['content' => [new QuillContent()]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_fails_on_invalid_json(): void
    {
        $validator = Validator::make(
            ['content' => 'invalid json'],
            ['content' => [new QuillContent()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_fails_on_missing_ops(): void
    {
        $validator = Validator::make(
            ['content' => json_encode(['invalid' => 'structure'])],
            ['content' => [new QuillContent()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validates_minimum_length(): void
    {
        $delta = json_encode([
            'ops' => [
                ['insert' => "Hi\n"]
            ]
        ]);

        $validator = Validator::make(
            ['content' => $delta],
            ['content' => [QuillContent::min(10)]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validates_maximum_length(): void
    {
        $delta = json_encode([
            'ops' => [
                ['insert' => str_repeat('a', 100) . "\n"]
            ]
        ]);

        $validator = Validator::make(
            ['content' => $delta],
            ['content' => [QuillContent::max(50)]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validates_required(): void
    {
        $emptyDelta = json_encode([
            'ops' => [
                ['insert' => "\n"]
            ]
        ]);

        $validator = Validator::make(
            ['content' => $emptyDelta],
            ['content' => [QuillContent::required()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validates_between_length(): void
    {
        $delta = json_encode([
            'ops' => [
                ['insert' => "Hello World\n"]
            ]
        ]);

        $validator = Validator::make(
            ['content' => $delta],
            ['content' => [QuillContent::between(5, 20)]]
        );

        $this->assertFalse($validator->fails());
    }
}
