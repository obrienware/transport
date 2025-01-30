<?php
require_once 'autoload.php';

use Transport\Snag;
use Transport\Utils;
use Transport\Vehicle;

$vehicleId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vehicle = new Vehicle($vehicleId);
?>
<div class="container">

<?php include 'inc.form-vehicle-update.php'; ?>




  <div class="d-flex justify-content-between">
    <h3>
      <i class="bi bi-square-fill" style="color:<?=$vehicle->color?>"></i>
      <?=$vehicle->name?>
    </h3>
    <button onclick="app.openTab('edit-vehicle', 'Vehicle (edit)', `section.edit-vehicle.php?id=<?=$vehicleId?>`);" class="btn btn-outline-primary btn-sm align-self-center">Edit</button>
  </div>

  <table class="table table-bordered table-sm">
    <tr>
      <th class="fit px-2 bg-body-secondary">Description</th>
      <td><?=$vehicle->description?></td>
      <th class="fit px-2 bg-body-secondary">License Plate</th>
      <td class="fit px-2"><?=$vehicle->licensePlate?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Passengers</th>
      <td colspan="3"><?=$vehicle->passengers?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Requires a CDL driver</th>
      <td colspan="3"><?=$vehicle->requireCDL ? 'Yes' : 'No' ?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Staging Location</th>
      <td colspan="3"><?=$vehicle->stagingLocation->name?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Next Trip/Event</th>
      <td colspan="3" id="nextTripEventDetail"></td>
    </tr>
  </table>


  <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab" aria-controls="pills-status" aria-selected="true">Status</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link position-relative" id="pills-snags-tab" data-bs-toggle="pill" data-bs-target="#pills-snags" type="button" role="tab" aria-controls="pills-snags" aria-selected="false">
        Snags
        <span id="snag-count" class="d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-maintenance-tab" data-bs-toggle="pill" data-bs-target="#pills-maintenance" type="button" role="tab" aria-controls="pills-maintenance" aria-selected="false">Maintenance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link position-relative" id="pills-document-tab" data-bs-toggle="pill" data-bs-target="#pills-document" type="button" role="tab" aria-controls="pills-document" aria-selected="false">
        Documents
        <span id="document-count" class="d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
      </button>
    </li>
    <!--
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false" disabled>Disabled</button>
    </li>
    -->
  </ul>

  <div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0">

      <div class="text-muted mb-3">
        Last updated:
        <?php if ($vehicle->lastUpdate):?>
          <?=Date('m/d h:ia', strtotime($vehicle->lastUpdate))?>
          (<?=Utils::ago($vehicle->lastUpdate)?> ago)
        <?php else:?>
          Never
        <?php endif; ?>
      </div>
      <div class="hstack gap-4 mb-3 justify-content-center">

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-bottle-water fa-3x"></i>
          <div>
            <?php if ($vehicle->restock === false): ?>
              <span class="fw-light badge bg-success w-100">Good</span>
            <?php elseif ($vehicle->restock === true) :?>
              <span class="fw-light badge bg-danger w-100">Needs</span>
            <?php else:?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-vacuum fa-3x"></i>
          <div>
            <?php if ($vehicle->cleanInterior === true): ?>
              <span class="fw-light badge bg-success w-100">Good</span>
            <?php elseif ($vehicle->cleanInterior === false) :?>
              <span class="fw-light badge bg-danger w-100">Needs</span>
            <?php else:?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-car-wash fa-3x"></i>
          <div>
            <?php if ($vehicle->cleanExterior === true): ?>
              <span class="fw-light badge bg-success w-100">Good</span>
            <?php elseif ($vehicle->cleanExterior === false) :?>
              <span class="fw-light badge bg-danger w-100">Needs</span>
            <?php else:?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-gas-pump fa-3x"></i>
          <div>
            <?php if ($vehicle->fuelLevel === null): ?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php elseif ($vehicle->fuelLevel <= 25) :?>
              <div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
                <div class="progress-bar bg-danger overflow-visible" style="width: <?=$vehicle->fuelLevel?>%">&nbsp;<?=fuelLevelAsFractions($vehicle->fuelLevel)?>&nbsp;</div>
              </div>
            <?php else:?>
              <div class="progress mt-1 bg-secondary" role="progressbar" style="height: 20px">
                <div class="progress-bar bg-success overflow-visible" style="width: <?=$vehicle->fuelLevel?>%">&nbsp;<?=fuelLevelAsFractions($vehicle->fuelLevel)?>&nbsp;</div>
              </div>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-engine-warning fa-3x"></i>
          <div>
            <?php if ($vehicle->hasCheckEngine === false): ?>
              <span class="fw-light badge bg-success w-100">Good</span>
            <?php elseif ($vehicle->hasCheckEngine === true) :?>
              <span class="fw-light badge bg-danger w-100">Attention</span>
            <?php else:?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-location-dot fa-3x"></i>
          <div>
            <?php if ($vehicle->locationId === null): ?>
              <span class="fw-light badge bg-body-secondary text-secondary w-100">unknown</span>
            <?php elseif ($vehicle->locationId !== $vehicle->stagingLocationId) :?>
              <span class="fw-light badge bg-danger w-100" data-bs-toggle="tooltip" data-bs-title="<?=$vehicle->currentLocation->name?>">Relocate</span>
            <?php else:?>
              <span class="fw-light badge bg-success w-100">Good</span>
            <?php endif;?>
          </div>
        </div>

        <div class="p-2 d-inline-block text-center" style="width:100px">
          <i class="fa-duotone fa-solid fa-gauge-simple fa-3x"></i>
          <div>
            <span class="fw-light badge bg-primary w-100"><?=$vehicle->mileage ? number_format($vehicle->mileage) : 'unknown'?></span>
          </div>
        </div>

      </div>


      <div class="text-end">
        <button id="btn-update-vehicle-status" class="btn btn-outline-primary btn-sm">Update</button>
      </div>
    </div>

    <div class="tab-pane fade" id="pills-snags" role="tabpanel" aria-labelledby="pills-snags-tab" tabindex="0"></div>

    <div class="tab-pane fade" id="pills-maintenance" role="tabpanel" aria-labelledby="pills-maintenance-tab" tabindex="0">...</div>

    <div class="tab-pane fade" id="pills-document" role="tabpanel" aria-labelledby="pills-document-tab" tabindex="0">...</div>

    <!-- <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">...</div> -->
  </div>  

</div>



<script>
  $(async ƒ => {

    const vehicleId = <?=$vehicleId?>;
    const vehicleUpdateForm = new VehicleUpdateClass('#vehicleUpdateModal');

    vehicleUpdateForm.onUpdate = async function (e, formData) {
      formData.vehicleId = vehicleId;
      const resp = await post('/api/post.update-vehicle.php', formData);
      if (resp?.result) {
        $(document).trigger('vehicleChange');
        $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
        return;
      }
      toastr.error(resp.message);
    }    

    $('#btn-update-vehicle-status').off('click').on('click', e => {
      vehicleUpdateForm.show();
    });

    $('#pills-document').load('section.vehicle-documents.php?vehicleId='+vehicleId);
    $('#pills-snags').load('section.vehicle-snags.php?vehicleId='+vehicleId);

    reFormat();

    // nextTripEventDetail
    const nextTrip = await get('/api/get.next-trip.php', {id: vehicleId});
    if (nextTrip.starts === null) return $('#nextTripEventDetail').html('Nothing scheduled');
    const starts = moment(nextTrip.starts, 'YYYY-MM-DD H:mm:ss');
    $('#nextTripEventDetail').html(
      `<div style="font-size: small" class="text-black-50">` +
      timeago.format(nextTrip.starts).toSentenceCase() + ' (' + starts.format('M/D h:mma') + ') ' 
      + `</div>`
      + `<div><i class="fa-solid fa-circle-right text-primary"></i> ${nextTrip.name}</div>`
    );

  });
</script>

<?php
function fuelLevelAsFractions($fuel_level)
{
  if ($fuel_level <= 10) {
    return 'Empty';
  } elseif ($fuel_level <= 20) {
    return '⅛';
  } elseif ($fuel_level <= 30) {
    return '¼';
  } elseif ($fuel_level <= 40) {
    return '⅜';
  } elseif ($fuel_level <= 60) {
    return '½';
  } elseif ($fuel_level <= 80) {
    return '¾';
  } else {
    return 'Full';
  }
}
?>