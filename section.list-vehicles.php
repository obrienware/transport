<?php require_once 'class.vehicle.php';?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Vehicles</h2>
    <button id="btn-add-vehicle" class="btn btn-outline-primary btn-sm my-auto px-3">
      <i class="fa-duotone fa-solid fa-car"></i>
      Add Vehicle
    </button>
  </div>
  <table id="table-vehicles" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th class="fit" data-dt-order="disable">&nbsp;</th>
        <th data-dt-order="disable">&nbsp;</th>
        <th>Name</th>
        <th>Vehicle Description</th>
        <th data-bs-toggle="tooltip" data-bs-title="Max Passengers" class="fit">PAX</th>
        <th data-dt-order="disable" data-bs-toggle="tooltip" data-bs-title="Requires a CDL driver" class="fit">CDL</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rs = Vehicle::getVehicles()): ?>
        <?php foreach ($rs as $item): ?>
          <?php $rowClass = ($item->check_engine) ? 'table-warning' : '';?>
          <tr data-id="<?=$item->id?>" class="<?=$rowClass?>">
            <?php if ($item->color): ?>
              <td class="fit">
                <i class="bi bi-circle-fill" style="color:<?=$item->color?>"></i>
              </td>
            <?php else: ?>
              <td class="fit">&nbsp;</td>
            <?php endif;?>
            <td>
              <?php if ($item->check_engine): ?>
                <i class="fa-duotone fa-solid fa-engine-warning fa-lg" style="color:var(--bs-orange)"></i>
              <?php endif; ?>
            </td>
            <td><?=$item->name?></td>
            <td><?=$item->description?></td>
            <td class="text-center fit"><?=$item->passengers?></td>
            <td class="text-center fit"><?=($item->require_cdl) ? '<i class="bi bi-check-square text-success"></i>' : ''?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">

  $(async ƒ => {

    let dataTable;
    let targetId;

    function reloadSection () {
      $('#<?=$_REQUEST["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    if ($.fn.dataTable.isDataTable('#table-vehicles')) {
      dataTable = $('#table-vehicles').DataTable();
    } else {
      dataTable = $('#table-vehicles').DataTable({
        responsive: true
      });
    }

    $('#table-vehicles tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('view-vehicle', 'Vehicle', `section.view-vehicle.php?id=${id}`);
    });

    $('#btn-add-vehicle').on('click', ƒ => {
      ƒ.preventDefault();
      app.openTab('edit-vehicle', 'New Vehicle', `section.edit-vehicle.php`);
    });

    $(document).off('vehicleChange.ns').on('vehicleChange.ns', reloadSection);


  });

</script>