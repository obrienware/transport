<?php
require_once 'autoload.php';

use Transport\EmailTemplates;
use Transport\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$template = new EmailTemplates($id);

if (!is_null($id) && !$template->getId()) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">

  <div class="row mb-3">
    <div class="col">
      <h3>Template: <?=$template->name?></h3>
      Available variables for this template:
      <?php foreach ($template->availableVariables as $variable): ?>
        <span class="badge bg-primary fw-light fs-6">{{<?=$variable?>}}</span>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <label for="template-content" class="form-label">Content</label>
      <textarea class="form-control font-monospace" id="template-content" rows="15"><?=$template->content?></textarea>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col">
      <button class="btn btn-primary" id="save-template">Save</button>
    </div>
  </div>
    
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    $('#save-template').off('click').on('click', async ƒ => {
      const content = $('#template-content').val();
      const resp = await net.post('/api/post.save-email-template.php', {
        id: <?=$template->getId()?>,
        content: content
      });
      if (resp?.result) {
        app.closeOpenTab();
        ui.toastr.success('Template saved.', 'Success');
      }
      ui.toastr.error(resp.result.errors[2], 'Error');
      console.log(resp);
    });

  });

</script>
