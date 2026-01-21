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
    const nameInput = document.getElementById('name');
    const dateInput = document.getElementById('pickup-date');

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
    button.addEventListener('click', async (e) => {
      // If another handler already called preventDefault, don't proceed
      if (e && e.defaultPrevented) return;

      // Clear any validation errors
      errors.textContent = '';
      phoneErrors.textContent = '';
      emailErrors.textContent = '';

      // Validate name
      const nameValue = (nameInput.value || '').trim();
      if (!nameValue) {
        errors.textContent = 'Please enter your name.';
        nameInput.focus();
        return;
      }

      // Basic email validation (require something@something.something)
      const emailValue = (emailInput.value || '').trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailValue)) {
        emailErrors.textContent = 'Please enter a valid email address.';
        emailInput.focus();
        return;
      }

      // Validate phone contains at least 10 numerical characters
      const phoneValue = phoneInput.value || '';
      const digitCount = (phoneValue.match(/\d/g) || []).length;
      if (digitCount < 10) {
        phoneErrors.innerHTML = "<p class='small-text'>Please enter a valid phone number with at least 10 digits.</p>";
        phoneInput.focus();
        return;
      }

      // Validate pickup date
      const dateValue = dateInput.value || '';
      if (!dateValue) {
        errors.textContent = 'Please select a pickup date.';
        dateInput.focus();
        return;
      }

      // Save customer details to session before payment
      const acquisitionMethod = document.querySelector('input[name="acquisition_method"]')?.value || 'pickup';
      const deliveryAddress = document.getElementById('delivery-address')?.value || '';

      try {
        const saveResponse = await fetch('/saveCustomerDetails', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            customer_name: nameValue,
            customer_phone: phoneValue,
            acquisition_date: dateValue,
            acquisition_method: acquisitionMethod,
            delivery_address: deliveryAddress
          })
        });

        if (!saveResponse.ok) {
          errors.textContent = 'Failed to save customer details. Please try again.';
          return;
        }
      } catch (err) {
        errors.textContent = 'Failed to save customer details. Please try again.';
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
