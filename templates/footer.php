</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const darkModeToggle = document.getElementById('dark_mode_toggle');
        const body = document.body;

        darkModeToggle.addEventListener('change', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('dark-mode', 'enabled');
            } else {
                localStorage.setItem('dark-mode', 'disabled');
            }
        });

        if (localStorage.getItem('dark-mode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }
    </script>
</body>
</html>
