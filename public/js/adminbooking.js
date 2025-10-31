document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const statusSelect = form.querySelector('select[name="status_id"]');

    // Auto-submit when typing stops (after 500ms)
    let typingTimer;
    searchInput.addEventListener('keyup', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => form.submit(), 500);
    });

    // Submit immediately when status changes
    statusSelect.addEventListener('change', () => form.submit());
});