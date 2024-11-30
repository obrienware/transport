<?php
header('Content-Type: application/json');
require_once 'class.utils.php';
require_once 'class.data.php';
$db = new data();
$sql = "
SELECT 
  t.id AS trip_id,
  a.iata AS arrival,
  t.ETA,
  b.iata AS departure,
  t.ETD,
  l.flight_number_prefix,
  t.flight_number
FROM trips t
LEFT OUTER JOIN locations a ON a.id = t.pu_location
LEFT OUTER JOIN locations b ON b.id = t.do_location
LEFT OUTER JOIN airlines l ON l.id = airline_id
WHERE
	((t.ETA IS NOT NULL AND DATE(t.ETA) = CURDATE())
	OR
	(t.ETD IS NOT NULL AND DATE(t.ETD) = CURDATE()))
  AND t.archived IS NULL
";
if ($rs = $db->get_results($sql)) {
  foreach ($rs as $item) {

    $type = ($item->arrival) ? 'arrival' : 'departure';
    $result = Utils::callApi('GET', 'https://aviation-edge.com/v2/public/timetable', [
      'key' => $_ENV['AVIATION_EDGE_KEY'],
      'type' => $type,
      'airline_iata' => $item->flight_number_prefix,
      'flight_num' => $item->flight_number
    ]);
    echo $result;
    $db->query(
      "UPDATE trips SET flight_info = :info WHERE id = :id",
      ['info' => $result, 'id' => $item->trip_id]
    );
    $resultObj = json_decode($result);

    $status = '';
    foreach ($resultObj as $info) {
      if ($type === 'arrival') {
        if ($info->arrival->iataCode == $item->arrival) $status = $info->status;
        if ($status == 'scheduled' AND $info->arrival->delay) $status = 'delayed';
      } else {
        if ($info->departure->iataCode == $item->departure) $status = $info->status;
        if ($status == 'scheduled' AND $info->departure->delay) $status = 'delayed';
      }
    }
    $db->query(
      "UPDATE trips SET flight_status = :status, flight_status_as_at = NOW() WHERE id = :id",
      ['status' => $status, 'id' => $item->trip_id]
    );
  }
}
