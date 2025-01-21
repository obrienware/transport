<?php
require_once 'autoload.php';
$parsedown = new Parsedown();
echo '<div class="container-fluid mt-2">';
echo $parsedown->text(file_get_contents('doc.release-notes.md'));
echo '</div>';