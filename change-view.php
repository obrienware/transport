<?php
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);
if ($user->hasRole([$_REQUEST['view']])) {
  $_SESSION['view'] = $_REQUEST['view'];
}
header('Location: /index.php');