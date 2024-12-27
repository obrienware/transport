<?php require_once 'class.airline.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Airlines</h2>
    <button id="btn-add-airline" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Airline
    </button>
  </div>
  <table id="table-airlines" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th>AirLine</th>
        <th data-bs-toggle="tooltip" data-bs-title="Flight Number Prefix.">Prefix</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rs = Airline::getAirlines()): ?>
        <?php foreach ($rs as $item): ?>
          <tr data-id="<?=$item->id?>">
            <td>
              <?php if ($item->image_filename): ?>
                <img src="/images/airlines/<?=$item->image_filename?>" style="max-height:35px">
              <?php endif; ?>
            </td>
            <td><?=$item->name?></td>
            <td><?=$item->flight_number_prefix?></td>
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

    if ($.fn.dataTable.isDataTable('#table-airlines')) {
      dataTable = $('#table-airlines').DataTable();
    } else {
      dataTable = $('#table-airlines').DataTable({
        responsive: true
      });
    }

    $('#table-airlines tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-airline', 'Airline (edit)', `section.edit-airline.php?id=${id}`);
    });

    $('#btn-add-airline').off('click').on('click', ƒ => {
      app.openTab('edit-airline', 'Airline (add)', `section.edit-airline.php`);
    });

    $(document).off('airlineChange.ns').on('airlineChange.ns', reloadSection);

  });

</script>