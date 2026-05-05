<?php
session_start();
include_once '../includes/sideBar.php';
include_once '../config/db.php';


if (!isset($_SESSION['account_id'])) {
  header("Location: login.php");
  exit();
}
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
        <button id="view-row"> <i class="bx bx-book-open"></i> View</button>
        <label>
          <input type="checkbox" id="toggleRemoved" style="text-transform:uppercase;">
          Show <b>Removed</b> and <b>Released</b> Applications
        </label>
        <button id="delete-row"> <i class="bx bx-trash"></i> Remove</button>
        <!-- <button id="save-row"> <i class="bx bx-save"></i> Save</button> -->
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

          <!-- FILTER ROW -->
          <tr id="filter-row">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </thead>

        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <script src="../assets/js/swal.js"></script>
  <script src="../assets/js/dataTables.js"></script>
  <script src="../assets/js/fetchApps.js"></script>
  <script src="../assets/js/rowSelect.js"></script>
  <script src="../assets/js/viewBtn.js"></script>
  <script src="../assets/js/logbookActions.js"></script>
  <script>
    window.table = null;



    $(document).ready(function() {

      window.table = new DataTable('#myTable', {
        paging: true,
        searching: true,
        info: true,
        ordering: true,
        pageLength: 15,
        // scrollY: "58vh", 
        scrollCollapse: false,
        // stateSave: true,
        // autoWidth: false,

        columnDefs: [{
            targets: 0,
            width: "8%"
          },
          {
            targets: 1,
            width: "12%"
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
          topEnd: 'pageLength',
          bottomStart: 'info',
          bottomEnd: 'paging'
        },

      });


      function applyTooltips() {
        $('#myTable tbody td').each(function() {

          let tooltip = bootstrap.Tooltip.getInstance(this);
          if (tooltip) tooltip.dispose();

          $(this).removeAttr('data-bs-toggle data-bs-placement title data-bs-title');

          let fullText = $(this).find('span').data('full');
          let displayText = $(this).text().trim();

          let tooltipText = fullText || displayText;

          if (this.offsetWidth < this.scrollWidth || fullText) {
            $(this).attr({
              'data-bs-toggle': 'tooltip',
              'data-bs-placement': 'top',
              'data-bs-title': tooltipText
            });

            new bootstrap.Tooltip(this);
          }
        });
      }

      // Initial run
      applyTooltips();

      // Reapply after DataTables redraw
      table.on('draw', function() {
        applyTooltips();
        applyRowStyles();
      });




      loadTable(); // initial load

      setInterval(loadTable, 3000); // refresh every 3 seconds

    });
  </script>




  <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                <label>Status</label>
                <div id="v_status" class="info-value"></div>
              </div>

              <div class="info-box">
                <label>Date Received</label>
                <div id="v_date" class="info-value"></div>
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



  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Edit Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" id="editAppNo">

            <div class="row">
              <div class="col-md-6">
                <label>Name</label>
                <input type="text" id="editName" class="form-control" maxlength="150">
              </div>

              <div class="col-md-6">
                <label>Contact No. <span class="text-muted">(Optional)</span></label>
                <!-- <small class="text-muted d-block">Leave empty if not applicable</small> -->
                <input type="text" id="editContact" class="form-control" maxlength="11" inputmode="numeric" placeholder="' N/A '">
              </div>

              <div class="col-md-6">
                <label>Project Title</label>
                <input type="text" id="editProject" class="form-control" maxlength="200">
              </div>

              <div class="col-md-6">
                <label>Location</label>
                <input type="text" id="editLocation" class="form-control" maxlength="200">
              </div>

              <div class="col-md-6">
                <label>Status</label>

                <select id="editStatus" class="form-control">
                  <option value="" hidden>Select Status</option>
                  <option value="Receiving">Receiving</option>
                  <option value="Processing">Processing</option>
                  <option value="Rechecking">Rechecking</option>
                  <option value="Returned">Returned</option>
                  <option value="For Order of Payment">For Order of Payment</option>
                  <option value="Releasing">Releasing</option>
                  <option value="Released">Released</option>
                  <option value="OTHER">Other...</option>
                </select>

                <!-- Hidden input for custom -->
                <input type="text" id="editStatusOther" class="form-control mt-2" placeholder="Enter custom status" style="display:none;" maxlength="100">
              </div>

              <div class="col-12">
                <label>Comments</label>
                <textarea id="editComments" class="form-control"></textarea>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Plan Type</label>

              <div class="d-flex flex-wrap gap-2" id="plan-type-container">

                <input type="checkbox" class="btn-check planType" id="pt1" value="Architectural">
                <label class="btn rounded-pill" for="pt1">Architectural</label>

                <input type="checkbox" class="btn-check planType" id="pt2" value="Structural">
                <label class="btn rounded-pill" for="pt2">Structural</label>

                <input type="checkbox" class="btn-check planType" id="pt3" value="Plumbing">
                <label class="btn rounded-pill" for="pt3">Plumbing</label>

                <input type="checkbox" class="btn-check planType" id="pt4" value="Electrical">
                <label class="btn rounded-pill" for="pt4">Electrical</label>

                <input type="checkbox" class="btn-check planType" id="pt5" value="Mechanical">
                <label class="btn rounded-pill" for="pt5">Mechanical</label>

                <input type="checkbox" class="btn-check planType" id="pt6" value="Electronics">
                <label class="btn rounded-pill" for="pt6">Electronics</label>

                <input type="checkbox" class="btn-check planType" id="pt7" value="Geodetic">
                <label class="btn rounded-pill" for="pt7">Geodetic</label>

              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="updateRow">Update</button>
        </div>

      </div>
    </div>
  </div>


  <!-- ADD APPLICATION MODAL -->
  <div class="modal fade" id="addAppModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Add Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="addAppForm">

            <div class="row">

              <!-- LEFT SIDE -->
              <div class="col-md-6">

                <div class="mb-2">
                  <label>Name</label>
                  <input maxlength="150" type="text" id="addName" name="addName" class="form-control" required>
                </div>

                <div class="mb-2">
                  <label>Project Title</label>
                  <input maxlength="200" type="text" id="addProject" name="addProject" class="form-control">
                </div>

                <div class="mb-2">
                  <label>Application Type</label>
                  <select id="addApplicationType" name="application_type" class="form-control">
                    <option value="" hidden>Select Type</option>
                    <option value="Building Permit">Building Permit</option>
                    <option value="Occupancy Permit">Occupancy Permit</option>
                    <option value="OTHER">Other...</option>
                  </select>

                  <input maxlength="150" type="text" id="addApplicationTypeOther"
                    class="form-control mt-2"
                    placeholder="Enter custom application type"
                    style="display:none;">
                </div>

                <div class="mb-2">
                  <label>Status</label>
                  <select id="addStatus" name="status" class="form-control">
                    <option value="" hidden>Select Status</option>
                    <option value="Receiving">Receiving</option>
                    <option value="Processing">Processing</option>
                    <option value="Rechecking">Rechecking</option>
                    <option value="Returned">Returned</option>
                    <option value="For Order of Payment">For Order of Payment</option>
                    <option value="Releasing">Releasing</option>
                    <option value="Released">Released</option>
                    <option value="OTHER">Other...</option>
                  </select>

                  <input maxlength="150" type="text" id="addStatusOther"
                    class="form-control mt-2"
                    placeholder="Enter custom status"
                    style="display:none;">
                </div>

              </div>

              <!-- RIGHT SIDE -->
              <div class="col-md-6">

                <div class="mb-2">
                  <label>Contact No</label>
                  <input type="text" id="addContact" name="addContact" class="form-control" maxlength="11" inputmode="numeric" placeholder="' N/A '">
                </div>

                <div class="mb-2">
                  <label>Location</label>
                  <input maxlength="200" type="text" id="addLocation" name="addLocation" class="form-control">
                </div>

              </div>

            </div>

            <!-- 🔥 FULL WIDTH BELOW -->

            <div class="row mt-2">

              <!-- Plan Type -->
              <div class="col-12 mb-3">
                <label class="form-label">Plan Type</label>

                <div class="d-flex flex-wrap gap-2">

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt1" value="Architectural">
                  <label class="btn rounded-pill" for="add_pt1">Architectural</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt2" value="Structural">
                  <label class="btn rounded-pill" for="add_pt2">Structural</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt3" value="Plumbing">
                  <label class="btn rounded-pill" for="add_pt3">Plumbing</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt4" value="Electrical">
                  <label class="btn rounded-pill" for="add_pt4">Electrical</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt5" value="Mechanical">
                  <label class="btn rounded-pill" for="add_pt5">Mechanical</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt6" value="Electronics">
                  <label class="btn rounded-pill" for="add_pt6">Electronics</label>

                  <input type="checkbox" class="btn-check addPlanType" id="add_pt7" value="Geodetic">
                  <label class="btn rounded-pill" for="add_pt7">Geodetic</label>

                </div>
              </div>

              <!-- Comments -->
              <div class="col-12 mb-2">
                <label>Comments</label>
                <textarea id="addComments" name="addComments" class="form-control"></textarea>
              </div>

            </div>

          </form>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveAppBtn">Save</button>
        </div>

      </div>
    </div>
  </div>

</body>

</html>