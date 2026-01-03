document.addEventListener('DOMContentLoaded', () => {
    // Set minimum date to 3 days from today
    const pickupDateInput = document.getElementById('pickup-date');
    if (pickupDateInput) {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 3);
        pickupDateInput.min = minDate.toISOString().split('T')[0];
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
