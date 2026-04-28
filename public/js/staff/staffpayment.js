let statusFilter;
let paymentTypeFilter;
let dateFrom;
let dateTo;

document.addEventListener('DOMContentLoaded', function () {
    statusFilter = document.getElementById('statusFilter');
    paymentTypeFilter = document.getElementById('paymentTypeFilter');
    dateFrom = document.getElementById('dateFrom');
    dateTo = document.getElementById('dateTo');

    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (paymentTypeFilter) paymentTypeFilter.addEventListener('change', applyFilters);
    if (dateFrom) dateFrom.addEventListener('change', applyFilters);
    if (dateTo) dateTo.addEventListener('change', applyFilters);

    const receiptModal = document.getElementById('receiptModal');
    if (receiptModal) {
        receiptModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeReceiptModal();
            }
        });
    }

    const approveModal = document.getElementById('approveModal');
    if (approveModal) {
        approveModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });
    }

    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeReceiptModal();
            closeApproveModal();
            closeRejectModal();
        }
    });
});

function paymentTypeLabel(type) {
    return type === 'issue' ? 'Issue Payment' : 'Booking Payment';
}

function paymentActionText(type) {
    return type === 'issue' ? 'issue payment' : 'booking payment';
}

function applyFilters() {
    const params = new URLSearchParams(window.location.search);

    if (statusFilter && statusFilter.value) {
        params.set('status', statusFilter.value);
    } else {
        params.delete('status');
    }

    if (paymentTypeFilter && paymentTypeFilter.value) {
        params.set('payment_type', paymentTypeFilter.value);
    } else {
        params.delete('payment_type');
    }

    if (dateFrom && dateFrom.value) {
        params.set('date_from', dateFrom.value);
    } else {
        params.delete('date_from');
    }

    if (dateTo && dateTo.value) {
        params.set('date_to', dateTo.value);
    } else {
        params.delete('date_to');
    }

    window.location.search = params.toString();
}

function openReceiptModal(imageUrl) {
    if (!imageUrl) {
        alert('No receipt uploaded.');
        return;
    }

    const modal = document.getElementById('receiptModal');
    const img = document.getElementById('receiptImage');

    if (img) {
        img.src = imageUrl;
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    const img = document.getElementById('receiptImage');

    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (img) {
        img.src = '';
    }
}

function openApproveModal(paymentId, bookingId = '', paymentType = 'booking', issueTitle = '', approveUrl = '') {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const bookingLabel = document.getElementById('approveBookingLabel');

    const title = document.getElementById('approveModalTitle');
    const subtitle = document.getElementById('approveModalSubtitle');
    const typeBadge = document.getElementById('approveTypeLabel');
    const typeHint = document.getElementById('approveTypeHint');
    const issueText = document.getElementById('approveIssueText');

    if (form) {
        form.action = approveUrl || `/staff/payments/${paymentId}/approve`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (title) {
        title.textContent = `Approve ${paymentTypeLabel(paymentType)}`;
    }

    if (subtitle) {
        subtitle.textContent = `Are you sure you want to approve this ${paymentActionText(paymentType)}?`;
    }

    if (typeBadge) {
        typeBadge.textContent = paymentTypeLabel(paymentType);
        typeBadge.className = paymentType === 'issue'
            ? 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800'
            : 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
    }

    if (typeHint) {
        typeHint.textContent = paymentType === 'issue'
            ? 'This will mark the payment as completed and resolve the related return issue.'
            : 'This will mark the payment as completed and confirm the booking.';
    }

    if (issueText) {
        if (paymentType === 'issue' && issueTitle) {
            issueText.textContent = `Issue: ${issueTitle}`;
            issueText.classList.remove('hidden');
        } else {
            issueText.textContent = '';
            issueText.classList.add('hidden');
        }
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const bookingLabel = document.getElementById('approveBookingLabel');
    const issueText = document.getElementById('approveIssueText');

    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (form) {
        form.action = '';
    }

    if (bookingLabel) {
        bookingLabel.textContent = '';
    }

    if (issueText) {
        issueText.textContent = '';
        issueText.classList.add('hidden');
    }
}

function openRejectModal(paymentId, bookingId = '', paymentType = 'booking', issueTitle = '', rejectUrl = '') {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const bookingLabel = document.getElementById('rejectBookingLabel');

    const title = document.getElementById('rejectModalTitle');
    const subtitle = document.getElementById('rejectModalSubtitle');
    const typeBadge = document.getElementById('rejectTypeLabel');
    const typeHint = document.getElementById('rejectTypeHint');
    const issueText = document.getElementById('rejectIssueText');

    if (form) {
        form.action = rejectUrl || `/staff/payments/${paymentId}/reject`;
    }

    if (bookingLabel) {
        bookingLabel.textContent = bookingId ? `#${bookingId}` : '';
    }

    if (title) {
        title.textContent = `Reject ${paymentTypeLabel(paymentType)}`;
    }

    if (subtitle) {
        subtitle.textContent = `Are you sure you want to reject this ${paymentActionText(paymentType)}?`;
    }

    if (typeBadge) {
        typeBadge.textContent = paymentTypeLabel(paymentType);
        typeBadge.className = paymentType === 'issue'
            ? 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800'
            : 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800';
    }

    if (typeHint) {
        typeHint.textContent = paymentType === 'issue'
            ? 'Please provide the reason for rejecting this issue payment before continuing.'
            : 'Please provide the reason for rejecting this booking payment before continuing.';
    }

    if (issueText) {
        if (paymentType === 'issue' && issueTitle) {
            issueText.textContent = `Issue: ${issueTitle}`;
            issueText.classList.remove('hidden');
        } else {
            issueText.textContent = '';
            issueText.classList.add('hidden');
        }
    }

    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const bookingLabel = document.getElementById('rejectBookingLabel');
    const issueText = document.getElementById('rejectIssueText');

    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (form) {
        form.reset();
        form.action = '';
    }

    if (bookingLabel) {
        bookingLabel.textContent = '';
    }

    if (issueText) {
        issueText.textContent = '';
        issueText.classList.add('hidden');
    }
}