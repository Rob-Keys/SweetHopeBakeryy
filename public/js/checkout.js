document.addEventListener('DOMContentLoaded', () => {
    let acquisition_method = "pickup"; // default

    function getFutureDate(daysAhead) {
        const date = new Date();
        date.setDate(date.getDate() + daysAhead);
        return date.toISOString().split('T')[0]; // format YYYY-MM-DD
    }

    function isDateValid(input, daysAhead) {
        const selectedDate = new Date(input.value);
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + daysAhead);
        return selectedDate >= minDate;
    }

    const log_customer_info = () => {
            fetch('/log_customer_info_api', {
                method: 'POST',
                body: JSON.stringify({
                    acquisition_method: acquisition_method,
                    acquisition_date: document.getElementById('pickup-date').value,
                    customer_phone: document.getElementById('phone').value,
                    customer_name: document.getElementById('name').value,
                    customer_email: document.getElementById('email').value,
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
        };

    const pickup_handler = () => {
        // delivery_button.classList.remove('selected');
        // pickup_button.classList.add('selected');
        acquisition_method = "pickup";

        /*
        method_container.innerHTML = `
            <p> Picking up from <a href="https://www.google.com/maps/search/?api=1&query=22207+USA" target="_blank"><strong>Arlington, VA, 22207</strong></a> </p>
            <div class="row">
                <p class="col-md-6">Pickup Date:</p>
                <input type="date" id="pickup-date" class="col-md-6" required>
            </div>
        `;
        */

        // Set minimum date to 3 days from today
        const pickupDateInput = document.getElementById('pickup-date');
        pickupDateInput.min = getFutureDate(3);
        pickupDateInput.addEventListener('change', log_customer_info);

        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('change', log_customer_info);

        const emailInput = document.getElementById('email');
        emailInput.addEventListener('change', log_customer_info);

        // Validation is now handled in the submit button handler below
        // No need for separate validation handler
    }

    // Add submit handler for the request button
    const submitButton = document.getElementById('pay-button');
    if (submitButton) {
        submitButton.addEventListener('click', async function(e) {
            // Don't prevent default if validation already failed
            if (e.defaultPrevented) return;

            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const pickupDateInput = document.getElementById('pickup-date');
            const errors = document.getElementById('confirm-errors');

            // Clear any previous errors
            errors.textContent = '';

            // Check all required fields
            if (!nameInput.value.trim()) {
                errors.textContent = 'Please enter your name.';
                nameInput.focus();
                return;
            }

            // Validate email format
            const emailValue = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailValue)) {
                errors.textContent = 'Please enter a valid email address.';
                emailInput.focus();
                return;
            }

            // Validate phone
            const phoneValue = phoneInput.value || '';
            const digitCount = (phoneValue.match(/\d/g) || []).length;
            if (digitCount < 10) {
                errors.textContent = 'Please enter a valid phone number with at least 10 digits.';
                phoneInput.focus();
                return;
            }

            // Validate pickup date
            if (!pickupDateInput.value) {
                errors.textContent = 'Please select a pickup date.';
                pickupDateInput.focus();
                return;
            }

            // Check if date is at least 3 days from today
            if (!isDateValid(pickupDateInput, 2)) {
                errors.textContent = 'Pickup date must be at least 3 days from today.';
                pickupDateInput.focus();
                return;
            }

            // All validation passed - save info and redirect
            try {
                await fetch('/log_customer_info_api', {
                    method: 'POST',
                    body: JSON.stringify({
                        acquisition_method: acquisition_method,
                        acquisition_date: pickupDateInput.value,
                        customer_phone: phoneInput.value,
                        customer_name: nameInput.value,
                        customer_email: emailInput.value,
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                // Redirect to return page
                window.location.href = '/return';
            } catch (error) {
                errors.textContent = 'An error occurred. Please try again.';
            }
        });
    }

    //pickup_button.addEventListener('click', pickup_handler);
    pickup_handler();

    /*
    delivery_button.addEventListener('click', () => {
        pickup_button.classList.remove('selected');
        delivery_button.classList.add('selected');
        acquisition_method = "delivery";

        method_container.innerHTML = `
            <div class="row">
                <p class="col-md-6">Delivery Address:</p>
                <input type="text" id="delivery-address" class="col-md-6" required>
            </div>
            <div class="row">
                <p class="col-md-6">Delivery Date:</p>
                <input type="date" id="delivery-date" class="col-md-6" required>
            </div>
        `;

        // Set minimum date to 7 days from today
        const deliveryDateInput = document.getElementById('delivery-date');
        deliveryDateInput.min = getFutureDate(7);
        const deliveryAddressInput = document.getElementById('delivery-address');
        deliveryDateInput.addEventListener('input', () => {
            fetch('/log_customer_info_api', {
                method: 'POST',
                body: JSON.stringify({
                    acquisition_method: acquisition_method,
                    acquisition_date: deliveryDateInput.value,
                    delivery_address: deliveryAddressInput.value
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
        });
        deliveryAddressInput.addEventListener('input', () => {
            fetch('/log_customer_info_api', {
                method: 'POST',
                body: JSON.stringify({
                    acquisition_method: acquisition_method,
                    acquisition_date: deliveryDateInput.value,
                    delivery_address: deliveryAddressInput.value
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
        });

        // Replace any existing validation handler with the delivery validator
        if (currentValidationHandler) {
            pay_button.removeEventListener('click', currentValidationHandler);
        }
        currentValidationHandler = validateDelivery;
        pay_button.addEventListener('click', currentValidationHandler);
    });
    */
});
