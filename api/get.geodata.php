<?php
header('Content-Type: application/json');
require_once 'class.utils.php';
require_once 'class.config.php';
$config = Config::get('system');
$keys = $config->keys;

// address | latlng

$placeId = $_REQUEST['placeId'];
$latlng = $_REQUEST['latlng'];
$address = $_REQUEST['address'];


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