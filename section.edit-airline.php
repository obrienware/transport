<?php
require_once 'autoload.php';

use Transport\Airline;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$airline = new Airline($id);

if (!is_null($id) && !$airline->getId())
{
  exit(Utils::showResourceNotFound());
}
$airlineId = $airline->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('airlines:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($airline->getId()): ?>
  <h2>Edit Airline</h2>
  <input type="hidden" id="airline-id" value="<?= $airline->getId() ?>">
<?php else: ?>
  <h2>Add Airline</h2>
  <input type="hidden" id="airline-id" value="">
<?php endif; ?>
<input type="hidden" id="airline-image-filename" value="<?= $airline->imageFilename ?>">


<div class="row">
  <div class="col-12 col-lg-8 col-xxl-6 mb-3">
    <label for="_airline-name" class="form-label">Name</label>
    <input type="text" class="form-control" id="_airline-name" placeholder="Airline Name" value="<?= $airline->name ?>">
  </div>

  <div class="col-3 col-lg-4 col-xxl-3 mb-3">
    <label for="_airline-flight-number-prefix" class="form-label">Flight Number Prefix</label>
    <input type="text" class="form-control" id="_airline-flight-number-prefix" value="<?= $airline->flightNumberPrefix ?>">
  </div>

  <div class="col-12 col-lg-6 col-xxl-3 mb-3">
    <label for="formFile" class="form-label">Upload Airline Image</label>
    <input class="form-control" type="file" id="airlineImage" accept=".jpg, .jpeg, .png">
  </div>

  <div><img id="airline-image" class="img-fluid d-none" style="max-height:40px"></div>

</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($airlineId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:airline', <?= $airlineId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:airline', '<?= $airlineId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'airlines',
        url: 'section.list-airlines.php',
        forceReload: true
      });
    }

    // const airlineId = <?= $airline->getId() ?? 'null' ?>;
    // let airlineImage = '<?= $airline->imageFilename ?>';

    function getData() {
      const airlineId = $('#airline-id').val();
      const airlineImage = $('#airline-image-filename').val();

      const formData = new FormData();
      if (airlineId) formData.append('id', airlineId);
      formData.append('name', input.cleanProperVal('#_airline-name'));
      formData.append('flightNumberPrefix', input.cleanUpperVal('#_airline-flight-number-prefix'));
      if ($('#airlineImage')[0].files[0]) formData.append('image', $('#airlineImage')[0].files[0]);
      return formData;
    }

    if ($('#airline-image-filename').val() !== '') {
      const airlineImage = $('#airline-image-filename').val();
      const url = `/images/airlines/${airlineImage}`;
      $('#airline-image').attr('src', url).removeClass('d-none');
    }

    if (!documentEventExists('buttonSave:airline')) {
      $(document).on('buttonSave:airline', async (e, id) => {
        const airlineId = id;
        const data = getData();
        const response = await fetch('/api/post.save-airline.php', {
          method: 'POST',
          body: data
        });
        const resp = await response.json();

        if (resp?.result) {
          $(document).trigger('airlineChange');
          if (airlineId) {
            ui.toastr.success('Airline saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Airline added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp?.errors[2], 'Error');
      });
    }

    if (!documentEventExists('buttonDelete:airline')) {
      $(document).on('buttonDelete:airline', async (e, id) => {
        const airlineId = id;
        if (await ui.ask('Are you sure you want to delete this airline?')) {
          const resp = await net.get('/api/get.delete-airline.php', {
            id: airlineId
          });
          if (resp?.result) {
            $(document).trigger('airlineChange');
            ui.toastr.success('Airline deleted.', 'Success');
            return backToList();
          }
          ui.toastr.error('There seems to be a problem deleting airline.', 'Error');
        }
      });
    }

  });
</script>