<?php
/**
 * Footer Include File
 * Common footer elements for all pages
 */
?>

<!-- Footer Scripts -->
<script src="assets/js/main.js"></script>

<!-- Mac-specific JavaScript optimizations -->
<script>
// Mac-specific optimizations
if (navigator.platform.indexOf('Mac') > -1) {
    // Add Mac-specific classes for styling
    document.body.classList.add('mac-platform');
    
    // Optimize for Mac trackpad scrolling
    document.addEventListener('wheel', function(e) {
        if (e.deltaMode === 0) {
            // Smooth scrolling for Mac trackpad
            e.preventDefault();
            window.scrollBy({
                top: e.deltaY,
                behavior: 'smooth'
            });
        }
    }, { passive: false });
}

// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('theme-light');
            const isLight = document.body.classList.contains('theme-light');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('theme-light');
        }
    }
});
</script>

</body>
</html>
