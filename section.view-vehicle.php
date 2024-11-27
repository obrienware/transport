<?php
require_once 'class.snag.php';
require_once 'class.vehicle.php';
$vehicleId = $_REQUEST['id'];
$vehicle = new Vehicle($vehicleId);
?>
<div class="container">

<div class="modal" tabindex="-1" id="locationUpdateModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Vehicle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <div class="container-fluid">

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="current-location" class="form-label">Location</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="current-location" 
                  placeholder=""/>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="current-mileage" class="form-label">Mileage</label>
                <input type="number" class="form-control" id="current-mileage" placeholder="Mileage" value="<?=$vehicle->mileage?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-2">
                <label for="current-fuel-level" class="form-label">How much fuel in the vehicle?</label>
                <input type="range" class="form-range" min="0" max="100" step="12.5" id="current-fuel-level" value="0">
              </div>
            </div>
          </div>
          

          <div class="row">
            <div class="col">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="current-is-clean-interior">
                <label class="form-check-label" for="current-is-clean-interior">Vehicle is clean inside</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="current-is-clean-exterior">
                <label class="form-check-label" for="current-is-clean-exterior">Vehicle is clean outside</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="current-needs-restocking">
                <label class="form-check-label" for="current-needs-restocking">Needs restocking (refreshments)</label>
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="btn-update-location" type="button" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>






  <div class="d-flex justify-content-between">
    <h3>
      <i class="bi bi-circle-fill" style="color:<?=$vehicle->color?>"></i>
      <?=$vehicle->name?>
    </h3>
    <button onclick="app.openTab('edit-vehicle', 'Vehicle (edit)', `section.edit-vehicle.php?id=<?=$vehicleId?>`);" class="btn btn-outline-primary btn-sm align-self-center">Edit</button>
  </div>

  <table class="table table-bordered table-sm">
    <tr>
      <th class="fit px-2">Description</th>
      <td><?=$vehicle->description?></td>
    </tr>
    <tr>
      <th class="fit px-2">Passengers</th>
      <td><?=$vehicle->passengers?></td>
    </tr>
    <tr>
      <th class="fit px-2">Requires a CDL driver</th>
      <td><?=$vehicle->requireCDL ? 'Yes' : 'No' ?></td>
    </tr>
    <tr>
      <th class="fit px-2">Staging Location</th>
      <td><?=$vehicle->stagingLocation?></td>
    </tr>
  </table>

  <table class="table table-bordered table-sm">
    <caption class="caption-top">As at <?=$vehicle->lastUpdate?></caption>
    <tr>
      <th class="fit px-2">Mileage</th>
      <td><?=$vehicle->mileage ? number_format($vehicle->mileage) : 'unknown'?></td>
      <th class="fit px-2">Check Engine Light On</th>
      <td><?=$vehicle->hasCheckEngine ? '<div class="badge bg-danger fs-6">YES</div>' : 'No' ?></td>
    </tr>
    <tr>
      <th class="fit px-2">Location</th>
      <td><?=$vehicle->currentLocation ?: 'Unverified'?></td>
      <th class="fit px-2">Needs cleaning</th>
      <td>
        <?php if ($vehicle->cleanExterior): ?>
          <div class="badge bg-danger">Exterior</div>
        <?php endif; ?>
        <?php if ($vehicle->cleanInterior): ?>
          <div class="badge bg-danger">Interior</div>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th class="fit px-2">Fuel Level</th>
      <td>
        <?php if ($vehicle->fuelLevel <= 25): ?>
          <span class="badge bg-danger fs-6"><?=$vehicle->fuelLevel?>%</span>
        <?php else:?>
          <?=$vehicle->fuelLevel?>%
        <?php endif; ?>
      </td>
      <th class="fit px-2">Needs restocking</th>
      <td>
        <?php if ($vehicle->restock === 1): ?>
          <div class="badge bg-danger fs-6">YES</div>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
    </tr>
  </table>

  <div class="card-footer text-center mb-5">
    <button id="btn-update-vehicle-status" class="btn btn-outline-primary btn-sm">Update</button>
  </div>

  
  <!-- SNAGLIST -->
  <div class="card mb-3">
    <div class="card-header text-center">
      <h5 class="mb-0">Snag List</h5>
    </div>
    <?php if ($rs = Snag::getSnags($vehicleId)): ?>
      <table class="table table-sm mb-0">
        <thead>
          <tr>
            <th class="fit">Date</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rs as $item): ?>
            <tr>
              <td class="datetime short"><?=$item->datetimestamp?></td>
              <td><?=$item->description?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="card-body text-center">
        There are no snags logged yet for this vehicle
      </div>
    <?php endif; ?>
    <div class="card-footer text-center">
      <button class="btn btn-outline-primary btn-sm">Add a snag</button>
    </div>
  </div>

</div>



<script>
  $(async ƒ => {

    const vehicleId = <?=$vehicleId?>;
    const options = {
      backdrop: 'static'
    }
    const modal = new bootstrap.Modal('#locationUpdateModal', options);

    new Autocomplete(document.getElementById('current-location'), {
      fullWidth: true,
      highlightTyped: true,
      liveServer: true,
      server: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
      onSelectItem: (data) => {
        $('#current-location')
          .data('id', data.value)
          .data('type', data.type)
          .data('value', data.label)
          .removeClass('is-invalid');
      }
    });

    

    $('#btn-update-vehicle-status').off('click').on('click', e => {
      modal.show();
    });

    $('#btn-update-location').off('click').on('click', async e => {
      const resp = await post('/api/post.update-vehicle-location.php', {
        vehicleId,
        locationId: $('#current-location').data('id'),
        fuelLevel: $('#current-fuel-level').val(),
        mileage: cleanNumberVal('#current-mileage'),
        cleanExterior: $('#current-is-clean-exterior').is(':checked'),
        cleanInterior: $('#current-is-clean-interior').is(':checked'),
        needsRestocking: $('#current-needs-restocking').is(':checked')
      });
      console.log(resp);
      if (resp?.result) {
        modal.hide();
        $('#<?=$_REQUEST["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
      }
    });

    reFormat();

  });
</script>