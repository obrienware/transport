<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Utils;

$config = Config::get('system');
$keys = $config->keys;

// address | latlng

$placeId = $_GET['placeId'];
$latlng = $_GET['latlng'];
$address = $_GET['address'];


if ($placeId) {
  $result = Utils::callApi('GET', 'https://places.googleapis.com/v1/places/'.$placeId, [
    'key' => $keys->GOOGLE_API_KEY,
    'fields' => 'id,formattedAddress,location'
  ]);
} else if($latlng) {
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'latlng' => $latlng
  ]);
} else {
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'address' => $address
  ]);  
}




die(json_encode(json_decode($result)));