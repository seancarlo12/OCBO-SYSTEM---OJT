<?php

// 🔒 Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<!-- Coding by CodingNepal || www.codingnepalweb.com -->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Boxicons CSS -->
     <link rel="stylesheet" href="../assets/style/boxIcon.css">
    
    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="../assets/style/bs.css" />
    <title>OCBO e-LogBook System</title>
    <link rel="stylesheet" href="../assets/style/globalStyle.css" />
    <link rel="stylesheet" href="../assets/style/sideBar.css" />
    <script src="../assets/js/jQuery.js"></script>

    <script>
        $(document).on("click", "#logoutBtn", function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Logout?",
                text: "Are you sure you want to log out?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, logout"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get("logout.php", function() {
                        window.location.href = "login.php";
                    });
                }
            });
        });
    </script>
</head>

<body>
    <!-- navbar -->
    <nav class="navbar">
        <div class="logo_item">
            <i class="bx bx-menu" id="sidebarOpen"></i>
            <img src="../assets/images/cab-logo.jpg" alt=""></i>OCBO e-LogBook System
        </div>
        <!-- <div class="search_bar">
                <input type="text" placeholder="Search" />
            </div> -->
        <div class="navbar_content">
            <i class="bi bi-grid"></i>
            <i class='bx bx-sun' id="darkLight"></i>
            <!-- <i class='bx bx-bell'></i> -->
            <!-- <i class='bx bx-user-circle'></i> -->
            <label id="profile-name">
                <?php echo $_SESSION['username']; ?>
            </label>
            <a id="logoutBtn"><i class='bx bx-log-out'></i></a>
            <!-- <img src="../assets/images/lerong.jpg" alt="" class="profile" /> -->
        </div>
    </nav>
    <!-- sidebar -->
    <nav class="sidebar">
        <div class="menu_content">
            <ul class="menu_items first_item">
                <div class="menu_title menu_dahsboard"></div>
                <li class="item">
                    <a href="../admin_portal/index.php" class="nav_link submenu_item" data-bs-toggle="tooltip" data-bs-placement="right" title="Home">
                        <span class="navlink_icon">
                            <i class="bx bx-home-alt"></i>
                        </span>
                        <span class="navlink">Home</span>
                    </a>
                </li>
            </ul>
            <ul class="menu_items">
                <div class="menu_title menu_logbook"></div>
                <li class="item">
                    <a href="../admin_portal/logbook.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="right" title="e-LogBook">
                        <span class="navlink_icon">
                            <i class="bx bxs-book"></i>
                        </span>
                        <span class="navlink">Applications</span>
                    </a>
                </li>
            </ul>
            <!-- <ul class="menu_items">
                <div class="menu_title menu_setting"></div>
                <li class="item">
                    <a href="../admin_portal/history.php" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="right" title="History">
                        <span class="navlink_icon">
                            <i class="bx bx-history"></i>
                        </span>
                        <span class="navlink">History</span>
                    </a>
                </li>
                <li class="item">
                    <a href="#" class="nav_link" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                        <span class="navlink_icon">
                            <i class="bx bx-log-out"></i>
                        </span>
                        <span class="navlink">Logout</span>
                    </a>
                </li>


            </ul> -->
            <!-- Sidebar Open / Close -->
            <div class="bottom_content">
                <div class="bottom expand_sidebar" data-bs-toggle="tooltip" data-bs-placement="right" title="Expand sidebar">
                    <span> Expand</span>
                    <i class='bx bx-chevrons-right'></i>
                </div>
                <div class="bottom collapse_sidebar" data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse sidebar">
                    <span> Collapse</span>
                    <i class='bx bx-chevrons-left'></i>
                </div>
            </div>
        </div>
    </nav>
    <!-- JavaScript -->
    <!-- Bootstrap JS bundle (includes Popper) -->
    <script src="../assets/js/bs.js"></script>
    <script src="../assets/js/sideBar.js"></script>
    <script src="../assets/js/jQuery.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.bootstrap && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                window.updateSidebarTooltips = function() {
                    var sidebar = document.querySelector('.sidebar');
                    if (!sidebar) return;
                    var isClosed = sidebar.classList.contains('close');
                    tooltipList.forEach(function(tooltip) {
                        if (isClosed) {
                            tooltip.enable();
                        } else {
                            tooltip.disable();
                        }
                    });
                };

                // Initial state
                window.updateSidebarTooltips();
            }
        });
    </script>





</body>

</html>