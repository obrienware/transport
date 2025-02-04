<?php 
require_once 'autoload.php';

use Transport\User;

$user = new User($_SESSION['user']->id);
$prefs = $user->getPreferences();
?>
<div class="container">

  <h2>Personal Preferences</h2>
  <div class="mb-3">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="" id="dailyDigestEmails">
      <label class="form-check-label" for="dailyDigestEmails">
        Receive Daily Digest Emails
      </label>
    </div>
  </div>

  <div class="text-end">
    <button id="btn-save-preferences" class="btn btn-primary">Save</button>
  </div>

</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {
    <?php if ($prefs->dailyDigestEmails):?> $('#dailyDigestEmails').prop('checked', true);<?php endif; ?>

    function getData() {
      const data = {};
      data.dailyDigestEmails = $('#dailyDigestEmails').prop('checked');
      return data;
    }

    $('#btn-save-preferences').off('click').on('click', async e => {
      const data = getData();
      const resp = await net.post('/api/post.save-user-preferences.php', data);
      app.closeOpenTab();
      if (resp.result) return ui.toastr('Preferences saved', 'success');
      ui.toastr('Error saving preferences', 'error');
    });
  });

</script>