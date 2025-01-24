<?php
require_once '../autoload.php';
use Transport\DriverNote;
$id = (int)$_GET['id'];
$note = new DriverNote($id);
$parsedown = new Parsedown();
echo '<div class="">';
echo $parsedown->text(
  preg_replace('/(?<!\|)\n/', "\n\n", $note->note)
);
echo '</div>';
// echo nl2br($note->note);
