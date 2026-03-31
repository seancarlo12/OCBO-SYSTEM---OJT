function openViewModal() {

    if (!window.selectedRowId) {
        alert("No row selected");
        return;
    }

    let data = table.rows().data().toArray();
    let selected = data.find(row => row[0] == window.selectedRowId);

    if (!selected) return;

    // Fill modal
    $('#v_appNo').text(selected[0]);
    $('#v_name').text(selected[1]);
    $('#v_contact').text(selected[2]);
    $('#v_type').text(selected[3]);
    $('#v_project').text(selected[4]);
    $('#v_location').text(selected[5]);
    $('#v_plan').text(selected[6]);
    $('#v_comments').text(selected[7]);
    $('#v_date').text($(selected[8]).text());
    $('#v_status').text(selected[9]);
    $('#v_updated').text($(selected[10]).text());

    // Show modal
    let modal = new bootstrap.Modal(document.getElementById('viewModal'));
    modal.show();
}

$('#view-row').on('click', function () {
    openViewModal();
});

$('#myTable tbody').on('dblclick', 'tr', function () {

    let data = table.row(this).data();
    if (!data) return;

    // Ensure row is selected first
    window.selectedRowId = data[0];
    applySelectedHighlight();

    openViewModal();
});