<?php 
require_once 'autoload.php';

use Transport\Blockout;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Driver Blockout Dates</h2>
    <button id="btn-add-blockout" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Blockout
    </button>
  </div>
  <table id="table-blockouts" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>Name</th>
        <th class="fit">From</th>
        <th class="fit">To</th>
        <th data-dt-order="disable">Note</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Blockout::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->user?></td>
            <td class="datetime short fit" data-order="<?=$row->from_datetime?>"><?=$row->from_datetime?></td>
            <td class="datetime short fit" data-order="<?=$row->to_datetime?>"><?=$row->to_datetime?></td>
            <td><?=$row->note?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-blockouts';
    const loadOnClick = {
      page: 'section.edit-blockout.php',
      tab: 'edit-blockout',
      title: 'Blockout (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'blockoutChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-blockout').off('click').on('click', ƒ => {
      app.openTab('new-blockout', 'Blockout (new)', `section.new-blockout.php`);
    });
  });

</script>
