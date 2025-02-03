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
      <tr class="table-dark">
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

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-airport-locations';
    const loadOnClick = {
      page: 'section.edit-airport-location.php',
      tab: 'edit-airport-location',
      title: 'Airport Location (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'airportLocationChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-airport-location').off('click').on('click', ƒ => {
      app.openTab('edit-airport-location', 'Airport Location (add)', `section.edit-airport-location.php`);
    });

  });

</script>
