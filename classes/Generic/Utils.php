<?php

declare(strict_types=1);

namespace Generic;

require_once __DIR__ . '/../../autoload.php';


// TODO: Much of this should be refactored as traits
class Utils
{

  public static function GUID()
  {
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
  }


  public static function randomPassword($length = 8)
  {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++)
    {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }
    return implode($pass);
  }


  public static function callApi($method, $url, $data = [], $auth = null, $headers = [])
  {
    $curl = curl_init();
    switch ($method)
    {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
        {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_PUT, 1);
        break;
      default:
        if (count($data) > 0)
        {
          $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    }

    // Optional Authentication:
    if (is_array($auth))
    {
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, $auth['username'] . ':' . $auth['password']);
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(['Accept: application/json'], $headers));

    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($curl);
    if (curl_errno($curl)) return 'Curl error: ' . curl_error($curl);
    return $result;
  }


  public static function ago($time1, $time2 = 'now', $short = false): string
  {
    if ($short)
    {
      $periods = array("sec", "min", "hr", "day", "wk", "mth", "yr", "dec");
    }
    else
    {
      $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    }
    $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

    $datetime1 = new \DateTime($time1);
    $datetime2 = new \DateTime($time2);

    $difference = $datetime2->getTimestamp() - $datetime1->getTimestamp();

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
    {
      $difference /= $lengths[$j];
    }
    $difference = round($difference);
    if ($difference != 1) $periods[$j] .= "s";
    return "$difference $periods[$j]";
  }


  public static function formattedPhoneNumber(string $number): string
  {
    if (str_contains($number, '+'))
    {
      return preg_replace('/(?<!^)\D/', '', $number); // Remove all non-numeric characters except the leading +
    }
    else
    {
      return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
    }
  }


  public static function showDate($date): string
  {
    $timezone = new \DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $givenDate = new \DateTime($date, $timezone);

    $today = new \DateTime('today', $timezone);
    $tomorrow = new \DateTime('tomorrow', $timezone);

    if ($givenDate->format('Y-m-d') == $today->format('Y-m-d')) return 'today';
    if ($givenDate->format('Y-m-d') == $tomorrow->format('Y-m-d')) return 'tomorrow';

    return self::timeAgo($date) . ' (' . $givenDate->format('D j M g:ia') . ')';
  }

  public static function timeAgo($datetime, $shorthand = false)
  {
    $timezone = new \DateTimeZone($_ENV['TZ'] ?? 'UTC');
    $now = new \DateTime('now', $timezone);
    $target = new \DateTime($datetime, $timezone);
    $diff = $now->diff($target);
    $isFuture = $target > $now;

    // Define full and shorthand unit names
    $units = [
      'y'   => ['full' => 'year', 'short' => 'yr'],
      'm'  => ['full' => 'month', 'short' => 'mth'],
      'd'    => ['full' => 'day', 'short' => 'day'],
      'h'   => ['full' => 'hour', 'short' => 'hr'],
      'i' => ['full' => 'minute', 'short' => 'min'],
      's' => ['full' => 'second', 'short' => 'sec'],
    ];

    foreach ($units as $unit => $names)
    {
      $value = $diff->$unit;
      if ($value > 0)
      {
        $unitName = $shorthand ? $names['short'] : $names['full'];
        return ($isFuture ? "In " : "") . $value . ' ' . $unitName . ($value > 1 ? 's' : '') . ($isFuture ? "" : " ago");
      }
    }

    return 'Just now';
  }



  public static function numberToOrdinalWord(int $number): string
  {
    $ordinals = [
      1 => 'firstly',
      2 => 'secondly',
      3 => 'thirdly',
      4 => 'fourthly',
      5 => 'fifthly'
    ];

    return $ordinals[$number] ?? 'out of range';
  }

  public static function getContrastColor(string $hexColor): string
  {
    // Remove the hash at the start if it's there
    $hexColor = ltrim($hexColor, '#');

    // Convert hex to RGB
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));

    // Calculate brightness (standard luminance formula)
    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

    // Return black or white depending on brightness
    return $brightness > 128 ? 'black' : 'white';
  }

  public static function showResourceNotFound(): void
  {
    echo "
      <div class=\"container-fluid text-center\">
        <div class=\"alert alert-danger mt-5 w-50 mx-auto\">
          <h1 class=\"fw-bold\">Sorry!</h1>
          <p class=\"lead\">The resource you're referencing cannot be located.</i></p>
        </div>
      </div>
    ";
  }
}
