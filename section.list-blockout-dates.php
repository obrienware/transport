<?php require_once 'class.blockout.php'; ?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Driver Blockout Dates</h2>
    <button id="btn-add-blockout" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Blockout
    </button>
  </div>
  <table id="table-blockouts" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>Name</th>
        <th class="fit">FROM</th>
        <th class="fit">TO</th>
        <th data-dt-order="disable">Note</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rs = Blockout::getBlockouts()): ?>
        <?php foreach ($rs as $item): ?>
          <tr data-id="<?=$item->id?>">
            <td><?=$item->user?></td>
            <td class="datetime short fit" data-order="<?=$item->from_datetime?>"><?=$item->from_datetime?></td>
            <td class="datetime short fit" data-order="<?=$item->to_datetime?>"><?=$item->to_datetime?></td>
            <td><?=$item->note?></td>
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

    if ($.fn.dataTable.isDataTable('#table-blockouts')) {
      dataTable = $('#table-blockouts').DataTable();
    } else {
      dataTable = $('#table-blockouts').DataTable({
        responsive: true
      });
    }

    $('#table-blockouts tbody tr').off('click').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-blockout', 'Blockout (edit)', `section.edit-blockout.php?id=${id}`);
    });

    $('#btn-add-blockout').off('click').on('click', ƒ => {
      app.openTab('new-blockout', 'Blockout (new)', `section.new-blockout.php`);
    });

    $(document).off('blockoutChange.ns').on('blockoutChange.ns', reloadSection);
  });

</script>
