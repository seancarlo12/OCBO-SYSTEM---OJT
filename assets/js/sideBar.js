const body = document.querySelector("body");
const darkLight = document.querySelector("#darkLight");
const sidebar = document.querySelector(".sidebar");
const submenuItems = document.querySelectorAll(".submenu_item");
const sidebarOpen = document.querySelector("#sidebarOpen");
const sidebarClose = document.querySelector(".collapse_sidebar");
const sidebarExpand = document.querySelector(".expand_sidebar");
const sidebarNavLinks = document.querySelectorAll(".sidebar .nav_link[data-href]");

sidebarNavLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
        const target = link.getAttribute("data-href");
        if (target) {
            e.preventDefault();
            window.location.href = target;
        }
    });
});
sidebarOpen.addEventListener("click", () => {
    sidebar.classList.toggle("close");
    if (typeof window.updateSidebarTooltips === "function") {
        window.updateSidebarTooltips();
    }
});
sidebarClose.addEventListener("click", () => {
    // Toggle between collapsed and expanded states without hover behavior
    const isClosed = sidebar.classList.contains("close");
    if (isClosed) {
        sidebar.classList.remove("close", "hoverable");
    } else {
        sidebar.classList.add("close", "hoverable");
    }
    if (typeof window.updateSidebarTooltips === "function") {
        window.updateSidebarTooltips();
    }
});
sidebarExpand.addEventListener("click", () => {
    sidebar.classList.remove("close", "hoverable");
    if (typeof window.updateSidebarTooltips === "function") {
        window.updateSidebarTooltips();
    }
});
darkLight.addEventListener("click", () => {
    body.classList.toggle("dark");
    if (body.classList.contains("dark")) {
        document.setI
        darkLight.classList.replace("bx-sun", "bx-moon");
    } else {
        darkLight.classList.replace("bx-moon", "bx-sun");
    }
});
// submenuItems.forEach((item, index) => {
//     item.addEventListener("click", () => {
//         item.classList.toggle("show_submenu");
//         submenuItems.forEach((item2, index2) => {
//             if (index !== index2) {
//                 item2.classList.remove("show_submenu");
//             }
//         });
//     });
// });
if (window.innerWidth < 768) {
    sidebar.classList.add("close");
} else {
    sidebar.classList.remove("close");
}

if (typeof window.updateSidebarTooltips === "function") {
    window.updateSidebarTooltips();
}