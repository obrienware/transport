<?php
declare(strict_types=1);

header('Content-Type: application/json');

$timezone = filter_input(INPUT_GET, 'timezone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($timezone) $_SESSION['userTimezone'] = $timezone;

require_once '../autoload.php';

use Transport\{ Authenticate, User };
use Generic\Logger;
Logger::logRequest();

$username = $_GET['username'];
$password = $_GET['password'];

if ($id = Authenticate::logIn($username, $password)) {
  $_SESSION['user'] = (object) [
    'id' => $id,
    'username' => $username,
    'authenticated' => true
  ];
  $user = new User($id);

  exit(json_encode([
    'authenticated' => true,
    'changePassword' => $user->changePassword
  ]));
}

exit(json_encode([
  'authenticated' => false
]));
