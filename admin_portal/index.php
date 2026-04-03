<?php
include_once '../includes/sideBar.php';
include_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>

    <link rel="stylesheet" href="../assets/style/globalStyle.css">
    <link rel="stylesheet" href="../assets/style/index.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div id="main-content">
    <h2 id="welc">System Overview</h2>

        <!-- TOP COUNTS -->
        <div class="card a1"><span class="count-title">Applications Today</span><br><span id="todayCount">0</span></div>
        <div class="card a2"><span class="count-title">Applications this Week</span><br><span id="weekCount">0</span></div>
        <div class="card a3"><span class="count-title">Applications this Month</span><br><span id="monthCount">0</span></div>

        <!-- PIE CHARTS -->
        <div class="right-grid">
            <span id="atp">Application Type Pie Chart</span>
            <div class="circle">
                <canvas id="typeChart"></canvas>
            </div>

            <span id="ptp">Plant Type Pie Chart</span>
            <div class="circle">
                <canvas id="planChart"></canvas>
            </div>
        </div>

        <!-- BAR CHART -->
        <div class="card a6">
            <canvas id="statusChart"></canvas>
        </div>

        <!-- STATUS LIST -->
        <div class="card a7 status-card">
            <h3 class="card-title">Count per Status</h3>
            <ul id="statusList" class="status-list"></ul>
        </div>

        <div class="card a8">
            <div class="followup-header">
                <h3 class="card-title">Needs Follow Up <small id="exc">(Applications with no updates for the last 3 or more days)</small></h3>
                <div class="search-wrapper">
                    <label for="followupSearch">Search:</label>
                    <input type="text" id="followupSearch" class="form-control" placeholder="Search applications...">
                </div>
            </div>
            <table class="followup-table">
                <thead>
                    <tr>
                        <th>App No</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody id="followupBody"></tbody>
            </table>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            loadDashboard();
        });


        $(document).on("keyup", "#followupSearch", function() {
            let value = $(this).val().toLowerCase();

            $("#followupBody tr").filter(function() {
                $(this).toggle(
                    $(this).text().toLowerCase().indexOf(value) > -1
                );
            });
        });

        /* =========================
           HELPERS
        ========================= */
        function isSameDay(d1, d2) {
            return d1.toDateString() === d2.toDateString();
        }

        function isThisWeek(date) {
            let now = new Date();

            let start = new Date(now);
            start.setHours(0, 0, 0, 0);
            start.setDate(start.getDate() - start.getDay());

            let end = new Date(start);
            end.setDate(start.getDate() + 6);
            end.setHours(23, 59, 59, 999);

            return date >= start && date <= end;
        }

        /* =========================
           LOAD DATA
        ========================= */
        function loadDashboard() {

            $.get("../config/getDashboardData.php", function(data) {

                let statusCounts = {};
                let planCounts = {};
                let typeCounts = {};

                let today = 0;
                let week = 0;
                let month = 0;

                const allowedStatuses = [
                    "Receiving",
                    "Processing",
                    "Returned",
                    "Rechecking",
                    "For Order Of Payment",
                    "Releasing",
                    "Released",
                    "Removed"
                ];

                data.forEach(row => {

                    /* =========================
                       STATUS
                    ========================= */
                    if (row.status) {
                        let status = row.status.trim();

                        if (!allowedStatuses.includes(status)) {
                            status = "Others";
                        }

                        statusCounts[status] = (statusCounts[status] || 0) + 1;
                    }

                    /* =========================
                       PLAN TYPE (comma separated)
                    ========================= */
                    if (row.plan_type) {
                        row.plan_type.split(',').forEach(plan => {

                            let normalized = plan
                                .trim() // remove spaces
                                .toLowerCase() // make consistent
                                .replace(/\s+/g, ' '); // fix double spaces

                            // Capitalize first letter (optional for display)
                            normalized = normalized.charAt(0).toUpperCase() + normalized.slice(1);

                            planCounts[normalized] = (planCounts[normalized] || 0) + 1;
                        });
                    }

                    /* =========================
                       APPLICATION TYPE
                    ========================= */
                    if (row.application_type) {
                        let t = row.application_type.trim();
                        typeCounts[t] = (typeCounts[t] || 0) + 1;
                    }

                    /* =========================
                       DATE COUNTS
                    ========================= */
                    let createdDate = new Date(row.date_received?.replace(' ', 'T'));

                    if (!isNaN(createdDate)) {

                        let now = new Date();

                        if (isSameDay(createdDate, now)) {
                            today++;
                        }

                        if (isThisWeek(createdDate)) {
                            week++;
                        }

                        if (
                            createdDate.getMonth() === now.getMonth() &&
                            createdDate.getFullYear() === now.getFullYear()
                        ) {
                            month++;
                        }
                    }
                });

                /* =========================
                   BAR CHART
                ========================= */
                new Chart(document.getElementById("statusChart"), {
                    type: "bar",
                    data: {
                        labels: Object.keys(statusCounts),
                        datasets: [{
                            label: "Applications",
                            data: Object.values(statusCounts),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                /* =========================
                   PIE - PLAN
                ========================= */
                new Chart(document.getElementById("planChart"), {
                    type: "pie",
                    data: {
                        labels: Object.keys(planCounts),
                        datasets: [{
                            data: Object.values(planCounts),
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.raw}`
                                }
                            }
                        }
                    }
                });

                /* =========================
                   PIE - TYPE
                ========================= */
                new Chart(document.getElementById("typeChart"), {
                    type: "pie",
                    data: {
                        labels: Object.keys(typeCounts),
                        datasets: [{
                            data: Object.values(typeCounts),
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.raw}`
                                }
                            }
                        }
                    }
                });

                /* =========================
                   STATUS LIST
                ========================= */
                let statusList = "";

                Object.keys(statusCounts).forEach(status => {
                    statusList += `
                <li class="status-item">
                    <span class="status-name">${status}</span>
                    <span class="status-count">${statusCounts[status]}</span>
                </li>
            `;
                });

                $("#statusList").html(statusList);

                /* =========================
                   TOP COUNTS
                ========================= */
                $("#todayCount").text(today);
                $("#weekCount").text(week);
                $("#monthCount").text(month);


                /* =========================
                   NEEDS FOLLOW UP
                ========================= */
                let followupRows = "";

                let now = new Date();

                data.forEach(row => {

                    let status = row.status?.trim();

                    // ❌ Skip unwanted statuses
                    if (status === "Released" || status === "Removed") return;

                    let lastUpdated = new Date(row.last_updated?.replace(' ', 'T'));

                    if (isNaN(lastUpdated)) return;

                    // Check if more than 3 days
                    let diffDays = (now - lastUpdated) / (1000 * 60 * 60 * 24);

                    if (diffDays >= 3) {

                        followupRows += `
                                        <tr>
                                            <td>${row.application_no}</td>
                                            <td>${row.name}</td>
                                            <td>${row.application_type}</td>
                                            <td>${row.location}</td>
                                            <td>${status}</td>
                                            <td>${formatDate(row.last_updated)}</td>
                                        </tr>
                                        `;
                    }
                });

                $("#followupBody").html(followupRows);

            });


        }

        $(document).on("dblclick", "#followupBody tr", function() {

            let appNo = $(this).find("td:first").text().trim();

            if (!appNo) return;

            window.location.href = `logbook.php?appNo=${encodeURIComponent(appNo)}`;
        });


        //HELPERSS
        function formatDate(datetime) {
            if (!datetime) return "N/A";

            let past = new Date(datetime.replace(' ', 'T'));
            let now = new Date();

            let diffMs = now - past;

            if (diffMs < 0) return "In the future";

            let diffSeconds = Math.floor(diffMs / 1000);
            let diffMinutes = Math.floor(diffSeconds / 60);
            let diffHours = Math.floor(diffMinutes / 60);
            let diffDays = Math.floor(diffHours / 24);
            let diffMonths = Math.floor(diffDays / 30);
            let diffYears = Math.floor(diffDays / 365);

            if (diffYears > 0) {
                return diffYears === 1 ? "1 year ago" : `${diffYears} years ago`;
            }

            if (diffMonths > 0) {
                return diffMonths === 1 ? "1 month ago" : `${diffMonths} months ago`;
            }

            if (diffDays > 0) {
                return diffDays === 1 ? "1 day ago" : `${diffDays} days ago`;
            }

            if (diffHours > 0) {
                return diffHours === 1 ? "1 hour ago" : `${diffHours} hours ago`;
            }

            if (diffMinutes > 0) {
                return diffMinutes === 1 ? "1 minute ago" : `${diffMinutes} minutes ago`;
            }

            return diffSeconds <= 1 ? "Just now" : `${diffSeconds} seconds ago`;
        }
    </script>

</body>

</html>