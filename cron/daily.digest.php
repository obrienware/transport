<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.config.php';
require_once 'class.trip.php';
require_once 'class.event.php';
require_once 'class.email.php';
require_once 'class.data.php';
$db = new data();

$config = Config::get('organization');

/**
 * We want to send an email digest (a summary of the items that need attention) for the manager, as well as
 * we need to send an email digest to all drivers.
 */

// Let's do the drivers first...
require_once 'class.user.php';
$drivers = User::getDrivers();
foreach ($drivers as $driver) {
  if (!$driver->personal_preferences) continue;
  $prefs = json_decode($driver->personal_preferences);
  if (!$prefs->dailyDigestEmails) continue;

  $email = new Email();
  $email->setSubject('Your Driver Digest for today');
  $email->addRecipient($driver->email_address, $driver->first_name.' '.$driver->last_name);
  $email->addReplyTo($config->email->defaultReplyTo);

  // Does this driver have any trips today?
  $trips = getTripsFor($driver->id);

  // Is this driver assigned to any events today?
  $events = getEventsFor($driver->id);

  if (count($trips) === 0 && count($events) === 0) {
    // Driver has no trips or events today
    $content = 'You are not assigned to any trips or events scheduled in the next 24hrs!';

  } else {

    $content = '';

    if ($trips) {
      $content .= "You are assigned to the following trips scheduled in the next 24hrs:\n\n";
      foreach ($trips as $row) {
        $trip = new Trip(($row->id));
        $content .= Date('g:ia', strtotime($trip->startDate)).': '.$trip->summary."\n";
        // Generate the driver sheet for this trip and attach it
        include '../inc.trip-driver-sheet.php';
        $filename = sys_get_temp_dir().'/'.$trip->getId().'-trip-driver-sheet.pdf';
        $pdf->output('F', $filename);
        $email->addAttachment($filename);
      }
      $content .= "\n";
    }

    if ($events) {
      $content .= "You are assigned to the following events scheduled in the next 24hrs:\n\n";
      foreach ($events as $row) {
        $event = new Event($row->id);
        $content .= Date('m/d g:ia', strtotime($event->startDate)).' - '.Date('m/d g:ia', strtotime($event->endDate)).': '.$event->name."\n";
      }  
      $content .= "\n";
    }

    $content .= "\nThe relevant driver sheets are attached hereto.";

  }

  $driverName = $driver->first_name;
  $content = "
Good morning {$driverName}!

".$content."

Have a blessed day!
Transportation Team
";
  $email->setContent($content);
  $email->sendText();
  unlink($filename);
}


function getTripsFor($driverId)
{
  global $db;
  $query = "SELECT * FROM trips WHERE start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR) AND driver_id = :driver_id";
  $params = ['driver_id' => $driverId];
  return $db->get_rows($query, $params);
}

function getEventsFor($driverId)
{
  global $db;
  $query = "
    SELECT * FROM events
    WHERE
    (
      start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
      OR end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
      OR CURDATE() BETWEEN start_date AND end_date
    )
    AND FIND_IN_SET(:driver_id, driver_ids)
  ";
  $params = ['driver_id' => $driverId];
  return $db->get_rows($query, $params);
}