function toggleNotifications(event) {
    event.stopPropagation();

    const notificationDropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');

    if (!notificationDropdown) return;

    if (userDropdown) {
        userDropdown.style.display = 'none';
    }

    notificationDropdown.style.display =
        notificationDropdown.style.display === 'block' ? 'none' : 'block';
}

function toggleUserDropdown(event) {
    event.stopPropagation();

    const userDropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');

    if (!userDropdown) return;

    if (notificationDropdown) {
        notificationDropdown.style.display = 'none';
    }

    userDropdown.style.display =
        userDropdown.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function (event) {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBell = document.getElementById('notificationBell');

    const userDropdown = document.getElementById('userDropdown');
    const userDropdownButton = document.getElementById('userDropdownButton');

    if (
        notificationDropdown &&
        notificationBell &&
        !notificationDropdown.contains(event.target) &&
        !notificationBell.contains(event.target)
    ) {
        notificationDropdown.style.display = 'none';
    }

    if (
        userDropdown &&
        userDropdownButton &&
        !userDropdown.contains(event.target) &&
        !userDropdownButton.contains(event.target)
    ) {
        userDropdown.style.display = 'none';
    }
});