<div class="container">
  <div class="row">
    <div class="col">
      <h1 class="fw-lighter text-bg-primary text-center py-2 mt-3">Transport</h1>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col">
      <label for="user-username" class="form-label">Username</label>
      <input type="text" class="form-control" id="user-username" placeholder="">
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <label for="user-password" class="form-label">Password</label>
      <input type="password" class="form-control" id="user-password" placeholder="">
    </div>
  </div>

  <div class="row">
    <div class="col d-flex justify-content-around">
      <button id="btn-login" class="btn btn-outline-primary px-5">Log In</button>
    </div>
  </div>
</div>

<script>
  $(async ƒ => {

    $('#user-username').on('keyup', async ƒ => {
      if (ƒ.keyCode === 13) return $('#user-password').select().focus();
    });

    $('#password').on('keyup', async ƒ => {
      if (ƒ.keyCode === 13) return $('#btn-login').click();
    });

    $('#btn-login').off('click').on('click', async ƒ => {
      const username = $.trim($('#user-username').val());
      const password = $.trim($('#user-password').val());
      const user = await post('api/post.user-login.php', {username, password});
      if (user === false) {
        return alert('Sorry, username/password is incorrect. Please try again');
      }
      userString = JSON.stringify(user);
      window.localStorage.setItem('user', userString);
      app.loadInitialPage();

    });

  });
</script>