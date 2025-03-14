<?php 
require_once '../autoload.php';

use Transport\Vehicle;
?>
<?php if ($rows = Vehicle::getAll()): ?>

  <ul class="list-group mx-2" id="vehicle-list">
    <?php foreach ($rows as $row): ?>
      <li class="list-group-item list-group-item-action vehicle-item" data-id="<?=$row->id?>">
        <div class="d-flex justify-content-between">
          <div>
            <i class="fa-solid fa-square fa-fw" style="color: <?=$row->color?>"></i>
            <?=$row->name?>
          </div>
          <?php if ($row->license_plate): ?>
            <span class="badge text-bg-primary px-2 ms-auto align-self-center"><?= $row->license_plate ?></span>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <div id="vehicle-detail" class="d-none p-3">
    <ul class="list-group mb-3">
      <li class="list-group-item">
        <div class="d-flex justify-content-between">
          <div class="vehicle-name"></div>
          <div id="vehicle-mileage" class="align-self-center badge bg-secondary"></div>
        </div>
      </li>
      <li class="list-group-item">
        <div style="font-size:small">Last known location:</div>
        <div id="vehicle-location"></div>
      </li>
      <li class="list-group-item">
        <div style="font-size:small">Next trip/event:</div>
        <div id="vehicle-next-trip">Checking...</div>
      </li>

      <li class="list-group-item">
        <div class="hstack mb-3">
          <div class="p-2 w-25 d-inline-block text-center item-toggle" data-id="restock">
            <i class="fa-duotone fa-solid fa-bottle-water fa-2x"></i>
            <div id="vehicle-restock"></div>
          </div>
          <div class="p-2 w-25 d-inline-block text-center item-toggle" data-id="cleanInterior">
            <i class="fa-duotone fa-solid fa-vacuum fa-2x"></i>
            <div id="vehicle-interior"></div>
          </div>
          <div class="p-2 w-25 d-inline-block text-center item-toggle" data-id="cleanExterior">
            <i class="fa-duotone fa-solid fa-car-wash fa-2x"></i>
            <div id="vehicle-exterior"></div>
          </div>
          <div class="p-2 w-25 d-inline-block text-center">
            <i class="fa-duotone fa-solid fa-gas-pump fa-2x"></i>
            <div id="vehicle-fuel-level"></div>
          </div>
        </div>
        <div class="hstack">
          <div class="p-2 w-25 d-inline-block text-center item-toggle" data-id="hasCheckEngine">
            <i class="fa-duotone fa-solid fa-engine-warning fa-2x"></i>
            <div id="vehicle-check-engine"></div>
          </div>
          <div class="p-2 w-25 d-inline-block text-center">
            <i class="fa-duotone fa-solid fa-location-dot fa-2x"></i>
            <div id="vehicle-relocate"></div>
          </div>

        </div>
      </li>
    </ul>
    <section id="vehicle-documents"></section>
    <div class="hstack gap-2">
      <button class="btn-close-vehicle-view btn btn-outline-primary">Done</button>
      <button id="btn-update-vehicle" class="btn btn-primary ms-auto">Update</button>
    </div>
  </div>

  <div id="vehicle-update-form" class="d-none p-3 bg-body">
    <h4 class="vehicle-name mb-3"></h4>

    <div class="mb-3">
      <label for="mileage" class="form-label">Mileage</label>
      <input type="number" class="form-control" id="mileage" placeholder="">
    </div>

    <!-- Current location of the vehicle -->
    <div class="mb-3">
      <label for="veh-location" class="form-label">Location</label>
      <input 
        type="text" 
        class="form-control" 
        id="veh-location" 
        placeholder="Current Location">
    </div>

    <div class="mb-3">
      <label for="fuel-level" class="form-label">Fuel Level</label>
      <input type="range" class="form-range" min="0" max="100" step="10" id="fuel-level" value="0">
    </div>

    <div class="mb-3 text-center">
      <label class="form-label">Engine Light Is On</label>
      <div>
        <div class="btn-group ~btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="opt-check-engine" id="btnradio10" value="0" autocomplete="off">
          <label class="btn btn-outline-success" for="btnradio10">No</label>

          <input type="radio" class="btn-check" name="opt-check-engine" id="btnradio11" value="" autocomplete="off" checked>
          <label class="btn btn-outline-secondary" for="btnradio11">Unknown</label>

          <input type="radio" class="btn-check" name="opt-check-engine" id="btnradio12" value="1" autocomplete="off">
          <label class="btn btn-outline-danger" for="btnradio12">Yes</label>
        </div>
      </div>
    </div>

    <div class="mb-3 text-center">
      <label class="form-label">Is Stocked</label>
      <div>
        <div class="btn-group ~btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="opt-restock" id="btnradio1" value="1" autocomplete="off">
          <label class="btn btn-outline-danger" for="btnradio1">No</label>

          <input type="radio" class="btn-check" name="opt-restock" id="btnradio2" value="" autocomplete="off" checked>
          <label class="btn btn-outline-secondary" for="btnradio2">Unknown</label>

          <input type="radio" class="btn-check" name="opt-restock" id="btnradio3" value="0" autocomplete="off">
          <label class="btn btn-outline-success" for="btnradio3">Yes</label>
        </div>
      </div>
    </div>

    <div class="mb-3 text-center">
      <label class="form-label">Is Clean Inside</label>
      <div>
        <div class="btn-group ~btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="opt-clean-interior" id="btnradio4" value="0" autocomplete="off">
          <label class="btn btn-outline-danger" for="btnradio4">No</label>

          <input type="radio" class="btn-check" name="opt-clean-interior" id="btnradio5" value="" autocomplete="off" checked>
          <label class="btn btn-outline-secondary" for="btnradio5">Unknown</label>

          <input type="radio" class="btn-check" name="opt-clean-interior" id="btnradio6" value="1" autocomplete="off">
          <label class="btn btn-outline-success" for="btnradio6">Yes</label>
        </div>
      </div>
    </div>

    <div class="mb-3 text-center">
      <label class="form-label">Is Clean Outside</label>
      <div>
        <div class="btn-group ~btn-group-sm" role="group">
          <input type="radio" class="btn-check" name="opt-clean-exterior" id="btnradio7" value="0" autocomplete="off">
          <label class="btn btn-outline-danger" for="btnradio7">No</label>

          <input type="radio" class="btn-check" name="opt-clean-exterior" id="btnradio8" value="" autocomplete="off" checked>
          <label class="btn btn-outline-secondary" for="btnradio8">Unknown</label>

          <input type="radio" class="btn-check" name="opt-clean-exterior" id="btnradio9" value="1" autocomplete="off">
          <label class="btn btn-outline-success" for="btnradio9">Yes</label>
        </div>
      </div>
    </div>

    <div class="hstack gap-2 mt-5">
      <button class="btn-close-vehicle-view btn btn-outline-primary">Cancel</button>
      <button id="btn-action-vehicle-update" class="btn btn-primary ms-auto">Update</button>
    </div>
  </div>

<?php else: ?>

  <div class="p-5 text-center">
    <div class="alert alert-info">No vehicles to display at this time</div>
  </div>

<?php endif;?>

<script>

  $(async ƒ => {

    let vehicleId;

    buildAutoComplete({
      selector: 'veh-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });

    function resetForm()
    {
      $('#veh-location').removeData().val('');
      $('input[name="opt-check-engine"][value=""]').prop('checked', true);
      $('input[name="opt-restock"][value=""]').prop('checked', true);
      $('input[name="opt-clean-interior"][value=""]').prop('checked', true);
      $('input[name="opt-clean-exterior"][value=""]').prop('checked', true);
      $('#fuel-level').val('0');
      $('#mileage').val('');
      $('#vehicle-documents').html('');
    }

    function getData() {
      const data = {vehicleId};
      if ($('#mileage').val() && parseInt($('#mileage').val()) > 0) data.mileage = parseInt($('#mileage').val());
      if ($('#veh-location').data('id')) data.locationId = $('#veh-location').data('id');
      if ($('input[name="opt-check-engine"]:checked').val() !== '') data.hasCheckEngine = $('input[name="opt-check-engine"]:checked').val();
      if ($('input[name="opt-restock"]:checked').val() !== '') data.restock = $('input[name="opt-restock"]:checked').val();
      if ($('input[name="opt-clean-interior"]:checked').val() !== '') data.cleanInterior = $('input[name="opt-clean-interior"]:checked').val();
      if ($('input[name="opt-clean-exterior"]:checked').val() !== '') data.cleanExterior = $('input[name="opt-clean-exterior"]:checked').val();
      if ($('#fuel-level').val() !== '0') data.fuelLevel = $('#fuel-level').val();
      return data;
    }

    $('.item-toggle').off('click').on('click', e => {
      const id = $(e.currentTarget).data('id');
      const state = $(e.currentTarget).data('state');
      toggleVehicleItem(id, state);
    });

    async function toggleVehicleItem(name, state) {
      const data = {vehicleId, name, state};
      const res = await post('api/post.vehicle-toggle.php', data);
      console.log(res);
      renderVehicleItem(name, res.state);
    }

    function renderVehicleItem(name, state) {
      let content;
      switch (name) {
        case 'restock':
          if (state === false) {
            content = `<span class="fw-light badge bg-success w-100">Good</span>`;
          } else if (state === true) {
            content = `<span class="fw-light badge bg-danger w-100">Needs</span>`;
          } else {
            content = `<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`;
          }
          $('#vehicle-restock').html(content);
          $('[data-id="restock"]').data('state', state);
          break;

        case 'cleanInterior':
          if (state === true) {
            content = `<span class="fw-light badge bg-success w-100">Good</span>`;
          } else if (state === false) {
            content = `<span class="fw-light badge bg-danger w-100">Needs</span>`;
          } else {
            content = `<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`;
          }
          $('#vehicle-interior').html(content);
          $('[data-id="cleanInterior"]').data('state', state);
          break;

        case 'cleanExterior':
          if (state === true) {
            content = `<span class="fw-light badge bg-success w-100">Good</span>`;
          } else if (state === false) {
            content = `<span class="fw-light badge bg-danger w-100">Needs</span>`;
          } else {
            content = `<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`;
          }
          $('#vehicle-exterior').html(content);
          $('[data-id="cleanExterior"]').data('state', state);
          break;

        case 'hasCheckEngine':
          if (state === false) {
            content = `<span class="fw-light badge bg-success w-100">Good</span>`;
          } else if (state === true) {
            content = `<span class="fw-light badge bg-danger w-100">Attention</span>`;
          } else {
            content = `<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`;
          }
          $('#vehicle-check-engine').html(content);
          $('[data-id="hasCheckEngine"]').data('state', state);
          break;
      }
    }


    $('.vehicle-item').off('click').on('click', async function (e) {
      const id = $(this).data('id');
      vehicleId = id;
      const vehicle = await get('/api/get.vehicle.php', {id});
      // console.log(vehicle);
      const backgroundColor = vehicle.color;
      const textColor = luminanceColor(backgroundColor);;
      const vehicleName = `<span class="py-1 px-3 rounded" style="background-color:${backgroundColor};color:${textColor}">${vehicle.name}</span>`;
      $('.vehicle-name,#vehicle-location,#vehicle-mileage').html('');
      $('.vehicle-name').html(vehicleName);

      $('#vehicle-location').html(vehicle.currentLocation?.name);
      $('#vehicle-mileage').html(vehicle?.mileage?.toLocaleString());

      renderVehicleItem('restock', vehicle.restock);
      renderVehicleItem('cleanInterior', vehicle.cleanInterior);
      renderVehicleItem('cleanExterior', vehicle.cleanExterior);
      renderVehicleItem('hasCheckEngine', vehicle.hasCheckEngine);

      if (vehicle.fuelLevel === null) {
        $('#vehicle-fuel-level').html(`<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`);
      } else if (vehicle.fuelLevel <= 25) {
        $('#vehicle-fuel-level').html(`<div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
            <div class="progress-bar bg-danger overflow-visible" style="width: ${vehicle.fuelLevel}%">&nbsp;${vehicle.fuelLevel}%&nbsp;</div>
        </div>`);
      } else {
        $('#vehicle-fuel-level').html(`<div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
            <div class="progress-bar bg-success overflow-visible" style="width: ${vehicle.fuelLevel}%">&nbsp;${vehicle.fuelLevel}%&nbsp;</div>
        </div>`);
      }

      if (vehicle.locationId === null) {
        $('#vehicle-relocate').html(`<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`);
      } else if (vehicle.locationId !== vehicle.stagingLocationId) {
        $('#vehicle-relocate').html(`<span class="fw-light badge bg-danger w-100">Relocate</span>`);
      } else {
        $('#vehicle-relocate').html(`<span class="fw-light badge bg-success w-100">Good</span>`);
      }

      $('#vehicle-list').addClass('d-none');
      $('#vehicle-detail').removeClass('d-none');

      $('#vehicle-documents').load('section.vehicle-documents.php?id='+id);

      const nextTrip = await get('/api/get.next-trip.php', {id});
      // console.log(nextTrip);
      if (nextTrip.starts === null) return $('#vehicle-next-trip').html('none');
      const starts = moment(nextTrip.starts, 'YYYY-MM-DD H:mm:ss');
      $('#vehicle-next-trip').html(
        timeago.format(nextTrip.starts) + ' (' + starts.format('M/D h:mma') + ')'
        + `<div>${nextTrip.name}</div>`
      );
    });

    $('.btn-close-vehicle-view').off('click').on('click', e => {
      $('#vehicle-detail').addClass('d-none');
      $('#vehicle-update-form').addClass('d-none');
      $('#vehicle-list').removeClass('d-none');
    });

    $('#btn-update-vehicle').off('click').on('click', e => {
      resetForm();
      $('#vehicle-detail').addClass('d-none');
      $('#vehicle-update-form').removeClass('d-none');
    });

    $('#btn-action-vehicle-update').off('click').on('click', async e => {
      const data = getData();
      // console.log(data);
      const res = await post('api/post.vehicle-update.php', data);
      // console.log(res);
      $('#vehicle-detail').addClass('d-none');
      $('#vehicle-update-form').addClass('d-none');
      $('#vehicle-list').removeClass('d-none');
    });

  });

</script>