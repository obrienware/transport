<?php
require_once 'autoload.php';

use Transport\Database;

$db = Database::getInstance();
$query = "SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND archived IS NULL ORDER BY created";
$params = ['vehicle_id' => $_GET['vehicleId']];
?>
<section id="section-vehicle-documents-list">

  <div class="container-fluid">
    <?php if ($rows = $db->get_rows($query, $params)): ?>

      <table class="table table-bordered table-striped">
        <?php foreach ($rows as $row): ?>
          <tr>
            <td>
              <div class="d-flex justify-content-between">
                <div>
                  <?php if ($row->file_type == 'application/pdf'): ?>
                    <i class="fa-solid fa-file-pdf me-2 fa-xl"></i>
                  <?php elseif ($row->file_type == 'image/png'): ?>
                    <i class="fa-solid fa-file-png me-2 fa-xl"></i>
                  <?php elseif ($row->file_type == 'image/jpg' || $row->file_type == 'image/jpeg'): ?>
                    <i class="fa-solid fa-file-jpg me-2 fa-xl"></i>
                  <?php else: ?>
                    <i class="fa-solid fa-file me-2 fa-xl"></i>
                  <?php endif; ?>
                  <a class="text-reset text-decoration-none text-capitalize" href="/documents/<?= $row->filename ?>" target="_blank"><?= $row->name ?></a>
                </div>
                <div><?= Date('m/d/Y', strtotime($row->created)) ?></div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php else: ?>

      <div class="d-flex my-3">
        <div class="alert alert-info mx-auto" role="alert">
          <i class="fa-solid fa-info-circle"></i>
          There are no documents for this vehicle at this time.
        </div>
      </div>

    <?php endif; ?>
  </div>

</section>

<section id="section-vehicle-document-upload">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div id="vehicle-document-dropzone" class="mb-3 dropzone">
          <div class="dz-message">
            <div>Drop files here, or click to upload</div>
            <div>(Valid file types are PDFs and Images)</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const vehicleId = '<?= $_GET['vehicleId'] ?>';
    let documentName = '';
    <?php $count = count($rows); ?>
    <?php if ($count > 0): ?>
      $('#document-count').html('<?= $count ?>').removeClass('d-none');
    <?php endif; ?>

    const myDropzone = new Dropzone('#vehicle-document-dropzone', {
      url: '/api/post.vehicle-document.php',
      autoProcessQueue: false,
      createImageThumbnails: false,
      acceptedFiles: 'image/*,application/pdf',
      disablePreviews: true,
      maxFilesize: (10 * 1024 * 1024), // in bytes, so 10MB
    });

    function reloadSection() {
      $('#section-vehicle-documents-list').parent('.tab-pane.active').load(`<?= $_SERVER['REQUEST_URI'] ?>`); // Refresh this page
    }

    myDropzone.on("sending", function(file, xhr, formData) {
      formData.append("vehicleId", vehicleId);
      formData.append("documentName", documentName);
    });

    myDropzone.on("addedfile", async file => {
      console.log("A file has been added");
      documentName = await ui.input('Please enter a name/description for this document');
      if (!documentName) return myDropzone.removeAllFiles(true);
      myDropzone.processQueue();
    });

    myDropzone.on('success', async function(file) {
      myDropzone.removeAllFiles(true);
      await ui.alertSuccess(documentName + ' has been uploaded.', 'Success');
      reloadSection();
    });

    myDropzone.on('error', async function(file, message) {
      myDropzone.removeAllFiles(true);
      await ui.alertError(message, 'Error');
    });
  });
</script>