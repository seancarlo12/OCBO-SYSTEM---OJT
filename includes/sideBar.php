<!DOCTYPE html>
<!-- Coding by CodingNepal || www.codingnepalweb.com -->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
    <title>OCBO e-LogBook System</title>
    <link rel="stylesheet" href="../assets/style/globalStyle.css" />
    <link rel="stylesheet" href="../assets/style/sideBar.css" />
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
            <i class='bx bx-bell'></i>
            <i class='bx bx-user-circle'></i>
            <label id="profile-name">OCBO Admin</label>
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
                        <span class="navlink">e-LogBook</span>
                    </a>
                </li>
            </ul>
            <ul class="menu_items">
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


            </ul>
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
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
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