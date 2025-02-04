<?php
require_once 'autoload.php';

use Transport\Database;

$treewalker = new TreeWalker([
  "debug" => false,          //true => return the execution time, false => not
  "returntype" => "array"   //Returntype = ["obj","jsonstring","array"]
]);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;

$db = Database::getInstance();
$query = "SELECT * FROM audit_trail WHERE id = :id";
$params = ['id' => $id];
$row = $db->get_row($query, $params);
if ($row->before && $row->after) {
	$before = json_decode($row->before);
	$after = json_decode($row->after);
	if (is_array($before)) $before = $before[0];
	$result = $treewalker->getdiff($after, $before, true);
  // echo '<pre>'; print_r($result); echo '</pre>';
	if (is_array($result['new']) && count($result['new']) === 0) {
		unset($result['new']);
	}
	if (is_array($result['removed']) && count($result['removed']) === 0) {
		unset($result['removed']);
	}
	if (is_array($result['edited']) && count($result['edited']) === 0) {
		unset($result['edited']);
	}
	if (isset($result['time'])) unset($result['time']);
}
?>
<h1>Audit Trail Detail</h1>

<div id="result-div">
	<table class="table table-sm table-bordered">
		<tr>
			<td><small><b>ID</b></small><br/><?=$row->id?></td>
			<td><small><b>User</b></small><br/><?=$row->user?></td>
      <td><small><b>When</b></small><br/><?=$row->datetimestamp?></td>
      <td><small><b>Action</b></small><br/><?=$row->action?></td>
			<td><small><b>Affected Table(s)</b></small><br/><?=$row->affected_tables?></td>
		</tr>
		<tr>
			<td colspan="5"><small><b>Description</b></small><br/><?=$row->description?></td>
		</tr>
	</table>

  <?php if ($result['edited']): ?>
    <table class="table table-striped table-bordered table-sm mt-4">
      <thead class="bg-yellow">
        <tr>
          <th>Fieldname</th>
          <th>Changed From (Value)</th>
          <th>To (Value)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result['edited'] as $field => $item): ?>
          <?php if ($field === 'modified' || $field === 'modifiedby') continue; ?>
          <tr>
            <th class="fit bg-primary text-white px-4"><?=$field?></th>
            <td><pre class="mb-0"><?=$item['oldvalue']?></pre></td>
            <td><pre class="mb-0"><?=$item['newvalue']?></pre></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif;?>
</div>

<div class="text-right">
  <button id="btn-close" class="btn btn-primary px-4">Close</button>
</div>

<script type="text/javascript">
	$(async ƒ => {

    $('#btn-close').off('click').on('click', ƒ => {
      $('#detail').addClass('d-none');
      $('#master').removeClass('d-none');
      $('#detail').html('');
    });

  });
</script>
