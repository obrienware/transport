<?php
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);
if ($user->hasRole([$_REQUEST['view'],'developer'])) {
  $_SESSION['view'] = $_REQUEST['view'];
}
header('Location: /index.php');