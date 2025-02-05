<?php

declare(strict_types=1);

namespace Generic;

class JsonInput
{
  private object $data;

  public function __construct()
  {
    $json = file_get_contents("php://input");
    $decoded = json_decode($json);

    // Handle invalid JSON input
    if (json_last_error() !== JSON_ERROR_NONE)
    {
      http_response_code(400);
      echo json_encode(["error" => "Invalid JSON input"]);
      exit;
    }

    // Store JSON data as an object (stdClass)
    $this->data = is_object($decoded) ? $decoded : new \stdClass();
  }

  /**
   * Get an integer value from JSON input.
   */
  public function getInt(string $key, ?int $default = null): ?int
  {
    return isset($this->data->$key) && is_numeric($this->data->$key)
      ? (int) $this->data->$key
      : $default;
  }

  /**
   * Get a sanitized string from JSON input.
   */
  public function getString(string $key, ?string $default = null): ?string
  {
    return isset($this->data->$key) && is_string($this->data->$key)
      ? trim(filter_var($this->data->$key, FILTER_SANITIZE_FULL_SPECIAL_CHARS))
      : $default;
  }

  /**
   * Get a raw string without sanitization (useful for passwords or special characters).
   */
  public function getRawString(string $key, ?string $default = null): ?string
  {
    return isset($this->data->$key) && is_string($this->data->$key)
      ? trim($this->data->$key)
      : $default;
  }

  /**
   * Get a float (decimal) value from JSON input.
   */
  public function getFloat(string $key, ?float $default = null): ?float
  {
    return isset($this->data->$key) && is_numeric($this->data->$key)
      ? (float) $this->data->$key
      : $default;
  }

  /**
   * Get a boolean value from JSON input.
   */
  public function getBool(string $key, ?bool $default = null): ?bool
  {
    return isset($this->data->$key)
      ? filter_var($this->data->$key, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
      : $default;
  }

  /**
   * Get an array from JSON input.
   */
  public function getArray(string $key, array $default = []): array
  {
    return isset($this->data->$key) && is_array($this->data->$key)
      ? (array) $this->data->$key
      : $default;
  }

  /**
   * Get a nested JSON object from input.
   */
  public function getObject(string $key): ?object
  {
    return isset($this->data->$key) && is_object($this->data->$key)
      ? $this->data->$key
      : null;
  }
}
