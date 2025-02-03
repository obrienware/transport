<?php 
require_once 'autoload.php';

use Transport\Location;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Locations</h2>
    <button id="btn-add-location" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Location
    </button>
  </div>
  <table id="table-locations" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
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

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-locations';
    const loadOnClick = {
      page: 'section.edit-location.php',
      tab: 'edit-location',
      title: 'Location (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'locationChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-location').off('click').on('click', ƒ => {
      app.openTab('edit-location', 'Location (add)', `section.edit-location.php`);
    });

  });

</script>
