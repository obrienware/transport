<?php
require_once 'autoload.php';

use Transport\Airport;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$airport = new Airport($id);

if (!is_null($id) && !$airport->getId())
{
  exit(Utils::showResourceNotFound());
}

$airportId = $airport->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('airports:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($airport->getId()): ?>
  <h2>Edit Airport</h2>
  <input type="hidden" id="airport-id" value="<?= $airportId ?>">
<?php else: ?>
  <h2>Add Airport</h2>
  <input type="hidden" id="airport-id" value="">
<?php endif; ?>

<div class="row">

  <div class="col-3 mb-3">
    <label for="_iata" class="form-label">IATA</label>
    <input type="text" class="form-control" id="_iata" placeholder="Airport Code" value="<?= $airport->IATA ?>">
  </div>

  <div class="col-12 col-lg-9 mb-3">
    <label for="_airport-name" class="form-label">Name</label>
    <input type="text" class="form-control" id="_airport-name" placeholder="Airport Name" value="<?= $airport->name ?>">
  </div>

  <div class="col-12 col-md-4 col-xl-3 mb-3">
    <label for="_airport-leadtime" class="form-label">Lead Time (in minutes)</label>
    <input type="number" class="form-control" id="_airport-leadtime" placeholder="" value="<?= $airport->leadTime ?>">
  </div>

  <div class="col-12 col-md-4 col-xl-3 mb-3">
    <label for="_airport-traveltime" class="form-label">Travel Time (in minutes)</label>
    <input type="number" class="form-control" id="_airport-traveltime" placeholder="" value="<?= $airport->travelTime ?>">
  </div>

  <div class="col-12 col-xl-6 mb-3">
    <div class="mb-3">
      <label for="_airport-staging-location" class="form-label">Staging Location</label>
      <input
        type="text"
        class="form-control"
        id="_airport-staging-location"
        placeholder="Staging Location"
        value="<?= $airport->stagingLocation->name ?>"
        data-id="<?= $airport->stagingLocationId ?>"
        data-value="<?= $airport->stagingLocation->name ?>">
      <div class="invalid-feedback">Please make a valid selection</div>
    </div>
  </div>

  <div class="col-12"></div>

  <div class="col-12 col-xxl-6">
    <div class="mb-3">
      <label for="_airport-arrival-instructions-small" class="form-label">Arrival Instructions (3pax or less)</label>
      <textarea class="form-control font-handwriting" id="_airport-arrival-instructions-small" rows="7" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $airport->arrivalInstructions ?></textarea>
    </div>
  </div>
  <div class="col-12 col-xxl-6">
    <div class="mb-3">
      <label for="_airport-arrival-instructions-group" class="form-label">Arrival Instructions (group)</label>
      <textarea class="form-control font-handwriting" id="_airport-arrival-instructions-group" rows="7" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $airport->arrivalInstructionsGroup ?></textarea>
    </div>
  </div>
</div>


<div class="d-flex justify-content-between mt-3">
  <?php if ($airportId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:airport', <?= $airportId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:airport', '<?= $airportId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'airports',
        url: 'section.list-airports.php',
        forceReload: true
      });
    }

    function getData() {
      const airportId = $('#airport-id').val();
      const data = {
        id: airportId,
        iata: $('#_iata').cleanUpperVal(),
        name: $('#_airport-name').cleanProperVal(),
        stagingLocationId: $('#_airport-staging-location').data('id'),
        leadTime: $('#_airport-leadtime').cleanDigitsVal(),
        travelTime: $('#_airport-traveltime').cleanDigitsVal(),
        arrivalInstructions: $('#_airport-arrival-instructions-small').cleanVal(),
        arrivalInstructionsGroup: $('#_airport-arrival-instructions-group').cleanVal(),
      };
      return data;
    }

    buildAutoComplete({
      selector: '_airport-staging-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });

    if (!documentEventExists('buttonSave:airport')) {
      $(document).on('buttonSave:airport', async (e, id) => {
        const airportId = id;
        const data = getData();
        const resp = await net.post('/api/post.save-airport.php', data);
        if (resp?.result) {
          $(document).trigger('airportChange');
          if (airportId) {
            ui.toastr.success('Airport saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Airport added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:airport')) {
      $(document).on('buttonDelete:airport', async (e, id) => {
        const airportId = id;
        if (await ui.ask('Are you sure you want to delete this airport?')) {
          const resp = await net.get('/api/get.delete-airport.php', {
            id: airportId
          });
          if (resp?.result) {
            $(document).trigger('airportChange');
            ui.toastr.success('Airport deleted.', 'Success');
            return backToList();
          }
          ui.toastr.error('There seems to be a problem deleting airport.', 'Error');
        }
      });
    }
  });
</script>