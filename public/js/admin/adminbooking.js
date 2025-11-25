document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const statusSelect = form.querySelector('select[name="status_id"]');
    const modal = document.getElementById("editModal");
    const closeModalBtn = document.getElementById("closeModal");
    const statusDropdown = document.getElementById("statusDropdown");
    const updateForm = document.getElementById("updateStatusForm");

    // Auto-submit when typing stops (after 500ms)
    let typingTimer;
    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => form.submit(), 500);
    });

    // Submit immediately when status changes
    statusSelect.addEventListener('change', () => form.submit());

//modal
    document.querySelectorAll(".editBookingBtn").forEach(btn => {
        btn.addEventListener("click", () => {
            const bookingId = btn.getAttribute("data-booking-id");
            const currentStatus = btn.getAttribute("data-status");

            // Set form action
            updateForm.action = `/admin/bookings/${bookingId}/update-status`;

            // Set current status in dropdown
            statusDropdown.value = currentStatus;

            // Show Modal
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        });
    });

    // Close Modal
    closeModalBtn.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    });

    // Close when clicking outside
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    });

});