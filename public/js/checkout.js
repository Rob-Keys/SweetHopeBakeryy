document.addEventListener('DOMContentLoaded', () => {
    // Set minimum date to 3 days from today
    const pickupDateInput = document.getElementById('pickup-date');
    if (pickupDateInput) {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 3);
        pickupDateInput.min = minDate.toISOString().split('T')[0];
    }

    // Load saved form data from sessionStorage
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (nameInput && sessionStorage.getItem('checkout_name')) {
        nameInput.value = sessionStorage.getItem('checkout_name');
    }
    if (emailInput && sessionStorage.getItem('checkout_email')) {
        emailInput.value = sessionStorage.getItem('checkout_email');
    }
    if (phoneInput && sessionStorage.getItem('checkout_phone')) {
        phoneInput.value = sessionStorage.getItem('checkout_phone');
    }
    if (pickupDateInput && sessionStorage.getItem('checkout_pickup_date')) {
        pickupDateInput.value = sessionStorage.getItem('checkout_pickup_date');
    }

    // Save form data to sessionStorage on input
    if (nameInput) {
        nameInput.addEventListener('input', (e) => {
            sessionStorage.setItem('checkout_name', e.target.value);
        });
    }
    if (emailInput) {
        emailInput.addEventListener('input', (e) => {
            sessionStorage.setItem('checkout_email', e.target.value);
        });
    }
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            sessionStorage.setItem('checkout_phone', e.target.value);
        });
    }
    if (pickupDateInput) {
        pickupDateInput.addEventListener('change', (e) => {
            sessionStorage.setItem('checkout_pickup_date', e.target.value);
        });
    }

    // Validate form on submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const pickupDateInput = document.getElementById('pickup-date');
            const errors = document.getElementById('confirm-errors');

            // Clear any previous errors
            errors.textContent = '';

            // Check all required fields
            if (!nameInput.value.trim()) {
                e.preventDefault();
                errors.textContent = 'Please enter your name.';
                nameInput.focus();
                return;
            }

            // Validate email format
            const emailValue = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailValue)) {
                e.preventDefault();
                errors.textContent = 'Please enter a valid email address.';
                emailInput.focus();
                return;
            }

            // Validate phone (at least 10 digits)
            const phoneValue = phoneInput.value || '';
            const digitCount = (phoneValue.match(/\d/g) || []).length;
            if (digitCount < 10) {
                e.preventDefault();
                errors.textContent = 'Please enter a valid phone number with at least 10 digits.';
                phoneInput.focus();
                return;
            }

            // Validate pickup date
            if (!pickupDateInput.value) {
                e.preventDefault();
                errors.textContent = 'Please select a pickup date.';
                pickupDateInput.focus();
                return;
            }

            // Check if date is at least 3 days from today
            const selectedDate = new Date(pickupDateInput.value);
            const minDate = new Date();
            minDate.setDate(minDate.getDate() + 2); // 2 days ahead means 3 days minimum
            if (selectedDate < minDate) {
                e.preventDefault();
                errors.textContent = 'Pickup date must be at least 3 days from today.';
                pickupDateInput.focus();
                return;
            }
        });
    }
});
