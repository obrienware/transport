<section id="upcoming-trips" class="p-3"></section>
<section id="trip-detail" class="d-none p-3"></section>

<script>
  $(async ƒ => {

    $('#upcoming-trips').load('section.upcoming-trips.php');

    setInterval(() => {
      // $('#upcoming-trips').load('section.upcoming-trips.php');
    }, 30*1000); // Every 30seconds

    window.showTripDetail = function (tripId)
    {
      console.log(tripId);
      $('#upcoming-trips').addClass('d-none');
      $('#trip-detail').removeClass('d-none');
      $('#trip-detail').html('loading...').load('section.trip-detail.php?id='+tripId);
    }

    window.showTripList = function ()
    {
      $('#trip-detail').addClass('d-none');
      $('#upcoming-trips').removeClass('d-none');
    }

  });
</script>