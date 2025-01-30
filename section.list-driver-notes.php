<?php
require_once 'autoload.php';

use Transport\DriverNote;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Driver Notes</h2>
    <button id="btn-add-driver-note" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Note
    </button>
  </div>
  <table id="tbl-driver-notes" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>Title</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = DriverNote::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->title?></td>
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

    if ($.fn.dataTable.isDataTable('#tbl-driver-notes')) {
      dataTable = $('#tbl-driver-notes').DataTable();
    } else {
      dataTable = $('#tbl-driver-notes').DataTable({
        responsive: true
      });
    }

    $('#tbl-driver-notes tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-driver-note', 'Note (edit)', `section.edit-driver-note.php?id=${id}`);
    });

    $('#btn-add-driver-note').off('click').on('click', ƒ => {
      app.openTab('edit-driver-note', 'Note (add)', `section.edit-driver-note.php`);
    });

    $(document).off('driverNoteChange.ns').on('driverNoteChange.ns', reloadSection);

  });

</script>

