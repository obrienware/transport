<?php
header('Content-Type: application/json');
require_once 'class.user.php';

$username = $_GET['username'];
$password = $_GET['password'];

require_once 'class.authenticate.php';

if ($id = Authenticate::logIn($username, $password)) {
  $_SESSION['user'] = (object) [
    'id' => $id,
    'username' => $username,
    'authenticated' => true
  ];
  $user = new User($id);
  $user->updateLastLogin();
  die(json_encode([
    'authenticated' => true,
    'changePassword' => $user->changePassword
  ]));
}

die(json_encode([
  'authenticated' => false
]));
