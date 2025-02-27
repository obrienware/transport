<?php

declare(strict_types=1);

namespace Generic;

use Transport\Database;

class Logger
{
  public static function logRequest(): void
  {
    $db = Database::getInstance();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $endpoint = $_SERVER['REQUEST_URI']; // Get the script path being called
    $postData = file_get_contents('php://input'); // Get the raw POST data
    $sessionData = json_encode($_SESSION); // Store session variables
    $ipAddress = $_SERVER['HTTP_X_REAL_IP'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $query = "
      INSERT INTO api_logs SET
        request_method = :request_method,
        endpoint = :endpoint,
        post_data = :post_data,
        session_data = :session_data,
        ip_address = :ip_address,
        user_agent = :user_agent
    ";
    $params = [
      'request_method' => $requestMethod,
      'endpoint' => $endpoint,
      'post_data' => $postData,
      'session_data' => $sessionData,
      'ip_address' => $ipAddress,
      'user_agent' => $userAgent
    ];
    $db->query($query, $params);
  }
}