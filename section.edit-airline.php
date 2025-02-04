<?php
require_once 'autoload.php';

use Transport\Airline;
use Transport\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$airline = new Airline($id);

if (!is_null($id) && !$airline->getId()) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <?php if ($airline->getId()): ?>
    <h2>Edit Airline</h2>
  <?php else: ?>
    <h2>Add Airline</h2>
  <?php endif; ?>
  <div>

    <div class="row">
      <div class="col mb-3">
        <label for="_airline-name" class="form-label">Name</label>
        <input type="text" class="form-control" id="_airline-name" placeholder="Airline Name" value="<?=$airline->name?>">
      </div>
    </div>

    <div class="row">
      <div class="col-3 mb-3">
        <label for="_airline-flight-number-prefix" class="form-label">Flight Number Prefix</label>
        <input type="text" class="form-control" id="_airline-flight-number-prefix" value="<?=$airline->flightNumberPrefix?>">
      </div>
    </div>

    <div class="row">
      <div><img id="airline-image" class="img-fluid d-none" style="max-height:40px"></div>
      <div class="mb-3">
        <label for="formFile" class="form-label">Upload Airline Image</label>
        <input class="form-control" type="file" id="airlineImage">
      </div>
    </div>

    <div class="row my-4">
      <div class="col d-flex justify-content-between">
        <?php if ($airline->getId()): ?>
          <button class="btn btn-outline-danger px-4" id="btn-delete-airline">Delete</button>
        <?php endif; ?>
        <button class="btn btn-primary px-4" id="btn-save-airline">Save</button>
      </div>
    </div>

  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const airlineId = <?=$airline->getId() ?? 'null'?>;
    let airlineImage = '<?=$airline->imageFilename?>';

    function reloadSection () {
      $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    function getData () {
      const formData = new FormData();
      if (airlineId) formData.append('id', airlineId);
      formData.append('name', input.cleanProperVal('#_airline-name'));
      formData.append('flightNumberPrefix', input.cleanUpperVal('#_airline-flight-number-prefix'));
      if ($('#airlineImage')[0].files[0]) formData.append('image', $('#airlineImage')[0].files[0]);
      return formData;
    }

    if (airlineImage !== '') {
      const url = `/images/airlines/${airlineImage}`;
      $('#airline-image').attr('src', url).removeClass('d-none');
    }

    $('#btn-save-airline').on('click', async ƒ => {
      const data = getData();
      const response = await fetch('/api/post.save-airline.php', {
        method: 'POST',
        body: data
      });
      const resp = await response.json();

      if (resp?.result) {
        $(document).trigger('airlineChange', {airlineId});
        app.closeOpenTab();
        if (airlineId) return ui.toastr.success('Airline saved.', 'Success');
        return ui.toastr.success('Airline added.', 'Success')
      }
      ui.toastr.error(resp?.errors[2], 'Error');
    });

    $('#btn-delete-airline').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this airline?')) {
        const resp = await net.get('/api/get.delete-airline.php', {
          id: airlineId
        });
        if (resp?.result) {
          $(document).trigger('airlineChange', {airlineId});
          app.closeOpenTab();
          return ui.toastr.success('Airline deleted.', 'Success')
        }
        ui.toastr.error('There seems to be a problem deleting airline.', 'Error');
      }
    });

  });

</script>
