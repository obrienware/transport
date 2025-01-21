<?php 
require_once '../autoload.php';

use Transport\VehicleDocument;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
?>
<?php if ($documents = VehicleDocument::getDocuments($id)): ?>
  <h4>Documents</h4>
  <ul class="list-group mb-4">
    <?php foreach ($documents as $document): ?>
      <a href="/documents/<?=$document->filename?>" class="list-group-item" target="_blank"><?=$document->name?></a>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>