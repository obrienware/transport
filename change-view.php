<?php
require_once 'autoload.php';

use Transport\User;

$user = new User($_SESSION['user']->id);
if ($user->hasRole([$_GET['view'],'developer'])) {
  $_SESSION['view'] = $_GET['view'];
}
header('Location: /index.php');