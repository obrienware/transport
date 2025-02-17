<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Guest, User };
use Generic\{ JsonInput, Utils };

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$phoneNumber = Utils::formattedPhoneNumber($input->getString('phoneNumber'));

$guest = new Guest($input->getInt('id'));
if (!$input->getInt('id'))
{
  $guest->getGuestByPhoneNumber($phoneNumber);
}
$guest->firstName = $input->getString('firstName');
$guest->lastName = $input->getString('lastName');
$guest->emailAddress = $input->getString('emailAddress');
$guest->phoneNumber = $phoneNumber;
$guest->type = $input->getString('type');

if ($guest->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $guest->getId()]));
}
exit(json_encode(['result' => false, 'error' => $guest->getLastError()]));
