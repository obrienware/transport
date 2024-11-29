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
    <caption class="caption-top">As at <?=$vehicle->lastUpdate ? Date('m/d h:ia', strtotime($vehicle->lastUpdate)) : ''?></caption>
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
    <div class="card-footer text-center">
      <button class="btn btn-outline-primary btn-sm">Add a snag</button>
    </div>
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