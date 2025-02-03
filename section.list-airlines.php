<?php 
require_once 'autoload.php';

use Transport\Airline;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Airlines</h2>
    <button id="btn-add-airline" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Airline
    </button>
  </div>
  <table id="table-airlines" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th data-dt-order="disable">&nbsp;</th>
        <th>AirLine</th>
        <th data-bs-toggle="tooltip" data-bs-title="Flight Number Prefix.">Prefix</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Airline::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td>
              <?php if ($row->image_filename): ?>
                <img src="/images/airlines/<?=$row->image_filename?>" style="max-height:35px">
              <?php endif; ?>
            </td>
            <td class="align-middle"><?=$row->name?></td>
            <td class="fw-bold fs-4 align-middle"><?=$row->flight_number_prefix?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-airlines';
    const loadOnClick = {
      page: 'section.edit-airline.php',
      tab: 'edit-airline',
      title: 'Airline (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'airlineChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-airline').off('click').on('click', ƒ => {
      app.openTab('edit-airline', 'Airline (add)', `section.edit-airline.php`);
    });

  });

</script>
