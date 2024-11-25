<?php
$out = fopen('php://stdout', 'w'); //output handler
$output = json_encode((object)[
  'uri' => $_SERVER['REQUEST_URI'],
  'user' => $_SESSION['user']->username
]);
fputs($out, "{$output}\n"); //writing output operation
fclose($out); //closing handler
