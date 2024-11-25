<?php
require_once 'class.parsedown.php';
$parsedown = new Parsedown();
echo $parsedown->text(file_get_contents('doc.release-notes.md'));