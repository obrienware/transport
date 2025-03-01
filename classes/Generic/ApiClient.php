<?php

declare(strict_types=1);

namespace Generic;

class ApiClient
{
  private string $baseUrl;
  private array $defaultHeaders;

  public function __construct(string $baseUrl, array $defaultHeaders = [])
  {
    $this->baseUrl = rtrim($baseUrl, '/');
    $this->defaultHeaders = $defaultHeaders;
  }

  public function get(string $endpoint, array $queryParams = [], array $headers = []): string|false
  {
    $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
    if (!empty($queryParams)) {
      $url .= '?' . http_build_query($queryParams);
    }

    $context = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => $this->buildHeaders($headers),
      ]
    ]);

    return @file_get_contents($url, false, $context);
  }

  public function postJson(string $endpoint, array $data, array $headers = []): string|false
  {
    $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
    $body = json_encode($data);

    $headers = array_merge($headers, [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($body),
    ]);

    $context = stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => $this->buildHeaders($headers),
        'content' => $body,
      ]
    ]);

    return @file_get_contents($url, false, $context);
  }

  public function postMultipart(string $endpoint, array $data, array $headers = []): string|false
  {
    $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

    $boundary = bin2hex(random_bytes(16));
    $body = $this->buildMultipartBody($data, $boundary);

    $headers = array_merge($headers, [
      "Content-Type: multipart/form-data; boundary=$boundary",
      "Content-Length: " . strlen($body),
    ]);

    $context = stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => $this->buildHeaders($headers),
        'content' => $body,
      ]
    ]);

    return @file_get_contents($url, false, $context);
  }

  private function buildHeaders(array $headers): string
  {
    return implode("\r\n", array_merge($this->defaultHeaders, $headers)) . "\r\n";
  }

  private function buildMultipartBody(array $data, string $boundary): string
  {
    $body = "";
    foreach ($data as $key => $value) {
      if (is_array($value) && isset($value['file'], $value['filename'])) {
        $fileContent = file_get_contents($value['file']);
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"$key\"; filename=\"{$value['filename']}\"\r\n";
        $body .= "Content-Type: application/octet-stream\r\n\r\n";
        $body .= $fileContent . "\r\n";
      } else {
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
        $body .= $value . "\r\n";
      }
    }
    $body .= "--$boundary--\r\n";
    return $body;
  }
}

// Example usage:
// $api = new ApiClient('https://api.example.com', ['User-Agent: PHP ApiClient']);
// $response = $api->get('/users', ['id' => 123]);
// echo $response;

// $response = $api->postJson('/users', ['name' => 'John Doe', 'email' => 'john@example.com']);
// echo $response;

// $response = $api->postMultipart('/upload', ['file' => ['file' => 'path/to/file.jpg', 'filename' => 'file.jpg']]);
// echo $response;
