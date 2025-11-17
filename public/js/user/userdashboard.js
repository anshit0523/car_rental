document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const menuLabels = document.querySelectorAll('.menu-label');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('w-56');
        sidebar.classList.toggle('w-20');
        menuLabels.forEach(label => label.classList.toggle('hidden'));
    });


});
