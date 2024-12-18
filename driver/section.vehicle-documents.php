<?php
require_once 'class.data.php';
$db = new data();
$sql = "SELECT * FROM vehicle_documents WHERE archived IS NULL AND vehicle_id = :vehicle_id ORDER BY uploaded DESC";
$data = ['vehicle_id' => $_REQUEST['id']];
?>
<?php if ($rs = $db->get_results($sql, $data)): ?>
  <h4>Documents</h4>
  <ul class="list-group mb-4">
    <?php foreach ($rs as $item): ?>
      <a href="/documents/<?=$item->filename?>" class="list-group-item" target="_blank"><?=$item->name?></a>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>