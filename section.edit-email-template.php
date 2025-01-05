<?php
require_once 'class.email-templates.php';
$template = new EmailTemplates($_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$template->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that template! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

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

  <script type="text/javascript">

    $(async ƒ => {

      $('#save-template').off('click').on('click', async ƒ => {
        const content = $('#template-content').val();
        const resp = await post('/api/post.save-email-template.php', {
          id: <?=$template->getId()?>,
          content: content
        });
        if (resp?.result) {
          app.closeOpenTab();
          toastr.success('Template saved.', 'Success');
        }
        toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      });

    });

  </script>

<?php endif; ?>