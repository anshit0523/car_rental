document.addEventListener("DOMContentLoaded", function () {

    const paymentOptions = document.querySelectorAll('.payment-option');
    const gcashSection = document.getElementById('gcashSection');
    const bankSection = document.getElementById('bankSection');
    const paymentMethodInput = document.getElementById('paymentMethod');

    /* PAYMENT METHOD SELECT */

    paymentOptions.forEach(option => {

        option.addEventListener('click', () => {

            const method = option.dataset.method;

            gcashSection.classList.add('hidden');
            bankSection.classList.add('hidden');

            paymentOptions.forEach(o => {
                o.classList.remove('border-blue-500', 'bg-blue-50');
            });

            option.classList.add('border-blue-500', 'bg-blue-50');

            paymentMethodInput.value = method;

            if (method === 'gcash') gcashSection.classList.remove('hidden');
            if (method === 'bank') bankSection.classList.remove('hidden');

        });

    });

    /* FORM VALIDATION */

    const form = document.getElementById('paymentForm');
    const errorModal = document.getElementById('errorModal');

    form.addEventListener('submit', function (e) {

        const method = paymentMethodInput.value;

        const gcashFile = document.querySelector('input[name="receipt_image"]').files.length;
        const bankFile = document.querySelector('input[name="bank_receipt"]').files.length;

        if (method === '') {
            e.preventDefault();
            showModal('Please select a payment method.');
            return;
        }

        if (method === 'gcash' && gcashFile === 0) {
            e.preventDefault();
            showModal('Please upload your GCash receipt.');
            return;
        }

        if (method === 'bank' && bankFile === 0) {
            e.preventDefault();
            showModal('Please upload your bank transfer receipt.');
            return;
        }

    });

    function showModal(message) {
        errorModal.querySelector('p').innerText = message;
        errorModal.classList.remove('hidden');
    }

    window.closeModal = function () {
        errorModal.classList.add('hidden');
    }

    /* GCASH PREVIEW */

    const gcashInput = document.getElementById('gcashInput');
    const gcashPreview = document.getElementById('gcashPreview');
    const gcashPreviewImg = document.getElementById('gcashPreviewImg');

    gcashInput?.addEventListener('change', function () {

        const file = this.files[0];

        if (file) {

            const reader = new FileReader();

            reader.onload = function (e) {
                gcashPreviewImg.src = e.target.result;
                gcashPreview.classList.remove('hidden');
            }

            reader.readAsDataURL(file);
        }

    });

    /* BANK PREVIEW */

    const bankInput = document.getElementById('bankInput');
    const bankPreview = document.getElementById('bankPreview');
    const bankPreviewImg = document.getElementById('bankPreviewImg');

    bankInput?.addEventListener('change', function () {

        const file = this.files[0];

        if (file) {

            const reader = new FileReader();

            reader.onload = function (e) {
                bankPreviewImg.src = e.target.result;
                bankPreview.classList.remove('hidden');
            }

            reader.readAsDataURL(file);
        }

    });

});