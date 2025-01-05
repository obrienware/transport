<?php
header('Content-Type: application/json');
require_once 'class.utils.php';
require_once 'class.data.php';
$db = data::getInstance();

class Weather
{
  public $latitude;
  public $longitude;
  public $data;

  public function __construct($latitude = NULL, $longitude = NULL)
  {
    $this->latitude = $latitude;
    $this->longitude = $longitude;
    if ($latitude AND $longitude) $this->getForecast();
  }

  public function getForecast()
  {
    $data = Utils::callApi('GET', 'https://api.open-meteo.com/v1/forecast', [
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'current' => 'temperature_2m,is_day,precipitation,rain,showers,snowfall,weather_code,cloud_cover',
      'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,rain_sum,showers_sum,snowfall_sum,precipitation_probability_max',
      'temperature_unit' => 'fahrenheit',
      'wind_speed_unit' => 'mph',
      'precipitation_unit' => 'inch',
      'timezone' => $_ENV['TZ']
    ]);
    $this->data = json_decode($data);
  }

  public function getCurrentTemp() {
    return $this->data->current->temperature_2m.$this->data->current_units->temperature_2m;
  }

  private function _getCodeData($weatherCode)
  {
    $db = data::getInstance();
    $query = "SELECT * FROM weather_codes WHERE code = :code";
    $params = ['code' => $weatherCode];
    return $db->get_row($query, $params);
  }

  public function getWeather() {
    $row = $this->_getCodeData($this->data->current->weather_code);
    $icon = NULL;
    if ($row->icon_day) {
      if ($this->data->current->is_day == 1) {
        $icon = '<i class="fs-1 wi '.$row->icon_day.'"></i>';
      } else {
        $icon = '<i class="fs-1 wi '.$row->icon_night.'"></i>';
      }
    }
    $result = (object)[
      'description' => $row->description,
      'temp' => $this->getCurrentTemp(),
      'min' => $this->data->daily->temperature_2m_min[0].$this->data->current_units->temperature_2m,
      'max' => $this->data->daily->temperature_2m_max[0].$this->data->current_units->temperature_2m,
      'icon' => $icon,
    ];
    return $result;
  }

  public function getForecastFor($dateTimeString)
  {
    foreach ($this->data->daily->time as $key => $value) {
      if ($value == $dateTimeString) {
        $index = $key;
        break;
      }
    }
    if (isset($index)) {
      $row = $this->_getCodeData($this->data->daily->weather_code[$index]);
      $icon = NULL;
      if ($row->icon_day) {
        $icon = '<i class="fs-1 wi '.$row->icon_day.'"></i>';
      }
      $result = (object)[
        'description' => $row->description,
        'min' => $this->data->daily->temperature_2m_min[$index].$this->data->current_units->temperature_2m,
        'max' => $this->data->daily->temperature_2m_max[$index].$this->data->current_units->temperature_2m,
        'icon' => $icon,
      ];
      return $result;
    }
    return false;
  }
}