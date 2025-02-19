<a href="#" class="back-to-top"><i class="fa-solid fa-circle-arrow-up fa-2x"></i></a>
<?php
require_once 'autoload.php';

use Transport\Database;

$db = Database::getInstance();
?>
<a href="#" class="back-to-top"><i class="fa-solid fa-circle-arrow-up fa-2x"></i></a>
<div id="audit-trail">
	<section class="master">
		<div class="container-fluid mt-2" id="master">
			<h1>Audit Trail</h1>
			<div class="row">
				<div class="col-auto">
					<div class="form-group">
						<label for="from_date" class="mb-0">From Date</label>
						<input type="date" id="from_date" class="form-control" value="<?= date('Y-m-d') ?>">
					</div>
				</div>
				<div class="col-auto">
					<div class="form-group">
						<label for="to_date" class="mb-0">To Date</label>
						<input type="date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>">
					</div>
				</div>
				<div class="col-auto">
					<div class="form-group">
						<label for="table" class="mb-0">Table</label>
						<select id="table" class="form-control">
							<option value="">All Tables</option>
							<?php if ($rows = $db->get_rows("SELECT DISTINCT affected_tables FROM audit_trail")): ?>
								<?php foreach ($rows as $row): ?>
									<option value="<?= $row->affected_tables ?>"><?= $row->affected_tables ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<div class="col-auto">
					<div class="form-group">
						<label for="user" class="mb-0">User</label>
						<select id="user" class="form-control">
							<option value="">All Users</option>
							<?php if ($rows = $db->get_rows("SELECT DISTINCT user FROM audit_trail WHERE user <> ''")): ?>
								<?php foreach ($rows as $row): ?>
									<option value="<?= $row->user ?>"><?= $row->user ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<div class="col-auto">
					<button class="btn btn-primary px-4 mt-4" onclick="$(document).trigger('report:audit-trail')">Report</button>
				</div>
			</div>

			<div class="mt-4 output"></div>
		</div>
	</section>

	<section class="detail d-none"></section>
</div>


<script>
	if (!documentEventExists('report:audit-trail')) {
		$(document).on('report:audit-trail', ƒ => {
			$('#audit-trail .master .output').html(`<img src="/images/ellipsis.svg" height="40" alt="...">`).load(`report-data.audit-trail.php` + net.queryParams({
				from_date: $('#from_date').val(),
				to_date: $('#to_date').val(),
				table: $('#table').val(),
				user: $('#user').val()
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
			$('html, body').animate({
				scrollTop: 0
			}, duration);
			return false;
		})


		$('#btn-report').off('click').on('click', ƒ => {
			console.log('report button clicked.')
			const from_date = $('#from_date').val();
			const to_date = $('#to_date').val();
			const table = $('#table').val();
			const user = $('#user').val();
			if (from_date.length === 0) return ui.toastr.error(`From Date is required`, 'ATTENTION');
			if (to_date.length === 0) return ui.toastr.error(`To Date is required`, 'ATTENTION');
			$('#report').prop('disabled', true);
			$('#output').html(report_loader).load(`report-data.audit-trail.php` + net.queryParams({
				from_date,
				to_date,
				table,
				user
			}), ƒ => {
				$('#report').prop('disabled', false);
			});
		});

	});
</script>