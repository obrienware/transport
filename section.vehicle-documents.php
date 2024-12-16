<?php
require_once 'class.data.php';
$db = new data();
$sql = "SELECT * FROM vehicle_documents WHERE vehicle_id = :vehicle_id AND archived IS NULL ORDER BY uploaded";
$data = ['vehicle_id' => $_REQUEST['vehicleId']];
?>
<section id="section-vehicle-documents-list">

  <div class="container">
    <?php if ($rs = $db->get_results($sql, $data)): ?>

      <table class="table table-bordered table-striped">
        <?php foreach ($rs as $item): ?>
          <tr>
            <td>
              <a class="text-reset text-decoration-none text-capitalize" href="/documents/<?=$item->filename?>" target="_blank"><?=$item->name?></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php else: ?>

      <div class="alert alert-info">
        There are no documents for this vehicle at this time.
      </div>

    <?php endif; ?>
  </div>

</section>

<section id="section-vehicle-document-upload">
    <div class="container">
      <div class="row">
        <div class="col">
          <div id="vehicle-document-dropzone" class="mb-3 dropzone">
          </div>
        </div>
      </div>
    </div>
</section>

<script type="text/javascript">

$(async ƒ => {

  const vehicleId = '<?=$_REQUEST['vehicleId']?>';
  let documentName = '';

  const myDropzone = new Dropzone('#vehicle-document-dropzone', {
    url: '/api/post.vehicle-document.php',
    autoProcessQueue: false,
    createImageThumbnails: false,
    acceptedFiles: 'image/*,application/pdf',
    disablePreviews: true,
  });

  function reloadSection () {
    $('#section-vehicle-documents-list').parent('.tab-pane.active').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
  }

  myDropzone.on("sending", function(file, xhr, formData) {
    formData.append("vehicleId", vehicleId);
    formData.append("documentName", documentName);
  });

  myDropzone.on("addedfile", async file => {
    console.log("A file has been added");
    documentName = await input('Please enter a name/description for this document');
    if (!documentName) return myDropzone.removeAllFiles(true);
    myDropzone.processQueue();
  });

  myDropzone.on('success', async function(file) {
    myDropzone.removeAllFiles(true);
    await alertSuccess(documentName + ' has been uploaded.', 'Success');
    reloadSection();
  });
});

</script>