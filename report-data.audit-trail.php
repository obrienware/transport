<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = data::getInstance();
$query = "
  SELECT * FROM audit_trail
  WHERE
    datetimestamp BETWEEN :from_date AND :to_date
";
$params = [
  'from_date' => $_REQUEST['from_date'],
  'to_date' => $_REQUEST['to_date'].' 23:59:59'
];
if ($_REQUEST['table']) {
  $query .= " AND affected_tables = :table ";
  $params['table'] = $_REQUEST['table'];
}
if ($_REQUEST['user']) {
  $query .= " AND user = :user";
  $params['user'] = $_REQUEST['user'];
}
$query .= " ORDER BY datetimestamp DESC";
?>
<?php if ($rows = $db->get_rows($query, $params)): ?>

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
      <?php foreach ($rows as $row): ?>
        <tr class="detail" data-id="<?=$row->id?>">
          <td class="fit"><?=date('d/m H:i', strtotime($row->datetimestamp))?></td>
          <td class="fit"><?=$row->user?></td>
          <td class="fit"><?=$row->action?></td>
          <td class="fit"><?=$row->affected_tables?></td>
          <td><?=$row->description?></td>
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
