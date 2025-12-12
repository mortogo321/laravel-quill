<?php

declare(strict_types=1);

namespace Mortogo321\LaravelQuill\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QuillContent implements ValidationRule
{
    protected ?int $minLength;
    protected ?int $maxLength;
    protected bool $required;

    public function __construct(
        ?int $minLength = null,
        ?int $maxLength = null,
        bool $required = false
    ) {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->required = $required;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if value is empty
        if (empty($value) || $value === '{"ops":[{"insert":"\\n"}]}') {
            if ($this->required) {
                $fail('The :attribute field is required.');
            }
            return;
        }

        // Try to decode as JSON (Delta format)
        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('The :attribute must be valid Quill content.');
            return;
        }

        // Validate Delta structure
        if (!isset($decoded['ops']) || !is_array($decoded['ops'])) {
            $fail('The :attribute must be valid Quill Delta format.');
            return;
        }

        // Extract plain text for length validation
        $plainText = $this->extractPlainText($decoded['ops']);
        $length = mb_strlen($plainText);

        if ($this->minLength !== null && $length < $this->minLength) {
            $fail("The :attribute must be at least {$this->minLength} characters.");
            return;
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            $fail("The :attribute must not exceed {$this->maxLength} characters.");
            return;
        }
    }

    protected function extractPlainText(array $ops): string
    {
        $text = '';

        foreach ($ops as $op) {
            if (isset($op['insert'])) {
                if (is_string($op['insert'])) {
                    $text .= $op['insert'];
                } elseif (is_array($op['insert'])) {
                    // Handle embeds (image, video, etc.) - count as a single character
                    $text .= ' ';
                }
            }
        }

        // Trim and remove extra whitespace
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Create a new rule instance with minimum length.
     */
    public static function min(int $length): self
    {
        return new self(minLength: $length);
    }

    /**
     * Create a new rule instance with maximum length.
     */
    public static function max(int $length): self
    {
        return new self(maxLength: $length);
    }

    /**
     * Create a new rule instance with both minimum and maximum length.
     */
    public static function between(int $min, int $max): self
    {
        return new self(minLength: $min, maxLength: $max);
    }

    /**
     * Create a new rule instance that requires content.
     */
    public static function required(): self
    {
        return new self(required: true);
    }
}
