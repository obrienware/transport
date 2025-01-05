<?php
$me = $_SESSION['user']->id;
require_once 'class.data.php';
$db = data::getInstance();
if ($_REQUEST['tripId']) {
  $query = "
    SELECT * FROM trip_messages m
    LEFT OUTER JOIN users u ON m.user_id = u.id
    WHERE m.trip_id = :trip_id
    ORDER BY m.datetimestamp
  ";
  $params = [
    'trip_id' => $_REQUEST['tripId']
  ];
}

if ($_REQUEST['eventId']) {
  $query = "
    SELECT * FROM event_messages m
    LEFT OUTER JOIN users u ON m.user_id = u.id
    WHERE m.event_id = :event_id
    ORDER BY m.datetimestamp
  ";
  $params = [
    'event_id' => $_REQUEST['eventId']
  ];
}

if (isset($query)) {
  if ($rows = $db->get_rows($query, $params)) {
    foreach ($rows as $row) {
      if ($row->user_id == $me) {
        $fromName = '';
        $fromClass = 'from-me';
      } else {
        $fromName = '<div style="font-size:x-small; color:gray" class="mb-0">'.$row->first_name.' '.$row->last_name.'</div>';
        $fromClass = 'from-them';
      }
      $message = nl2br(htmlentities($row->message));
      echo "
        {$fromName}
        <div class='{$fromClass}'>
          <p>{$message}</p>
        </div>
        <div class='clear'></div>
      ";
    }
  }
}