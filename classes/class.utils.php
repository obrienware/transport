<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

class Utils
{
  
  static public function GUID() 
  {
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
  }


  static public function randomPassword($length = 8) 
  {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
  }


  static public function callApi ($method, $url, $data = [], $auth = null, $headers = []) 
  {
    $curl = curl_init();
    switch ($method) {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_PUT, 1);
        break;
      default:
        if (count($data) > 0) {
          $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    }
  
    // Optional Authentication:
    if (is_array($auth)) {
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl, CURLOPT_USERPWD, $auth['username'].':'.$auth['password']);
    }
  
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(['Accept: application/json'], $headers));
  
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    
    $result = curl_exec($curl);
    if(curl_errno($curl)) return 'Curl error: '.curl_error($curl);
    return $result;
  }


  static public function ago($time1, $time2 = 'now', $short = false): string
  {
    if ($short) {
      $periods = array("sec", "min", "hr", "day", "wk", "mth", "yr", "dec");
    } else {
      $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    }
    $lengths = array("60","60","24","7","4.35","12","10");
    $time1 = strtotime($time1);
    $time2 = strtotime($time2);
  
    $difference = $time2 - $time1;
  
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
      $difference /= $lengths[$j];
    }
    $difference = round($difference);
    if($difference != 1) $periods[$j].= "s";
    return "$difference $periods[$j]";
  }


  static public function formattedPhoneNumber(string $number): string
	{
		if (str_contains($number, '+')) {
			return preg_replace('/(?<!^)\D/', '', $number); // Remove all non-numeric characters except the leading +
		} else {
			return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
		}	
	}


  static public function showDate($date): string
  {
    $baseline = Date('Y-m-d', strtotime($date));
    if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline))) return 'today';
    if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline.' -1 day'))) return 'tomorrow';
    return 'In '.self::ago('now', $date).' ('.Date('l m/d @ g:ia', strtotime($date)).')';
  }


  static public function numberToOrdinalWord(int $number): string
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
  
}