<?php
require 'inc.user.php';
allowRoles(['admin','manager','developer']);
?>

<?php if ($_GET['node'] === 'system' && !allowedRoles(['admin','developer'])): ?>
  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold"><i class="fa-sharp-duotone fa-solid fa-lock"></i> Denied</h1>
      <p class="lead">Sorry, your role does not permit you to edit this node. If you have any questions about this, please contact an admin.</p>
    </div>
  </div>
  <?php include 'inc.footer.php'; die();?>
<?php endif; ?>

<div class="container-fluid mt-3">
  <h1 id="node-title" class="mb-0 fw-bold"></h1>
  <div class="mb-4 text-danger text-opacity-75">Please edit with caution</div>
	<div id="editor" class="border" style="height:calc(100vh - 350px) !important; min-height: 300px; font-size:max(10px, min(1.2vw, 14px)); resize: vertical">loading...</div>


  <div id="node-last-updated" class="text-muted text-end fs-6"></div>
  <div id="buttons" class="text-end mt-2">
    <button id="save-node-config" class="btn btn-primary px-4">Save</button>
  </div>
</div>

<script type="text/javascript">
  $(async ƒ => {

    const node = '<?=$_GET['node']?>';
    const data = await get(`/api/get.node-config.php?node=${node}`);
    let masterConfig;

    try {
      masterConfig = JSON.parse(data.json);
    } catch (err) {

      console.error(err.message);
      $('#editor').text('ERROR loading config!');
      $('#buttons').hide();
      return;

    }

    const lastUpdate = masterConfig.updated;
    $('#node-title').html('Node: ' + node);
    if (lastUpdate) $('#node-last-updated').html(`Last updated: ${lastUpdate.date} by ${lastUpdate.by}`);

    const editor = ace.edit('editor', { 
      mode:'ace/mode/json5',
      theme: 'ace/theme/xcode',
      tabSize: 2,
      showPrintMargin: false
    });
    editor.setValue(data.json5, -1);
    // setTimeout(ƒ => editor.getSession().foldAll(5, 0, 1), 100);


    $('#save-node-config').off('click').on('click', async ƒ => {

      const configString = editor.getValue();
      let nodeConfig;

      // Check if the configString is valid json5
      try {
        nodeConfig = JSON5.parse(configString);
      } catch (err) {
        console.error(err);
        alertError(err.message, 'Invalid JSON5');
        return;
      }

      const resp = await post(`/api/post.node-config.php`, {configString, nodeConfig, node});
      if (resp.result) {
        return toastr.success(`Config updated.`, 'Success');
      }
      toastr.error(`Config not updated`, 'ERROR');
    });

  });
</script>
