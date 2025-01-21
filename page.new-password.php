<?php $return = ($_GET['return']) ? base64_decode($_GET['return']) : './';?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="Richard O'Brien">
<link rel="icon" type="image/png" href="/Icon.png" />

<title>Transportation Management Console</title>

<!-- Fontawesome - necessary for icons -->
<script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>

<!-- Necessary Javascript that should come before anything else -->
<!-- Still rely heavily on jQuery. It serves our purpose quite well -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- And of course our Bootstrap javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Ace theme(s) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/ace.min.js" integrity="sha512-9xNuS6O4ZXZdCVDekkW4ApP8MfH2SCyK7Wsd4g0l3KDmbNld2vsozYGY6kQup0VKB0iT89cLj/DiRSV7WjfUaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/mode-json5.min.js" integrity="sha512-qiTdaKQmVlm7hpUZaFVX5tdDgH6oqZx2I9ChggYoeuECMjCMRT/+YBvA0RcxX5hCRW9dP1ZNRGWqOnX3MtU17w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/theme-xcode.min.js" integrity="sha512-GdLo/7fZFpTgzlSOPaN1qzIRRTq4NHXr5bH5sfBgiIuU/bqo+bvXAAb1+IWJ5mwYSYApoUDbFKc46Bbn883IgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/json5/2.2.3/index.min.js" integrity="sha512-44jdhc+R2TFfzBflS3/dGNEABiNUxBkkrqwO7GWTvGsj3HkQNr3GESvI9PUvAxmqxSnTosR0Ij9y3+o+6J1hig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- All our customized javascript -->
<script type="text/javascript" src="js/common.js?<?=filemtime('js/common.js')?>"></script>

<!-- Stylesheets -->
<!-- Our main (custom) Bootstrap theme -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- And of course our own styling -->
<link rel="stylesheet" type="text/css" href="css/style.css?<?=filemtime('css/style.css')?>">

</head>

<body>
  <div class="container-login100">
    <div class="row w-100">
			<div class="bg-white rounded d-flex flex-column text-center shadow-lg p-4 mx-auto" style="max-width:375px">
        <h1 class="my-4 fw-bold text-primary-emphasis">
					<img src="/images/logo.svg" style="height:2em"/>
					Transport
				</h1>
        <h2 class="mb-5 fw-light">Please choose a new password</h2>
        <div class="form-floating mb-3">
          <input type="password" class="form-control" id="password" placeholder="New Password">
          <label for="password">New Password</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="confirm-password" placeholder="Confirm Password">
          <label for="confirm-password">Confirm Password</label>
        </div>
        <div class="mt-4">
          <button id="btnContinue" class="px-5 btn btn-primary btn-lg">Continue</button>
        </div>
      </div>
    </div>
  </div>

  
  <script type="text/javascript">

    $(async ƒ => {

			$('#password').on('keyup', async ƒ => {
				if (ƒ.keyCode === 13) return $('#confirm-password').select().focus();
			});

			$('#confirm-password').on('keyup', async ƒ => {
				if (ƒ.keyCode === 13) return $('#btnContinue').click();
			});

			$('#btnContinue').on('click', async ƒ => {
        const userId = <?=$_SESSION['user']->id?>;
        const password = cleanVal('#password');
        const confirmPassword = cleanVal('#confirm-password');

        if (password.length < 8) {
          return alertError('Your password should be at least 8 characters long.', 'Try Again.');
        }
        if (password !== confirmPassword) {
          return alertError('Your password confirmation doesn\'t match your new password.', 'Try Again.');
        }

        const resp = await post('/api/post.reset-password.php', {id: userId, password});
        if (resp?.result) {
					return location.href=`<?=$return?>`;
				}
        await alertError('There seems to be a problem resetting your password.', 'Hmm..');
				$('#username').select().focus();
      });


    });

  </script>
</body>
</html>