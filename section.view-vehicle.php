<?php
require_once 'class.snag.php';
require_once 'class.vehicle.php';
$vehicleId = $_REQUEST['id'];
$vehicle = new Vehicle($vehicleId);
?>
<div class="container-fluid">

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
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Passengers</th>
      <td><?=$vehicle->passengers?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Requires a CDL driver</th>
      <td><?=$vehicle->requireCDL ? 'Yes' : 'No' ?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Staging Location</th>
      <td><?=$vehicle->stagingLocation?></td>
    </tr>
  </table>


  <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab" aria-controls="pills-status" aria-selected="true">Status</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-snags-tab" data-bs-toggle="pill" data-bs-target="#pills-snags" type="button" role="tab" aria-controls="pills-snags" aria-selected="false">Snags</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Maintenance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false" disabled>Disabled</button>
    </li>
  </ul>

  <div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0">
      <table class="table table-bordered table-sm">
        <caption class="caption-top">As at <?=$vehicle->lastUpdate ? Date('m/d h:ia', strtotime($vehicle->lastUpdate)) : ''?></caption>
        <tr>
          <th class="fit px-2 bg-body-secondary">Mileage</th>
          <td><?=$vehicle->mileage ? number_format($vehicle->mileage) : '<div class="badge bg-dark-subtle">Unknown</div>'?></td>
          <th class="fit px-2 bg-body-secondary">Check Engine Light On</th>
          <td><?=$vehicle->hasCheckEngine ? '<div class="badge bg-danger fs-6">YES</div>' : 'No' ?></td>
        </tr>
        <tr>
          <th class="fit px-2 bg-body-secondary">Location</th>
          <td><?=$vehicle->currentLocation ?: 'Unverified'?></td>
          <th class="fit px-2 bg-body-secondary">Needs cleaning</th>
          <td>
            <?php if ($vehicle->cleanExterior === 0): ?>
              <div class="badge bg-danger">Exterior</div>
            <?php endif; ?>
            <?php if ($vehicle->cleanInterior === 0): ?>
              <div class="badge bg-danger">Interior</div>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th class="fit px-2 bg-body-secondary">Fuel Level</th>
          <td>
            <?php if ($vehicle->fuelLevel): ?>
              <?php if ($vehicle->fuelLevel <= 25): ?>
                <span class="badge bg-danger fs-6"><?=$vehicle->fuelLevel?>%</span>
              <?php else:?>
                <?=$vehicle->fuelLevel?>%
              <?php endif; ?>
            <?php else:?>
              <div class="badge bg-dark-subtle">Unknown</div>
            <?php endif; ?>
          </td>
          <th class="fit px-2 bg-body-secondary">Needs restocking</th>
          <td>
            <?php if ($vehicle->restock === 1): ?>
              <div class="badge bg-danger fs-6">YES</div>
            <?php elseif ($vehicle->restock === 0): ?>
              No
            <?php else: ?>
              <div class="badge bg-dark-subtle">Unknown</div>
            <?php endif; ?>
          </td>
        </tr>
      </table>

      <div class="text-end">
        <button id="btn-update-vehicle-status" class="btn btn-outline-primary btn-sm">Update</button>
      </div>
    </div>

    <div class="tab-pane fade" id="pills-snags" role="tabpanel" aria-labelledby="pills-snags-tab" tabindex="0">
      <!-- SNAGLIST -->
      <?php if ($rs = Snag::getSnags($vehicleId)): ?>
        <table class="table table-bordered table-sm mb-0">
          <thead>
            <tr>
              <th class="fit">Date</th>
              <th>Description</th>
              <th>Acknowledged</th>
              <th>Resolution</th>
              <th>Comments</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rs as $item): ?>
              <tr>
                <td class="datetime short" style="font-size:small"><?=$item->datetimestamp?></td>
                <td>
                  <div><?=$item->description?></div>
                  <div><div class="badge bg-dark-subtle"><?=$item->created_by?></div></div>
                </td>
                <td>
                  <?php if ($item->acknowledged): ?>
                    <div><div class="badge bg-dark-subtle"><?=$item->acknowledged_by?></div></div>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($item->resolved): ?>
                    <div><?=$item->resolution?></div>
                    <div><div class="badge bg-dark-subtle"><?=$item->resolved_by?></div></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div><?=$item->comments?></div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="card-body text-center">
          There are no snags logged yet for this vehicle
        </div>
      <?php endif; ?>

    </div>

    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">...</div>
    <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">...</div>
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
        $('#<?=$_REQUEST["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
      }
    }    

    $('#btn-update-vehicle-status').off('click').on('click', e => {
      vehicleUpdateForm.show();
    });

    reFormat();

  });
</script>