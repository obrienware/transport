<?php 
require_once 'autoload.php';

use Transport\Vehicle;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-2">
    <h2>Vehicles</h2>
    <button id="btn-add-vehicle" class="btn btn-outline-primary btn-sm my-auto px-3">
      <i class="fa-duotone fa-solid fa-car"></i>
      Add Vehicle
    </button>
  </div>
  <table id="table-vehicles" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th class="fit">ID</th>
        <th class="fit" data-dt-order="disable">&nbsp;</th>
        <th>Name</th>
        <th>Vehicle Description</th>
        <th>License Plate</th>
        <th data-bs-toggle="tooltip" data-bs-title="Max Passengers" class="fit">PAX</th>
        <th data-dt-order="disable" data-bs-toggle="tooltip" data-bs-title="Requires a CDL driver" class="fit">CDL</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Vehicle::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td class="fit"><?=$row->id?></td>
            <td class="fit">
              <?php if ($row->check_engine): ?><i class="fa-solid fa-engine-warning fa-xl me-2" style="color:var(--bs-orange)"></i><?php endif;?>
              <?php if ($row->clean_interior === 0): ?><i class="fa-duotone fa-solid fa-vacuum fa-xl text-info-emphasis me-2"></i><?php endif;?>
              <?php if ($row->clean_exterior === 0): ?><i class="fa-duotone fa-solid fa-car-wash fa-xl text-info-emphasis me-2"></i><?php endif;?>
              <?php if ($row->restock === 1): ?><i class="fa-duotone fa-solid fa-bottle-water fa-xl text-info-emphasis me-2"></i><?php endif;?>
            <td>
              <span class="tag w-100" style="font-size:1rem" data-color="<?=$row->color?>"><?=$row->name?></span>
            </td>
            <td><?=$row->description?></td>
            <td><?=$row->license_plate?></td>
            <td class="text-center fit"><?=$row->passengers?></td>
            <td class="text-center fit"><?=($row->require_cdl == 1) ? '<i class="fa-regular fa-square-check fa-lg"></i>' : ''?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">

  $('.tag').each((index, self) => {
    const color = $(self).data('color');
    $(self).css('background-color', color);
    $(self).css('color', luminanceColor(color));
  });

  $(async ƒ => {

    let dataTable;
    let targetId;

    function reloadSection () {
      $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
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