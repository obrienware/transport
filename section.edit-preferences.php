<?php 
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);
$prefs = $user->preferences;
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

<script type="text/javascript">

  $(async ƒ => {
    <?php if ($prefs->dailyDigestEmails):?> $('#dailyDigestEmails').prop('checked', true);<?php endif; ?>

    function getData() {
      const data = {};
      data.dailyDigestEmails = $('#dailyDigestEmails').prop('checked');
      return data;
    }

    $('#btn-save-preferences').off('click').on('click', async e => {
      const data = getData();
      const resp = await post('/api/post.save-user-preferences.php', data);
      console.log(resp);
      app.closeOpenTab();
    });
  });

</script>