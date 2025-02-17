<?php
require_once 'autoload.php';

use Transport\EmailTemplates;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$template = new EmailTemplates($id);

if (!is_null($id) && !$template->getId())
{
  exit(Utils::showResourceNotFound());
}
$templateId = $template->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('emailTemplates:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<h3>Template: <?= $template->name ?></h3>
Available variables for this template:
<?php foreach ($template->availableVariables as $variable): ?>
  <span class="badge bg-primary fw-light fs-6">{{<?= $variable ?>}}</span>
<?php endforeach; ?>

<div class="row">
  <div class="col">
    <label for="template-content" class="form-label">Content</label>
    <textarea class="form-control font-monospace" id="template-content" rows="15"><?= $template->content ?></textarea>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:emailTemplate', '<?= $templateId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'emailTemplates',
        url: 'section.list-email-templates.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:emailTemplate')) {
      $(document).on('buttonSave:emailTemplate', async (e, id) => {
        const templateId = id;
        const content = $('#template-content').val();
        const resp = await net.post('/api/post.save-email-template.php', {
          id: templateId,
          content: content
        });
        if (resp?.result) {
          ui.toastr.success('Template saved.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

  });
</script>