            </section>
        </div> <!-- /container-fluid -->
    </main>

    <footer class="footer text-center text-white py-3">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> 
                <a href="./" class="text-white"><?= $faucetName; ?></a>. 
                All Rights Reserved. Version: <?= $core->getVersion(); ?><br>
                Powered by <a href="https://coolscript.hu" class="text-white">CoolScript</a>
            </p>
            <p>Current server time: <?= date('d-m-Y H:i'); ?></p>
        </div>
    </footer>

    <!-- Bootstrap & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
            sidebar.classList.toggle('active');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
        }

        function closeSidebar() {
            if (window.innerWidth < 992) {
                document.body.classList.remove('sidebar-open');
                sidebar.classList.remove('active');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebar();
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) closeSidebar();
            });
        });

        let resizeTimer;
        window.addEventListener('resize', function() {
            document.body.classList.add('resize-animation-stopper');
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                document.body.classList.remove('resize-animation-stopper');
                if (window.innerWidth >= 992) {
                    document.body.classList.remove('sidebar-open');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                    if (sidebar) sidebar.classList.remove('active');
                }
            }, 100);
        });
    });
    </script>

</body>
</html>


