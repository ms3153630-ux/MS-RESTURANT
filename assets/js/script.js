/**
 * Saffron & Spice Gourmet Restaurant - Frontend & Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
  // Auto dismiss flash alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      try {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      } catch (e) {}
    }, 5000);
  });

  // Quantity input controls (+ / - buttons)
  const qtyBtns = document.querySelectorAll('.qty-btn');
  qtyBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const isPlus = this.classList.contains('qty-plus');
      const input = this.parentElement.querySelector('.qty-input');
      if (input) {
        let val = parseInt(input.value) || 1;
        if (isPlus) {
          val++;
        } else {
          val = Math.max(1, val - 1);
        }
        input.value = val;

        // If in cart table with auto-submit
        const form = input.closest('.cart-update-form');
        if (form) {
          form.submit();
        }
      }
    });
  });

  // Search filter on Menu Page
  const menuSearchInput = document.getElementById('menuSearchInput');
  if (menuSearchInput) {
    menuSearchInput.addEventListener('input', function () {
      const filter = this.value.toLowerCase().trim();
      const foodItems = document.querySelectorAll('.menu-item-card');

      foodItems.forEach(function (item) {
        const title = (item.getAttribute('data-name') || '').toLowerCase();
        const desc = (item.getAttribute('data-desc') || '').toLowerCase();
        const category = (item.getAttribute('data-category') || '').toLowerCase();

        if (title.includes(filter) || desc.includes(filter) || category.includes(filter)) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }

  // Image preview on file input change
  const imgInputs = document.querySelectorAll('.image-preview-input');
  imgInputs.forEach(function (input) {
    input.addEventListener('change', function () {
      const targetId = this.getAttribute('data-preview-target');
      const previewImg = document.getElementById(targetId);
      if (previewImg && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          previewImg.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
});
