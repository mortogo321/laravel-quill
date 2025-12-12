<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuillDelta implements ValidationRule
{
    protected array $allowedFormats;
    protected bool $allowImages;
    protected bool $allowVideos;

    public function __construct(
        array $allowedFormats = [],
        bool $allowImages = true,
        bool $allowVideos = true
    ) {
        $this->allowedFormats = $allowedFormats;
        $this->allowImages = $allowImages;
        $this->allowVideos = $allowVideos;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('The :attribute must be valid JSON.');
            return;
        }

        if (!isset($decoded['ops']) || !is_array($decoded['ops'])) {
            $fail('The :attribute must be valid Quill Delta format.');
            return;
        }

        foreach ($decoded['ops'] as $index => $op) {
            if (!$this->validateOp($op, $fail, $index)) {
                return;
            }
        }
    }

    protected function validateOp(array $op, Closure $fail, int $index): bool
    {
        if (!isset($op['insert'])) {
            $fail("Invalid operation at index {$index}: missing 'insert' key.");
            return false;
        }

        $insert = $op['insert'];
        $attributes = $op['attributes'] ?? [];

        // Check for disallowed embeds
        if (is_array($insert)) {
            if (isset($insert['image']) && !$this->allowImages) {
                $fail('Images are not allowed in the :attribute field.');
                return false;
            }
            if (isset($insert['video']) && !$this->allowVideos) {
                $fail('Videos are not allowed in the :attribute field.');
                return false;
            }
        }

        // Check for disallowed formats
        if (!empty($this->allowedFormats) && !empty($attributes)) {
            foreach (array_keys($attributes) as $format) {
                if (!in_array($format, $this->allowedFormats)) {
                    $fail("Format '{$format}' is not allowed in the :attribute field.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create a rule that only allows specific formats.
     */
    public static function onlyFormats(array $formats): self
    {
        return new self(allowedFormats: $formats);
    }

    /**
     * Create a rule that disallows images.
     */
    public static function withoutImages(): self
    {
        return new self(allowImages: false);
    }

    /**
     * Create a rule that disallows videos.
     */
    public static function withoutVideos(): self
    {
        return new self(allowVideos: false);
    }

    /**
     * Create a rule that only allows plain text (no formatting or embeds).
     */
    public static function plainTextOnly(): self
    {
        return new self(
            allowedFormats: [],
            allowImages: false,
            allowVideos: false
        );
    }
}
