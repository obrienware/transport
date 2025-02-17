<?php
require_once 'autoload.php';

use Generic\Utils;
use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$vehicle = new Vehicle($id);
if (!is_null($id) && !$vehicle->getId())
{
  exit(Utils::showResourceNotFound());
}
$vehicleId = $vehicle->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('vehicle:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>


<div class="row">

  <div class="col-12 col-lg-2 col-xxl-12 mb-3">
    <label for="vehicle-color" class="form-label">Color picker</label>
    <input type="color" class="form-control form-control-color" id="vehicle-color" value="<?= $vehicle->color ?: '#ffffff' ?>" title="Choose your color">
  </div>

  <div class="col-12 col-lg-10 col-xl-6 col-xxl-6 mb-3">
    <label for="vehicle-name" class="form-label">Name</label>
    <input type="text" class="form-control" id="vehicle-name" placeholder="Name" value="<?= $vehicle->name ?>">
  </div>

  <div class="col-12 col-xl-8 col-xxl-6 mb-3">
    <label for="vehicle-description" class="form-label">Description</label>
    <input type="text" class="form-control" id="vehicle-description" placeholder="Vehicle Description" value="<?= $vehicle->description ?>">
  </div>

  <div class="col-12 col-lg-4 col-xl-3 mb-3">
    <label for="vehicle-passengers" class="form-label">Max Passengers</label>
    <input type="number" class="form-control" id="vehicle-passengers" placeholder="Number of passengers" value="<?= $vehicle->passengers ?>">
  </div>

  <div class="col-12 col-lg-4 col-xl-3 mb-3">
    <label for="vehicle-license-plate" class="form-label">License Plate</label>
    <input type="text" class="form-control" id="vehicle-license-plate" placeholder="License Plate" value="<?= $vehicle->licensePlate ?>">
  </div>

  <div class="col-12 col-lg-4 mb-3 pt-4">
    <div class="pretty p-svg p-curve">
      <input class="~form-check-input" type="checkbox" value="1" id="vehicle-requireCDL" <?= $vehicle->requireCDL ? 'checked' : '' ?>>
      <div class="state p-primary">
        <!-- svg path -->
        <svg class="svg svg-icon" viewBox="0 0 20 20">
          <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
        </svg>
        <label>Requires CDL?</label>
      </div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($vehicleId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:vehicle', <?= $vehicleId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:vehicle', '<?= $vehicleId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'vehicles',
        url: 'section.list-vehicles.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:vehicle')) {
      $(document).on('buttonSave:vehicle', async (e, id) => {
        const resp = await net.post('/api/post.save-vehicle.php', {
          id,
          color: $('#vehicle-color').val(),
          name: $('#vehicle-name').cleanVal(),
          description: $('#vehicle-description').cleanVal(),
          passengers: input.int($('#vehicle-passengers').val(), null),
          licensePlate: $('#vehicle-license-plate').cleanUpperVal(),
          requireCDL: $('#vehicle-requireCDL').isChecked(),
        });
        if (resp?.result) {
          $(document).trigger('vehicleChange');
          if (vehicleId) {
            ui.toastr.success('Vehicle saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Vehicle added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:vehicle')) {
      $(document).on('buttonDelete:vehicle', async (e, id) => {
        const vehicleId = id;
        if (await ui.ask('Are you sure you want to delete this vehicle?')) {
          const resp = await net.get('/api/get.delete-vehicle.php', {
            id: vehicleId
          });
          if (resp?.result) {
            $(document).trigger('vehicleChange');
            ui.toastr.success('Vehicle deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting vehicle.', 'Error');
        }
      });
    }
  });
</script>