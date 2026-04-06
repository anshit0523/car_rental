document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModal');
    const statusDropdown = document.getElementById('statusDropdown');
    const updateForm = document.getElementById('updateStatusForm');
    const statusHelpText = document.getElementById('statusHelpText');
    const adminMessageWrapper = document.getElementById('adminMessageWrapper');
    const adminMessage = document.getElementById('adminMessage');
    const saveStatusBtn = document.getElementById('saveStatusBtn');

    const allowedTransitions = {
        Pending: ['Cancelled'],
        Confirmed: ['Cancelled'],
        Return: ['Checkup', 'Damage', 'Needs Repair', 'Completed'],
        Active: [],
        Completed: [],
        Cancelled: [],
        Checkup: [],
        Damage: [],
        'Needs Repair': [],
        Failed: [],
    };

    const issueStatuses = ['Checkup', 'Damage', 'Needs Repair'];

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

    document.querySelectorAll('.editBookingBtn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.dataset.bookingId;
            const currentStatus = (this.dataset.statusName || '').trim();

            updateForm.action = window.staffBookingUpdateUrlTemplate.replace(':id', bookingId);
            statusDropdown.innerHTML = '';

            const transitions = allowedTransitions[currentStatus] || [];

            if (transitions.length > 0) {
                transitions.forEach(statusName => {
                    const statusId = window.bookingStatusMap[statusName];

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

                toggleAdminMessageField();
            } else {
                const option = document.createElement('option');
                option.textContent = 'No available transitions';
                option.disabled = true;
                option.selected = true;
                statusDropdown.appendChild(option);

                statusDropdown.disabled = true;

                if (saveStatusBtn) {
                    saveStatusBtn.disabled = true;
                    saveStatusBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                statusHelpText.textContent = `No manual action available for ${currentStatus}.`;
                statusHelpText.classList.remove('hidden');

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

    if (updateForm) {
        updateForm.addEventListener('submit', function (e) {
            if (!statusDropdown) return;

            const selectedText = statusDropdown.options[statusDropdown.selectedIndex]?.text?.trim() || '';
            const action = updateForm.action || '';
            const bookingIdMatch = action.match(/\/staff\/bookings\/(\d+)\/update-status/);

            if (!bookingIdMatch) return;

            const bookingId = bookingIdMatch[1];

            if (issueStatuses.includes(selectedText)) {
                e.preventDefault();
                window.location.href = `/staff/bookings/${bookingId}/return-issue/create?status=${encodeURIComponent(selectedText)}`;
            }
        });
    }

    function closeModal() {
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        if (statusDropdown) {
            statusDropdown.innerHTML = '';
            statusDropdown.disabled = false;
        }

        if (saveStatusBtn) {
            saveStatusBtn.disabled = false;
            saveStatusBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        if (statusHelpText) {
            statusHelpText.textContent = '';
            statusHelpText.classList.add('hidden');
        }

        if (adminMessageWrapper) adminMessageWrapper.classList.add('hidden');
        if (adminMessage) adminMessage.value = '';
    }

    closeModalBtn?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });
});