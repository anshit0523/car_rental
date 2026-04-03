document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const searchInput = form?.querySelector('input[name="search"]');
    const statusSelect = form?.querySelector('select[name="status_id"]');

    const modal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModal');
    const statusDropdown = document.getElementById('statusDropdown');
    const updateForm = document.getElementById('updateStatusForm');
    const statusHelpText = document.getElementById('statusHelpText');
    const saveStatusBtn = document.getElementById('saveStatusBtn');

   
    const adminMessageWrapper = document.getElementById('adminMessageWrapper');
    const adminMessage = document.getElementById('adminMessage');

    let typingTimer;

    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => form.submit(), 500);
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', () => form.submit());
    }

    const allowedTransitions = {
        Pending: ['Cancelled'],
        Confirmed: ['Cancelled'],
        Return: ['Completed', 'Checkup', 'Damage', 'Needs Repair'],
        Active: [],
        Completed: [],
        Cancelled: [],
        Checkup: [],
        Damage: [],
        'Needs Repair': [],
        Failed: [],
    };

    const statusMap = window.bookingStatusMap || {};

    function toggleAdminMessageField() {
        if (!statusDropdown || !adminMessageWrapper) return;

        const selectedText = statusDropdown.options[statusDropdown.selectedIndex]?.text?.trim() || '';

        const showFor = ['Completed', 'Checkup', 'Damage', 'Needs Repair'];

        if (showFor.includes(selectedText)) {
            adminMessageWrapper.classList.remove('hidden');
        } else {
            adminMessageWrapper.classList.add('hidden');
            if (adminMessage) {
                adminMessage.value = '';
            }
        }
    }

    document.querySelectorAll('.editBookingBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const bookingId = btn.getAttribute('data-booking-id');
            const currentStatusName = (btn.getAttribute('data-status-name') || '').trim();

            updateForm.action = `/admin/bookings/${bookingId}/update-status`;

            const allowed = allowedTransitions[currentStatusName] || [];

            statusDropdown.innerHTML = '';

            if (allowed.length > 0) {
                allowed.forEach(statusName => {
                    const statusId = statusMap[statusName];

                    if (statusId) {
                        const option = document.createElement('option');
                        option.value = statusId;
                        option.textContent = statusName;
                        statusDropdown.appendChild(option);
                    }
                });

                statusDropdown.disabled = false;

                if (saveStatusBtn) {
                    saveStatusBtn.disabled = false;
                    saveStatusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }

                statusHelpText.textContent = '';
                statusHelpText.classList.add('hidden');

                // new
                toggleAdminMessageField();
            } else {
                statusDropdown.disabled = true;

                if (saveStatusBtn) {
                    saveStatusBtn.disabled = true;
                    saveStatusBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                statusHelpText.textContent = `No manual action available for ${currentStatusName}.`;
                statusHelpText.classList.remove('hidden');

                // new
                if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
                if (adminMessage) adminMessage.value = '';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    if (statusDropdown) {
        statusDropdown.addEventListener('change', toggleAdminMessageField);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        statusDropdown.innerHTML = '';
        statusDropdown.disabled = false;

        if (saveStatusBtn) {
            saveStatusBtn.disabled = false;
            saveStatusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        statusHelpText.textContent = '';
        statusHelpText.classList.add('hidden');

        // new
        if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
        if (adminMessage) adminMessage.value = '';
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
});