export function initListPage({
  tableId,
  dataTableOptions,
  loadOnClick,
  reloadOnEventName,
  parentSectionId,
  thisURI,
}) {

  function reloadSection () {
    $(parentSectionId).load(thisURI); // Refresh this page
  }

  if ($.fn.dataTable.isDataTable(`#${tableId}`) ) {
    $(`#${tableId}`).DataTable();
  } else {
    $(`#${tableId}`).DataTable(dataTableOptions);
  }

  $(`#${tableId} tbody`).on('click', 'tr', event => {
    event.preventDefault();
    const id = $(event.currentTarget).data('id');
    if (!id) return;
    app.openTab(loadOnClick.tab, loadOnClick.title, `${loadOnClick.page}?id=${id}`);
  });

  $(document).off(`${reloadOnEventName}.ns`).on(`${reloadOnEventName}.ns`, reloadSection);
}