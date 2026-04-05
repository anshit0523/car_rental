let statusFilter;
let dateFrom;
let dateTo;

document.addEventListener('DOMContentLoaded', function () {
    statusFilter = document.getElementById('statusFilter');
    dateFrom = document.getElementById('dateFrom');
    dateTo = document.getElementById('dateTo');

    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (dateFrom) dateFrom.addEventListener('change', applyFilters);
    if (dateTo) dateTo.addEventListener('change', applyFilters);

    const receiptModal = document.getElementById('receiptModal');
    if (receiptModal) {
        receiptModal.addEventListener('click', function (e) {
            if (e.target === this) closeReceiptModal();
        });
    }
});

function applyFilters() {
    const params = new URLSearchParams(window.location.search);

    if (statusFilter?.value) params.set('status', statusFilter.value);
    else params.delete('status');

    if (dateFrom?.value) params.set('date_from', dateFrom.value);
    else params.delete('date_from');

    if (dateTo?.value) params.set('date_to', dateTo.value);
    else params.delete('date_to');

    window.location.search = params.toString();
}

function openReceiptModal(imageUrl) {
    const modal = document.getElementById('receiptModal');
    const img = document.getElementById('receiptImage');

    img.src = imageUrl;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openApproveModal(paymentId, bookingId) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const label = document.getElementById('approveBookingLabel');

    form.action = `/staff/payments/${paymentId}/approve`;
    label.textContent = `#${bookingId}`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openRejectModal(paymentId, bookingId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const label = document.getElementById('rejectBookingLabel');

    form.action = `/staff/payments/${paymentId}/reject`;
    label.textContent = `#${bookingId}`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}