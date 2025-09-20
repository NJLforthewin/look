// Dashboard UI interactions
document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    document.getElementById('logoutBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../login/index.php';
        }
    });

    // Sidebar toggle
    document.getElementById('menu-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if (sidebar.classList.contains('collapsed')) {
            this.innerHTML = '<i class="fas fa-chevron-right"></i>';
            overlay.classList.remove('show');
        } else {
            this.innerHTML = '<i class="fas fa-chevron-left"></i>';
            if (window.innerWidth <= 768) {
                overlay.classList.add('show');
            }
        }
    });

    // Mobile overlay click
    document.getElementById('overlay').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const menuToggle = document.getElementById('menu-toggle');
        
        sidebar.classList.add('collapsed');
        mainContent.classList.remove('expanded');
        this.classList.remove('show');
        menuToggle.innerHTML = '<i class="fas fa-chevron-right"></i>';
    });

    // Handle resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (window.innerWidth > 768) {
            overlay.classList.remove('show');
        }
    });

    // Tab navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and tabs
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding tab
            const tabId = this.getAttribute('data-tab');
            const tabElement = document.getElementById(tabId);
            if (tabElement) {
                tabElement.classList.add('active');
            }
        });
    });
});