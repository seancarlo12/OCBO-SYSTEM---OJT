$(document).on("change", "#toggleRemoved", function () {
  console.log("TOGGLE CHANGED"); // test
  //  clear selection
  window.selectedRowId = null;
  $("#myTable tbody tr").removeClass("selected-row");

  loadTable();
});

function applySelectedHighlight() {
  $("#myTable tbody tr").each(function () {
    let data = table.row(this).data();
    if (!data) return;

    let appNo = data[0];

    if (appNo === window.selectedRowId) {
      $(this).addClass("selected-row");
    } else {
      $(this).removeClass("selected-row");
    }
  });
}

function formatDateTime(datetimeStr) {
  let date = new Date(datetimeStr);

  let options = {
    year: "numeric",
    month: "short",
    day: "2-digit",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
    timeZone: "Asia/Manila", // 🇵🇭 Philippine Time
  };

  return date.toLocaleString("en-US", options);
}

function formatDate(dateStr) {
  let date = new Date(dateStr);

  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "2-digit",
    timeZone: "Asia/Manila",
  });
}

let lastDataHash = null;

function getDataHash(data) {
  return JSON.stringify(data);
}

function loadTable() {
  let currentSelected = window.selectedRowId; // save id

  $.get("../config/getApps.php", function (data) {
    let result = JSON.parse(data);

    let showRemoved = $("#toggleRemoved").is(":checked");

    // include toggle state
    let newHash = JSON.stringify(result) + showRemoved;

    if (newHash === lastDataHash) return;

    lastDataHash = newHash;

    $('[data-bs-toggle="tooltip"]').tooltip("dispose");

    table.clear();

    result.forEach(function (row) {
      // ❌ skip removed if toggle is OFF
      if (
        !showRemoved &&
        (row.status === "Removed" || row.status === "Released")
      ) {
        return;
      }

      table.row.add([
        row.application_no,
        row.name,
        row.contact_no,
        row.application_type,
        row.project_title,
        row.location,
        row.plan_type,
        row.comments,

        // DATE RECEIVED
        `<span data-full="${formatDateTime(row.date_received)}">
          ${formatDate(row.date_received)}
        </span>`,

        row.status,

        // LAST UPDATED
        `<span data-full="${formatDateTime(row.last_updated)}">
          ${formatDate(row.last_updated)}
        </span>`,
      ]);
    });

    table.draw(false);

        // apply URL search ONCE after table render
    if (window.urlSearchAppNo) {
      table.search(window.urlSearchAppNo).draw(false);

      // 🔥 clear immediately so it won't re-trigger
      window.urlSearchAppNo = null;
    }

    //  RESTORE selection AFTER redraw
    window.selectedRowId = currentSelected;

    applySelectedHighlight();
    applyRowStyles();
  });
  console.log("fetchapp");
}

function applyRowStyles() {
  $("#myTable tbody tr").each(function () {
    let data = window.table.row(this).data();
    if (!data) return;

    let status = data[9];

    // ✅ Removed styling
    if (status === "Removed") {
      $(this).addClass("removed-row");
    } else {
      $(this).removeClass("removed-row");
    }

    // ✅ Stale styling
    // last_updated cell is HTML (span with tooltip title), extract real datetime first.
    let rawDate = data[10];
    let dateSource =
      $("<div>").html(rawDate).find("span").attr("title") || rawDate;

    // Fallback: support strings like "2026-04-02 10:20:00"
    let normalized = String(dateSource).trim().replace(" ", "T");
    let lastUpdated = new Date(normalized);
    if (!rawDate) return;
    if (Number.isNaN(lastUpdated.getTime())) return;
    let now = new Date();

    let diffDays = (now - lastUpdated) / (1000 * 60 * 60 * 24);

    if (diffDays >= 3) {
      $(this).addClass("stale-row");
    } else {
      $(this).removeClass("stale-row");
    }
  });
}
