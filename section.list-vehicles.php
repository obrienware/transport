<?php 
require_once 'autoload.php';

use Transport\Vehicle;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-2">
    <h2>Vehicles</h2>
    <button id="btn-add-vehicle" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Vehicle
    </button>
  </div>
  <table id="table-vehicles" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th class="fit">ID</th>
        <th class="fit no-sort no-search">&nbsp;</th>
        <th class="">Name</th>
        <th class="no-sort">Vehicle Description</th>
        <th class="">License Plate</th>
        <th data-bs-toggle="tooltip" data-bs-title="Max Passengers" class="fit">PAX</th>
        <th data-bs-toggle="tooltip" data-bs-title="Requires a CDL driver" class="fit no-sort no-search">CDL</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Vehicle::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td class="fit"><?=$row->id?></td>
            <td class="fit align-middle">
              <?php if ($row->check_engine): ?><i title="Check Engine Warning" class="fa-solid fa-engine-warning fa-xl me-2" style="color:var(--bs-orange)"></i><?php endif;?>
              <?php if ($row->clean_interior === 0): ?><i title="Needs a clean/vacuum" class="fa-duotone fa-solid fa-vacuum fa-xl text-info-emphasis me-2"></i><?php endif;?>
              <?php if ($row->clean_exterior === 0): ?><i title="Needs a wash" class="fa-duotone fa-solid fa-car-wash fa-xl text-info-emphasis me-2"></i><?php endif;?>
              <?php if ($row->restock === 1): ?><i title="Needs to be restocked (snacks/refreshments)" class="fa-duotone fa-solid fa-bottle-water fa-xl text-info-emphasis me-2"></i><?php endif;?>
            <td class="align-middle p-0" data-order="<?=$row->name?>">
              <span class="tag w-100" style="font-size:1rem" data-color="<?=$row->color?>"><?=$row->name?></span>
            </td>
            <td><?=$row->description?></td>
            <td data-order="<?=$row->license_plate?>">
              <?php if ($row->license_plate): ?>
                <span class="tag tag-primary px-2"><?=$row->license_plate?></span>
              <?php endif; ?>
            </td>
            <td class="text-center align-middle fit p-0"><?=$row->passengers?></td>
            <td class="text-center align-middle fit p-0"><?=($row->require_cdl == 1) ? '<i class="fa-solid fa-circle-check fa-xl text-warning"></i>' : ''?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">

  $('.tag').each((index, self) => {
    const color = $(self).data('color');
    if (!color) return;
    // $(self).css('border-color', color);
    // $(self).css('background-color', hexToRgba(color, .7));
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