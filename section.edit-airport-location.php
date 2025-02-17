<?php
require_once 'autoload.php';

use Transport\{Airline, Airport, AirportLocation};
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$airportLocation = new AirportLocation($id);

if (!is_null($id) && !$airportLocation->getId())
{
  exit(Utils::showResourceNotFound());
}
$airportLocationId = $airportLocation->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('airportLocations:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($airportLocation->getId()): ?>
  <h2>Edit Airport Location</h2>
  <input type="hidden" id="airport-location-id" value="<?= $airportLocationId ?>">
<?php else: ?>
  <h2>Add Airport Location</h2>
  <input type="hidden" id="airport-location-id" value="">
<?php endif; ?>

<div class="row">

  <div class="col-12">
    <label for="_airport" class="form-label">Airport</label>
    <select class="form-select" id="_airport">
      <option value="">Select Airport</option>
      <?php foreach (Airport::getAll() as $row): ?>
        <option value="<?= $row->id ?>" <?= ($row->id == $airportLocation->airportId) ? 'selected' : '' ?>><?= $row->name ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12">
    <label for="_airline" class="form-label">Airline</label>
    <select class="form-select" id="_airline">
      <option value="">Select Airline</option>
      <?php foreach (Airline::getAll() as $row): ?>
        <option value="<?= $row->id ?>" <?= ($row->id == $airportLocation->airlineId) ? 'selected' : '' ?>><?= $row->name ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12">
    <label for="_type" class="form-label">Type</label>
    <select class="form-select" id="_type">
      <option value="">Select Type</option>
      <option value="Arrival" <?= ($airportLocation->type == 'Arrival') ? 'selected' : '' ?>>Arrival</option>
      <option value="Departure" <?= ($airportLocation->type == 'Departure') ? 'selected' : '' ?>>Departure</option>
    </select>
  </div>

  <div class="col-12">
    <label for="_location" class="form-label">Location</label>
    <input
      type="text"
      class="form-control"
      id="_location"
      placeholder="Location"
      value="<?= $airportLocation->location->name ?>"
      data-id="<?= $airportLocation->locationId ?>"
      data-value="<?= $airportLocation->location->name ?>">
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($airportLocationId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:airportLocation', <?= $airportLocationId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:airportLocation', '<?= $airportLocationId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'airportLocations',
        url: 'section.list-airport-locations.php',
        forceReload: true
      });
    }

    buildAutoComplete({
      selector: '_location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });

    async function getData() {
      const airportLocationId = $('#airport-location-id').val();
      const data = {};
      if (airportLocationId) data.id = airportLocationId;
      data.airportId = $('#_airport').val();
      data.airlineId = $('#_airline').val();
      data.type = $('#_type').val();
      data.locationId = $('#_location').data('id');
      return data;
    }

    if (!documentEventExists('buttonSave:airportLocation')) {
      $(document).on('buttonSave:airportLocation', async (e, id) => {
        const airportLocationId = id;
        const data = await getData();
        const resp = await net.post('/api/post.save-airport-location.php', data);
        if (resp?.result) {
          $(document).trigger('airportLocationChange');
          if (airportLocationId) {
            ui.toastr.success('Airport Location saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Airport Location added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:airportLocation')) {
      $(document).on('buttonDelete:airportLocation', async (e, id) => {
        const airportLocationId = id;
        if (await ui.ask('Are you sure you want to delete this airport location?')) {
          const resp = await net.get('/api/get.delete-airport-location.php', {
            id: airportLocationId
          });
          if (resp?.result) {
            $(document).trigger('airportLocationChange');
            ui.toastr.success('Airport Location deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting airport location.', 'Error');
        }
      });
    }
  });
</script>