<?php 
require_once 'autoload.php';

use Transport\Guest;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Contacts</h2>
    <button id="btn-add-guest" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Contact
    </button>
  </div>
  <table id="table-guests" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>Contact Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th data-bs-toggle="tooltip" data-bs-title="Contact opted in for text notifications">Notifications</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Guest::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->first_name.' '.$row->last_name?></td>
            <td><?=$row->phone_number?></td>
            <td><?=$row->email_address?></td>
            <td>
              <?php if ($row->opt_in): ?>
                <?php if (!$row->opt_out): ?>
                  <span class="badge bg-success fw-light">Opted In</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-guests';
    const loadOnClick = {
      page: 'section.edit-guest.php',
      tab: 'edit-guest',
      title: 'Guest (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'guestChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-guest').off('click').on('click', ƒ => {
      app.openTab('edit-guest', 'Contact (edit)', `section.edit-guest.php`);
    });

  });

</script>
