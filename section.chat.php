<?php
$me = $_SESSION['user']->id;
require_once 'class.data.php';
$db = new Data();
if ($_REQUEST['tripId']) {
  $sql = "
    SELECT * FROM trip_messages m
    LEFT OUTER JOIN users u ON m.user_id = u.id
    WHERE m.trip_id = :trip_id
    ORDER BY m.datetimestamp
  ";
  $data = [
    'trip_id' => $_REQUEST['tripId']
  ];
}

if ($_REQUEST['eventId']) {
  $sql = "
    SELECT * FROM event_messages m
    LEFT OUTER JOIN users u ON m.user_id = u.id
    WHERE m.event_id = :event_id
    ORDER BY m.datetimestamp
  ";
  $data = [
    'event_id' => $_REQUEST['eventId']
  ];
}

if (isset($sql)) {
  if ($rs = $db->get_results($sql, $data)) {
    foreach ($rs as $item) {
      if ($item->user_id == $me) {
        $fromName = '';
        $fromClass = 'from-me';
      } else {
        $fromName = '<div style="font-size:x-small; color:gray" class="mb-0">'.$item->first_name.' '.$item->last_name.'</div>';
        $fromClass = 'from-them';
      }
      $message = nl2br(htmlentities($item->message));
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