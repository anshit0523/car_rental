document.addEventListener("DOMContentLoaded", function () {
    const paymentOptions = document.querySelectorAll(".payment-option");
    const gcashSection = document.getElementById("gcashSection");
    const bankSection = document.getElementById("bankSection");
    const paymentMethodInput = document.getElementById("paymentMethod");

    const form = document.getElementById("paymentForm");
    const errorModal = document.getElementById("errorModal");
    const submitBtn = document.getElementById("submitPaymentBtn");

    const gcashInput = document.getElementById("gcashInput");
    const gcashPreview = document.getElementById("gcashPreview");
    const gcashPreviewImg = document.getElementById("gcashPreviewImg");

    const bankInput = document.getElementById("bankInput");
    const bankPreview = document.getElementById("bankPreview");
    const bankPreviewImg = document.getElementById("bankPreviewImg");

    let isSubmitting = false;

    function hideAllPaymentSections() {
        if (gcashSection) gcashSection.classList.add("hidden-panel");
        if (bankSection) bankSection.classList.add("hidden-panel");
    }

    function clearActiveOptions() {
        paymentOptions.forEach(option => {
            option.classList.remove("is-active");
        });
    }

    function showModal(message) {
        if (!errorModal) return;

        const textEl = errorModal.querySelector(".error-modal-text");
        if (textEl) textEl.textContent = message;

        errorModal.style.display = "flex";
    }

    window.closeModal = function () {
        if (!errorModal) return;
        errorModal.style.display = "none";
    };

    paymentOptions.forEach(option => {
        option.addEventListener("click", function () {
            const method = this.dataset.method;

            clearActiveOptions();
            this.classList.add("is-active");

            hideAllPaymentSections();
            paymentMethodInput.value = method;

            if (method === "gcash" && gcashSection) {
                gcashSection.classList.remove("hidden-panel");
            }

            if (method === "bank" && bankSection) {
                bankSection.classList.remove("hidden-panel");
            }
        });
    });

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            if (isSubmitting) return;

            const method = paymentMethodInput ? paymentMethodInput.value : "";
            const gcashFileCount = gcashInput?.files?.length || 0;
            const bankFileCount = bankInput?.files?.length || 0;

            if (!method) {
                showModal("Please select a payment method.");
                return;
            }

            if (method === "gcash" && gcashFileCount === 0) {
                showModal("Please upload your GCash receipt.");
                return;
            }

            if (method === "bank" && bankFileCount === 0) {
                showModal("Please upload your bank transfer receipt.");
                return;
            }

            isSubmitting = true;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = "Processing...";
            }

            form.submit();
        });
    }

    if (gcashInput && gcashPreview && gcashPreviewImg) {
        gcashInput.addEventListener("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                gcashPreviewImg.src = e.target.result;
                gcashPreview.style.display = "block";
            };
            reader.readAsDataURL(file);
        });
    }

    if (bankInput && bankPreview && bankPreviewImg) {
        bankInput.addEventListener("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                bankPreviewImg.src = e.target.result;
                bankPreview.style.display = "block";
            };
            reader.readAsDataURL(file);
        });
    }
});