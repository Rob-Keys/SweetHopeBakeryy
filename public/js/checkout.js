document.addEventListener('DOMContentLoaded', () => {
    const pickup_button = document.getElementById('pickup-button');
    const delivery_button = document.getElementById('delivery-button');
    const method_container = document.getElementById('method-container');
    const pay_button = document.getElementById('pay-button');
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

    // Keep a reference to the current pay-button validation handler so it can be removed
    let currentValidationHandler = null;

    // Named validation handlers so removeEventListener works
    function validatePickup(e) {
        const pickupDateInput = document.getElementById('pickup-date');
        if (!pickupDateInput || !isDateValid(pickupDateInput, 5)) {
            e.preventDefault();
            alert('Pickup date must be at least 5 days from today.');
        }
    }

    function validateDelivery(e) {
        const deliveryDateInput = document.getElementById('delivery-date');
        if (!deliveryDateInput || !isDateValid(deliveryDateInput, 7)) {
            e.preventDefault();
            alert('Delivery date must be at least 7 days from today.');
        }
    }

    const pickup_handler = () => {
        delivery_button.classList.remove('selected');
        pickup_button.classList.add('selected');
        acquisition_method = "pickup";

        method_container.innerHTML = `
            <p> Picking up from 123 Main St, Arlington, VA </p>
            <div class="row">
                <p class="col-md-6">Pickup Date:</p>
                <input type="date" id="pickup-date" class="col-md-6" required>
            </div>
        `;

        // Set minimum date to 5 days from today
        const pickupDateInput = document.getElementById('pickup-date');
        pickupDateInput.min = getFutureDate(5);
        pickupDateInput.addEventListener('change', () => {
            fetch('/log_customer_info_api', {
                method: 'POST',
                body: JSON.stringify({
                    acquisition_method: acquisition_method,
                    acquisition_date: pickupDateInput.value,
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
        });

        // Replace any existing validation handler with the pickup validator
        if (currentValidationHandler) {
            pay_button.removeEventListener('click', currentValidationHandler);
        }
        currentValidationHandler = validatePickup;
        pay_button.addEventListener('click', currentValidationHandler);
    }

    pickup_button.addEventListener('click', pickup_handler);
    pickup_handler();

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
        deliveryDateInput.addEventListener('change', () => {
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
        deliveryAddressInput.addEventListener('change', () => {
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

});
