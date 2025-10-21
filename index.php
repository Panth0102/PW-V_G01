<?php
/**
 * SkillSwap Login Page
 * Main entry point for user authentication
 */

$page_title = "Login";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

// Include database connection
require_once 'config/connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_input = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';

    if (!empty($username_input) && !empty($password_input)) {
        // Prevent SQL injection
        $username_input = mysqli_real_escape_string($conn, $username_input);
        
        // Get user by email
        $sql = "SELECT User_ID, Name, Password FROM Users WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['Password'];
            
            // Check password (plain text comparison for development)
            if ($password_input === $stored_password) {
                // Login successful
                $_SESSION['user_id'] = $row['User_ID'];
                $_SESSION['user_name'] = $row['Name'];
                $_SESSION['login_time'] = time();
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}

$conn->close();
?>

    <div class="container">
        <!-- Navigation Bar -->
        <nav class="main-nav">
            <div class="nav-brand">
                <div class="logo-small"></div>
                <span class="brand-text">SkillSwap</span>
            </div>
            <div class="nav-actions">
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                    <svg class="theme-icon dark-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <svg class="theme-icon light-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Side - Login Form -->
            <div class="login-section">
                <div class="login-container">
                    <div class="login-header">
                        <h1 class="login-title">Welcome back</h1>
                        <p class="login-subtitle">Sign in to your SkillSwap account</p>
                    </div>

                    <form action="index.php" method="post" class="login-form" onsubmit="return validateForm()">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-error">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="email" class="form-label">Email address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input type="email" id="email" name="username" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <circle cx="12" cy="16" r="1"></circle>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="remember">
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                            <a href="#" class="forgot-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="login-btn">
                            <span>Sign In</span>
                            <svg class="btn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12,5 19,12 12,19"></polyline>
                            </svg>
                        </button>
                    </form>

                    <div class="divider">
                        <span>or</span>
                    </div>

                    <div class="signup-prompt">
                        <p>Don't have an account? <a href="signup.php" class="signup-link">Create one now</a></p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <div class="hero-badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"></path>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"></path>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"></path>
                            <path d="M12 3c0 1-1 3-3 3s-3-2-3-3 1-3 3-3 3 2 3 3"></path>
                            <path d="M12 21c0-1 1-3 3-3s3 2 3 3-1 3-3 3-3-2-3-3"></path>
                        </svg>
                        Trusted by 10,000+ learners
                    </div>
                    
                    <h1 class="hero-title">
                        Master new skills through
                        <span class="gradient-text">peer learning</span>
                    </h1>
                    
                    <p class="hero-description">
                        Connect with experts and learners worldwide. Share your knowledge, 
                        learn new skills, and grow together in our vibrant community.
                    </p>

                    <div class="hero-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </div>
                            <div class="feature-text">
                                <h3>Expert Mentors</h3>
                                <p>Learn from industry professionals</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="feature-text">
                                <h3>Global Community</h3>
                                <p>Connect with learners worldwide</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                            </div>
                            <div class="feature-text">
                                <h3>Skill Exchange</h3>
                                <p>Trade knowledge and expertise</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="floating-card card-1">
                        <div class="card-content">
                            <div class="card-avatar"></div>
                            <div class="card-info">
                                <h4>Sarah Chen</h4>
                                <p>Teaching Python</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="floating-card card-2">
                        <div class="card-content">
                            <div class="card-avatar"></div>
                            <div class="card-info">
                                <h4>Mike Johnson</h4>
                                <p>Learning Design</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="floating-card card-3">
                        <div class="card-content">
                            <div class="card-avatar"></div>
                            <div class="card-info">
                                <h4>Emma Davis</h4>
                                <p>Teaching Music</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        var username = document.forms[0]["username"].value;
        var password = document.forms[0]["password"].value;
        if (username == "") {
            alert("Email must be filled out");
            return false;
        }
        if (password == "") {
            alert("Password must be filled out");
            return false;
        }
        return true;
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.querySelector('.eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><path d="M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }

    // Add floating animation to cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.floating-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.5}s`;
        });
    });
    </script>

<?php include 'includes/footer.php'; ?>