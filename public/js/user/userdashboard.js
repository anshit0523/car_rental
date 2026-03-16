

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!toggleBtn || !sidebar) return;

    // Toggle sidebar visibility
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('-translate-x-full');
        overlay?.classList.toggle('hidden');
    });

    // Close sidebar when clicking overlay
    overlay?.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Close sidebar when clicking a link
    const navLinks = sidebar.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) { // lg breakpoint
                sidebar.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            sidebar.classList.remove('-translate-x-full');
            overlay?.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Prevent body scroll when sidebar is open on mobile
    const preventScroll = function(e) {
        if (!sidebar.classList.contains('-translate-x-full')) {
            e.preventDefault();
        }
    };

    document.body.addEventListener('touchmove', preventScroll, false);

    document.addEventListener("click", function (e) {

        const dropdown = document.getElementById("notificationDropdown");
        const bell = document.getElementById("notificationBell");

        if (!dropdown || !bell) return;

        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.classList.add("hidden");
        }

    });

    function loadNotifications()
{
    fetch('/user/notifications/latest')
    .then(res => res.json())
    .then(data => {

        const dropdown = document.querySelector("#notificationDropdown .overflow-y-auto");
        const badge = document.querySelector("#notificationBell span");

        // update badge
        if(data.unread > 0)
        {
            if(!badge)
            {
                const newBadge = document.createElement("span");
                newBadge.className = "absolute top-1 right-1 w-3 h-3 bg-red-500 rounded-full";
                document.getElementById("notificationBell").appendChild(newBadge);
            }
        }

        dropdown.innerHTML = "";

        if(data.notifications.length === 0)
        {
            dropdown.innerHTML = `<div class="p-4 text-sm text-gray-500">No notifications</div>`;
            return;
        }

        data.notifications.forEach(n => {

            dropdown.innerHTML += `
                <a href="${n.link ?? '#'}"
                   class="block px-4 py-3 hover:bg-gray-50 border-b">

                    <p class="text-sm font-medium text-gray-900">
                        ${n.title}
                    </p>

                    <p class="text-xs text-gray-500">
                        ${n.message}
                    </p>

                </a>
            `;
        });

    });
}

// refresh every 10 seconds
setInterval(loadNotifications, 10000);
});

function toggleNotifications() {

    const dropdown = document.getElementById("notificationDropdown");

    if (!dropdown) return;

    dropdown.classList.toggle("hidden");}