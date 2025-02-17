<?php
require_once 'autoload.php';

use Generic\{InputHandler, Utils};
use Transport\Vehicle;

$vehicleId = InputHandler::getInt(INPUT_GET, 'vehicleId');
$vehicle = new Vehicle($vehicleId);
?>
<?php include 'inc.form-vehicle-update.php'; ?>

<div class="d-flex my-3">
  <div class="alert alert-info mx-auto" role="alert">
    <i class="fa-solid fa-info-circle"></i>
    Last updated:
    <?php if ($vehicle->lastUpdate): ?>
      <?= Date('m/d h:ia', strtotime($vehicle->lastUpdate)) ?>
      (<?= Utils::timeAgo($vehicle->lastUpdate) ?>)
    <?php else: ?>
      Never
    <?php endif; ?>
  </div>
</div>

<style>
  .guage-container {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fill, minmax(100px, 13%));
  }
  .guage-container > * {
    justify-self: center;
  }
</style>

<div class="guage-container">

  <div class="p-2 d-inline-block text-center pointer item-toggle" style="width:100px" data-id="restock" data-state="<?= $vehicle->restock ?>">
    <i class="fa-duotone fa-solid fa-bottle-water fa-3x"></i>
    <div id="vehicle-restock"></div>
  </div>

  <div class="p-2 d-inline-block text-center pointer item-toggle" style="width:100px" data-id="cleanInterior" data-state="<?= $vehicle->cleanInterior ?>">
    <i class="fa-duotone fa-solid fa-vacuum fa-3x"></i>
    <div id="vehicle-interior"></div>
  </div>

  <div class="p-2 d-inline-block text-center pointer item-toggle" style="width:100px" data-id="cleanExterior" data-state="<?= $vehicle->cleanExterior ?>">
    <i class="fa-duotone fa-solid fa-car-wash fa-3x"></i>
    <div id="vehicle-exterior"></div>
  </div>

  <div class="p-2 d-inline-block text-center" style="width:100px">
    <i id="vehicle-fuel-icon" class="fa-duotone fa-solid fa-gas-pump fa-3x pointer"></i>
    <div id="vehicle-fuel-level" class="pointer"></div>
  </div>

  <div class="p-2 d-inline-block text-center pointer item-toggle" style="width:100px" data-id="hasCheckEngine" data-state="<?= $vehicle->hasCheckEngine ?>">
    <i class="fa-duotone fa-solid fa-engine-warning fa-3x"></i>
    <div id="vehicle-check-engine"></div>
  </div>

  <div class="p-2 d-inline-block text-center" style="width:100px">
    <i class="fa-duotone fa-solid fa-location-dot fa-3x"></i>
    <div>
      <?php if ($vehicle->locationId === null): ?>
        <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
      <?php elseif ($vehicle->locationId !== $vehicle->stagingLocationId) : ?>
        <span class="fw-light badge bg-danger w-100" data-bs-toggle="tooltip" data-bs-title="<?= $vehicle->currentLocation->name ?>">Relocate</span>
      <?php else: ?>
        <span class="fw-light badge bg-success w-100">Good</span>
      <?php endif; ?>
    </div>
  </div>

  <div id="vehicle-mileage-icon" class="p-2 d-inline-block text-center pointer" style="width:100px">
    <i class="fa-duotone fa-solid fa-gauge-simple fa-3x"></i>
    <div id="vehicle-mileage"></div>
  </div>

</div>

<!-- 
<div class="text-end">
  <button id="btn-update-vehicle-status" class="btn btn-outline-primary btn-sm">Update</button>
</div> -->

<script>

  $(async ƒ => {

    const vehicleId = <?= $vehicleId ?>;
    const vehicleUpdateForm = new VehicleUpdateClass('#vehicleUpdateModal');
    const vehicle = await net.get('/api/get.vehicle.php', {
      id: vehicleId
    });

    vehicleUpdateForm.onUpdate = async function(e, formData) {
      formData.vehicleId = vehicleId;
      const resp = await net.post('/api/post.update-vehicle.php', formData);
      if (resp?.result) {
        $(document).trigger('vehicleChange');
        $('#pills-status').load(`<?= $_SERVER['REQUEST_URI'] ?>`); // Refresh this page
        return;
      }
      ui.toastr.error(resp.message);
    }

    $('#btn-update-vehicle-status').off('click').on('click', e => {
      vehicleUpdateForm.show();
    });


    function fuelLevelAsFractions(fuelLevel) {
      if (fuelLevel <= 10) return 'Empty';
      if (fuelLevel <= 20) return '⅛';
      if (fuelLevel <= 30) return '¼';
      if (fuelLevel <= 40) return '⅜';
      if (fuelLevel <= 60) return '½';
      if (fuelLevel <= 80) return '¾';
      return 'Full';
    }


    $('.item-toggle').off('click').on('click', e => {
      const id = $(e.currentTarget).data('id');
      const state = $(e.currentTarget).data('state');
      toggleVehicleItem(id, state);
    });

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

        case 'fuelLevel':
          if (state === null) {
            content = `<span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>`;
            content = `
              <div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
                <div class="progress-bar bg-danger overflow-visible" style="width: 0%">&nbsp;unknown&nbsp;</div>
              </div>
            `;
          } else if (state <= 25) {
            content = `
              <div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
                <div class="progress-bar bg-danger overflow-visible" style="width: ${state}%">&nbsp;${fuelLevelAsFractions(state)}&nbsp;</div>
              </div>
            `;
          } else {
            content = `
              <div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
                <div class="progress-bar bg-success overflow-visible" style="width: ${state}%">&nbsp;${fuelLevelAsFractions(state)}&nbsp;</div>
              </div>
            `;
          }
          $('#vehicle-fuel-level').html(content);
          break;

        case 'mileage':
          content = `<span class="fw-light badge bg-primary w-100">${state ? input.decimal(state, 0) : 'unknown'}</span>`;
          $('#vehicle-mileage').html(content);
          break;
      }
    }

    async function toggleVehicleItem(name, state) {
      const data = {
        vehicleId,
        name,
        state
      };
      const res = await net.post('api/post.vehicle-toggle.php', data);
      console.log(res);
      renderVehicleItem(name, res.state);
    }


    renderVehicleItem('restock', vehicle.restock);
    renderVehicleItem('cleanInterior', vehicle.cleanInterior);
    renderVehicleItem('cleanExterior', vehicle.cleanExterior);
    renderVehicleItem('hasCheckEngine', vehicle.hasCheckEngine);
    renderVehicleItem('fuelLevel', vehicle.fuelLevel);
    renderVehicleItem('mileage', vehicle.mileage);



    $('#vehicle-fuel-level').off('click').on('click', async e => {
      const percentage = getHorizontalClickPercentage(e, e.currentTarget);
      console.log(`Clicked at ${percentage}%`);
      const data = {
        vehicleId,
        name: 'fuel',
        value: percentage
      };
      const res = await net.post('api/post.vehicle-update.php', data);
      console.log(res);
      renderVehicleItem('fuelLevel', percentage);
    });

    function getHorizontalClickPercentage(event, element) {
      const rect = element.getBoundingClientRect();
      const offsetX = event.clientX - rect.left;
      const width = rect.width;
      const percentage = (offsetX / width) * 100;
      return percentage.toFixed(0); // Return percentage with 2 decimal places
    }

    $('#vehicle-fuel-icon').off('click').on('click', async e => {
      const data = {
        vehicleId,
        name: 'fuel',
        value: null
      };
      const res = await net.post('api/post.vehicle-update.php', data);
      console.log(res);
      renderVehicleItem('fuelLevel', null);
    });

    $('#vehicle-mileage-icon').off('click').on('click', async e => {
      const previousMileage = parseInt($('#vehicle-mileage').text());
      let mileage = await ui.getNumber('Enter mileage');
      if (mileage === undefined) return;
      if (mileage === '') {
        const ans = await ui.ask('Are you sure you want to clear the mileage?');
        if (!ans) return;
        mileage = null;
      }
      mileage = parseInt(mileage);
      if (mileage < previousMileage) {
        ui.toastr.error('Mileage cannot be less than previous mileage', 'Error');
        return;
      }
      const data = {
        vehicleId,
        name: 'mileage',
        value: parseInt(mileage)
      };
      const res = await net.post('api/post.vehicle-update.php', data);
      console.log(res);
      renderVehicleItem('mileage', mileage);
    });

  });
</script>