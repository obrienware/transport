<?php
header('Content-Type: application/json');
require_once 'class.user.php';
echo json_encode(User::getUserSession($_REQUEST['username']));