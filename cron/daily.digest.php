<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.config.php';
require_once 'class.data.php';
require_once 'class.email.php';
require_once 'class.email-templates.php';
require_once 'class.event.php';
require_once 'class.template.php';
require_once 'class.trip.php';
require_once 'class.user.php';
require_once 'class.utils.php';

$config = Config::get('organization');

/**
 * We want to send an email digest (a summary of the items that need attention) for the manager, as well as
 * we need to send an email digest to all drivers.
 */

// Let's do the drivers first...
$drivers = User::getDrivers();
foreach ($drivers as $driver) {
  if (!$driver->personal_preferences) continue;
  $prefs = json_decode($driver->personal_preferences);
  if (!$prefs->dailyDigestEmails) continue;

  $content = [];
  $trips = getTripsFor($driver->id);
  $events = getEventsFor($driver->id);

  if (count($trips) === 0 && count($events) === 0) {
    $content[] = "You are not assigned to any trips or events scheduled in the next 24hrs!";
    $content[] = '';
  } else {
    if ($trips) $content += getTextForUpcomingTrip($trips);
    if ($events) $content += getTextForUpcomingEvent($events);
  }
  $template = new Template(EmailTemplates::get('Email Basic'));
  $templateVariables = [
    'content' => implode("\n", $content),
    'name' => $driver->first_name
  ];

  $email = new Email();
  $email->addRecipient($driver->email_address, $driver->first_name.' '.$driver->last_name);
  $email->addReplyTo($config->email->defaultReplyTo);
  $email->setSubject('Your Driver Digest for today');
  $email->setContent($template->render($templateVariables));
  if ($trips) {
    $attachments = getAttachmentsForUpcomingTrip($trips);
    foreach ($attachments as $attachment) {
      $email->addAttachment($attachment);
    }
  }
  $email->sendText();
  if ($attachments) {
    foreach ($attachments as $filename) {
      unlink($filename);
    }
  }
}


// Now let's do the managers...
// Things to check for:
// - Unconfirmed trips
// - Unconfirmed events
$content = [];
if ($unconfirmedTrips = getUnconfirmedTrips()) {
  $content += getUnconfirmedTripsText($unconfirmedTrips);
}
if ($unconfirmedEvents = getUnconfirmedEvents()) {
  $content += getUnconfirmedEventsText($unconfirmedEvents);
}
if (count($content) === 0) {
  $content[] = "There are no unconfirmed trips or events scheduled in the next 7 days!";
}

$managers = User::getManagers();
foreach ($managers as $manager) {
  $template = new Template(EmailTemplates::get('Email Basic'));
  $templateVariables = [
    'content' => implode("\n", $content),
    'name' => $manager->first_name
  ];

  $email = new Email();
  $email->addRecipient($manager->email_address, $manager->first_name.' '.$manager->last_name);
  $email->addReplyTo($config->email->defaultReplyTo);
  $email->setSubject('Your Manager Digest for today');
  $email->setContent($template->render($templateVariables));
  $email->sendText();
}


function getTripsFor(int $driverId): array
{
  $db = data::getInstance();
  $query = "
    SELECT * FROM trips 
    WHERE 
      start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR) 
      AND driver_id = :driver_id
      AND archived IS NULL
    ORDER BY start_date
    LIMIT 5
  ";
  $params = ['driver_id' => $driverId];
  return $db->get_rows($query, $params);
}

function getEventsFor(int $driverId): array
{
  $db = data::getInstance();
  $query = "
    SELECT * FROM events
    WHERE
      (
        start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
        OR end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
        OR CURDATE() BETWEEN start_date AND end_date
      )
      AND FIND_IN_SET(:driver_id, driver_ids)
      AND archived IS NULL
    ORDER BY start_date
    LIMIT 5
  ";
  $params = ['driver_id' => $driverId];
  return $db->get_rows($query, $params);
}

function getTextForUpcomingTrip(array $trips): array
{
  $content = [];
  $content[] = 'You are assigned to the following TRIPS scheduled in the next 24hrs:';
  $content[] = '';
  foreach ($trips as $index => $row) {
    $trip = new Trip(($row->id));

    $content[] = strtoupper(Utils::numberToOrdinalWord($index + 1)).',';
    $content[] = "You have a trip scheduled for ".Date('g:ia', strtotime($trip->startDate)).' '.Utils::showDate($trip->startDate).".";
    $content[] = "SUMMARY: ".$trip->summary.".";
    $content[] = "You are to use the ".$trip->vehicle->name." for this trip. (".$trip->vehiclePUOptions." - ".$trip->vehicleDOOptions.")";
    $content[] = "You are to pick up ".$trip->guests." at ".$trip->puLocation->name." at ".Date('g:ia', strtotime($trip->pickupDate)).".";
    $content[] = "You are to drop off the guest(s) at ".$trip->doLocation->name;
    $content[] = "The contact person for this trip is ".$trip->guest->getName()." ".$trip->guest->phoneNumber.".";
    $content[] = "Expect to transport ".$trip->passengers." passenger(s).";
    $content[] = str_repeat('-', 72);
    $content[] = '';
  }
  $content[] = "The relevant driver sheets are attached hereto.";
  $content[] = '';
  return $content;
}

function getTextForUpcomingEvent(array $events): array
{
  $content = [];
  $content[] = 'You are assigned to the following EVENTS scheduled in the next 24hrs:';
  $content[] = '';
  foreach ($events as $index => $row) {
    $event = new Event($row->id);
    $content[] = strtoupper(Utils::numberToOrdinalWord($index + 1)).',';
    $content[] = Date('m/d g:ia', strtotime($event->startDate)).' - '.Date('m/d g:ia', strtotime($event->endDate)).': '.$event->name;
    $content[] = str_repeat('-', 72);
    $content[] = '';
  }
  $content[] = '';
  return $content;
}

function getAttachmentsForUpcomingTrip(array $trips): array
{
  $attachments = [];
  foreach ($trips as $row) {
    $trip = new Trip(($row->id));
    include '../inc.trip-driver-sheet.php';
    $filename = sys_get_temp_dir().'/'.$trip->getId().'-trip-driver-sheet.pdf';
    $pdf->output('F', $filename);
    $attachments[] = $filename;
  }
  return $attachments;
}

function getUnconfirmedTrips(): array
{
  $db = data::getInstance();
  $query = "
    SELECT * FROM trips
    WHERE 
      start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
      AND archived IS NULL
    ORDER BY start_date
  ";
  return $db->get_rows($query);
}

function getUnconfirmedEvents(): array
{
  $db = data::getInstance();
  $query = "
    SELECT * FROM events
    WHERE 
      start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
      AND archived IS NULL
    ORDER BY start_date
  ";
  return $db->get_rows($query);
}

function getUnconfirmedTripsText(array $trips): array
{
  $content[] = 'The following TRIPS are unconfirmed and require your attention:';
  $content[] = '';
  foreach ($trips as $row) {
    $trip = new Trip($row->id);
    $content[] = Date('m/d g:ia', strtotime($trip->startDate)).': '.$trip->summary;
    $content[] = str_repeat('-', 72);
    $content[] = '';
  }
  return $content;
}

function getUnconfirmedEventsText(array $events): array
{
  $content[] = 'The following EVENTS are unconfirmed and require your attention:';
  $content[] = '';
  foreach ($events as $row) {
    $event = new Event($row->id);
    $content[] = Date('m/d g:ia', strtotime($event->startDate)).' - '.Date('m/d g:ia', strtotime($event->endDate)).': '.$event->name;
    $content[] = str_repeat('-', 72);
    $content[] = '';
  }
  return $content;
}