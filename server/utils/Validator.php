<?php
/**
 * Input Validation & Sanitization Utility
 *
 * Provides static methods for common validation tasks
 * used across the application.
 */

class Validator
{
    /**
     * Sanitize a string value (trim + strip tags).
     *
     * @param string $value Raw input string
     * @return string Sanitized string
     */
    public static function sanitizeString(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate an email address.
     *
     * @param string $email Email to validate
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if a string meets minimum length requirement.
     *
     * @param string $value  Input string
     * @param int    $minLen Minimum character length
     * @return bool
     */
    public static function minLength(string $value, int $minLen): bool
    {
        return mb_strlen($value, 'UTF-8') >= $minLen;
    }

    /**
     * Check if a string meets maximum length requirement.
     *
     * @param string $value  Input string
     * @param int    $maxLen Maximum character length
     * @return bool
     */
    public static function maxLength(string $value, int $maxLen): bool
    {
        return mb_strlen($value, 'UTF-8') <= $maxLen;
    }

    /**
     * Check if a value is not empty after trimming.
     *
     * @param mixed $value Input value
     * @return bool
     */
    public static function isNotEmpty(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return !empty($value);
    }

    /**
     * Validate that a value is within an allowed set.
     *
     * @param string $value   Value to check
     * @param array  $allowed Array of allowed values
     * @return bool
     */
    public static function isInList(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    /**
     * Check if a value is a positive integer.
     *
     * @param mixed $value Value to check
     * @return bool
     */
    public static function isPositiveInt(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    }

    /**
     * Validate an uploaded file (image).
     *
     * @param array $file        $_FILES element
     * @param int   $maxSizeMB   Maximum file size in megabytes
     * @param array $allowedTypes Allowed MIME types
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public static function validateImageUpload(
        array $file,
        int   $maxSizeMB = 5,
        array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    ): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'File upload error. Code: ' . $file['error']];
        }

        if ($file['size'] > ($maxSizeMB * 1024 * 1024)) {
            return ['valid' => false, 'error' => "File size exceeds {$maxSizeMB}MB limit."];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes, true)) {
            return ['valid' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
        }

        return ['valid' => true, 'error' => null];
    }
}
