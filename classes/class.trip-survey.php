<?php

require_once 'class.data.php';
if (!isset($db)) $db = new data();

class TripSurvey
{
  private $surveyId;
  private $dateTimeStamp;

  public $tripId;
  public $ratingRoad;
  public $ratingWeather;
  public $ratingTrip;
  public $guestIssues;
  public $comments;

  public function __construct($surveyId = null)
  {
    if ($surveyId) $this->getSurvey($surveyId);
  }

  public function getSurvey(int $surveyId): bool
  {
    global $db;
    $sql = "SELECT * FROM trip_surveys WHERE id = :id";
    $data = ['id' => $surveyId];
    if ($item = $db->get_row($sql, $data)) {
      $this->surveyId = $item->id;
      $this->tripId = $item->trip_id;
      $this->ratingRoad = $item->rating_road;
      $this->ratingTrip = $item->rating_trip;
      $this->ratingWeather = $item->rating_weather;
      $this->guestIssues = $item->guest_issues;
      $this->comments = $item->comments;
      return true;
    }
    return false;
  }

  public function save()
  {
    global $db;
    $data = [
      'trip_id' => $this->tripId,
      'rating_trip' => $this->ratingTrip,
      'rating_weather' => $this->ratingWeather,
      'rating_road' => $this->ratingRoad,
      'guest_issues' => $this->guestIssues,
      'comments' => $this->comments
    ];
    if ($this->surveyId) {
      $data['id'] = $this->surveyId;
      $data['datetimestamp'] = $this->dateTimeStamp;
      $sql = "
        UPDATE tripsurveys SET
          trip_id = :trip_id,
          datetimestamp = :datetimestamp,
          rating_trip = :rating_trip,
          rating_weather = :rating_weather,
          rating_road = :rating_road,
          guest_issues = :guest_issues,
          comments = :comments
        WHERE id = :id
      ";
    } else {
      $sql = "
        INSERT INTO trip_surveys SET
          trip_id = :trip_id,
          datetimestamp = NOW(),
          rating_trip = :rating_trip,
          rating_weather = :rating_weather,
          rating_road = :rating_road,
          guest_issues = :guest_issues,
          comments = :comments
      ";
    }
		$result = $db->query($sql, $data);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
  }

	static public function delete($surveyId)
	{
		global $db;
		$sql = 'DELETE trip_surveys WHERE id = :id';
		$data = ['id' => $surveyId];
		return $db->query($sql, $data);
	}

}