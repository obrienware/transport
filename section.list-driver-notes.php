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

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'tbl-driver-notes';
    const loadOnClick = {
      page: 'section.edit-driver-note.php',
      tab: 'edit-driver-note',
      title: 'Note (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'driverNoteChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-driver-note').off('click').on('click', ƒ => {
      app.openTab('edit-driver-note', 'Note (add)', `section.edit-driver-note.php`);
    });

  });

</script>

