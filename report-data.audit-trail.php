<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = new data();
$sql = "
  SELECT * FROM audit_trail
  WHERE
    datetimestamp BETWEEN :from_date AND :to_date
";
$data = [
  'from_date' => $_REQUEST['from_date'],
  'to_date' => $_REQUEST['to_date'].' 23:59:59'
];
if ($_REQUEST['table']) {
  $sql .= " AND affected_tables = :table ";
  $data['table'] = $_REQUEST['table'];
}
if ($_REQUEST['user']) {
  $sql .= " AND user = :user";
  $data['user'] = $_REQUEST['user'];
}
$sql .= " ORDER BY datetimestamp DESC";
?>
<?php if ($rs = $db->get_results($sql, $data)): ?>

  <table class="table table-striped table-sm">
    <thead>
      <tr>
        <th class="fit">Date</th>
        <th class="fit">User</th>
        <th class="fit">Action</th>
        <th class="fit">Affected Table</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rs as $item): ?>
        <tr class="detail" data-id="<?=$item->id?>">
          <td class="fit"><?=date('d/m H:i', strtotime($item->datetimestamp))?></td>
          <td class="fit"><?=$item->user?></td>
          <td class="fit"><?=$item->action?></td>
          <td class="fit"><?=$item->affected_tables?></td>
          <td><?=$item->description?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <div class="alert alert-primary">There are no records matching your query.</div>

<?php endif; ?>

<script type="text/javascript">
	$(async ƒ => {

    $('.detail').on('dblclick', async ƒ => {
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      // location.href = `page.audit-detail.php?id=${encodeURIComponent(id)}`;
      $('#master').addClass('d-none');
      $('#detail').removeClass('d-none').load(`section.audit-trail-detail.php?id=${encodeURIComponent(id)}`);
    });

  });
</script>
