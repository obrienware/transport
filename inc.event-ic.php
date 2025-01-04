<?php
require_once 'class.ics.php';

$ics = new ICS([
  'dtstart' => $event->startDate,
  'dtend' => $event->endDate,
  'description' => $event->notes,
  'summary' => $event->name,
]);
if ($event->location) $ics->set('location', str_replace("\n", "\\n", $event->location->mapAddress));
