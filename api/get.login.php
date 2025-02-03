<?php
declare(strict_types=1);

header('Content-Type: application/json');

$timezone = filter_input(INPUT_GET, 'timezone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($timezone) $_SESSION['userTimezone'] = $timezone;

require_once '../autoload.php';

use Transport\Authenticate;
use Transport\User;

$username = $_GET['username'];
$password = $_GET['password'];

if ($id = Authenticate::logIn($username, $password)) {
  $_SESSION['user'] = (object) [
    'id' => $id,
    'username' => $username,
    'authenticated' => true
  ];
  $user = new User($id);

  die(json_encode([
    'authenticated' => true,
    'changePassword' => $user->changePassword
  ]));
}

die(json_encode([
  'authenticated' => false
]));
