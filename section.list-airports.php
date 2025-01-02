<?php require_once 'class.airport.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Airports</h2>
    <button id="btn-add-airport" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Airport
    </button>
  </div>
  <table id="table-airports" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>IATA</th>
        <th>Airport</th>
        <th data-bs-toggle="tooltip" data-bs-title="Time to arrive at airport before scheduled flight.">Lead Time</th>
        <th data-bs-toggle="tooltip" data-bs-title="Time to travel from airport to staging location.">Travel Time</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Airport::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->iata?></td>
            <td><?=$row->name?></td>
            <td data-order="<?=$row->lead_time?>"><?=intdiv($row->lead_time, 60).':'.sprintf('%02s', ($row->lead_time % 60))?></td>
            <td data-order="<?=$row->travel_time?>"><?=intdiv($row->travel_time, 60).':'.sprintf('%02s', ($row->travel_time % 60))?></td>
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

    if ($.fn.dataTable.isDataTable('#table-airports')) {
      dataTable = $('#table-airports').DataTable();
    } else {
      dataTable = $('#table-airports').DataTable({
        responsive: true
      });
    }

    $('#table-airports tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-airport', 'Airport (edit)', `section.edit-airport.php?id=${id}`);
    });

    $('#btn-add-airport').off('click').on('click', ƒ => {
      app.openTab('edit-airport', 'Airport (add)', `section.edit-airport.php`);
    });

    $(document).off('airportChange.ns').on('airportChange.ns', reloadSection);

  });

</script>
