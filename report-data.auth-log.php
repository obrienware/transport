<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = new data();
$sql = "
  SELECT * FROM authentication_log
  WHERE
    datetimestamp BETWEEN :from_date AND :to_date
";
$data = [
  'from_date' => $_REQUEST['from_date'],
  'to_date' => $_REQUEST['to_date'].' 23:59:59'
];
$sql .= " ORDER BY datetimestamp DESC";
?>
<?php if ($rs = $db->get_results($sql, $data)): ?>

  <table class="table table-striped table-sm">
    <thead>
      <tr>
        <th class="fit">Date</th>
        <th class="fit">Username</th>
        <th class="fit">Password</th>
        <th>Successful</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rs as $item): ?>
        <tr class="<?=($item->successful) ? 'table-success' : 'table-danger'?>">
          <td class="fit"><?=date('d/m H:i', strtotime($item->datetimestamp))?></td>
          <td class="fit"><?=$item->username?></td>
          <td class="fit"><?=$item->password?></td>
          <td><?=$item->successful?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <div class="alert alert-primary">There are no records matching your query.</div>

<?php endif; ?>
