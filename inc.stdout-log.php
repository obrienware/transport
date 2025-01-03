<?php
// This is a way of sending debug information to the server's stdout (log file) without sending it to the client.
$out = fopen('php://stdout', 'w'); //output handler
$output = json_encode((object)[
  'uri' => $_SERVER['REQUEST_URI'],
  'user' => $_SESSION['user']->username
]);
fputs($out, "{$output}\n"); //writing output operation
fclose($out); //closing handler
