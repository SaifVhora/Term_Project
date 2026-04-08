// SecondHand Marketplace - Client-side JS

document.addEventListener('DOMContentLoaded', function () {

  // Register: confirm passwords match
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      const pass = document.querySelector('input[name="password"]').value;
      const conf = document.querySelector('input[name="confirm_password"]').value;
      if (pass !== conf) { e.preventDefault(); alert('Passwords do not match.'); }
      if (pass.length < 6) { e.preventDefault(); alert('Password must be at least 6 characters.'); }
    });
  }

  // Prevent negative price
  const priceInput = document.querySelector('input[name="price"]');
  if (priceInput) {
    priceInput.addEventListener('input', function () {
      if (this.value < 0) this.value = 0;
    });
  }

  // Auto-dismiss success alerts after 4s
  document.querySelectorAll('.alert-success').forEach(function (el) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
      if (bsAlert) bsAlert.close();
    }, 4000);
  });

});
