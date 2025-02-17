<?php
require_once 'autoload.php';

use Transport\Vehicle;
?>
<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('vehicle:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'vehicles', url: 'section.edit-vehicle.php', forceReload: true });">
    Add Vehicle
  </button>
</div>

<div class="d-flex justify-content-between mt-2">
  <h2>Vehicles</h2>
</div>

<table id="table-vehicles" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th class="fit">ID</th>
      <th class="fit no-sort no-search">&nbsp;</th>
      <th class="" data-priority="1">Name</th>
      <th class="no-sort">Vehicle Description</th>
      <th class="">License Plate</th>
      <th data-bs-toggle="tooltip" data-bs-title="Max Passengers" class="fit">PAX</th>
      <th data-bs-toggle="tooltip" data-bs-title="Requires a CDL driver" class="fit no-sort no-search">CDL</th>
      <th class="fit no-sort" data-priority="2">&nbsp</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Vehicle::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td class="fit"><?= $row->id ?></td>
          <td class="fit align-middle">
            <?php if ($row->check_engine): ?><i title="Check Engine Warning" class="fa-solid fa-engine-warning fa-xl me-2" style="color:var(--bs-orange)"></i><?php endif; ?>
            <?php if ($row->clean_interior === 0): ?><i title="Needs a clean/vacuum" class="fa-duotone fa-solid fa-vacuum fa-xl text-info-emphasis me-2"></i><?php endif; ?>
            <?php if ($row->clean_exterior === 0): ?><i title="Needs a wash" class="fa-duotone fa-solid fa-car-wash fa-xl text-info-emphasis me-2"></i><?php endif; ?>
            <?php if ($row->restock === 1): ?><i title="Needs to be restocked (snacks/refreshments)" class="fa-duotone fa-solid fa-bottle-water fa-xl text-info-emphasis me-2"></i><?php endif; ?>
          <td class="align-middle p-0" data-order="<?= $row->name ?>">
            <span class="tag w-100" style="font-size:1rem" data-color="<?= $row->color ?>"><?= $row->name ?></span>
          </td>
          <td><?= $row->description ?></td>
          <td data-order="<?= $row->license_plate ?>">
            <?php if ($row->license_plate): ?>
              <span class="tag tag-primary px-2"><?= $row->license_plate ?></span>
            <?php endif; ?>
          </td>
          <td class="text-center align-middle fit p-0"><?= $row->passengers ?></td>
          <td class="text-center align-middle fit p-0"><?= ($row->require_cdl == 1) ? '<i class="fa-solid fa-circle-check fa-xl text-warning"></i>' : '' ?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('loadMainSection', {sectionId: 'vehicles', url: 'section.view-vehicle.php?id=<?=$row->id?>', forceReload: true})"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>

  $('.tag').each((index, self) => {
    const color = $(self).data('color');
    if (!color) return;
    $(self).css('background-color', color);
    $(self).css('color', luminanceColor(color));
  });

  if (!documentEventExists('vehicle:reloadList')) {
    $(document).on('vehicle:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'vehicles',
        url: 'section.list-vehicles.php',
        forceReload: true
      });
    });
  }


  $(async ƒ => {

    const tableId = 'table-vehicles';
    const dataTableOptions = {
      responsive: true,
      paging: false,
    };
    const reloadOnEventName = 'vehicleChange';
    const parentSectionId = `#<?= $_GET["loadedToId"] ?>`;
    const thisURI = `<?= $_SERVER['REQUEST_URI'] ?>`;

    initListPage({
      tableId,
      dataTableOptions,
      reloadOnEventName,
      parentSectionId,
      thisURI
    });

  });
</script>