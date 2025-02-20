<?php 
require_once '../autoload.php';

use Transport\VehicleDocument;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');
?>
<h4>Documents</h4>
<?php if ($documents = VehicleDocument::getDocuments($id)): ?>
  <ul class="list-group mb-4">
    <?php foreach ($documents as $document): ?>
      <a href="/documents/<?=$document->filename?>" class="list-group-item" target="_blank"><?=$document->name?></a>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="alert alert-info">No documents for this vehicle at this time</div>
<?php endif; ?>

<section class="d-none">
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
</section>

<script type="text/javascript">
  function uploadPhoto() {
    try {
      var file = document.getElementById('photoFile').files[0];
      var documentName = document.getElementById('documentName').value;
      var vehicleId = <?=$id?>;

      var reader = new FileReader();
      reader.onload = function(event) {
        var img = new Image();
        img.onload = function() {
          var canvas = document.createElement('canvas');
          var ctx = canvas.getContext('2d');
          
          // Set the desired width and height
          var maxWidth = 800;
          var maxHeight = 800;
          var width = img.width;
          var height = img.height;

          if (width > height) {
            if (width > maxWidth) {
              height *= maxWidth / width;
              width = maxWidth;
            }
          } else {
            if (height > maxHeight) {
              width *= maxHeight / height;
              height = maxHeight;
            }
          }

          canvas.width = width;
          canvas.height = height;
          ctx.drawImage(img, 0, 0, width, height);

          canvas.toBlob(function(blob) {
            var formData = new FormData();
            formData.append('file', blob, file.name);
            formData.append('vehicleId', vehicleId);
            formData.append('documentName', documentName);

            fetch('/api/post.vehicle-document.php', {
              method: 'POST',
              body: formData
            }).then(response => {
              if (response.ok) {
                return $('#vehicles-content').load('section.vehicles.php');
                // location.reload();
              }
              alert(response.statusText);
            });
          }, file.type);
        };
        img.src = event.target.result;
      };
      reader.readAsDataURL(file);
    } catch (e) {
      alert(e.message);
    }
  }
</script>