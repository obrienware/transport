<?php
require_once 'class.utils.php';
require_once 'class.data.php';
if (!isset($db)) $db = new data();

Class Waypoints
{
  public $tripId;
  private $waypoints;
  private $sequece;

  public function __construct(int $tripId)
  {
    $this->tripId = $tripId;
    $this->waypoints = [];
    $this->sequece = 0;
  }

  public function add(string $description, $locationId, $targetDateTime = null)
  {
    $this->waypoints[] = (object) [
      'seq' => $this->sequece++,
      'description' => $description,
      'locationId' => $locationId,
      'dateTime' => $targetDateTime,
      'targetDateTime' => $targetDateTime
    ];
  }

  public function save()
  {
    global $db;
    // First check if we have waypoints for this trip
    if ($db->get_var("SELECT COUNT(*) FROM trip_waypoints WHERE trip_id = :trip_id", ['trip_id' => $this->tripId]) <= 0) {
      $this->calculate();
      foreach ($this->waypoints as $seq => $item) {
        $sql = "
          INSERT INTO trip_waypoints SET
            trip_id = :trip_id,
            seq = :seq,
            date_time = :date_time,
            location_id = :location_id,
            target = :target,
            description = :description,
            duration = :duration
        ";
        $data = [
          'trip_id' => $this->tripId,
          'seq' => $seq,
          'date_time' => $item->dateTime ?: NULL,
          'location_id' => $item->locationId,
          'target' => $item->targetDateTime ? 1 : 0,
          'description' => $item->description,
          'duration' => $item->duration ?: NULL
        ];
        $db->query($sql, $data);
      }
      }
  }

  private function calculate()
  {
    // We want to find out target date time and work backwards applying a estimated time to the previous waypoints
    $targetFound = false;
    foreach($this->waypoints as $index => $item) {
      if ($item->targetDateTime) {
        $targetFound = true;
        break;
      }
    }
    if ($targetFound) {
      while ($index -1 >= 0) {
        // We want to calculate the time it takes to travel from the previous waypoint to the current one
        $destination = new Location($this->waypoints[$index]->locationId);
        $origin = new Location($this->waypoints[$index -1]->locationId);
        $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/distancematrix/json', [
          'key' => $_ENV['GOOGLE_API_KEY'],
          'origins' => $origin->lat.','.$origin->lon,
          'destinations' => $destination->lat.','.$destination->lon,
          'units' => 'imperial',
          'traffic_model' => 'pessimistic',
          'departure_time' => strtotime($this->waypoints[$index]->dateTime) // This is a little skewed but we can't set the arrival time
        ]);
        $json = json_decode($result);
        if ($json->status != 'OK') break;
        $duration = $json->rows[0]->elements[0]->duration_in_traffic->value;
        $this->waypoints[$index-1]->dateTime = Date('Y-m-d H:i:s', strtotime($this->waypoints[$index]->dateTime) - $duration);
        $this->waypoints[$index-1]->duration = ($duration / 60);
        $index--;
      }
    }
  }
}