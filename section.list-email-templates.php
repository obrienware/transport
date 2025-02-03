<?php 
require_once 'autoload.php';

use Transport\EmailTemplates;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Email Templates</h2>
  </div>
  <table id="table-templates" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = EmailTemplates::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->name?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-templates';
    const loadOnClick = {
      page: 'section.edit-email-template.php',
      tab: 'edit-template',
      title: 'Email Template (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'emailTemplateChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});
    
  });

</script>
