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
    emailInput.addEventListener('blur', () => {
      const newEmail = emailInput.value;
      checkout.updateEmail(newEmail).then((result) => {
        if (result.error) {
          emailErrors.textContent = result.error.message;
        }
      });
    });

    const button = document.getElementById('pay-button');
    const errors = document.getElementById('confirm-errors');
    button.addEventListener('click', () => {
      // Clear any validation errors
      errors.textContent = '';

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