<?php
/**
 * SkillSwap Offer Skill Page
 * Page for users to offer or seek skills
 */

$page_title = "Offer/Seek Skill";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];
$skillName = $skillType = $description = $price = $duration = $maxStudents = "";
$categories = [];
$skillNameErr = $skillTypeErr = $descriptionErr = $categoriesErr = $priceErr = $durationErr = $maxStudentsErr = $generalErr = "";
$successMessage = "";

// Fetch categories for the dropdown
$categories_sql = "SELECT Category_ID, Category_Name FROM Categories ORDER BY Category_Name";
$categories_result = $conn->query($categories_sql);

$availableCategories = [];
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $availableCategories[$row['Category_ID']] = $row['Category_Name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Skill Name
    if (empty($_POST["skill_name"])) {
        $skillNameErr = "Skill Name is required";
    } else {
        $skillName = mysqli_real_escape_string($conn, $_POST["skill_name"]);
    }

    // Validate Skill Type
    if (empty($_POST["skill_type"])) {
        $skillTypeErr = "Skill Type is required";
    } else {
        $skillType = mysqli_real_escape_string($conn, $_POST["skill_type"]);
        if ($skillType != 'Offering' && $skillType != 'Seeking') {
            $skillTypeErr = "Invalid Skill Type";
        }
    }

    // Validate Description
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    // Validate Price (only for Offering skills)
    if ($skillType == 'Offering') {
        if (empty($_POST["price"])) {
            $priceErr = "Price is required for offered skills";
        } else {
            $price = floatval($_POST["price"]);
            if ($price < 0) {
                $priceErr = "Price must be a positive number";
            }
        }

        // Validate Duration
        if (empty($_POST["duration"])) {
            $durationErr = "Duration is required for offered skills";
        } else {
            $duration = intval($_POST["duration"]);
            if ($duration < 1) {
                $durationErr = "Duration must be at least 1 hour";
            }
        }

        // Validate Max Students
        if (empty($_POST["max_students"])) {
            $maxStudentsErr = "Maximum students is required for offered skills";
        } else {
            $maxStudents = intval($_POST["max_students"]);
            if ($maxStudents < 1) {
                $maxStudentsErr = "Maximum students must be at least 1";
            }
        }
    }

    // Validate Categories
    if (empty($_POST["categories"])) {
        $categoriesErr = "At least one category is required";
    } else {
        $selectedCategories = $_POST["categories"];
        foreach ($selectedCategories as $cat_id) {
            if (!array_key_exists($cat_id, $availableCategories)) {
                $categoriesErr = "Invalid category selected";
                break;
            }
        }
        $categories = $selectedCategories;
    }

    // If no errors, proceed with inserting the skill
    if (empty($skillNameErr) && empty($skillTypeErr) && empty($descriptionErr) && empty($categoriesErr) && empty($priceErr) && empty($durationErr) && empty($maxStudentsErr)) {
        if ($skillType == 'Offering') {
            $insertSkillSql = "INSERT INTO Skills (User_ID, Skill_Name, Skill_Type, Description, Price, Duration_Hours, Max_Students) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($insertSkillSql)) {
                $stmt->bind_param("isssdii", $user_id, $skillName, $skillType, $description, $price, $duration, $maxStudents);
            } else {
                $generalErr = "Error preparing skill insertion statement: " . $conn->error;
            }
        } else {
            $insertSkillSql = "INSERT INTO Skills (User_ID, Skill_Name, Skill_Type, Description) VALUES (?, ?, ?, ?)";
            if ($stmt = $conn->prepare($insertSkillSql)) {
                $stmt->bind_param("isss", $user_id, $skillName, $skillType, $description);
            } else {
                $generalErr = "Error preparing skill insertion statement: " . $conn->error;
            }
        }
        
        if (isset($stmt) && $stmt->execute()) {
            $new_skill_id = $conn->insert_id;
            $stmt->close();

            // Insert into SkillCategories
            $insertSkillCatSql = "INSERT INTO SkillCategories (Skill_ID, Category_ID) VALUES (?, ?)";
            if ($stmtCat = $conn->prepare($insertSkillCatSql)) {
                foreach ($categories as $cat_id) {
                    $stmtCat->bind_param("ii", $new_skill_id, $cat_id);
                    $stmtCat->execute();
                }
                $stmtCat->close();
                $successMessage = "Skill posted successfully!";
                // Clear form fields
                $skillName = $skillType = $description = $price = $duration = $maxStudents = "";
                $categories = [];
            } else {
                $generalErr = "Error preparing skill category statement: " . $conn->error;
            }
        } else {
            if (isset($stmt)) {
                $generalErr = "Error executing skill insertion: " . $stmt->error;
            }
        }
    }
}

$conn->close();
?>

    <div class="navbar">
        <div class="nav-left">
            <span class="brand-title">SkillSwap</span>
            <a href="dashboard.php">Home</a>
            <a href="offer_skill.php">Offer/Seek Skill</a>
            <a href="messages.php">Messages</a>
        </div>
        <div class="nav-right">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Offer or Seek a Skill</h1>
            <p>Share your expertise or find someone to learn from</p>
        </div>

        <div class="form-container">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($generalErr)): ?>
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <?php echo htmlspecialchars($generalErr); ?>
                </div>
            <?php endif; ?>
            
            <form action="offer_skill.php" method="post" class="skill-form">
                <div class="form-group">
                    <label for="skill_name" class="form-label">Skill Name</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                        <input type="text" id="skill_name" name="skill_name" placeholder="e.g., Web Development, Cooking, Photography" value="<?php echo htmlspecialchars($skillName); ?>" required>
                    </div>
                    <?php if (!empty($skillNameErr)): ?>
                        <div class="field-error"><?php echo htmlspecialchars($skillNameErr); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="skill_type" class="form-label">Skill Type</label>
                    <div class="select-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"></path>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"></path>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"></path>
                        </svg>
                        <select id="skill_type" name="skill_type" required>
                            <option value="">Select Type</option>
                            <option value="Offering" <?php echo ($skillType == 'Offering') ? 'selected' : ''; ?>>I'm Offering This Skill</option>
                            <option value="Seeking" <?php echo ($skillType == 'Seeking') ? 'selected' : ''; ?>>I'm Seeking This Skill</option>
                        </select>
                    </div>
                    <?php if (!empty($skillTypeErr)): ?>
                        <div class="field-error"><?php echo htmlspecialchars($skillTypeErr); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <div class="textarea-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                        <textarea id="description" name="description" rows="5" placeholder="Describe your skill, experience level, what you can teach or what you want to learn..."><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <?php if (!empty($descriptionErr)): ?>
                        <div class="field-error"><?php echo htmlspecialchars($descriptionErr); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Pricing fields - only show for Offering skills -->
                <div id="pricing-fields" style="display: none;">
                    <div class="form-group">
                        <label for="price" class="form-label">Price (₹ INR)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1v22"></path>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <input type="number" id="price" name="price" placeholder="e.g., 1999" min="0" step="0.01" value="<?php echo htmlspecialchars($price); ?>">
                        </div>
                        <?php if (!empty($priceErr)): ?>
                            <div class="field-error"><?php echo htmlspecialchars($priceErr); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">Course Duration (Hours)</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12,6 12,12 16,14"></polyline>
                            </svg>
                            <input type="number" id="duration" name="duration" placeholder="e.g., 20" min="1" value="<?php echo htmlspecialchars($duration); ?>">
                        </div>
                        <?php if (!empty($durationErr)): ?>
                            <div class="field-error"><?php echo htmlspecialchars($durationErr); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="max_students" class="form-label">Maximum Students</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <input type="number" id="max_students" name="max_students" placeholder="e.g., 10" min="1" value="<?php echo htmlspecialchars($maxStudents); ?>">
                        </div>
                        <?php if (!empty($maxStudentsErr)): ?>
                            <div class="field-error"><?php echo htmlspecialchars($maxStudentsErr); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="categories" class="form-label">Categories</label>
                    <div class="categories-wrapper">
                        <div class="categories-grid">
                            <?php foreach ($availableCategories as $cat_id => $cat_name): ?>
                                <label class="category-item">
                                    <input type="checkbox" name="categories[]" value="<?php echo $cat_id; ?>" <?php echo (in_array($cat_id, $categories)) ? 'checked' : ''; ?>>
                                    <span class="category-checkbox"></span>
                                    <span class="category-name"><?php echo htmlspecialchars($cat_name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (!empty($categoriesErr)): ?>
                        <div class="field-error"><?php echo htmlspecialchars($categoriesErr); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-btn">
                    <span>Post Skill</span>
                    <svg class="btn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12,5 19,12 12,19"></polyline>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <script>
    // Show/hide pricing fields based on skill type
    document.addEventListener('DOMContentLoaded', function() {
        const skillTypeSelect = document.getElementById('skill_type');
        const pricingFields = document.getElementById('pricing-fields');
        const priceInput = document.getElementById('price');
        const durationInput = document.getElementById('duration');
        const maxStudentsInput = document.getElementById('max_students');

        function togglePricingFields() {
            if (skillTypeSelect.value === 'Offering') {
                pricingFields.style.display = 'block';
                priceInput.required = true;
                durationInput.required = true;
                maxStudentsInput.required = true;
            } else {
                pricingFields.style.display = 'none';
                priceInput.required = false;
                durationInput.required = false;
                maxStudentsInput.required = false;
                // Clear values when hiding
                priceInput.value = '';
                durationInput.value = '';
                maxStudentsInput.value = '';
            }
        }

        skillTypeSelect.addEventListener('change', togglePricingFields);
        
        // Check initial state
        togglePricingFields();
    });
    </script>

<?php include 'includes/footer.php'; ?>
