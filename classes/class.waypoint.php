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

  public function add(string $description, $locationId, $isPickupLocation = false)
  {
    $this->waypoints[] = (object) [
      'seq' => $this->sequece++,
      'description' => $description,
      'locationId' => $locationId,
      'isPickupLocation' => $isPickupLocation
    ];
  }

  public function save()
  {
    global $db;
    // For now, we need to replace any existing waypoints with our newly generated waypoints.
    // In future we'll first need to check if the user has manually modified the waypoints
    $db->query("DELETE FROM trip_waypoints WHERE trip_id = :trip_id", ['trip_id' => $this->tripId]);

    foreach ($this->waypoints as $seq => $item) {
      $sql = "
        INSERT INTO trip_waypoints SET
          trip_id = :trip_id,
          seq = :seq,
          location_id = :location_id,
          pickup = :pickup,
          description = :description
      ";
      $data = [
        'trip_id' => $this->tripId,
        'seq' => $seq,
        'location_id' => $item->locationId,
        'pickup' => $item->isPickupLocation ? 1 : 0,
        'description' => $item->description,
      ];
      $db->query($sql, $data);
    }
  }
}