document.addEventListener('DOMContentLoaded', function() {

// ========== FUNCTION: COMBINED FILTER ==========
function filterTable() {
    let searchValue = document.getElementById('userSearch').value.toLowerCase();
    let roleValue = document.getElementById('roleFilter').value.toLowerCase();

    document.querySelectorAll("table tbody tr").forEach(row => {

        let name = row.cells[0].innerText.toLowerCase();
        let email = row.cells[1].innerText.toLowerCase();

        // Read role text (Admin/User)
        let roleBadge = row.cells[2].innerText.toLowerCase();

        let matchesSearch =
            name.includes(searchValue) ||
            email.includes(searchValue);

        let matchesRole =
            roleValue === "" || roleBadge.includes(roleValue);

        row.style.display = (matchesSearch && matchesRole) ? "" : "none";
    });
}

document.getElementById('userSearch').addEventListener('keyup', filterTable);
document.getElementById('roleFilter').addEventListener('change', filterTable);

 window.openAddModal = function() {
        document.getElementById('modalTitle').innerText = 'Add User';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('formMethod').value = 'POST';

        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('role').value = '';

        showModal();
    }

    window.openEditModal = function(user) {
        document.getElementById('modalTitle').innerText = 'Edit User';
        document.getElementById('userForm').action = `/admin/users/${user.id}`;
        document.getElementById('formMethod').value = 'PUT';

        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('password').value = '';
        document.getElementById('role').value = user.role_id;

        showModal();
    }

    // ========== MODAL SHOW WITH ANIMATION ==========
    function showModal() {
        const modal = document.getElementById('userModal');
        const modalContent = document.getElementById('modalContent');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-90');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    // ========== MODAL CLOSE WITH ANIMATION ==========
    window.closeModal = function() {
        const modal = document.getElementById('userModal');
        const modalContent = document.getElementById('modalContent');

        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-90');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }


    // ========== SUCCESS MODAL POPUP ==========
window.showSuccess = function (message) {
    const modal = document.getElementById('successModal');
    const content = document.getElementById('successContent');
    const msg = document.getElementById('successMessage');

    msg.innerText = message;

    modal.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-90');
        content.classList.add('scale-100');
    }, 10);

    // Auto close after 2 seconds
    setTimeout(() => {
        closeSuccess();
    }, 2500);
};

window.closeSuccess = function () {
    const modal = document.getElementById('successModal');
    const content = document.getElementById('successContent');

    modal.classList.add('opacity-0');
    content.classList.add('scale-90');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

 // ========== Delete MODAL POPUP ==========

window.openDeleteModal = function(id) {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteContent');

    document.getElementById('deleteForm').action = `/admin/users/${id}`;

    modal.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-90');
        content.classList.add('scale-100');
    }, 10);
};
window.closeDeleteModal = function() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteContent');

    modal.classList.add('opacity-0');
    content.classList.add('scale-90');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};

window.showError = function(message) {
    const modal = document.getElementById('errorModal');
    const content = document.getElementById('errorContent');
    const msg = document.getElementById('errorMessage');

    msg.innerText = message;

    modal.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-90');
        content.classList.add('scale-100');
    }, 10);

    setTimeout(() => {
        closeError();
    }, 2500);
};

window.closeError = function() {
    const modal = document.getElementById('errorModal');
    const content = document.getElementById('errorContent');

    modal.classList.add('opacity-0');
    content.classList.add('scale-90');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
};


});