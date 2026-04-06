document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModal');
    const statusDropdown = document.getElementById('statusDropdown');
    const updateForm = document.getElementById('updateStatusForm');
    const statusHelpText = document.getElementById('statusHelpText');
    const adminMessageWrapper = document.getElementById('adminMessageWrapper');
    const adminMessage = document.getElementById('adminMessage');

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

    document.querySelectorAll('.editBookingBtn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.dataset.bookingId;
            const currentStatus = this.dataset.statusName.trim();

            updateForm.action = window.staffBookingUpdateUrlTemplate.replace(':id', bookingId);
            statusDropdown.innerHTML = '';

            const transitions = allowedTransitions[currentStatus] || [];

            if (transitions.length === 0) {
                const option = document.createElement('option');
                option.textContent = 'No available transitions';
                option.disabled = true;
                option.selected = true;
                statusDropdown.appendChild(option);
            } else {
                transitions.forEach(statusName => {
                    const option = document.createElement('option');
                    option.value = window.bookingStatusMap[statusName];
                    option.textContent = statusName;
                    statusDropdown.appendChild(option);
                });
            }

            if (currentStatus === 'Return') {
                adminMessageWrapper.classList.remove('hidden');
                statusHelpText.classList.remove('hidden');
                statusHelpText.textContent = 'You may add a note for damage, repair, or checkup.';
            } else {
                adminMessageWrapper.classList.add('hidden');
                statusHelpText.classList.add('hidden');
                adminMessage.value = '';
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    closeModalBtn?.addEventListener('click', function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
});