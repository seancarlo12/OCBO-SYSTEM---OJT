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
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

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
        icon: 'error',
        title: 'No row selected'
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

  if (["Receiving", "Processing", "Rechecking", "Returned"].includes(status)) {
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