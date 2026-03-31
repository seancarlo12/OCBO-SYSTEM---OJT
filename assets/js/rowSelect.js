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

      clearSelection();

      window.selectedRowId = data[0];

      $(this).addClass("selected-row");
    });

    // RESTORE ON DRAW (after reload, pagination, etc.)
    table.on("draw", function () {
      if (!window.selectedRowId) return;

      let rows = table.rows().nodes();

      $(rows).each(function () {
        let data = table.row(this).data();
        if (!data) return;

        if (data[0] === window.selectedRowId) {
          $(this).addClass("selected-row");
        }
      });
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
