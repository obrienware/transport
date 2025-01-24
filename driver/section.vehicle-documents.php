<?php 
require_once '../autoload.php';

use Transport\VehicleDocument;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
?>
<h4>Documents</h4>
<?php if ($documents = VehicleDocument::getDocuments($id)): ?>
  <ul class="list-group mb-4">
    <?php foreach ($documents as $document): ?>
      <a href="/documents/<?=$document->filename?>" class="list-group-item" target="_blank"><?=$document->name?></a>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<div class="mb-3">
  <label for="photoFile" class="form-label">Optional image upload</label>
  <input type="file" id="photoFile" name="photoFile" class="form-control" accept="image/*">
</div>
<div class="mb-3">
  <label for="documentName" class="form-label">Document name</label>
  <input type="text" id="documentName" name="documentName" class="form-control">
</div>
<div class="text-end mb-4">
  <button class="btn btn-outline-primary" onclick="uploadPhoto()">Upload</button>
</div>

<script type="text/javascript">
  function uploadPhoto() {
    var file = document.getElementById('photoFile').files[0];
    var formData = new FormData();
    formData.append('file', file);
    formData.append('vehicleId', <?=$id?>);
    formData.append('documentName', document.getElementById('documentName').value);
    fetch('/api/post.vehicle-document.php', {
      method: 'POST',
      body: formData
    }).then(response => {
      if (response.ok) {
        $('#vehicles-content').load('section.vehicles.php');
        // location.reload();
      }
    });
  }
</script>