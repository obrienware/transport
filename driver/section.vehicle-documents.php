<?php require_once 'class.vehicle-document.php';?>
<?php if ($documents = VehicleDocument::getDocuments($_REQUEST['id'])): ?>
  <h4>Documents</h4>
  <ul class="list-group mb-4">
    <?php foreach ($documents as $document): ?>
      <a href="/documents/<?=$document->filename?>" class="list-group-item" target="_blank"><?=$document->name?></a>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>