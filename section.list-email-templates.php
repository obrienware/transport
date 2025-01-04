<?php require_once 'class.email-templates.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Email Templates</h2>
  </div>
  <table id="table-templates" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = EmailTemplates::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->name?></td>
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

    if ($.fn.dataTable.isDataTable('#table-templates')) {
      dataTable = $('#table-templates').DataTable();
    } else {
      dataTable = $('#table-templates').DataTable({
        responsive: true,
        // paging: true,
      });
    }

    function bindRowClick () {
      $('#table-templates tbody tr').off('click').on('click', ƒ => {
        ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
        const self = ƒ.currentTarget;
        const id = $(self).data('id');
        targetId = id;
        app.openTab('edit-template', 'Email Template (edit)', `section.edit-email-template.php?id=${id}`);
      });
    }
    bindRowClick()
    dataTable.on('draw.dt', bindRowClick);

  });

</script>
