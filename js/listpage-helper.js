export function initListPage({
  tableId,
  dataTableOptions,
  loadOnClick = null,
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
    // event.preventDefault();
    // const id = $(event.currentTarget).data('id');
    // if (!id) return;

    // // If we're using the "tabs" UI, then open a new tab as before
    // if (window.app !== undefined && window.app.openTab !== undefined)
    //   return app.openTab(loadOnClick.tab, loadOnClick.title, `${loadOnClick.page}?id=${id}`);
    
    // // Otherwise, trigger the event that will load the sub-section
    // parentSectionId = (parentSectionId.charAt(0) === '#') ? parentSectionId.slice(1) : parentSectionId;
    // $(document).trigger('loadMainSection', {
    //   sectionId: parentSectionId,
    //   url: `${loadOnClick.page}?id=${id}`,
    //   forceReload: true
    // });
  });

  $(document).off(`${reloadOnEventName}.ns`).on(`${reloadOnEventName}.ns`, reloadSection);
}