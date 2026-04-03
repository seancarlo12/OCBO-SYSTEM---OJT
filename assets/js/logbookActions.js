// $('#edit-row').on('click', function() {
//     if (selectedRowId) {
//       alert("Selected Application No: " + selectedRowId);
//     } else {
//       alert("No row selected");
//     }
//   });
const Toast = Swal.mixin({
  toast: true,
  position: "bottom-end",
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,

  didOpen: (toast) => {
    // ✅ pause timer on hover
    toast.addEventListener("mouseenter", Swal.stopTimer);
    toast.addEventListener("mouseleave", Swal.resumeTimer);
  },
});

// EDIT ROW

$(document).on("change", "#editStatus", function () {
  if ($(this).val() === "OTHER") {
    $("#editStatusOther").show().focus();
  } else {
    // ✅ hide + CLEAR text
    $("#editStatusOther").val("").hide();
  }
});


$(document).on("input", "#editContact", function () {
  let value = this.value;

  // allow empty (so user can clear it)
  if (!value) return;

  // allow typing "N/A"
  if (value.toUpperCase() === "N/A") {
    this.value = "N/A";
    return;
  }

  // otherwise numeric only
  value = value.replace(/\D/g, "");

  // enforce 09
  if (value.length >= 1 && value[0] !== "0") {
    value = "0" + value;
  }
  if (value.length >= 2 && value.substring(0, 2) !== "09") {
    value = "09" + value.substring(2);
  }

  this.value = value.slice(0, 11);
});

$("#edit-row").on("click", function () {
  if (!selectedRowId) {
    Toast.fire({
      icon: "error",
      title: "No row selected",
    });
    return;
  }

  let rowData = table
    .rows()
    .data()
    .toArray()
    .find((row) => row[0] == selectedRowId);
  if (!rowData) return;

  let status = rowData[9];

  if (["Receiving", "Processing", "Rechecking", "Returned", "Releasing", "Released", "For Order of Payment"].includes(status)) {
    $("#editStatus").val(status);
    $("#editStatusOther").hide().val("");
  } else {
    $("#editStatus").val("OTHER");
    $("#editStatusOther").show().val(status);
  }

  $("#editAppNo").val(rowData[0]);
  $("#editName").val(rowData[1]);
  $("#editContact").val(rowData[2]);
  $("#editProject").val(rowData[4]);
  $("#editLocation").val(rowData[5]);
  $("#editComments").val(rowData[7]);

  // ✅ Handle Plan Type (column index = 6)
  let planTypes = rowData[6] ? rowData[6].split(",") : [];

  $(".planType").prop("checked", false); // reset

  planTypes.forEach((type) => {
    $('.planType[value="' + type.trim() + '"]').prop("checked", true);
  });

  $("#editModal").modal("show");
});

$(document).on("click", "#updateRow", function (e) {
  e.preventDefault();

  let appNo = $("#editAppNo").val();

  let name = $("#editName").val().trim();
  let contact = $("#editContact").val().trim();
  let project = $("#editProject").val().trim();
  let location = $("#editLocation").val().trim();
  let comments = $("#editComments").val().trim();

  let selectedPlans = [];
  $(".planType:checked").each(function () {
    selectedPlans.push($(this).val());
  });

  let planString = selectedPlans.join(", ");

  let statusVal = $("#editStatus").val();
  let statusOther = $("#editStatusOther").val().trim();

  let row = table
    .rows()
    .eq(0)
    .filter(function (rowIdx) {
      return table.cell(rowIdx, 0).data() == appNo;
    });

  let oldData = table.row(row).data();

  // 🚨 VALIDATIONS
  if (!name) {
    return Toast.fire({ icon: "error", title: "Name is required" });
  }

  // if empty → set to N/A
  if (!contact) {
    contact = "N/A";
  }

  let contactRegex = /^(N\/A|09\d{9})$/;

  if (!contactRegex.test(contact)) {
    return Toast.fire({
      icon: "error",
      title: "Contact number must be 11 digits starting with 09.",
    });
  }

  if (!project) {
    return Toast.fire({ icon: "error", title: "Project Title is required" });
  }

  if (!location) {
    return Toast.fire({ icon: "error", title: "Location is required" });
  }

  if (selectedPlans.length === 0) {
    return Toast.fire({
      icon: "error",
      title: "Select at least one Plan Type",
    });
  }

  if (!statusVal) {
    return Toast.fire({ icon: "error", title: "Select a status" });
  }

  if (statusVal === "OTHER" && !statusOther) {
    return Toast.fire({ icon: "error", title: "Enter custom status" });
  }

  // resolve final status
  let finalStatus = statusVal === "OTHER" ? statusOther : statusVal;

  let newData = [
    appNo,
    name,
    contact,
    oldData[3],
    project,
    location,
    planString,
    comments,
    oldData[8],
    finalStatus,
    oldData[10],
  ];

  // ✅ check if changed
  if (JSON.stringify(oldData) === JSON.stringify(newData)) {
    return Toast.fire({
      icon: "info",
      title: "No changes made",
    });
  }

  // ✅ SEND TO PHP FIRST
  $.ajax({
    url: "../config/updateApp.php",
    type: "POST",
    data: {
      appNo: appNo,
      name: name,
      contact: contact,
      project: project,
      location: location,
      plans: planString,
      comments: comments,
      status: finalStatus,
    },
    success: function (response) {
      let res = JSON.parse(response);

      if (res.status === "success") {
        // ✅ Update table only if DB is successful
        table.row(row).data(newData).draw(false);

        Toast.fire({
          icon: "success",
          title: "Updated successfully",
        });

        $("#editModal").modal("hide");
      } else {
        Toast.fire({
          icon: "error",
          title: res.message || "Update failed",
        });
      }
    },
    error: function () {
      Toast.fire({
        icon: "error",
        title: "Server error",
      });
    },
  });
});

// DELETE ROW (change status to removed)

$(document).on("click", "#delete-row", function () {
  let selectedId = window.selectedRowId;

  if (!selectedRowId) {
    Toast.fire({
      icon: "error",
      title: "No row selected",
    });
    return;
  }

  // 🔍 Get selected row data
  let rowData = window.table
    .rows()
    .data()
    .toArray()
    .find((row) => row[0] == selectedId);

  // ❌ Row not found (edge case)
  if (!rowData) {
    Toast.fire({
      icon: "error",
      title: "Selected row not found",
    });
    return;
  }

  let status = rowData[9];

  // ❌ Already removed
  if (status === "Removed") {
    Toast.fire({
      icon: "error",
      title: "This record is already removed",
    });
    return;
  }

  // confirm first
  Swal.fire({
    title: "Remove this record?",
    html:
      "This will remove <b>Application No. " +
      selectedId +
      "</b> in the table.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("../config/removeApp.php", { id: selectedId }, function (res) {
        let response = JSON.parse(res);

        if (response.success) {
          Toast.fire({
            icon: "success",
            title: "Application: " + selectedId + " removed Successfully",
          });

          loadTable(); //  reload data

        } else {
          Swal.fire("Error", response.message, "error");
        }
      });
    }
  });
});


// ADD RECORD

$(document).on("change", "#addStatus", function () {
  if ($(this).val() === "OTHER") {
    $("#addStatusOther").show().focus();
  } else {
    $("#addStatusOther").val("").hide();
  }
});

$(document).on("change", "#addApplicationType", function () {
  if ($(this).val() === "OTHER") {
    $("#addApplicationTypeOther").show().focus();
  } else {
    $("#addApplicationTypeOther").val("").hide();
  }
});

$(document).on("input", "#addContact", function () {
  let value = this.value;

  if (!value) return;

  if (value.toUpperCase() === "N/A") {
    this.value = "N/A";
    return;
  }

  value = value.replace(/\D/g, "");

  if (value.length >= 1 && value[0] !== "0") {
    value = "0" + value;
  }
  if (value.length >= 2 && value.substring(0, 2) !== "09") {
    value = "09" + value.substring(2);
  }

  this.value = value.slice(0, 11);
});

function getAddPlanTypes() {
  let selected = [];

  $(".addPlanType:checked").each(function () {
    selected.push($(this).val());
  });

  return selected.join(", ");
}

function getFinalStatusAdd() {
  let status = $("#addStatus").val();

  if (status === "OTHER") {
    return $("#addStatusOther").val();
  }

  return status;
}

function getApplicationTypeValue() {
  let value = $("#addApplicationType").val();

  if (value === "OTHER") {
    return $("#addApplicationTypeOther").val();
  }

  return value;
}

$(document).on("click", "#add-row", function () {
  $("#addAppForm").trigger("reset");

  $("#addApplicationTypeOther").hide().val("");
  $("#addStatusOther").hide().val("");
  $(".addPlanType").prop("checked", false);

  const modal = new bootstrap.Modal(document.getElementById("addAppModal"));
  modal.show();
});


$(document).on("click", "#saveAppBtn", function (e) {
  e.preventDefault();

  let name = $("#addName").val().trim();
  let contact = $("#addContact").val().trim();
  let project = $("#addProject").val().trim();
  let location = $("#addLocation").val().trim();
  let comments = $("#addComments").val().trim();

  let selectedPlans = [];
  $(".addPlanType:checked").each(function () {
    selectedPlans.push($(this).val());
  });

  let planString = selectedPlans.join(", ");

  let finalStatus = getFinalStatusAdd();
  let finalApplicationType = getApplicationTypeValue();

  // 🚨 VALIDATIONS (same as edit)

  if (!name) {
    return Toast.fire({ icon: "error", title: "Name is required" });
  }

  if (!contact) {
    contact = "N/A";
  }

  let contactRegex = /^(N\/A|09\d{9})$/;

  if (!contactRegex.test(contact)) {
    return Toast.fire({
      icon: "error",
      title: "Contact number must be 11 digits starting with 09.",
    });
  }

  if (!project) {
    return Toast.fire({ icon: "error", title: "Project Title is required" });
  }

  if (!location) {
    return Toast.fire({ icon: "error", title: "Location is required" });
  }

  if (selectedPlans.length === 0) {
    return Toast.fire({
      icon: "error",
      title: "Select at least one Plan Type",
    });
  }

  if (!$("#addStatus").val()) {
    return Toast.fire({ icon: "error", title: "Select a status" });
  }
  
  if ($("#addStatus").val() === "OTHER" && !$("#addStatusOther").val().trim()) {
    return Toast.fire({ icon: "error", title: "Enter custom status" });
  }

  if (!$("#addApplicationType").val()) {
    return Toast.fire({ icon: "error", title: "Select Application Type" });
  }
  
  if ($("#addApplicationType").val() === "OTHER" && !$("#addApplicationTypeOther").val().trim()) {
    return Toast.fire({ icon: "error", title: "Enter custom application type" });
  }

  // ✅ SEND TO PHP FIRST
  $.ajax({
    url: "../config/saveApp.php",
    type: "POST",
    data: {
      name: name,
      contact: contact,
      project: project,
      location: location,
      plans: planString,
      comments: comments,
      status: finalStatus,
      application_type: finalApplicationType,
    },
    success: function (response) {
      let res = JSON.parse(response);

      if (res.status === "success") {

        let appNo = res.appNo; //  important (from DB)

        let newRow = [
          appNo,
          name,
          contact,
          finalApplicationType,
          project,
          location,
          planString,
          comments,
          "", // adjust if needed
          finalStatus,
          "",
        ];

        // ✅ ADD TO TABLE ONLY AFTER DB SUCCESS
        table.row.add(newRow).draw(false);

        Toast.fire({
          icon: "success",
          title: "Added successfully",
        });

        $("#addAppModal").modal("hide");

        // optional: reset form
        $("#addAppForm").trigger("reset");

      } else {
        Toast.fire({
          icon: "error",
          title: res.message || "Insert failed",
        });
      }
    },
    error: function () {
      Toast.fire({
        icon: "error",
        title: "Server error",
      });
    },
  });
});