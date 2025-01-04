<?php
require_once 'class.data.php';;

class EmailTemplates
{
  public static function get($templateName) {
    $db = new data();
    $sqlQuery = "SELECT content FROM email_templates WHERE name = :name";
    $params = ['name' => $templateName];
    return $db->get_var($sqlQuery, $params);
  }
}