<?php
/**
 * Footer Include File
 * Common footer elements for all pages
 */
?>

<!-- Main Footer -->
<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-brand">
                    <h3>SkillSwap</h3>
                    <p>Connecting learners and experts worldwide through skill exchange.</p>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="dashboard.php">Browse Skills</a></li>
                    <li><a href="offer_skill.php">Offer a Skill</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="my_courses.php">My Courses</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Categories</h4>
                <ul class="footer-links">
                    <li><a href="dashboard.php?category=1">Technology</a></li>
                    <li><a href="dashboard.php?category=2">Design</a></li>
                    <li><a href="dashboard.php?category=3">Business</a></li>
                    <li><a href="dashboard.php?category=4">Languages</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Community</h4>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#help">Help Center</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#privacy">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; <?php echo date('Y'); ?> SkillSwap. All rights reserved.</p>
                <div class="footer-stats">
                    <?php 
                    // Show skills count if available from the page, otherwise show static message
                    if (isset($total_skills) && is_numeric($total_skills)) {
                        echo '<span>' . number_format($total_skills) . ' Skills Available</span>';
                    } else {
                        echo '<span>Join Our Learning Community</span>';
                    }
                    ?>
                    <span>Built with ❤️ for learners</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Footer Scripts -->
<script src="assets/js/main.js"></script>
<script src="navbar.js"></script>

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
