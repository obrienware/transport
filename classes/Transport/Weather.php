<?php

declare(strict_types=1);

namespace Transport;

header('Content-Type: application/json');

require_once __DIR__ . '/../../autoload.php';

use DateTime;
use DateTimeZone;

use Transport\Database;
use Generic\ApiClient;

class Weather
{
  use Log;

  public ?DateTimeZone $timezone = null;

  public string $latitude;
  public string $longitude;
  public $data;
  public string $userAgent;

  public function __construct($latitude = NULL, $longitude = NULL)
  {
    $timezoneString = isset($_SESSION['userTimezone']) ? $_SESSION['userTimezone'] : $_ENV['TZ'];
    $this->timezone = new DateTimeZone($timezoneString);

    $this->userAgent = "WeatherApp/1.0 (transport.obrienware.com, weather@obrienware.com)";

    $this->latitude = $latitude;
    $this->longitude = $longitude;
    if ($latitude and $longitude) $this->getForecast();
  }

  private function _extract_condition_from_icon($iconUrl)
  {
    $pattern = '#icons/[^/]+/[^/]+/([^?]+)#'; // Regex to capture condition from the URL
    preg_match($pattern, $iconUrl, $matches);
    $condition = $matches[1] ?? "";
    $condition = explode('/', $condition)[0];
    $condition = explode(',', $condition)[0];
    return $condition;
  }

  public function getAlerts()
  {
    $db = Database::getInstance();
    $query = "SELECT data, last_checked, NOW() as just_now FROM weather_alerts WHERE latitude = :latitude AND longitude = :longitude";
    $params = ['latitude' => $this->latitude, 'longitude' => $this->longitude];
    if ($row = $db->get_row($query, $params)) {
      $lastChecked = strtotime($row->last_checked);
      $now = strtotime($row->just_now);
      $diff = $now - $lastChecked;
      self::log("Last checked: $lastChecked, Now: $now, Diff: $diff");
      // Make sure that we're only updating every hour!
      if ($diff < 3600) {
        return json_decode($row->data);
      }
    }
    return $this->fetchAlerts();
  }

  private function fetchAlerts()
  {
    $api = new ApiClient("https://api.weather.gov/", ['User-Agent: ' . $this->userAgent]);
    $data = $api->get('/alerts/active', ['point' => "{$this->latitude},{$this->longitude}"]);
    $query = "
      INSERT INTO weather_alerts SET
        latitude = :latitude,
        longitude = :longitude,
        data = :data,
        last_checked = NOW()
      ON DUPLICATE KEY UPDATE
        data = :data,
        last_checked = NOW()
    ";
    $params = [
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'data' => json_encode($data)
    ];
    $db = Database::getInstance();
    $db->query($query, $params);
    return $data;
  }

  public function getForecast()
  {
    $db = Database::getInstance();
    $query = "SELECT data, last_checked, NOW() as just_now FROM forecast_data WHERE latitude = :latitude AND longitude = :longitude";
    $params = ['latitude' => $this->latitude, 'longitude' => $this->longitude];
    self::log($params);
    if ($row = $db->get_row($query, $params)) {
      $lastChecked = strtotime($row->last_checked);
      $now = strtotime($row->just_now);
      $diff = $now - $lastChecked;
      self::log("Last checked: $lastChecked, Now: $now, Diff: $diff");
      // Make sure that we're only updating every hour!
      if ($diff < 3600) {
        $this->data = json_decode($row->data);
        return;
      }
    }
    return $this->fetchForecast();
  }


  public function fetchForecast()
  {
    $lat = $this->latitude;
    $lon = $this->longitude;

    $api = new ApiClient("https://api.weather.gov/", ['User-Agent: ' . $this->userAgent]);
    $gridpointData = json_decode($api->get("/points/$lat,$lon"));

    if (!$gridpointData || !isset($gridpointData->properties->forecast)) {
      self::log("Error: Unable to retrieve forecast URL.");
      return "Error: Unable to retrieve forecast URL.";
    }

    $forecastUrl = $gridpointData->properties->forecast;
    $api = new ApiClient($forecastUrl, ['User-Agent: ' . $this->userAgent]);
    $forecastData = json_decode($api->get(''));

    if (!$forecastData || !isset($forecastData->properties->periods)) {
      self::log("Error: Unable to retrieve forecast data.");
      return "Error: Unable to retrieve forecast data.";
    }

    $periods = $forecastData->properties->periods;

    if (is_array($periods)) {
      foreach ($periods as $key => $period) {
        $condition = $this->_extract_condition_from_icon($period->icon);
        $isNight = $period->isDaytime ? false : true;
        $icon = $this->_getWeatherIcon($condition, $isNight);
        $periods[$key]->iconClass = $icon;
        $periods[$key]->icon = '<i class="wi ' . $icon . '"></i>';
        $periods[$key]->condition = $condition;
      }
    }

    $query = "
      INSERT INTO forecast_data SET
        latitude = :latitude,
        longitude = :longitude,
        data = :data,
        last_checked = NOW()
      ON DUPLICATE KEY UPDATE
        data = :data,
        last_checked = NOW()
    ";
    $params = [
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'data' => json_encode($periods)
    ];
    $db = Database::getInstance();
    $db->query($query, $params);
    $this->data = $periods;
  }


  private function _getWeatherIcon(string $condition, bool $isNight = false): string
  {
    $db = Database::getInstance();
    $query = "SELECT * FROM weather_codes WHERE nws_condition = :condition";
    $params = ['condition' => $condition];
    if ($row = $db->get_row($query, $params)) {
      if ($isNight) return $row->icon_night;
      return $row->icon_day;
    }
    return '';
  }

  public function getCurrentTemp()
  {
    if (isset($this->data[0]->temperature))
      return $this->data[0]->temperature . 'º' . $this->data[0]->temperatureUnit;
    return '';
  }

  public function getForecastFor($date)
  {
    $date = new DateTime($date, $this->timezone);
    foreach ($this->data as $period) {
      $start = new DateTime($period->startTime, $this->timezone);
      $end = new DateTime($period->endTime, $this->timezone);
      if ($date >= $start && $date <= $end) return $period;
    }
    return null;
  }
}
