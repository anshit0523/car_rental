document.addEventListener('DOMContentLoaded', function () {
    const pickupDate = document.getElementById('pickup_date');
    const returnDate = document.getElementById('return_date');

    if (!pickupDate || !returnDate) return;

    const today = new Date().toISOString().split('T')[0];

    pickupDate.min = today;

    if (!pickupDate.value) {
        returnDate.min = today;
    } else {
        returnDate.min = pickupDate.value;
    }

    pickupDate.addEventListener('change', function () {
        returnDate.min = this.value || today;

        if (returnDate.value && returnDate.value < returnDate.min) {
            returnDate.value = '';
        }
    });
});