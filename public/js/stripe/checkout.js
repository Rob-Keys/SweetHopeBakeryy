document.addEventListener('DOMContentLoaded', () => {
  initialize();
});

// Create a Checkout Session
async function initialize() {
  // Get public key from server for development/prod switching
  const stripe_public_key = await fetch('/get_stripe_public_key').then((r) => r.text());
  const stripe = Stripe(stripe_public_key);

  const fetchClientSecret = async () => {
    const response = await fetch("/checkoutAPI", {
      method: "POST",
    });
    const { clientSecret } = await response.json();
    return clientSecret;
  };

  stripe.initCheckout({fetchClientSecret})
  .then((checkout) => {
    const emailInput = document.getElementById('email');
    const emailErrors = document.getElementById('email-errors');
    const phoneInput = document.getElementById('phone');
    const phoneErrors = document.getElementById('phone-errors');
  
    emailInput.addEventListener('blur', () => {
      const newEmail = emailInput.value;
      checkout.updateEmail(newEmail).then((result) => {
        if (result.error) {
          emailErrors.innerHTML = "<p class='small-text'>" + result.error.message + "</p>";
        }
      });
    });

    const button = document.getElementById('pay-button');
    const errors = document.getElementById('confirm-errors');
    button.addEventListener('click', (e) => {
      // If another handler already called preventDefault, don't proceed
      if (e && e.defaultPrevented) return;

      // Clear any validation errors
      errors.textContent = '';
      phoneErrors.textContent = '';

      // Validate phone contains at least 10 numerical characters
      const phoneValue = phoneInput.value || '';
      const digitCount = (phoneValue.match(/\d/g) || []).length;
      if (digitCount < 10) {
        // Prevent other click handlers (including ones that may call checkout.confirm())
        if (e) {
          e.preventDefault();
          e.stopImmediatePropagation();
        }
        phoneErrors.innerHTML = "<p class='small-text'>Please enter a valid phone number with at least 10 digits.</p>";
        phoneInput.focus();
        return;
      }

      checkout.confirm().then((result) => {
        if (result.type === 'error') {
          errors.textContent = result.error.message;
        }
      });
    });

    const paymentElement = checkout.createPaymentElement();
    paymentElement.mount('#payment-element');
  });
}