<?php
require_once 'autoload.php';

use Transport\Trip;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$trip = new Trip($id);
if ($trip->getId() && !$trip->confirmed && $trip->originalRequest) $originalRequest = json_decode($trip->originalRequest);

if (!is_null($id) && !$trip->getId())
{
  exit(Utils::showResourceNotFound());
}
?>
<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('loadMainSection', { sectionId: 'trips', url: 'section.list-trips.php', forceReload: true });">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<h2>Edit Trip</h2>
<input type="hidden" id="trip-end-date" value="<?= $trip->endDate ?>" />
<input type="hidden" id="tripId" name="tripId" value="<?= $trip->getId() ?>" />

<div class="grid-container">

  <!-- Trip Summary Card -->
  <card class="card text-bg-secondary" style="grid-column: 1 / -1;">
    <div class="card-header d-flex justify-content-between">
      <div style="font-weight:100" class="fs-4"><?= $trip->getId() ?>: <?= $trip->summary ?></div>
      <div>
        <?php if ($trip->isConfirmed()): ?>
          <span class="badge bg-success" style="font-size:medium; font-weight:200">Confirmed</span>
        <?php else: ?>
          <span class="badge bg-danger" style="font-size:medium; font-weight:200">Unconfirmed</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="card-body text-bg-light container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12 col-lg-7 col-xxl-5 d-none">
          <label for="trip-summary" class="form-label">Summary</label>
          <input type="text" class="form-control" id="trip-summary" placeholder="Summary" value="<?= $trip->summary ?>">
        </div>
        <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
          <label for="trip-pickup-date" class="form-label" data-bs-toggle="tooltip" data-bs-title="The point at which you'd meet the guest/group">Date / Time</label>
          <input type="datetime-local" class="form-control" id="trip-pickup-date" value="<?= $trip->pickupDate ?>" min="<?= date('Y-m-d\TH:i') ?>">
        </div>
        <div class="w-100 d-none d-sm-block d-md-none"></div>
        <div class="col-sm-6 col-lg-4 col-xl-3 col-xxl-2">
          <label for="trip-lead-time" class="form-label" data-bs-toggle="tooltip" data-bs-title="When the actual trip starts">Lead Time</label>
          <div class="input-group">
            <input type="text" class="form-control" id="trip-lead-time" value="<?= round(abs((strtotime($trip->startDate) - strtotime($trip->pickupDate)) / 60 / 60), 2) ?>" placeholder="e.g. 1.5" />
            <span class="input-group-text">hour(s)</span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-3 col-xxl-2">
          <label for="trip-duration" class="form-label" data-bs-toggle="tooltip" data-bs-title="From start to end">Total Trip Duration</label>
          <div class="input-group">
            <input type="text" class="form-control" id="trip-duration" value="<?= $trip->endDate ? round(abs((strtotime($trip->endDate) - strtotime($trip->startDate)) / 60 / 60), 2) : '' ?>" placeholder="e.g. 1.5" />
            <span class="input-group-text">hour(s)</span>
          </div>
          <input type="hidden" id="trip-end-date" value="<?= $trip->endDate ?>" />
        </div>
      </div>
    </div>
  </card>


  <!-- Pick Up Card -->
  <card class="card bg-dark-subtle">
    <div class="card-header">
      <i class="fa-solid fa-up"></i>
      Pick Up
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12">
          <label for="trip-guests" class="form-label">Guest(s) / Group (Name)</label>
          <input type="text" class="form-control" id="trip-guests" placeholder="" value="<?= $trip->guests ?>">
        </div>
        <div class="col-12 col-lg-9">
          <label for="trip-guest" class="form-label">Contact Person</label>
          <input
            type="text"
            class="form-control"
            id="trip-guest"
            placeholder="Contact"
            value="<?= $trip->guestId ? $trip->guest->getName() : '' ?>"
            data-value="<?= $trip->guestId ? $trip->guest->getName() : '' ?>"
            data-id="<?= $trip->guestId ?>">
          <div class="invalid-feedback">Please make a valid selection</div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <label for="trip-passengers" class="form-label">Passengers</label>
          <input type="number" class="form-control" id="trip-passengers" placeholder="# Passengers" value="<?= $trip->passengers ?>">
        </div>
        <div class="col-12">
          <label for="trip-pu-location" class="form-label">Location</label>
          <input
            type="text"
            class="form-control"
            id="trip-pu-location"
            placeholder="Pick Up Location"
            value="<?= $trip->puLocation->name ?>"
            data-id="<?= $trip->puLocationId ?>"
            data-value="<?= $trip->puLocation->name ?>"
            data-type="<?= $trip->puLocation ? $trip->puLocation->type : '' ?>">
          <div class="invalid-feedback">Please make a valid selection</div>
          <?php if (isset($originalRequest->type) && $originalRequest->type == 'airport-pickup'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->airport ?></div>
          <?php elseif (isset($originalRequest->type) && $originalRequest->type == 'airport-dropoff'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->location ?></div>
          <?php elseif (isset($originalRequest->type) && $originalRequest->type == 'point-to-point'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->location ?></div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </card>


  <!-- Drop Off Card -->
  <card class="card bg-dark-subtle">
    <div class="card-header">
      <i class="fa-solid fa-down"></i>
      Drop Off
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12">
          <label for="trip-do-location" class="form-label">Location</label>
          <input
            type="text"
            class="form-control"
            id="trip-do-location"
            placeholder="Drop Off Location"
            value="<?= $trip->doLocation->name ?>"
            data-value="<?= $trip->doLocation->name ?>"
            data-id="<?= $trip->doLocationId ?>"
            data-type="<?= $trip->doLocation ? $trip->doLocation->type : '' ?>">
          <div class="invalid-feedback">Please make a valid selection</div>
          <?php if (isset($originalRequest->type) && $originalRequest->type == 'airport-dropoff'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->airport ?></div>
          <?php elseif (isset($originalRequest->type) && $originalRequest->type == 'airport-pickup'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->location ?></div>
          <?php elseif (isset($originalRequest->type) && $originalRequest->type == 'point-to-point'): ?>
            <div class="form-text text-primary"><b>Requestor:</b> <?= $originalRequest->destination ?></div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </card>

  <!-- Flight Details Card -->
  <card id="flight-info" class="card bg-dark-subtle d-none">
    <div class="card-header d-flex justify-content-between">
      <div>
        <i class="fa-duotone fa-solid fa-plane-tail"></i>
        Flight Details
      </div>
      <div id="flight-verified" class="d-none" title="Verified">
      <i class="fa-solid fa-certificate fa-xl text-danger"></i>
      </div>
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12">
          <label for="trip-airline-id" class="form-label">Airline</label>
          <div>
            <select id="trip-airline-id" data-live-search="true" show-tick class="form-control" data-size="5" data-container="#vehicle-edit-container"></select>
          </div>
        </div>
        <div id="airline-image" class="col-6 pt-4">
          <?php if ($trip->airline->imageFilename): ?>
            <img src="/images/airlines/<?= $trip->airline->imageFilename ?>" class="img-fluid">
          <?php endif; ?>
        </div>
        <div class="col-6">
          <label for="trip-flight-number" class="form-label">Flight Number</label>
          <div class="input-group">
            <span class="input-group-text" id="flight-number-prefix"><?= $trip->airline ? $trip->airline->flightNumberPrefix : '&nbsp;&nbsp;' ?></span>
            <input type="text" class="form-control" id="trip-flight-number" placeholder="Flight number without the prefix" value="<?= $trip->flightNumber ?>">
          </div>
        </div>
        <div class="offset-1 col-10 d-none" id="eta-section">
          <div class="input-group mt-3">
            <span class="input-group-text">ETA</span>
            <input type="datetime-local" class="form-control" id="trip-eta" value="<?= $trip->ETA ?>" min="<?= date('Y-m-d\TH:i') ?>">
          </div>
        </div>

        <div class="offset-1 col-10 d-none" id="etd-section">
          <div class="input-group mt-3">
            <span class="input-group-text">ETD</span>
            <input type="datetime-local" class="form-control" id="trip-etd" value="<?= $trip->ETD ?>" min="<?= date('Y-m-d\TH:i') ?>">
          </div>
        </div>
      </div>
    </div>
  </card>


  <!-- Driver / Vehicle Card -->
  <card class="card bg-dark-subtle">
    <div class="card-header">
      Driver / Vehicle
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12">
          <label for="trip-driver-id" class="form-label">Driver</label>
          <div><select id="trip-driver-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
        </div>
        <div class="col-12">
          <label for="trip-vehicle-id" class="form-label">Vehicle</label>
          <div><select id="trip-vehicle-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
        </div>
        <div class="col-12">
          <label for="trip-vehicle-pu-options" class="form-label">Where to pick vehicle up from</label>
          <div>
            <select id="trip-vehicle-pu-options" class="form-control" data-container="#vehicle-edit-container">
              <option></option>
              <option value="pick up from staging" <?= $trip->vehiclePUOptions == 'pick up from staging' ? 'selected' : '' ?>>Pick up from staging</option>
              <option value="guest will have vehicle" <?= $trip->vehiclePUOptions == 'guest will have vehicle' ? 'selected' : '' ?>>Guest will have vehicle</option>
              <option value="commence from current location" <?= $trip->vehiclePUOptions == 'commence from current location' ? 'selected' : '' ?>>Commence from current location</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <label for="trip-vehicle-do-options" class="form-label">Where to leave vehicle</label>
          <div>
            <select id="trip-vehicle-do-options" class="form-control" data-container="#vehicle-edit-container">
              <option></option>
              <option value="return to staging" <?= $trip->vehicleDOOptions == 'return to staging' ? 'selected' : '' ?>>Return to staging</option>
              <option value="leave vehicle with guest" <?= $trip->vehicleDOOptions == 'leave vehicle with guest' ? 'selected' : '' ?>>Leave vehicle with guest(s)</option>
              <option value="remain at destination" <?= $trip->vehicleDOOptions == 'remain at destination' ? 'selected' : '' ?>>Remain at destination</option>
            </select>
          </div>
        </div>
        <div class="col-12">
          <label for="trip-driver-notes" class="form-label">Notes for the driver</label>
          <textarea class="form-control font-handwriting" id="trip-driver-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $trip->driverNotes ?></textarea>
        </div>
      </div>
    </div>
  </card>


  <!-- Requestor Card -->
  <card class="card bg-dark-subtle">
    <div class="card-header">
      Requestor
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">
        <div class="col-12">
          <label for="trip-requestor" class="form-label">Requestor</label>
          <input
            type="text"
            class="form-control"
            id="trip-requestor"
            placeholder="Requestor"
            value="<?= $trip->requestorId ? $trip->requestor->getName() : '' ?>"
            data-value="<?= $trip->requestorId ? $trip->requestor->getName() : '' ?>"
            data-id="<?= $trip->requestorId ?>" />
          <div class="invalid-feedback">Please make a valid selection</div>
        </div>
        <div class="col-12">
          <label for="trip-guest-notes" class="form-label">Notes for the guest</label>
          <textarea class="form-control font-handwriting" id="trip-guest-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $trip->guestNotes ?></textarea>
        </div>
      </div>
    </div>
  </card>


  <!-- General Card -->
  <card class="card bg-dark-subtle">
    <div class="card-header">
      General
    </div>
    <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
      <div class="row">

        <div class="col-12 col-lg-4 mb-3">
          <div class="pretty p-svg p-curve">
            <input class="~form-check-input" type="checkbox" value="1" id="trip-require-more-info" <?= $trip->requireMoreInfo ? 'checked' : '' ?> />
            <div class="state p-danger">
              <!-- svg path -->
              <svg class="svg svg-icon" viewBox="0 0 20 20">
                <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
              </svg>
              <label>Require More Information</label>
            </div>
          </div>
        </div>

        <div class="col-12">
          <label for="trip-general-notes" class="form-label">General Notes</label>
          <textarea class="form-control font-handwriting" id="trip-general-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $trip->generalNotes ?></textarea>
        </div>
      </div>
    </div>
  </card>

</div>




<div class="d-flex justify-content-between mt-3">
  <?php if ($trip->getId()): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:trip', <?= $trip->getId() ?>)">Delete</button>
    <div class="mx-auto" style="color:lightslategray; font-size:small">
      <div>Created: <?= (new DateTime($trip->created))->format('m/d/Y H:ia') ?> (<?= ucwords($trip->createdBy) ?>)</div>
      <?php if ($trip->modified): ?>
        <div>Modified: <?= (new DateTime($trip->modified))->format('m/d/Y H:ia') ?> (<?= ucwords($trip->modifiedBy) ?>)</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (!$trip->isConfirmed()): ?>
    <button class="btn btn-primary ms-auto me-2" onclick="$(document).trigger('buttonSaveAndConfirm:trip', '<?= $trip->getId() ?>')">Save & Confirm</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:trip', '<?= $trip->getId() ?>')">Save</button>
</div>





<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('trip:reloadList');;
    }

    const airlines = await net.get('/api/get.resource-airlines.php');

    $('#trip-airline-id').append($('<option>'));
    $.each(airlines, function(i, item) {
      $('#trip-airline-id').append($('<option>', {
        value: item.id,
        text: item.name
      }));
    });

    $('#trips select').selectpicker({
      container: false
    });

    async function loadResources() {
      const tripId = $('#tripId').val() ? $('#tripId').intVal() : null;
      const leadTime = isNaN(parseFloat($('#trip-lead-time').cleanNumberVal())) ? 0 : parseInt($('#trip-lead-time').cleanNumberVal() * 60);
      const pickupDate = moment($('#trip-pickup-date').val());
      const startDate = moment(pickupDate).subtract(leadTime, 'm');
      const endDate = moment(startDate).add(input.cleanNumberVal('#trip-duration'), 'h');

      const drivers = await net.get('/api/get.available-drivers.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
        tripId
      });
      $('#trip-driver-id').selectpicker('destroy');
      $('#trip-driver-id option').remove();
      $('#trip-driver-id').append($('<option>'));
      $.each(drivers, function(i, item) {
        const optionProps = {
          value: item.id,
          text: item.driver,
          // disabled: !item.available,
        }
        if (!item.available) {
          optionProps.style = `background-color:crimson; color: white`;
          optionProps['data-icon'] = 'fa-solid fa-triangle-exclamation';
        }
        $('#trip-driver-id').append($('<option>', optionProps));
      });
      $('#trip-driver-id').selectpicker({
        container: false
      })

      const vehicles = await net.get('/api/get.available-vehicles.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
        tripId
      });
      $('#trip-vehicle-id').selectpicker('destroy');
      $('#trip-vehicle-id option').remove();
      $('#trip-vehicle-id').append($('<option>'));
      $.each(vehicles, function(i, item) {
        const optionProps = {
          value: item.id,
          text: item.name,
          // 'data-content': `<i class="bi bi-square-fill" style="color:${item.color}"></i> ${item.name}`,
        }
        if (!item.available) {
          optionProps.style = `background-color:crimson; color: white`;
          optionProps['data-icon'] = 'fa-solid fa-triangle-exclamation';
        }
        $('#trip-vehicle-id').append($('<option>', optionProps));
      });
      $('#trip-vehicle-id').selectpicker({
        container: false
      });
    }

    // $('input').off('change').on('change', e => {
    //   formDirty = true;
    //   $('#trip-action-buttons button').addClass('disabled');
    // })

    if ($('#tripId').val()) {
      await loadResources();
      $('#trip-airline-id').selectpicker('val', '<?= $trip->airlineId ?>');
      $('#trip-driver-id').selectpicker('val', '<?= $trip->driverId ?>');
      $('#trip-vehicle-id').selectpicker('val', '<?= $trip->vehicleId ?>');
    }

    $('#trip-airline-id').append($('<option>'));
    $.each(airlines, function(i, item) {
      $('#trip-airline-id').append($('<option>', {
        value: item.id,
        text: item.name
      }));
    });


    $('#trip-pickup-date, #trip-duration, #trip-lead-time').on('change', async ƒ => {
      // The vehicle and/or driver may not be available in the new period, but if they are they will remain "selected".
      const saveDriverId = input.val('#trip-driver-id');
      const saveVehicleId = input.val('#trip-vehicle-id');
      await loadResources();
      $('#trip-driver-id').selectpicker('val', saveDriverId);
      $('#trip-vehicle-id').selectpicker('val', saveVehicleId);
    });

    buildAutoComplete({
      selector: 'trip-pu-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });
    $('#trip-pu-location').on('change', checkForFlight);

    buildAutoComplete({
      selector: 'trip-guest',
      apiUrl: '/api/get.autocomplete-guests.php'
    });

    buildAutoComplete({
      selector: 'trip-do-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name']
    });
    $('#trip-do-location').on('change', checkForFlight);

    $('#trip-airline-id').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
      const airlineId = $('#trip-airline-id').val();
      const item = airlines.filter(airline => airline.id == airlineId);
      const airline = item[0];
      if (airline) {
        $('#flight-number-prefix').html(airline.flight_number_prefix);
        $('#airline-image').html(airline.image_filename ? `<img src="/images/airlines/${airline.image_filename}" class="img-fluid"/>` : '');
      }
    });

    buildAutoComplete({
      selector: 'trip-requestor',
      apiUrl: '/api/get.autocomplete-requestors.php'
    });

    function checkForFlight() {
      console.debug('Checking for flight');
      if ($('#trip-pu-location').data('type') === 'airport' || $('#trip-do-location').data('type') === 'airport') {
        $('#flight-info').removeClass('d-none');
        if ($('#trip-pu-location').data('type') === 'airport') {
          $('#eta-section').removeClass('d-none');
        } else {
          $('#eta-section').addClass('d-none');
        }
        if ($('#trip-do-location').data('type') === 'airport') {
          $('#etd-section').removeClass('d-none');
        } else {
          $('#etd-section').addClass('d-none');
        }
      } else {
        $('#flight-info').addClass('d-none');
      }
    }
    checkForFlight();

    $('#trip-flight-number').on('change', verifyFlight);

    async function verifyFlight() {
      console.debug('Verifying flight...');
      const data = await getData();
      console.log(data);
      if (!data.flightNumber) return;
      const flightIata = $('#flight-number-prefix').html() + data.flightNumber;
      const type = (data.ETA) ? 'arrival' : 'departure';
      const date = (data.ETA) ? moment(data.ETA, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') : moment(data.ETD, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
      const locationId = (data.ETA) ? $('#trip-pu-location').data('id') : $('#trip-do-location').data('id');
      const resp = await net.get('/api/get.verify-flight.php', {
        flightIata,
        type,
        date,
        locationId
      });
      console.log(resp);
      if (resp) {
        $('#flight-verified').removeClass('d-none');
      }
    }

    if (!documentEventExists('buttonSave:trip')) {
      $(document).on('buttonSave:trip', async (e, tripId) => {
        const data = await getData();
        if (data) {
          const resp = await net.post('/api/post.save-trip.php', data);
          if (resp?.result) {
            $(document).trigger('tripChange', {
              tripId
            });
            if (tripId) {
              ui.toastr.success('Trip saved.', 'Success');
              return backToList();
            }
            ui.toastr.success('Trip added.', 'Success');
            return backToList();
          }
          ui.toastr.error(resp.error, 'Error');
          console.error(resp);
        }
      });
    }

    if (!documentEventExists('buttonSaveAndConfirm:trip')) {
      $(document).on('buttonSaveAndConfirm:trip', async (e, tripId) => {
        const data = await getData();
        if (data) {
          const resp = await net.post('/api/post.save-trip.php', data);
          if (resp?.result) {
            // Confirm the trip
            const newResp = await net.post('/api/post.confirm-trip.php', {
              id: tripId
            });
            if (newResp?.result) {
              $(document).trigger('tripChange');
              ui.toastr.success('Trip Saved and Confirmed.', 'Success');
              return backToList();
            }
          }
        }
        ui.toastr.error(resp.error, 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:trip')) {
      $(document).on('buttonDelete:trip', async (e, id) => {
        if (await ui.ask('Are you sure you want to delete this trip?')) {
          const resp = await net.get('/api/get.delete-trip.php', {
            id
          });
          if (resp?.result) {
            $(document).trigger('tripChange', {
              id
            });
            ui.toastr.success('Trip deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting this trip.', 'Error');
        }
      });
    }


    $('input, textarea').on('change', getData);


    async function getData() {
      const tripId = $('#tripId').val() ? $('#tripId').intVal() : null;
      const leadTime = isNaN(parseFloat($('#trip-lead-time').cleanNumberVal())) ? 0 : parseInt($('#trip-lead-time').cleanNumberVal() * 60);
      const pickupDate = moment($('#trip-pickup-date').val());
      const startDate = moment(pickupDate).subtract(leadTime, 'm');
      const endDate = moment(startDate).add(input.cleanNumberVal('#trip-duration'), 'h');

      const data = {
        tripId,
        id: tripId
      };
      let control;
      let controlDataValue;

      data.summary = input.cleanVal('#trip-summary');
      data.startDate = startDate.format('YYYY-MM-DD HH:mm:ss');
      data.pickupDate = pickupDate.format('YYYY-MM-DD HH:mm:ss');
      data.endDate = endDate.format('YYYY-MM-DD HH:mm:ss');

      control = $('#trip-pu-location');
      if (control.data('value') != control.val()) {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'locations',
            url: 'section.edit-location.php'
          });
        }
        return false;
      }
      data.puLocationId = control.data('id');
      data.puLocationId = (data.puLocationId == '') ? null : parseInt(data.puLocationId);

      control = $('#trip-guest');
      controlDataValue = $(`<tag>${control.data('value')}</tag>`).text(); // We need this so that values like "Richard O&#039;Brien" can be seen as "Richard O'Brien"
      if (controlDataValue != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized guest or group. Would you like to add a new one?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'guests',
            url: 'section.edit-guest.php'
          });
        }
        return false
      }
      data.guestId = control.data('id');
      data.guestId = (data.guestId == '') ? null : parseInt(data.guestId);
      data.guests = $('#trip-guests').cleanVal();
      data.passengers = $('#trip-passengers').cleanNumberVal();

      control = $('#trip-do-location');
      if (control.data('value') != control.val()) {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'locations',
            url: 'section.edit-location.php'
          });
        }
        return false;
      }
      data.doLocationId = control.data('id');
      data.doLocationId = (data.doLocationId == '') ? null : parseInt(data.doLocationId);

      data.vehicleId = $('#trip-vehicle-id').val();
      data.vehicleId = (data.vehicleId == '') ? null : parseInt(data.vehicleId);
      data.driverId = $('#trip-driver-id').val();
      data.driverId = (data.driverId == '') ? null : parseInt(data.driverId);
      data.airlineId = $('#trip-airline-id').val();
      data.airlineId = (data.airlineId == '') ? null : parseInt(data.airlineId);

      data.flightNumber = $('#trip-flight-number').intVal().toString();

      // We cannot have an ETA AND an ETD. This has previously precipitated errors
      if ($('#trip-pu-location').data('type') === 'airport') {
        data.ETA = $('#trip-eta').val() ? moment($('#trip-eta').val()).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETD = null;
      } else {
        data.ETD = $('#trip-etd').val() ? moment($('#trip-etd').val()).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETA = null;
      }

      data.vehiclePUOptions = $('#trip-vehicle-pu-options').val();
      data.vehicleDOOptions = $('#trip-vehicle-do-options').val();

      control = $('#trip-requestor');
      if (control.data('value') != control.val()) {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized user. Would you like to add a new user?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'users',
            url: 'section.edit-user.php'
          });
        }
        return false;
      }
      data.requestorId = control.data('id');
      data.requestorId = (data.requestorId == '') ? null : parseInt(data.requestorId);

      data.guestNotes = $('#trip-guest-notes').cleanVal();
      data.driverNotes = $('#trip-driver-notes').cleanVal();
      data.generalNotes = $('#trip-general-notes').cleanVal();

      data.requireMoreInfo = $('#trip-require-more-info').prop('checked');
      
      return data;
    }

    verifyFlight();

  });

  <?php if (!$trip->isEditable()): ?>
    // $('.tab-pane.active input').prop('disabled', true);
    // $('.tab-pane.active textarea').prop('disabled', true);
    // $('.tab-pane.active select').prop('disabled', true);
    // $('.tab-pane.active select').selectpicker('destroy')
  <?php endif; ?>
</script>