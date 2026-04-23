document.addEventListener("DOMContentLoaded", () => {
  // ========= Version A (mobile menu) =========
  const menuToggle = document.getElementById("menuToggle");
  const menuClose  = document.getElementById("menuClose");
  const mobileMenu = document.getElementById("mobileMenu");

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.remove("hidden");
    });

    if (menuClose) {
      menuClose.addEventListener("click", () => {
        mobileMenu.classList.add("hidden");
      });
    }

    mobileMenu.querySelectorAll("a").forEach(link => {
      link.addEventListener("click", () => mobileMenu.classList.add("hidden"));
    });

    // close when clicking outside the side menu
    mobileMenu.addEventListener("click", (e) => {
      if (e.target === mobileMenu) {
        mobileMenu.classList.add("hidden");
      }
    });
  }

  // ========= Version B (sidebar) =========
  const toggleSidebar = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (toggleSidebar && sidebar) {
    const open = () => {
      sidebar.classList.remove("-translate-x-full");
      sidebar.classList.add("translate-x-0");
      if (overlay) overlay.classList.remove("hidden");
    };

    const close = () => {
      sidebar.classList.add("-translate-x-full");
      sidebar.classList.remove("translate-x-0");
      if (overlay) overlay.classList.add("hidden");
    };

    const isOpen = () => !sidebar.classList.contains("-translate-x-full");

    toggleSidebar.addEventListener("click", () => {
      isOpen() ? close() : open();
    });

    if (overlay) overlay.addEventListener("click", close);

    sidebar.querySelectorAll("a").forEach(link => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 1024) close();
      });
    });
  }
});