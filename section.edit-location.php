<?php
require_once 'autoload.php';

use Transport\Location;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$location = new Location($id);
$locationId = $location->getId();

if (!is_null($id) && !$locationId) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <?php if ($locationId): ?>
    <h2>Edit Location</h2>
  <?php else: ?>
    <h2>Add Location</h2>
  <?php endif; ?>
  <div>
    <input id="location-place-id" type="hidden" value="<?=$location->placeId?>" />
    <div class="row">
      <div class="col mb-3">
        <label for="location-name" class="form-label">Name</label>
        <input type="text" class="form-control" id="location-name" placeholder="Name" value="<?=$location->name?>">
      </div>
      <div class="col mb-3">
        <label for="location-short-name" class="form-label">Short Name</label>
        <input type="text" class="form-control" id="location-short-name" placeholder="Short Name" value="<?=$location->shortName?>">
      </div>
      <div class="col-auto mb-3">
        <label for="location-type" class="form-label">Location Type</label>
        <div>
          <select id="location-type">
            <option></option>
            <option <?=($location->type == 'airport') ? 'selected':''?> value="airport">Airport</option>
            <option <?=($location->type == 'accommodations') ? 'selected':''?> value="accommodations">Accommodations</option>
            <option <?=($location->type == 'business') ? 'selected':''?> value="business">Busines/Organization</option>
            <option <?=($location->type == 'home') ? 'selected':''?> value="home">Private Home</option>
          </select>
        </div>
      </div>
      <div class="col-2 mb-3">
        <label for="location-iata" class="form-label">IATA</label>
        <input type="text" class="form-control" id="location-iata" placeholder="" value="<?=$location->IATA?>">
      </div>
    </div>

    <div class="row">
      <div class="col mb-3">
        <label for="location-map-address" class="form-label">Map Address</label>
        <input type="text" class="form-control" id="location-map-address" placeholder="Address" value="<?=$location->mapAddress?>">
      </div>
      <div class="col-3 mb-3">
        <label for="location-lat" class="form-label">LAT</label>
        <input type="text" class="form-control" id="location-lat" placeholder="" value="<?=$location->lat?>">
      </div>
      <div class="col-3 mb-3">
        <label for="location-lon" class="form-label">LON</label>
        <input type="text" class="form-control" id="location-lon" placeholder="" value="<?=$location->lon?>">
      </div>
      <div class="col-auto">
        <?php if ($location->placeId): ?>
          <button id="btn-location-verify" class="btn btn-success mt-4" title="Google verify">
            <i class="fa-duotone fa-solid fa-square-check"></i>
            Verified
          </button>
        <?php else: ?>
          <button id="btn-location-verify" class="btn btn-danger mt-4" title="Google verify">
            <i class="fa-duotone fa-solid fa-square-xmark"></i>
            Verified
          </button>
        <?php endif;?>
      </div>
    </div>

    <div class="row">
      <div class="col mb-4">
        <textarea id="location-description" rows="5" class="form-control"><?=$location->description?></textarea>
      </div>
    </div>

    <div class="row my-4">
      <div class="col d-flex justify-content-between">
        <?php if ($locationId): ?>
          <button class="btn btn-outline-danger px-4" id="btn-delete-location">Delete</button>
        <?php endif; ?>
        <button class="btn btn-outline-secondary" id="btn-navigate">
          <i class="fa-duotone fa-solid fa-map-location"></i>
          Show map location
        </button>
        <button class="btn btn-primary px-4" id="btn-save-location">Save</button>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col">
      <div class="ratio ratio-16x9">
        <iframe id="iframe-map" class="rounded-4" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const locationId = <?=$locationId ?: 'null'?>;
    $('#btn-save-location').off('click').off('click').on('click', async ƒ => {
      const resp = await net.post('/api/post.save-location.php', {
        id: locationId,
        name: input.cleanVal('#location-name'),
        shortName: input.cleanVal('#location-short-name'),
        type: input.val('#location-type'),
        IATA: input.cleanUpperVal('#location-iata'),
        mapAddress: input.cleanVal('#location-map-address'),
        description: input.cleanVal('#location-description'),
        lat: input.cleanNumberVal('#location-lat'),
        lon: input.cleanNumberVal('#location-lon'),
        placeId: input.val('#location-place-id'),
        meta: $('#location-map-address').data('meta')
      });
      if (resp?.result) {
        $(document).trigger('locationChange', {locationId});
        app.closeOpenTab();
        if (locationId) return ui.toastr.success('Location saved.', 'Success');
        return ui.toastr.success('Location added.', 'Success')
      }
      ui.toastr.error(resp .result.errors[2], 'Error');
      console.log(resp);
    });

    $('#btn-delete-location').off('click').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this location?')) {
        const resp = await net.get('/api/get.delete-location.php', {
          id: locationId
        });
        if (resp?.result) {
          $(document).trigger('locationChange', {locationId});
          app.closeOpenTab();
          return ui.toastr.success('Location deleted.', 'Success')
        }
        console.log(resp);
        ui.toastr.error('There seems to be a problem deleting the location.', 'Error');
      }
    });

    $('#location-lat, #location-lon').off('change').on('change', () => {
      $('#location-place-id').val('');
      mapVerified(false);
    });

    $('#btn-location-verify').off('click').on('click', async function () {
      let lat = input.cleanNumberVal('#location-lat');
      let lon = input.cleanNumberVal('#location-lon');
      const address = input.cleanVal('#location-map-address');
      const placeId = input.val('#location-place-id');

      // If we have a place ID we should use that first.
      // Failing which if we have co-ordinates we should use that second
      // If we only have an address (lastly) we should use that.

      if (placeId) {
        const resp = await net.get('/api/get.geodata.php', {placeId});
        $('#location-lat').val(resp?.location?.latitude);
        $('#location-lon').val(resp?.location?.longitude);
        $('#location-place-id').val(resp.id);
        $('#location-map-address').val(resp?.formattedAddress);
        return mapVerified(true);
      }

      if (lat && lon) {
        const latlng = `${lat},${lon}`;
        const resp = await net.get('/api/get.geodata.php', {latlng});
        $('#location-lat').val(resp?.results[0]?.geometry?.location?.lat);
        $('#location-lon').val(resp?.results[0]?.geometry?.location?.lng);
        $('#location-place-id').val(resp?.results[0]?.place_id);
        $('#location-map-address').val(resp?.results[0].formatted_address);
        return mapVerified(true);
      }
      {
        const resp = await net.get('/api/get.geodata.php', {address});
        $('#location-lat').val(resp?.results[0]?.geometry?.location?.lat);
        $('#location-lon').val(resp?.results[0]?.geometry?.location?.lng);
        $('#location-place-id').val(resp?.results[0]?.place_id);
        $('#location-map-address').val(resp?.results[0].formatted_address);
        return mapVerified(true);
      }
    });

    $('select').selectpicker();

    $('#btn-navigate').off('click').on('click', function () {
      const iframe = document.getElementById('iframe-map');
      let lat = input.cleanNumberVal('#location-lat');
      let lon = input.cleanNumberVal('#location-lon');
      iframe.src = `https://www.google.com/maps?output=embed&z=15&q=${lat},${lon}`;
    });

  });

  function mapVerified ($value) {
    if ($value) {
      $('#btn-location-verify').html(`<i class="fa-duotone fa-solid fa-square-check"></i> Verified`).removeClass('btn-danger').addClass('btn-success');
    } else {
      $('#btn-location-verify').html(`<i class="fa-duotone fa-solid fa-square-xmark"></i> Verified`).removeClass('btn-success').addClass('btn-danger');
    }
  }

</script>
