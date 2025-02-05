<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User };
use Generic\{ JsonInput, Utils };

$input = new JsonInput();

$sessionUser = new User($_SESSION['user']->id);

$user = new User($input->getInt('id'));
$user->username = $input->getString('username');
$user->firstName = $input->getString('firstName');
$user->lastName = $input->getString('lastName');
$user->emailAddress = $input->getString('emailAddress');
$phoneNumber = $input->getString('phoneNumber');
if ($phoneNumber)
{
  $user->phoneNumber = Utils::formattedPhoneNumber($phoneNumber);
}
$roles = $input->getString('roles');
$user->roles = $roles ? explode(',', $roles) : [];

$user->position = $input->getString('position');
$user->departmentId = $input->getInt('departmentId');
$user->CDL = $input->getBool('cdl');

if ($user->save(userResponsibleForOperation: $sessionUser->getUsername()))
{
  if ($input->getBool('resetPassword')) $user->resetPassword();
  exit(json_encode(['result' => $user->getId()]));
}
exit(json_encode(['result' => false, 'error' => $user->getLastError()]));
