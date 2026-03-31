<?php
include_once '../includes/sideBar.php';
include_once '../config/db.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logbook</title>

  <link rel="stylesheet" href="../assets/style/globalStyle.css">
  <link rel="stylesheet" href="../assets/style/logbook.css">
  <link rel="stylesheet" href="../assets/style/dataTables.css">


</head>

<body>
  <div id="main-content">
    <div id="table-container">
      <div id="control-table">
        <button id="add-row"> <i class="bx bx-plus"></i> Add</button>
        <button id="edit-row"> <i class="bx bx-edit-alt"></i> Edit</button>
        <button id="delete-row"> <i class="bx bx-trash"></i> Delete</button>
        <button id="save-row"> <i class="bx bx-save"></i> Save</button>
        <button id="view-row"> <i class="bx bx-book-open"></i> View</button>
      </div>
      <table id="myTable">
        <thead>
          <tr>
            <th>Application No.</th>
            <th>Name</th>
            <th>Contact No.</th>
            <th>Application Type</th>
            <th>Project Title</th>
            <th>Location</th>
            <th>Plan Type</th>
            <th>Comments</th>
            <th>Date Received</th>
            <th>Status</th>
            <th>Last Updated</th>
          </tr>
        </thead>

        <tbody>
        </tbody>
      </table>
    </div>
  </div>


  <script src="../assets/js/dataTables.js"></script>
  <script src="../assets/js/fetchApps.js"></script>
  <script src="../assets/js/rowSelect.js"></script>
  <script src="../assets/js/viewBtn.js"></script>
  <script>
    $('#add-row').on('click', function() {
      if (selectedRowId) {
        alert("Selected Application No: " + selectedRowId);
      } else {
        alert("No row selected");
      }
    });


    window.table = null;



    $(document).ready(function() {

      window.table = new DataTable('#myTable', {
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        pageLength: 16,
        scrollY: "63vh", // fixed height
        scrollCollapse: false,
        // autoWidth: false,

        columnDefs: [{
            targets: 0,
            width: "5%"
          },
          {
            targets: 1,
            width: "10%"
          },
          {
            targets: 2,
            width: "8%",
            type: "string"
          },
          {
            targets: 4,
            width: "20%"
          },
          {
            targets: 5,
            width: "20%"
          },
          {
            targets: [8, 10],
            width: "9%"
          },
          {
            targets: [6, 7],
            width: "9%"
          },
        ],

        layout: {
          topStart: 'search',
          topEnd: '',
          bottomStart: 'info',
          bottomEnd: 'paging'
        }
      });


      function applyTooltips() {
        $('#myTable tbody td').each(function() {

          // Remove previous tooltip first
          $(this).removeAttr('data-bs-toggle title');

          // Check if text is truncated
          if (this.offsetWidth < this.scrollWidth) {
            $(this).attr({
              'data-bs-toggle': 'tooltip',
              'data-bs-placement': 'top',
              'title': $(this).text().trim()
            });
          }
        });

        // Destroy old tooltips to prevent duplicates
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('#myTable [data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(el) {
          new bootstrap.Tooltip(el);
        });
      }

      // Initial run
      applyTooltips();

      // Reapply after DataTables redraw
      table.on('draw', function() {
        applyTooltips();
      });




      loadTable(); // initial load

      setInterval(loadTable, 3000); // refresh every 3 seconds

    });
  </script>




  <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">

        <!-- Header -->
        <div class="modal-header text-white">
          <h5 class="modal-title fw-bold">Application Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Body -->
        <div class="modal-body p-4">

          <div class="row g-3">

            <!-- Left Column -->
            <div class="col-md-6">
              <div class="info-box">
                <label>Application No.</label>
                <div id="v_appNo" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Name</label>
                <div id="v_name" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Contact No.</label>
                <div id="v_contact" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Application Type</label>
                <div id="v_type" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Project Title</label>
                <div id="v_project" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Location</label>
                <div id="v_location" class="info-value"></div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">

              <div class="info-box">
                <label>Plan Type</label>
                <div id="v_plan" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Comments</label>
                <div id="v_comments" class="info-value text-wrap"></div>
              </div>

              <div class="info-box">
                <label>Date Received</label>
                <div id="v_date" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Status</label>
                <div id="v_status" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Last Updated</label>
                <div id="v_updated" class="info-value"></div>
              </div>
            </div>

          </div>

        </div>

      </div>
    </div>
  </div>

</body>

</html>