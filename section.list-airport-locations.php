<?php 
require_once 'autoload.php';

use Transport\AirportLocation;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Airport Locations</h2>
    <button id="btn-add-airport-location" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Airport Location
    </button>
  </div>
  <table id="table-airport-locations" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>Airport</th>
        <th>Airline</th>
        <th>&nbsp;</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = AirportLocation::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->airport?></td>
            <td><?=$row->airline?></td>
            <td><?=$row->type?></td>
            <td><?=$row->location?></td>
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
      $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    if ($.fn.dataTable.isDataTable('#table-airport-locations')) {
      dataTable = $('#table-airport-locations').DataTable();
    } else {
      dataTable = $('#table-airport-locations').DataTable({
        responsive: true
      });
    }

    $('#table-airport-locations tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-airport-location', 'Airport Location (edit)', `section.edit-airport-location.php?id=${id}`);
    });

    $('#btn-add-airport-location').off('click').on('click', ƒ => {
      app.openTab('edit-airport-location', 'Airport Location (add)', `section.edit-airport-location.php`);
    });

    $(document).off('airportLocationChange.ns').on('airportLocationChange.ns', reloadSection);

  });

</script>
