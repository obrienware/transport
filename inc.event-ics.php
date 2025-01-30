<?php
require_once 'autoload.php';

$ics = new ICS([
  'dtstart' => $event->startDate,
  'dtend' => $event->endDate,
  'description' => $event->notes,
  'summary' => $event->name,
]);
if ($event->location && $event->location->mapAddress) 
  $ics->set('location', str_replace("\n", "\\n", $event->location->mapAddress));
