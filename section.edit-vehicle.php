<?php
require_once 'autoload.php';

use Generic\Utils;
use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$vehicle = new Vehicle($id);
if (!is_null($id) && !$vehicle->getId()) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <div class="row">
    <div class="col-2">
      <label for="vehicle-color" class="form-label">Color picker</label>
      <input type="color" class="form-control form-control-color" id="vehicle-color" value="<?=$vehicle->color ?: '#ffffff'?>" title="Choose your color">
    </div>
    <div class="col mb-3">
      <label for="vehicle-name" class="form-label">Name</label>
      <input type="text" class="form-control" id="vehicle-name" placeholder="Name" value="<?=$vehicle->name?>">
    </div>
    <div class="col-8 mb-3">
      <label for="vehicle-description" class="form-label">Description</label>
      <input type="text" class="form-control" id="vehicle-description" placeholder="Vehicle Description" value="<?=$vehicle->description?>">
    </div>
  </div>

  <div class="row">
    <div class="offset-2 col-2 mb-3">
      <label for="vehicle-passengers" class="form-label">Max Passengers</label>
      <input type="number" class="form-control" id="vehicle-passengers" placeholder="Number of passengers" value="<?=$vehicle->passengers?>">
    </div>

    <div class="col-2 mb-3">
      <label for="vehicle-license-plate" class="form-label">License Plate</label>
      <input type="text" class="form-control" id="vehicle-license-plate" placeholder="License Plate" value="<?=$vehicle->licensePlate?>">
    </div>

    <div class="col mb-3 pt-4">
      <!-- <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="vehicle-requireCDL" <?=$vehicle->requireCDL ? 'checked' : ''?>>
        <label class="form-check-label" for="vehicle-requireCDL">Requires CDL?</label>
      </div> -->
      <div class="pretty p-svg p-curve">
        <input class="~form-check-input" type="checkbox" value="1" id="vehicle-requireCDL" <?=$vehicle->requireCDL ? 'checked' : ''?>>
        <div class="state p-success">
          <!-- svg path -->
          <svg class="svg svg-icon" viewBox="0 0 20 20">
            <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
          </svg>
          <label>Requires CDL?</label>
        </div>
      </div>

      <!--
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="vehicle-hasCheckEngine" <?=$vehicle->hasCheckEngine ? 'checked' : ''?>>
        <label class="form-check-label" for="vehicle-hasCheckEngine">Check-engine light on?</label>
      </div>
      -->
    </div>
  </div>

  <div class="row my-4">
    <div class="col d-flex justify-content-between">
      <?php if ($vehicle->getId()): ?>
        <button class="btn btn-outline-danger px-4" id="btn-delete-vehicle">Delete</button>
      <?php endif; ?>
      <button class="btn btn-primary px-4" id="btn-save-vehicle">Save</button>
    </div>
  </div>

</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const vehicleId = <?=$vehicle->getId() ?? 'null'?>;
    $('#btn-save-vehicle').off('click').on('click', async ƒ => {
      const resp = await net.post('/api/post.save-vehicle.php', {
        vehicleId,
        color: $('#vehicle-color').val(),
        name: input.cleanVal('#vehicle-name'),
        description: input.cleanVal('#vehicle-description'),
        passengers: input.int($('#vehicle-passengers').val(), null),
        licensePlate: input.cleanUpperVal('#vehicle-license-plate'),
        // mileage: int(val('#vehicle-mileage'), null),
        requireCDL: $('#vehicle-requireCDL').is(':checked'),
      });
      if (resp?.result) {
        $(document).trigger('vehicleChange', {vehicleId});
        app.closeOpenTab();
        if (vehicleId) return ui.toastr.success('Vehicle saved.', 'Success');
        return ui.toastr.success('Vehicle added.', 'Success');
      }
      ui.toastr.error(resp.result.errors[2], 'Error');
      console.log(resp);
    });

    $('#btn-delete-vehicle').off('click').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this user?')) {
        const resp = await net.get('/api/get.delete-vehicle.php', {
          id: vehicleId
        });
        if (resp?.result) {
          app.closeOpenTab();
          $(document).trigger('vehicleChange', {vehicleId});
          return ui.toastr.success('Vehicle deleted.', 'Success')
        }
        console.log(resp);
        ui.toastr.error('There seems to be a problem deleting vehicle.', 'Error');
      }
    });

  });

</script>
