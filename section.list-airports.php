<?php
require_once 'autoload.php';

use Transport\Airport;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Airports</h2>
    <button id="btn-add-airport" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Airport
    </button>
  </div>
  <table id="table-airports" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>IATA</th>
        <th>Airport</th>
        <th data-bs-toggle="tooltip" data-bs-title="Time to arrive at airport before scheduled flight.">Lead Time</th>
        <th data-bs-toggle="tooltip" data-bs-title="Time to travel from airport to staging location.">Travel Time</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Airport::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?= $row->id ?>">
            <td><?= $row->iata ?></td>
            <td><?= $row->name ?></td>
            <td data-order="<?= $row->lead_time ?>"><?= intdiv($row->lead_time, 60) . ':' . sprintf('%02s', ($row->lead_time % 60)) ?></td>
            <td data-order="<?= $row->travel_time ?>"><?= intdiv($row->travel_time, 60) . ':' . sprintf('%02s', ($row->travel_time % 60)) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-airports';
    const loadOnClick = {
      page: 'section.edit-airport.php',
      tab: 'edit-airport',
      title: 'Airport (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'airportChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-airport').off('click').on('click', ƒ => {
      app.openTab('edit-airport', 'Airport (add)', `section.edit-airport.php`);
    });

  });
</script>