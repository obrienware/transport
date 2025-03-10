<a href="#" class="back-to-top"><i class="fa-solid fa-circle-arrow-up fa-2x"></i></a>
<div class="container-fluid mt-2" id="master">
	<h1>Authentication Log</h1>
	<div class="row">
		<div class="col-auto">
			<div class="form-group">
				<label for="from_date" class="mb-0">From Date</label>
				<input type="date" id="from_date" class="form-control" value="<?=date('Y-m-d')?>">
			</div>
		</div>
		<div class="col-auto">
			<div class="form-group">
				<label for="to_date" class="mb-0">To Date</label>
				<input type="date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>">
			</div>
		</div>
		<div class="col-auto">
			<button class="btn btn-primary px-4 mt-4" onclick="$(document).trigger('report:auth-log')">Report</button>
		</div>
	</div>

	<div class="mt-4" id="output"></div>
</div>
<div class="container" id="auth-log-detail"></div>

<script>

	if (!documentEventExists('report:auth-log')) {
		$(document).on('report:auth-log', ƒ => {
			$('#auth-log-detail').html(`<img src="/images/ellipsis.svg" height="40" alt="...">`).load(`report-data.auth-log.php` + net.queryParams({
				from_date: $('#from_date').val(),
				to_date: $('#to_date').val()
			}));
		});
	}

	$(async ƒ => {

		// To facilitate scroll-to-top (recommended on all reports)
    $(window).on('scroll', ƒ => {
      if ($(window).scrollTop() > offset) {
        $('.back-to-top').fadeIn(duration);
      } else {
        $('.back-to-top').fadeOut(duration);
      }
    });
    $('.back-to-top').on('click', ƒ => {
      ƒ.preventDefault();
      $('html, body').animate({scrollTop: 0}, duration);
      return false;
    })


		$('#btn-report').off('click').on('click', ƒ => {
      console.log('report button clicked.')
			const from_date = $('#from_date').val();
			const to_date = $('#to_date').val();
			if (from_date.length === 0) return ui.toastr.error(`From Date is required`, 'ATTENTION');
			if (to_date.length === 0) return ui.toastr.error(`To Date is required`, 'ATTENTION');
			$('#report').prop('disabled', true);
			$('#output').html(report_loader).load(`report-data.auth-log.php` + net.queryParams({
        from_date, to_date
      }), ƒ => {
        $('#report').prop('disabled', false);
      });
		});

  });
</script>
