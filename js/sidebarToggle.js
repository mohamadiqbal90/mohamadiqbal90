document.getElementById('sidebarToggle').addEventListener('click', function() {
    var sidebar = document.getElementById('sidebar1');
    var mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('open');
});
