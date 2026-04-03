$(document).ready(function () {

  function initRowSelect() {
    if (!window.table) {
      setTimeout(initRowSelect, 100);
      return;
    }

    const table = window.table;
    window.selectedRowId = null;

    function clearSelection() {
      window.selectedRowId = null;
      $("#myTable tbody tr").removeClass("selected-row");
    }

    // =========================
    // CLICK ROW
    // =========================
    $("#myTable tbody").on("click", "tr", function () {
      let data = table.row(this).data();
      if (!data) return;

      if (data[9] === "Removed") {
        $(this).addClass("removed-row");
      }

      let rowId = data[0];

      // Toggle select
      if (window.selectedRowId === rowId) {
        clearSelection();
        return;
      }

      clearSelection();
      window.selectedRowId = rowId;
      $(this).addClass("selected-row");
    });

    // =========================
    // RESET ON TABLE CHANGE
    // =========================
    table.on("page.dt order.dt search.dt", function () {
      clearSelection();
    });
  }

  // =========================
  // AUTO SELECT FROM URL
  // =========================
  
  window.urlSearchAppNo = null;

  function applySearchFromURL() {

    const url = new URL(window.location.href);
    const appNo = url.searchParams.get("appNo");
  
    if (!appNo) return;
  
    window.urlSearchAppNo = appNo; // 🔥 store it
  
  }
  // =========================
  // INIT
  // =========================
  initRowSelect();
  applySearchFromURL();

});


function validateSelectedRow() {
  let exists = false;

  window.table.rows().every(function () {
    let data = this.data();
    if (data && data[0] == window.selectedRowId) {
      exists = true;
    }
  });

  if (!exists) {
    window.selectedRowId = null;
    $("#myTable tbody tr").removeClass("selected-row");
  }
}