<?php
require_once 'autoload.php';

use Transport\EmailTemplates;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('emailTemplates:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Email Templates</h2>
</div>

<table id="table-templates" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Name</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = EmailTemplates::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->name ?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('loadMainSection', {sectionId: 'emailTemplates', url: 'section.edit-email-template.php?id=<?= $row->id ?>', forceReload: true})"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<script>
  if (!documentEventExists('emailTemplates:reloadList')) {
    $(document).on('emailTemplates:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'emailTemplates',
        url: 'section.list-email-templates.php',
        forceReload: true
      });
    });
  }

  $(async ƒ => {
    const tableId = 'table-templates';
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'emailTemplateChange';
    const parentSectionId = `#<?= $_GET["loadedToId"] ?>`;
    const thisURI = `<?= $_SERVER['REQUEST_URI'] ?>`;

    initListPage({
      tableId,
      dataTableOptions,
      reloadOnEventName,
      parentSectionId,
      thisURI
    });
  });
</script>