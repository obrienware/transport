<?php
require_once 'autoload.php';

use Transport\Location;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$location = new Location($id);
$locationId = $location->getId();

if (!is_null($id) && !$locationId)
{
  exit(Utils::showResourceNotFound());
}
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('locations:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($locationId): ?>
  <h2>Edit Location</h2>
  <input type="hidden" id="location-id" value="<?= $locationId ?>">
<?php else: ?>
  <h2>Add Location</h2>
  <input type="hidden" id="location-id" value="">
<?php endif; ?>
<input id="location-place-id" type="hidden" value="<?= $location->placeId ?>" />
<input id="location-osm-type" type="hidden" value="<?= $location->osmType ?>" />
<input id="location-osm-id" type="hidden" value="<?= $location->osmId ?>" />

<div class="row">

  <div class="col-12 col-xl-8 mb-3">
    <label for="location-name" class="form-label">Name</label>
    <input type="text" class="form-control" id="location-name" placeholder="Name" value="<?= $location->name ?>">
  </div>

  <div class="col-12 col-lg-6 col-xl-4 mb-3">
    <label for="location-short-name" class="form-label">Short Name</label>
    <input type="text" class="form-control" id="location-short-name" placeholder="Short Name" value="<?= $location->shortName ?>">
  </div>

  <div class="col-12 col-lg-6 col-xl-4 col-xxl-3 mb-3">
    <label for="location-type" class="form-label">Location Type</label>
    <div>
      <select id="location-type" onchange="$('#location-iata').closest('.col-iata').toggleClass('d-none', this.value != 'airport')" class="form-select">
        <option></option>
        <option <?= ($location->type == 'airport') ? 'selected' : '' ?> value="airport">Airport</option>
        <option <?= ($location->type == 'accommodations') ? 'selected' : '' ?> value="accommodations">Accommodations</option>
        <option <?= ($location->type == 'business') ? 'selected' : '' ?> value="business">Busines/Organization</option>
        <option <?= ($location->type == 'home') ? 'selected' : '' ?> value="home">Private Home</option>
        <option <?= ($location->type == 'virtual') ? 'selected' : '' ?> value="virtual">Virtual Location</option>
      </select>
    </div>
  </div>

  <div class="col-2 mb-3 col-iata <?= $location->type != 'airport' ? 'd-none' : '' ?>">
    <label for="location-iata" class="form-label">IATA</label>
    <input type="text" class="form-control" id="location-iata" placeholder="" value="<?= $location->IATA ?>">
  </div>

  <div class="col-12 col-xl-6 mb-3">
    <label for="location-map-address" class="form-label">Map Address</label>
    <input type="text" class="form-control" id="location-map-address" placeholder="Address" value="<?= $location->mapAddress ?>">
  </div>

  <div class="col-12"></div>

  <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
    <label for="location-lat" class="form-label">LAT</label>
    <input type="text" class="form-control" id="location-lat" placeholder="" value="<?= $location->lat ?>">
  </div>

  <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
    <label for="location-lon" class="form-label">LON</label>
    <input type="text" class="form-control" id="location-lon" placeholder="" value="<?= $location->lon ?>">
  </div>

  <div class="col-2 mb-3">
    <?php if ($location->placeId || $location->osmId): ?>
      <button id="btn-location-verify" class="btn btn-success mt-4 nowrap" title="Google verify">
        <i class="fa-duotone fa-solid fa-square-check"></i>
        Verified
      </button>
    <?php else: ?>
      <button id="btn-location-verify" class="btn btn-danger mt-4 nowrap" title="Google verify">
        <i class="fa-duotone fa-solid fa-square-xmark"></i>
        Verified
      </button>
    <?php endif; ?>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <label for="location-description" class="form-label">Location description</label>
    <textarea id="location-description" rows="5" class="form-control"><?= $location->description ?></textarea>
  </div>
</div>

<div class="row my-4">
  <div class="col d-flex justify-content-between">
    <?php if ($locationId): ?>
      <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:location', <?= $locationId ?>)">Delete</button>
    <?php endif; ?>
    <button class="btn btn-outline-secondary" id="btn-navigate">
      <i class="fa-duotone fa-solid fa-map-location"></i>
      Show map location
    </button>
    <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:location', '<?= $locationId ?>')">Save</button>
  </div>
</div>

<div class="row">
  <div class="col">
    <div class="ratio ratio-16x9">
      <iframe id="iframe-map" class="rounded-4" allowfullscreen></iframe>
    </div>
  </div>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'locations',
        url: 'section.list-locations.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:location')) {
      $(document).on('buttonSave:location', async (e, id) => {
        const locationId = id;
        const resp = await net.post('/api/post.save-location.php', {
          id: locationId,
          name: $('#location-name').cleanVal(),
          shortName: $('#location-short-name').cleanVal(),
          type: $('#location-type').val(),
          IATA: $('#location-iata').cleanUpperVal(),
          mapAddress: $('#location-map-address').cleanVal(),
          description: $('#location-description').cleanVal(),
          lat: $('#location-lat').cleanNumberVal(),
          lon: $('#location-lon').cleanNumberVal(),
          placeId: $('#location-place-id').val(),
          osmType: $('#location-osm-type').val(),
          osmId: $('#location-osm-id').val(),
          meta: $('#location-map-address').data('meta')
        });
        if (resp?.result) {
          $(document).trigger('locationChange');
          if (locationId) {
            ui.toastr.success('Location saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Location added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:location')) {
      $(document).on('buttonDelete:location', async (e, id) => {
        const locationId = id;
        if (await ui.ask('Are you sure you want to delete this location?')) {
          const resp = await net.get('/api/get.delete-location.php', {
            id: locationId
          });
          if (resp?.result) {
            $(document).trigger('locationChange');
            ui.toastr.success('Location deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting the location.', 'Error');
        }
      });
    }

    $('#location-lat, #location-lon').off('change').on('change', () => {
      $('#location-place-id').val('');
      $('#location-osm-type').val('');
      $('#location-osm-id').val('');
      mapVerified(false);
    });

    $('#btn-location-verify').off('click').on('click', async function() {
      let lat = $('#location-lat').cleanNumberVal();
      let lon = $('#location-lon').cleanNumberVal();
      const address = $('#location-map-address').cleanVal();
      const placeId = $('#location-place-id').val();
      const osmType = $('#location-osm-type').val();
      const osmId = $('#location-osm-id').val();

      // If we have an OSM ID we should use that second.
      // Failing which if we have co-ordinates we should use that third.
      // If we only have an address (lastly) we should use that.

      if (osmId) {
        const resp = await net.get('/api/get.geodata.php', {
          osmType,
          osmId
        });
        console.log(resp);
        if (resp.result === false) {
          console.error(resp.error);
          ui.toastr.error(resp.error, 'Error');
          return mapVerified(false);
        }
        const addr = resp.data.addresstags;
        $('#location-lat').val(resp.data.geometry.coordinates[1]);
        $('#location-lon').val(resp.data.geometry.coordinates[0]);
        $('#location-map-address').val(`${addr.housenumber} ${addr.street}, ${addr.city}, ${addr.state}, ${addr.postcode}`);
        return mapVerified(true);
      }

      if (lat && lon) {
        const latlng = `${lat},${lon}`;
        const resp = await net.get('/api/get.geodata.php', {
          latlng
        });
        console.log(resp);
        if (resp.result === false) {
          console.error(resp.error);
          ui.toastr.error(resp.error, 'Error');
          return mapVerified(false);
        }
        $('#location-lat').val(resp.data.lat);
        $('#location-lon').val(resp.data.lon);
        $('#location-osm-type').val(resp.data.osm_type);
        $('#location-osm-id').val(resp.data.osm_id);
        $('#location-map-address').val(resp.data.display_name);
        return mapVerified(true);
      }

      {
        const resp = await net.get('/api/get.geodata.php', {
          address
        });
        console.log(resp);
        if (resp.result === false) {
          console.error(resp.error);
          ui.toastr.error(resp.error, 'Error');
          return mapVerified(false);
        }
        $('#location-lat').val(resp.data.lat);
        $('#location-lon').val(resp.data.lon);
        $('#location-osm-type').val(resp.data.osm_type);
        $('#location-osm-id').val(resp.data.osm_id);
        $('#location-map-address').val(resp.data.display_name);
        return mapVerified(true);
      }
    });

    // $('select').selectpicker();

    $('#btn-navigate').off('click').on('click', function() {
      const iframe = document.getElementById('iframe-map');
      let lat = $('#location-lat').cleanNumberVal();
      let lon = $('#location-lon').cleanNumberVal();
      iframe.src = `https://www.google.com/maps?output=embed&z=15&q=${lat},${lon}`;
    });

  });

  function mapVerified($value) {
    if ($value) {
      $('#btn-location-verify').html(`<i class="fa-duotone fa-solid fa-square-check"></i> Verified`).removeClass('btn-danger').addClass('btn-success');
    } else {
      $('#btn-location-verify').html(`<i class="fa-duotone fa-solid fa-square-xmark"></i> Verified`).removeClass('btn-success').addClass('btn-danger');
    }
  }
</script>