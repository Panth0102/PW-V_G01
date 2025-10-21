<?php
/**
 * SkillSwap Signup Page
 * User registration page
 */

$page_title = "Sign Up";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

// Include database connection
require_once 'config/connect.php';

$name = $email = $contactNumber = $location = $password_input = $confirmPassword = "";
$nameErr = $emailErr = $passwordErr = $confirmPasswordErr = $generalErr = "";
$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = mysqli_real_escape_string($conn, $_POST["name"]);
    }

    // Validate Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }

        // Check if email already exists
        $checkEmailSql = "SELECT User_ID FROM Users WHERE Email = ?";
        $stmt = $conn->prepare($checkEmailSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $emailErr = "Email already registered";
        }
        $stmt->close();
    }

    // Validate Password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password_input = $_POST["password"];
        if (strlen($password_input) < PASSWORD_MIN_LENGTH) {
            $passwordErr = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
        }
    }

    // Validate Confirm Password
    if (empty($_POST["confirm_password"])) {
        $confirmPasswordErr = "Confirm Password is required";
    } else {
        $confirmPassword = $_POST["confirm_password"];
        if ($password_input != $confirmPassword) {
            $confirmPasswordErr = "Passwords do not match";
        }
    }

    // Get optional fields
    $contactNumber = mysqli_real_escape_string($conn, $_POST["contact_number"] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST["location"] ?? '');

    // If no errors, proceed with registration
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Store password as plain text for development/testing
        $plain_password = $password_input;

        $sql = "INSERT INTO Users (Name, Email, Contact_Number, Location, Password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $contactNumber, $location, $plain_password);

        if ($stmt->execute()) {
            $registrationSuccess = true;
            // Clear form fields on successful registration
            $name = $email = $contactNumber = $location = $password_input = $confirmPassword = "";
        } else {
            $generalErr = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-content">
                <form action="signup.php" method="post" class="auth-form" onsubmit="return validateSignupForm()">
                    <h1 class="form-title">Create your account</h1>
                    <p class="form-subtitle">Join SkillSwap and start learning</p>
                    
                    <?php if ($registrationSuccess): ?>
                        <div class="alert alert-success">Registration successful! You can now log in.</div>
                    <?php endif; ?>
                    <?php if (!empty($generalErr)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($generalErr); ?></div>
                    <?php endif; ?>

                    <input type="text" placeholder="Full Name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <?php if (!empty($nameErr)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($nameErr); ?></div>
                    <?php endif; ?>

                    <input type="email" placeholder="Email address" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if (!empty($emailErr)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($emailErr); ?></div>
                    <?php endif; ?>

                    <input type="tel" placeholder="Contact Number (Optional)" name="contact_number" value="<?php echo htmlspecialchars($contactNumber); ?>">

                    <input type="text" placeholder="Location (Optional)" name="location" value="<?php echo htmlspecialchars($location); ?>">

                    <input type="password" placeholder="Password" name="password" required>
                    <?php if (!empty($passwordErr)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($passwordErr); ?></div>
                    <?php endif; ?>

                    <input type="password" placeholder="Confirm Password" name="confirm_password" required>
                    <?php if (!empty($confirmPasswordErr)): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($confirmPasswordErr); ?></div>
                    <?php endif; ?>

                    <button type="submit">Sign Up</button>
                </form>
                <p class="signup-link">Already have an account? <a href="index.php">Log In</a></p>
            </div>
            <div class="auth-image">
                <div class="brand-showcase">
                    <div class="logo-icon"></div>
                    <h1>SkillSwap</h1>
                    <p>Join the Learning Community</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateSignupForm() {
            var name = document.forms[0]["name"].value;
            var email = document.forms[0]["email"].value;
            var password = document.forms[0]["password"].value;
            var confirmPassword = document.forms[0]["confirm_password"].value;

            if (name == "") {
                alert("Name must be filled out");
                return false;
            }

            if (email == "") {
                alert("Email must be filled out");
                return false;
            }

            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address");
                return false;
            }

            if (password == "") {
                alert("Password must be filled out");
                return false;
            }
            if (password.length < 6) {
                alert("Password must be at least 6 characters long");
                return false;
            }

            if (confirmPassword == "") {
                alert("Confirm Password must be filled out");
                return false;
            }

            if (password != confirmPassword) {
                alert("Passwords do not match");
                return false;
            }

            return true;
        }
    </script>

<?php include 'includes/footer.php'; ?>
