<?php
/**
 * SkillSwap Skill Details Page
 * View individual skill details and enroll
 */

$page_title = "Skill Details";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];
$skill_id = $_GET['id'] ?? 0;
$error_message = "";
$success_message = "";

// Fetch skill details
$sql = "SELECT s.*, u.Name as Instructor_Name, u.Email as Instructor_Email, u.Contact_Number as Instructor_Contact,
               (SELECT COUNT(*) FROM Enrollments e WHERE e.Skill_ID = s.Skill_ID AND e.Status = 'Active') as Current_Enrollments,
               (SELECT AVG(r.Rating) FROM Reviews r WHERE r.Skill_ID = s.Skill_ID) as Average_Rating,
               (SELECT COUNT(*) FROM Reviews r WHERE r.Skill_ID = s.Skill_ID) as Total_Reviews
        FROM Skills s 
        JOIN Users u ON s.User_ID = u.User_ID 
        WHERE s.Skill_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $skill_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("location: dashboard.php");
    exit;
}

$skill = $result->fetch_assoc();
$stmt->close();

// Check if user is already enrolled
$enrollment_check = "SELECT * FROM Enrollments WHERE Student_User_ID = ? AND Skill_ID = ?";
$stmt = $conn->prepare($enrollment_check);
$stmt->bind_param("ii", $user_id, $skill_id);
$stmt->execute();
$enrollment_result = $stmt->get_result();
$is_enrolled = $enrollment_result->num_rows > 0;
$enrollment = $is_enrolled ? $enrollment_result->fetch_assoc() : null;
$stmt->close();

// Fetch skill categories
$categories_sql = "SELECT c.Category_Name FROM Categories c 
                   JOIN SkillCategories sc ON c.Category_ID = sc.Category_ID 
                   WHERE sc.Skill_ID = ?";
$stmt = $conn->prepare($categories_sql);
$stmt->bind_param("i", $skill_id);
$stmt->execute();
$categories_result = $stmt->get_result();
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['Category_Name'];
}
$stmt->close();

// Fetch recent reviews
$reviews_sql = "SELECT r.*, u.Name as Student_Name FROM Reviews r 
                JOIN Users u ON r.Student_User_ID = u.User_ID 
                WHERE r.Skill_ID = ? ORDER BY r.Review_Date DESC LIMIT 5";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $skill_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt->close();

$conn->close();
?>

<?php include 'includes/navbar.php'; ?>

    <div class="page-wrapper">
        <div class="skill-detail-container">
            <!-- Skill Header -->
            <div class="skill-header">
                <div class="skill-info">
                    <div class="skill-badge <?php echo strtolower($skill['Skill_Type']); ?>">
                        <?php echo $skill['Skill_Type']; ?>
                    </div>
                    <h1 class="skill-title"><?php echo htmlspecialchars($skill['Skill_Name']); ?></h1>
                    <div class="skill-meta">
                        <span class="instructor">By <?php echo htmlspecialchars($skill['Instructor_Name']); ?></span>
                        <?php if ($skill['Average_Rating']): ?>
                            <div class="rating">
                                <span class="stars">
                                    <?php 
                                    $rating = round($skill['Average_Rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '★' : '☆';
                                    }
                                    ?>
                                </span>
                                <span class="rating-text"><?php echo number_format($skill['Average_Rating'], 1); ?> (<?php echo $skill['Total_Reviews']; ?> reviews)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="categories">
                        <?php foreach ($categories as $category): ?>
                            <span class="category-tag"><?php echo htmlspecialchars($category); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($skill['Skill_Type'] == 'Offering'): ?>
                    <div class="skill-pricing">
                        <div class="price-card">
                            <div class="price">₹<?php echo number_format($skill['Price'], 0); ?></div>
                            <div class="price-details">
                                <div class="detail-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12,6 12,12 16,14"></polyline>
                                    </svg>
                                    <?php echo $skill['Duration_Hours']; ?> hours
                                </div>
                                <div class="detail-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                    <?php echo $skill['Current_Enrollments']; ?>/<?php echo $skill['Max_Students']; ?> enrolled
                                </div>
                            </div>
                            
                            <?php if ($skill['User_ID'] == $user_id): ?>
                                <div class="own-skill-notice">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4"></path>
                                        <circle cx="12" cy="12" r="10"></circle>
                                    </svg>
                                    This is your skill
                                </div>
                            <?php elseif ($is_enrolled): ?>
                                <div class="enrolled-notice">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4"></path>
                                        <circle cx="12" cy="12" r="10"></circle>
                                    </svg>
                                    You are enrolled
                                </div>
                                <a href="my_courses.php" class="btn btn-secondary">View My Courses</a>
                            <?php elseif ($skill['Current_Enrollments'] >= $skill['Max_Students']): ?>
                                <div class="full-notice">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                    Course is full
                                </div>
                            <?php else: ?>
                                <a href="payment.php?skill_id=<?php echo $skill_id; ?>" class="btn btn-primary">
                                    <span>Enroll Now</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12,5 19,12 12,19"></polyline>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Skill Description -->
            <div class="skill-content">
                <div class="content-section">
                    <h2>About this skill</h2>
                    <div class="description">
                        <?php echo nl2br(htmlspecialchars($skill['Description'])); ?>
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="content-section">
                    <h2>Instructor</h2>
                    <div class="instructor-card">
                        <div class="instructor-avatar">
                            <?php echo strtoupper(substr($skill['Instructor_Name'], 0, 1)); ?>
                        </div>
                        <div class="instructor-info">
                            <h3><?php echo htmlspecialchars($skill['Instructor_Name']); ?></h3>
                            <p>Email: <?php echo htmlspecialchars($skill['Instructor_Email']); ?></p>
                            <?php if ($skill['Instructor_Contact']): ?>
                                <p>Contact: <?php echo htmlspecialchars($skill['Instructor_Contact']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <?php if (!empty($reviews)): ?>
                    <div class="content-section">
                        <h2>Reviews</h2>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <div class="reviewer-name"><?php echo htmlspecialchars($review['Student_Name']); ?></div>
                                        <div class="review-rating">
                                            <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $review['Rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                        <div class="review-date"><?php echo date('M j, Y', strtotime($review['Review_Date'])); ?></div>
                                    </div>
                                    <?php if ($review['Review_Text']): ?>
                                        <div class="review-text"><?php echo nl2br(htmlspecialchars($review['Review_Text'])); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>