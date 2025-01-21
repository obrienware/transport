<?php
header('Content-Type: application/json');

require_once '../../autoload.php';

use Transport\User;

echo json_encode(User::getUserSession($_GET['username']));