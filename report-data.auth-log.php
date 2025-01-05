<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = data::getInstance();
$query = "
  SELECT * FROM authentication_log
  WHERE
    datetimestamp BETWEEN :from_date AND :to_date
";
$params = [
  'from_date' => $_REQUEST['from_date'],
  'to_date' => $_REQUEST['to_date'].' 23:59:59'
];
$query .= " ORDER BY datetimestamp DESC";
?>
<?php if ($rows = $db->get_rows($query, $params)): ?>

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
      <?php foreach ($rows as $row): ?>
        <tr class="<?=($row->successful) ? 'table-success' : 'table-danger'?>">
          <td class="fit"><?=date('d/m H:i', strtotime($row->datetimestamp))?></td>
          <td class="fit"><?=$row->username?></td>
          <td class="fit"><?=$row->password?></td>
          <td><?=$row->successful?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php else: ?>

  <div class="alert alert-primary">There are no records matching your query.</div>

<?php endif; ?>
