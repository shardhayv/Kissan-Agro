/**
 * Kissan Agro Foods - Admin Panel JavaScript
 * Handles responsive functionality for the admin panel
 */

document.addEventListener("DOMContentLoaded", function () {
  // Sidebar toggle for mobile
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");
  const content = document.querySelector(".content");
  const navbar = document.querySelector(".navbar");
  const adminFooter = document.querySelector(".admin-footer");

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function () {
      document.body.classList.toggle("sidebar-open");

      // Add/remove classes for sidebar visibility
      sidebar.classList.toggle("show");

      // Update aria-expanded attribute
      const isOpen = sidebar.classList.contains("show");
      sidebarToggle.setAttribute("aria-expanded", isOpen);
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", function (event) {
    const windowWidth = window.innerWidth;

    // Only on mobile screens
    if (windowWidth < 992 && document.body.classList.contains("sidebar-open")) {
      // Check if click is outside sidebar and not on the toggle button
      if (
        !sidebar.contains(event.target) &&
        !sidebarToggle.contains(event.target)
      ) {
        document.body.classList.remove("sidebar-open");
        sidebar.classList.remove("show");
        if (sidebarToggle) {
          sidebarToggle.setAttribute("aria-expanded", "false");
        }
      }
    }
  });

  // Handle responsive tables
  const responsiveTables = document.querySelectorAll(".table-responsive");
  if (responsiveTables.length > 0) {
    // Add horizontal scroll indicator for tables that overflow
    responsiveTables.forEach((table) => {
      table.addEventListener("scroll", function () {
        if (this.scrollLeft > 0) {
          this.classList.add("has-scroll");
        } else {
          this.classList.remove("has-scroll");
        }
      });
    });
  }

  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Confirm delete
  const deleteButtons = document.querySelectorAll(".delete-btn");
  if (deleteButtons) {
    deleteButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        if (
          !confirm(
            "Are you sure you want to delete this item? This action cannot be undone."
          )
        ) {
          e.preventDefault();
        }
      });
    });
  }

  // Responsive form layout adjustments
  const adjustFormLayout = () => {
    const windowWidth = window.innerWidth;
    const formGroups = document.querySelectorAll(".form-group.row");

    formGroups.forEach((group) => {
      if (windowWidth < 768) {
        group.classList.remove("row");
        const label = group.querySelector(".col-form-label");
        if (label) {
          label.classList.remove("text-md-end");
        }
      } else {
        group.classList.add("row");
        const label = group.querySelector(".col-form-label");
        if (label) {
          label.classList.add("text-md-end");
        }
      }
    });
  };

  // Enhanced table responsiveness
  const enhanceTableResponsiveness = () => {
    const tables = document.querySelectorAll(".table");
    const windowWidth = window.innerWidth;

    tables.forEach((table) => {
      // Add mobile-optimized class to all tables
      table.classList.add("mobile-optimized-table");

      // Handle very small screens
      if (windowWidth < 576) {
        // Add classes to hide less important columns on very small screens
        const lessImportantCells = table.querySelectorAll(
          "th:nth-child(n+4):not(:last-child), td:nth-child(n+4):not(:last-child)"
        );
        lessImportantCells.forEach((cell) => {
          cell.classList.add("d-none-xs");
        });
      } else {
        // Remove the classes if screen is larger
        const hiddenCells = table.querySelectorAll(".d-none-xs");
        hiddenCells.forEach((cell) => {
          cell.classList.remove("d-none-xs");
        });
      }
    });
  };

  // Enhance form buttons on mobile
  const enhanceFormButtons = () => {
    const windowWidth = window.innerWidth;
    const formButtons = document.querySelectorAll("form .btn");

    if (windowWidth < 576) {
      // Add form-actions class to button containers
      formButtons.forEach((button) => {
        const parent = button.parentElement;
        if (
          parent &&
          !parent.classList.contains("table-actions") &&
          !parent.classList.contains("form-actions")
        ) {
          if (parent.querySelector(".btn + .btn")) {
            parent.classList.add("form-actions");
          }
        }
      });
    }
  };

  // Enhance filter sections
  const enhanceFilterSections = () => {
    const filterForms = document.querySelectorAll('form[action*="?"]');
    filterForms.forEach((form) => {
      form.classList.add("filter-section");
    });
  };

  // Call all responsive adjustments
  const applyResponsiveAdjustments = () => {
    adjustFormLayout();
    enhanceTableResponsiveness();
    enhanceFormButtons();
    enhanceFilterSections();
  };

  // Call on load and resize
  applyResponsiveAdjustments();
  window.addEventListener("resize", applyResponsiveAdjustments);
});
