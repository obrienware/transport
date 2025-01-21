<?php
require_once 'class.data.php';

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
    $db = data::getInstance();
    $query = "SELECT * FROM trip_surveys WHERE id = :id";
    $params = ['id' => $surveyId];
    if ($row = $db->get_row($query, $params)) {
      $this->surveyId = $row->id;
      $this->tripId = $row->trip_id;
      $this->ratingRoad = $row->rating_road;
      $this->ratingTrip = $row->rating_trip;
      $this->ratingWeather = $row->rating_weather;
      $this->guestIssues = $row->guest_issues;
      $this->comments = $row->comments;
      return true;
    }
    return false;
  }

  public function save()
  {
    $db = data::getInstance();
    $params = [
      'trip_id' => $this->tripId,
      'rating_trip' => $this->ratingTrip,
      'rating_weather' => $this->ratingWeather,
      'rating_road' => $this->ratingRoad,
      'guest_issues' => $this->guestIssues,
      'comments' => $this->comments
    ];
    if ($this->surveyId) {
      $params['id'] = $this->surveyId;
      $params['datetimestamp'] = $this->dateTimeStamp;
      $query = "
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
      $query = "
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
		$result = $db->query($query, $params);
		return [
			'result' => $result,
			'errors' => $db->errorInfo
		];
  }

	public static function delete($surveyId)
	{
		$db = data::getInstance();
		$query = 'DELETE trip_surveys WHERE id = :id';
		$params = ['id' => $surveyId];
		return $db->query($query, $params);
	}

}