/**
 * Kissan Agro Foods - Main JavaScript File
 */

document.addEventListener("DOMContentLoaded", function () {
  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize Bootstrap popovers
  var popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Contact form validation
  const contactForm = document.getElementById("contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      let isValid = true;
      const name = document.getElementById("name");
      const email = document.getElementById("email");
      const subject = document.getElementById("subject");
      const message = document.getElementById("message");

      // Simple validation
      if (name.value.trim() === "") {
        showError(name, "Name is required");
        isValid = false;
      } else {
        removeError(name);
      }

      if (email.value.trim() === "") {
        showError(email, "Email is required");
        isValid = false;
      } else if (!isValidEmail(email.value)) {
        showError(email, "Please enter a valid email");
        isValid = false;
      } else {
        removeError(email);
      }

      if (subject.value.trim() === "") {
        showError(subject, "Subject is required");
        isValid = false;
      } else {
        removeError(subject);
      }

      if (message.value.trim() === "") {
        showError(message, "Message is required");
        isValid = false;
      } else {
        removeError(message);
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }

  // Product filter functionality
  const categoryFilter = document.getElementById("categoryFilter");
  if (categoryFilter) {
    const selectWrapper = categoryFilter.closest(".custom-select-wrapper");

    // Add active class on focus
    categoryFilter.addEventListener("focus", function () {
      selectWrapper.classList.add("active");
    });

    // Remove active class on blur
    categoryFilter.addEventListener("blur", function () {
      selectWrapper.classList.remove("active");
    });

    // Submit the form when the dropdown value changes
    categoryFilter.addEventListener("change", function () {
      // Submit the form when the dropdown value changes
      this.closest("form").submit();
    });
  }

  // Admin panel - confirm delete
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

  // Image preview for product form
  const imageInput = document.getElementById("image");
  const imagePreview = document.getElementById("imagePreview");
  if (imageInput && imagePreview) {
    imageInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreview.src = e.target.result;
          imagePreview.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

// Helper functions
function showError(input, message) {
  const formGroup = input.parentElement;
  const errorElement =
    formGroup.querySelector(".invalid-feedback") ||
    document.createElement("div");

  errorElement.className = "invalid-feedback";
  errorElement.innerText = message;

  if (!formGroup.querySelector(".invalid-feedback")) {
    formGroup.appendChild(errorElement);
  }

  input.classList.add("is-invalid");
}

function removeError(input) {
  input.classList.remove("is-invalid");
  const errorElement = input.parentElement.querySelector(".invalid-feedback");
  if (errorElement) {
    errorElement.remove();
  }
}

function isValidEmail(email) {
  const re =
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}
