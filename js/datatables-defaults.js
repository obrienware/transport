if ($.fn?.dataTable?.defaults) {
  /* Set defaults for DataTables */
  $.extend(true, $.fn.dataTable.defaults, {
    // dom: "<'d-flex flex-wrap'<'mr-2'f><l><'ml-auto'B>><'row my-2'<'col-sm-12'tr>><'d-flex flex-wrap'<i><'ml-auto'p>>",
    // dom: "<'d-flex flex-wrap'<'me-2'f><l><'ms-auto'B>><'row my-0'<'col'tr>><'d-flex flex-wrap'<><'ms-auto'>>",
    layout: {
      topStart: ['search'],
      topEnd: ['pageLength']
    },
    stateSave: true,
    language: {
      paginate: {
        next: '<i class="fa-solid fa-circle-chevron-right"></i>',
        previous: '<i class="fa-solid fa-circle-chevron-left"></i>',
        first: '<i class="fa-solid fa-circle-arrow-left"></i>',
        last: '<i class="fa-solid fa-circle-arrow-right"></i>',
      },
      search: 'Filter',
      searchPlaceholder: 'Text to filter',
      // lengthMenu:
      //       'Display <select class="form-select form-select-sm">' +
      //       '<option value="5">5</option>' +
      //       '<option value="10">10</option>' +
      //       '<option value="20">20</option>' +
      //       '<option value="30">30</option>' +
      //       '<option value="40">40</option>' +
      //       '<option value="50">50</option>' +
      //       '<option value="-1">All</option>' +
      //       '</select> records'
    },
    buttons: [],
    columnDefs: [
      {
        targets: 'no-sort',
        searchable: false,
        orderable: false,
      },
      {
        targets: 'no-search',
        searchable: false,
      }
    ],
    paging: false
  });
  // $.fn.dataTable.moment('YYYY-M-D');
  // $.fn.dataTable.moment('YYYY-M-D h:mm a');
}
