<?php require_once 'class.location.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Locations</h2>
    <button id="btn-add-location" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Location
    </button>
  </div>
  <table id="table-locations" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>Name</th>
        <th>Map Address</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Location::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->name?></td>
            <td><?=$row->map_address?></td>
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

    if ($.fn.dataTable.isDataTable('#table-locations')) {
      dataTable = $('#table-locations').DataTable();
    } else {
      dataTable = $('#table-locations').DataTable({
        responsive: true,
        paging: true,
      });
    }

    function bindRowClick () {
      $('#table-locations tbody tr').off('click').on('click', ƒ => {
        ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
        const self = ƒ.currentTarget;
        const id = $(self).data('id');
        targetId = id;
        app.openTab('edit-location', 'Location (edit)', `section.edit-location.php?id=${id}`);
      });
    }
    bindRowClick()
    dataTable.on('draw.dt', bindRowClick);

    $('#btn-add-location').off('click').on('click', ƒ => {
      app.openTab('edit-location', 'Location (add)', `section.edit-location.php`);
    });

    $(document).off('locationChange.ns').on('locationChange.ns', reloadSection);

  });

</script>
