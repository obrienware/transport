<?php

declare(strict_types=1);

namespace Generic;

class InputHandler
{
  public static function getInt($type, $key)
  {
    $value = filter_input($type, $key, FILTER_VALIDATE_INT);
    return $value === false ? null : $value;
  }

  public static function getString($type, $key)
  {
    $value = filter_input($type, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    return is_string($value) ? trim($value) : null;
  }

  public static function getBool($type, $key)
  {
    return filter_input($type, $key, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }

  public static function getFloat($type, $key)
  {
    $value = filter_input($type, $key, FILTER_VALIDATE_FLOAT);
    return $value === false ? null : $value;
  }

  public static function getArray($type, $key)
  {
    $values = filter_input($type, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    return is_array($values) ? array_map('trim', $values) : [];
  }

  /**
   * Handle uploaded files safely.
   * @param string $key The name of the file input field.
   * @param array $allowedTypes Allowed MIME types (e.g., ['image/jpeg', 'image/png']).
   * @param int $maxSize Max file size in bytes (e.g., 2MB = 2 * 1024 * 1024).
   * @return array|null File details if valid, or NULL if invalid/missing.
   */
  public static function getFile(string $key, array $allowedTypes = [], int $maxSize = 2_000_000): ?array
  {
    if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK)
    {
      return null; // No file uploaded or an error occurred
    }

    $file = $_FILES[$key];

    // Validate MIME type
    if (!empty($allowedTypes) && !in_array(mime_content_type($file['tmp_name']), $allowedTypes, true))
    {
      return null;
    }

    // Validate file size
    if ($file['size'] > $maxSize)
    {
      return null;
    }

    return $file; // Return file details (name, type, tmp_name, error, size)
  }

  /**
   * Move uploaded file to a secure location.
   * @param string $key The file input name.
   * @param string $destination The target directory.
   * @return string|null The new file path if moved successfully, or NULL on failure.
   */
  public static function saveFile(string $key, string $destination): ?string
  {
    $file = self::getFile($key);
    if (!$file) return null;

    // Ensure the destination directory exists
    if (!is_dir($destination))
    {
      mkdir($destination, 0777, true);
    }

    // Extract the file extension safely
    $fileInfo = pathinfo($file['name']);
    $extension = isset($fileInfo['extension']) ? strtolower($fileInfo['extension']) : '';

    // Validate extension (ensure it's not empty or unexpected)
    if ($extension === '' || !preg_match('/^[a-z0-9]+$/', $extension))
    {
      return null; // Prevent saving files without a valid extension
    }

    // Generate a unique filename with the correct extension
    $newFileName = uniqid() . '.' . $extension;
    $filePath = rtrim($destination, '/') . '/' . $newFileName;

    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath))
    {
      return $newFileName;
    }

    return null;
  }
}
