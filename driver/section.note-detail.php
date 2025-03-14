<?php
require_once '../autoload.php';

use Transport\DriverNote;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;

$note = new DriverNote($id);

$parsedown = new Parsedown();
echo '<div class="">';
echo $parsedown->text(
  preg_replace('/(?<!\|)\n/', "\n\n", $note->note)
);
echo '</div>';
// echo nl2br($note->note);
