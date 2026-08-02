<script>
    (function () {
        var stored = localStorage.getItem('theme');
        var isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', isDark);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (!localStorage.getItem('theme')) {
                document.documentElement.classList.toggle('dark', e.matches);
            }
        });

        window.toggleTheme = function () {
            var dark = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark', dark);
            localStorage.setItem('theme', dark ? 'dark' : 'light');
        };
    })();
</script>
