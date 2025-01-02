<?php require_once 'class.guest.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Contacts</h2>
    <button id="btn-add-guest" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Contact
    </button>
  </div>
  <table id="table-guests" class="table table-striped table-hover row-select">
    <thead>
      <tr>
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
                  <span class="badge bg-success">Opted In</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
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

    if ( $.fn.dataTable.isDataTable( '#table-guests' ) ) {
      dataTable = $('#table-guests').DataTable();
    } else {
      dataTable = $('#table-guests').DataTable({
        responsive: true,
        paging: true,
      });
    }

    function bindRowClick () {
      $('#table-guests tbody tr').on('click', ƒ => {
        ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
        const self = ƒ.currentTarget;
        const id = $(self).data('id');
        targetId = id;
        app.openTab('edit-guest', 'Contact (edit)', `section.edit-guest.php?id=${id}`);
      });
    }
    bindRowClick()
    dataTable.on('draw.dt', bindRowClick);

    $('#btn-add-guest').off('click').on('click', ƒ => {
      app.openTab('edit-guest', 'Contact (edit)', `section.edit-guest.php`);
    });

    $(document).off('guestChange.ns').on('guestChange.ns', reloadSection);

  });

</script>
