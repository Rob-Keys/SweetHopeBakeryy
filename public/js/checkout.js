document.addEventListener('DOMContentLoaded', () => {
    const pickup_button = document.getElementById('pickup-button');
    const delivery_button = document.getElementById('delivery-button');
    const method_container = document.getElementById('method-container');
    const pay_button = document.getElementById('pay-button');

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

    const pickup_handler = () => {
        delivery_button.classList.remove('selected');
        pickup_button.classList.add('selected');

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

        pay_button.addEventListener('click', (e) => {
            if (!isDateValid(pickupDateInput, 5)) {
                e.preventDefault();
                alert('Pickup date must be at least 5 days from today.');
            }
        });
    }

    pickup_button.addEventListener('click', pickup_handler);
    pickup_handler();

    delivery_button.addEventListener('click', () => {
        pickup_button.classList.remove('selected');
        delivery_button.classList.add('selected');

        method_container.innerHTML = `
            <div class="row">
                <p class="col-md-6">Delivery Address:</p>
                <input type="text" class="col-md-6" required>
            </div>
            <div class="row">
                <p class="col-md-6">Delivery Date:</p>
                <input type="date" id="delivery-date" class="col-md-6" required>
            </div>
        `;

        // Set minimum date to 7 days from today
        const deliveryDateInput = document.getElementById('delivery-date');
        deliveryDateInput.min = getFutureDate(7);

        pay_button.addEventListener('click', (e) => {
            if (!isDateValid(deliveryDateInput, 7)) {
                e.preventDefault();
                alert('Delivery date must be at least 7 days from today.');
            }
        });
    });
});
