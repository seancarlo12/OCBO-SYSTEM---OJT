function applySelectedHighlight() {
    $('#myTable tbody tr').each(function () {

        let data = table.row(this).data();
        if (!data) return;

        let appNo = data[0];

        if (appNo === window.selectedRowId) {
            $(this).addClass('selected-row');
        } else {
            $(this).removeClass('selected-row');
        }
    });
}

function formatDateTime(datetimeStr) {
    let date = new Date(datetimeStr);

    let options = {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZone: 'Asia/Manila' // 🇵🇭 Philippine Time
    };

    return date.toLocaleString('en-US', options);
}

function formatDate(dateStr) {
    let date = new Date(dateStr);

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        timeZone: 'Asia/Manila'
    });
}




let lastDataHash = null;

function getDataHash(data) {
    return JSON.stringify(data);
}

function loadTable() {

    let currentSelected = window.selectedRowId; // save id

    $.get("../config/getApps.php", function(data) {

        let result = JSON.parse(data);

        let newHash = JSON.stringify(result);

        if (newHash === lastDataHash) return;

        lastDataHash = newHash;

        $('[data-bs-toggle="tooltip"]').tooltip('dispose');

        table.clear();

        result.forEach(function(row) {
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
                `<span data-bs-toggle="tooltip" title="${formatDateTime(row.date_received)}">
                ${formatDate(row.date_received)}
                </span>`,

                row.status,

                // LAST UPDATED
                `<span data-bs-toggle="tooltip" title="${formatDateTime(row.last_updated)}">
                    ${formatDate(row.last_updated)}
                </span>`
            ]);
        });

        table.draw(false);

        // 🔥 RESTORE selection AFTER redraw
        window.selectedRowId = currentSelected;

        applySelectedHighlight();
    });
    console.log("fetchapp");
}