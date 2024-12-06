<?php
header('Content-Type: application/json');
$phone = formattedPhoneNumber($_REQUEST['phone']);

require_once 'class.data.php';
$db = new data();
$sql = "REPLACE INTO opt_in_text SET tel = :tel, opt_in = NOW()";
$data = ['tel' => $phone];
$result = $db->query($sql, $data);
echo json_encode(['result' => $result]);


function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}