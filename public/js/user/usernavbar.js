document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const dropdown = document.getElementById('notificationDropdown');
    const bell = document.getElementById('notificationBell');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('-translate-x-full');
            overlay?.classList.toggle('hidden');
        });

        overlay?.addEventListener('click', function () {
            sidebar.classList.add('-translate-x-full');
            overlay?.classList.add('hidden');
        });
    }

    document.addEventListener('click', function (e) {
        if (!dropdown || !bell) return;

        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            closeNotificationsDropdown();
        }
    });

    if (dropdown && bell) {
        loadNotifications();
        setInterval(loadNotifications, 10000);
    }
});

function toggleNotifications(event) {
    if (event) event.stopPropagation();

    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;

    const isHidden =
        dropdown.classList.contains('hidden') || dropdown.style.display === 'none';

    if (isHidden) {
        dropdown.classList.remove('hidden');
        dropdown.style.display = 'block';
    } else {
        closeNotificationsDropdown();
    }
}

function closeNotificationsDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;

    dropdown.classList.add('hidden');
    dropdown.style.display = 'none';
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function loadNotifications() {
    fetch('/user/notifications/latest', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        const dropdownList = document.querySelector('#notificationDropdown .notification-list');
        const bell = document.getElementById('notificationBell');
        let badge = document.getElementById('notificationBadge');

        if (!dropdownList || !bell) return;

        if (data.unread > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notificationBadge';
                badge.style.position = 'absolute';
                badge.style.top = '8px';
                badge.style.right = '8px';
                badge.style.minWidth = '10px';
                badge.style.height = '10px';
                badge.style.background = '#ef4444';
                badge.style.borderRadius = '9999px';
                bell.appendChild(badge);
            }
        } else if (badge) {
            badge.remove();
        }

        dropdownList.innerHTML = '';

        if (!data.notifications || data.notifications.length === 0) {
            dropdownList.innerHTML = `
                <div style="padding:16px 18px; font-size:13px; color:#777;">
                    No notifications
                </div>
            `;
            return;
        }

        data.notifications.forEach(n => {
            const isUnread = !n.is_read;

            dropdownList.innerHTML += `
                <a href="${escapeHtml(n.read_url)}"
                   style="display:block; padding:14px 18px; border-bottom:1px solid #f5f5f5; text-decoration:none; background:${isUnread ? '#f8faff' : '#fff'};">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:6px;">
                        <p style="margin:0; font-size:14px; font-weight:600; color:#111; line-height:1.4;">
                            ${escapeHtml(n.title)}
                        </p>
                        ${isUnread ? `
                            <span style="flex-shrink:0; font-size:10px; font-weight:700; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:9999px; padding:4px 7px; line-height:1;">
                                New
                            </span>
                        ` : ''}
                    </div>

                    <p style="margin:0 0 6px; font-size:12px; color:#666; line-height:1.5;">
                        ${escapeHtml(n.message)}
                    </p>

                    <p style="margin:0; font-size:11px; color:#aaa;">
                        ${escapeHtml(n.time)}
                    </p>
                </a>
            `;
        });
    })
    .catch(error => {
        console.error('Failed to load notifications:', error);
    });
}