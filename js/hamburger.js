document.addEventListener("DOMContentLoaded", function() {
    const hamburgerMenu = document.querySelector(".hamburger-menu");
    const mobileNav = document.querySelector(".mobile-nav");

    if (hamburgerMenu && mobileNav) {
        hamburgerMenu.addEventListener("click", function() {
            mobileNav.classList.toggle("active");
        });
    }
});
