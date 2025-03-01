<?php

declare(strict_types=1);

namespace Transport;

header('Content-Type: application/json');

require_once __DIR__ . '/../../autoload.php';

use Generic\Utils;
use Transport\Database;

function log(mixed $message): void
  {
    $out = fopen('php://stdout', 'w'); //output handler
    $output = json_encode($message, JSON_PRETTY_PRINT);
    fputs($out, $output.PHP_EOL); //writing output operation
    fclose($out); //closing handler
  }

class Weather
{
  public string $latitude;
  public string $longitude;
  public $data;
  public string $userAgent;

  public function __construct($latitude = NULL, $longitude = NULL)
  {
    log("Weather::__construct($latitude, $longitude)");
    $this->userAgent = "WeatherApp/1.0 (transport.obrienware.com, weather@obrienware.com)";

    $this->latitude = $latitude;
    $this->longitude = $longitude;
    if ($latitude and $longitude) $this->getForecast();
  }

  private function fetch_data($url, $userAgent) {
    $options = [
        "http" => [
            "header" => "User-Agent: $userAgent\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return 'null';
    }

    return json_decode($response);
}

  private function _extract_condition_from_icon($iconUrl)
  {
    $pattern = '#icons/[^/]+/[^/]+/([^?]+)#'; // Regex to capture condition from the URL
    preg_match($pattern, $iconUrl, $matches);

    return $matches[1] ?? "Unknown Condition";
  }

  public function getAlerts()
  {
    $db = Database::getInstance();
    $query = "SELECT data, last_checked, NOW() as just_now FROM weather_alerts WHERE latitude = :latitude AND longitude = :longitude";
    $params = ['latitude' => $this->latitude, 'longitude' => $this->longitude];
    if ($row = $db->get_row($query, $params))
    {
      $lastChecked = strtotime($row->last_checked);
      $now = strtotime($row->just_now);
      $diff = $now - $lastChecked;
      log("Last checked: $lastChecked, Now: $now, Diff: $diff");
      // Make sure that we're only updating every hour!
      if ($diff < 3600)
      {
        return json_decode($row->data);
      }
    }
    return $this->fetchAlerts();
  }

  private function fetchAlerts()
  {
    $data = $this->fetch_data("https://api.weather.gov/alerts/active?point={$this->latitude},{$this->longitude}", $this->userAgent);
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
    log($params);
    if ($row = $db->get_row($query, $params))
    {
      $lastChecked = strtotime($row->last_checked);
      $now = strtotime($row->just_now);
      $diff = $now - $lastChecked;
      log("Last checked: $lastChecked, Now: $now, Diff: $diff");
      // Make sure that we're only updating every hour!
      if ($diff < 3600)
      {
        $this->data = json_decode($row->data);
        return;
      }
    }
    // log($db->errorInfo); return;
    return $this->fetchForecast();
  }


  public function fetchForecast()
  {
    $lat = $this->latitude;
    $lon = $this->longitude;
    $userAgent = $this->userAgent;

    $gridpointUrl = "https://api.weather.gov/points/$lat,$lon";
    $gridpointData = $this->fetch_data($gridpointUrl, $userAgent);

    if (!$gridpointData || !isset($gridpointData->properties->forecast))
    {
      log("Error: Unable to retrieve forecast URL.");
      return "Error: Unable to retrieve forecast URL.";
    }

    $forecastUrl = $gridpointData->properties->forecast;
    $forecastData = $this->fetch_data($forecastUrl, $userAgent);

    if (!$forecastData || !isset($forecastData->properties->periods))
    {
      log("Error: Unable to retrieve forecast data.");
      return "Error: Unable to retrieve forecast data.";
    }

    $periods = $forecastData->properties->periods;
    
    if (is_array($periods))
    {
      foreach ($periods as $key => $period)
      {
        $condition = $this->_extract_condition_from_icon($period->icon);
        $isNight = $period->isDaytime ? false : true;
        $icon = $this->_getWeatherIcon($condition, $isNight);
        $periods[$key]->iconClass = $icon;
        $periods[$key]->icon = '<i class="wi ' . $icon . '"></i>';
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
    if ($row = $db->get_row($query, $params))
    {
      if ($isNight) return $row->icon_night;
      return $row->icon_day;
    }
    return '';
  }

  public function getCurrentTemp()
  {
    if (isset($this->data[0]->temperature))
      return $this->data[0]->temperature . 'º' . $this->data[0]->temperatureUnit;
    return 'Unknown';
  }
}
