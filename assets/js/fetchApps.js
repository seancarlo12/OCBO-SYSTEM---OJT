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

function buildFilters() {
  let api = table;

  // ✅ COLUMN FILTER CONFIG
  let columnFilters = {
    0: "text",
    1: "text",
    2: "text",
    3: "dropdown",
    4: "text",
    5: "text",
    6: "dropdown",
    7: "text",
    8: "text",      // Date Received (no filter)
    9: "dropdown",
    10: "text"
  };

  $('#filter-row th').each(function (colIndex) {
    let column = api.column(colIndex);
    $(this).empty();

    let type = columnFilters[colIndex] || "none";

    // ❌ SKIP COMPLETELY
    if (type === "none") return;

    // 🔽 DROPDOWN FILTER
// 🔽 DROPDOWN FILTER
if (type === "dropdown") {
  let select = document.createElement('select');
  select.classList.add('form-control', 'form-control-sm');
  select.add(new Option('All', ''));

  $(this).append(select);

  $(select).on('click', function (e) {
    e.stopPropagation();
  });

  let uniqueValues = new Set();

  column.data().each(function (d) {
    if (!d) return;

    let text = $('<div>').html(d).text();

    text.split(',').forEach(function (item) {
      let clean = item.trim();
      if (clean) uniqueValues.add(clean);
    });
  });

  // ✅ FORCE FIXED OPTIONS FOR COLUMN 9
  if (colIndex == 9) {
    ["Removed", "Released"].forEach(val => {
      uniqueValues.add(val);
    });
  }

  [...uniqueValues].sort().forEach(function (val) {
    select.add(new Option(val));
  });

  select.addEventListener('change', function () {
    let val = select.value;

    if (!val) {
      column.search('').draw();
    } else {
      column.search('\\b' + val + '\\b', true, false).draw();
    }
  });
}

    // 🔍 TEXT FILTER
    else if (type === "text") {
      let input = document.createElement('input');
      input.type = "text";
      input.placeholder = "Search...";
      input.classList.add('form-control', 'form-control-sm');

      $(this).append(input);

      $(input).on('click', function (e) {
        e.stopPropagation();
      });

      input.addEventListener('keyup', function () {
        column.search(this.value).draw();
      });
    }
  });

  // prevent sorting from filter row
  $('#filter-row th').on('click', function (e) {
    e.stopPropagation();
  });
}


function loadTable() {
  let currentSelected = window.selectedRowId;

  $.get("../config/getApps.php", function (data) {
    let result = JSON.parse(data);

    let showRemoved = $("#toggleRemoved").is(":checked");

    let newHash = JSON.stringify(result) + showRemoved;
    if (newHash === lastDataHash) return;

    lastDataHash = newHash;

    $('[data-bs-toggle="tooltip"]').tooltip("dispose");

    // ✅ IMPORTANT: clear ONCE
    table.clear();

    let rows = [];

    result.forEach(function (row) {
      if (
        !showRemoved &&
        (row.status === "Removed" || row.status === "Released")
      ) {
        return;
      }

      rows.push([
        row.application_no,
        row.name,
        row.contact_no,
        row.application_type,
        row.project_title,
        row.location,
        row.plan_type,
        row.comments,

        `<span data-full="${formatDateTime(row.date_received)}">
          ${formatDate(row.date_received)}
        </span>`,

        row.status,

        `<span data-full="${formatDateTime(row.last_updated)}">
          ${formatDate(row.last_updated)}
        </span>`
      ]);
    });

    // ✅ IMPORTANT: add ALL rows at once
    table.rows.add(rows).draw(false);

    // URL search
    if (window.urlSearchAppNo) {
      table.search(window.urlSearchAppNo).draw(false);
      window.urlSearchAppNo = null;
    }

    // restore selection
    window.selectedRowId = currentSelected;

    // build filters once
    if (!window.filtersBuilt) {
      buildFilters();
      window.filtersBuilt = true;
    }

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
