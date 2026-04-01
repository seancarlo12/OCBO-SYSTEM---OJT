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

    // CLICK ROW
    $("#myTable tbody").on("click", "tr", function () {
      let data = table.row(this).data();
      if (!data) return;
    
      let rowId = data[0];
    
      // If already selected → unselect
      if (window.selectedRowId === rowId) {
        clearSelection();
        window.selectedRowId = null;
        return;
      }
    
      // Otherwise select new row
      clearSelection();
      window.selectedRowId = rowId;
      $(this).addClass("selected-row");
    });



    table.on("draw", function () {
      applySelectedHighlight();
    });

    // RESET ON PAGE / SORT / SEARCH
    table.on("page.dt order.dt search.dt", function () {
      clearSelection();
      console.log("cleared");
    });
  }

  initRowSelect();
});
