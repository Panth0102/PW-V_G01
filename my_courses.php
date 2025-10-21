<?php
/**
 * SkillSwap My Courses Page
 * Display user's enrolled courses and teaching courses
 */

$page_title = "My Courses";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];

// Fetch enrolled courses (as student)
$enrolled_sql = "SELECT e.*, s.Skill_Name, s.Duration_Hours, s.Price, u.Name as Instructor_Name, 
                        p.Payment_Date, p.Amount, p.Transaction_ID
                 FROM Enrollments e
                 JOIN Skills s ON e.Skill_ID = s.Skill_ID
                 JOIN Users u ON s.User_ID = u.User_ID
                 LEFT JOIN Payments p ON e.Payment_ID = p.Payment_ID
                 WHERE e.Student_User_ID = ?
                 ORDER BY e.Enrollment_Date DESC";

$stmt = $conn->prepare($enrolled_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$enrolled_result = $stmt->get_result();
$enrolled_courses = [];
while ($row = $enrolled_result->fetch_assoc()) {
    $enrolled_courses[] = $row;
}
$stmt->close();

// Fetch teaching courses (as instructor)
$teaching_sql = "SELECT s.*, 
                        (SELECT COUNT(*) FROM Enrollments e WHERE e.Skill_ID = s.Skill_ID AND e.Status = 'Active') as Current_Enrollments,
                        (SELECT SUM(p.Amount) FROM Payments p WHERE p.Skill_ID = s.Skill_ID AND p.Payment_Status = 'Completed') as Total_Earnings
                 FROM Skills s
                 WHERE s.User_ID = ? AND s.Skill_Type = 'Offering'
                 ORDER BY s.Date_Posted DESC";

$stmt = $conn->prepare($teaching_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$teaching_result = $stmt->get_result();
$teaching_courses = [];
while ($row = $teaching_result->fetch_assoc()) {
    $teaching_courses[] = $row;
}
$stmt->close();

$conn->close();
?>

    <div class="navbar">
        <div class="nav-left">
            <span class="brand-title">SkillSwap</span>
            <a href="dashboard.php">Home</a>
            <a href="offer_skill.php">Offer/Seek Skill</a>
            <a href="messages.php">Messages</a>
            <a href="my_courses.php" class="active">My Courses</a>
        </div>
        <div class="nav-right">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="courses-container">
            <div class="page-header">
                <h1>My Courses</h1>
                <p>Manage your learning journey and teaching activities</p>
            </div>

            <!-- Course Tabs -->
            <div class="course-tabs">
                <button class="tab-btn active" onclick="showTab('enrolled')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    Enrolled Courses (<?php echo count($enrolled_courses); ?>)
                </button>
                <button class="tab-btn" onclick="showTab('teaching')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    Teaching Courses (<?php echo count($teaching_courses); ?>)
                </button>
            </div>

            <!-- Enrolled Courses Tab -->
            <div id="enrolled-tab" class="tab-content active">
                <?php if (empty($enrolled_courses)): ?>
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <h2>No Enrolled Courses</h2>
                        <p>You haven't enrolled in any courses yet. Browse available skills to start learning!</p>
                        <a href="dashboard.php" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php else: ?>
                    <div class="courses-grid">
                        <?php foreach ($enrolled_courses as $course): ?>
                            <div class="course-card enrolled">
                                <div class="course-header">
                                    <h3><?php echo htmlspecialchars($course['Skill_Name']); ?></h3>
                                    <div class="course-status <?php echo strtolower($course['Status']); ?>">
                                        <?php echo $course['Status']; ?>
                                    </div>
                                </div>
                                
                                <div class="course-info">
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span>Instructor: <?php echo htmlspecialchars($course['Instructor_Name']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12,6 12,12 16,14"></polyline>
                                        </svg>
                                        <span><?php echo $course['Duration_Hours']; ?> hours</span>
                                    </div>
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 1v22"></path>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        <span>Paid: ₹<?php echo number_format($course['Amount'], 0); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span>Enrolled: <?php echo date('M j, Y', strtotime($course['Enrollment_Date'])); ?></span>
                                    </div>
                                </div>

                                <div class="progress-section">
                                    <div class="progress-label">
                                        <span>Progress</span>
                                        <span><?php echo $course['Progress']; ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $course['Progress']; ?>%"></div>
                                    </div>
                                </div>

                                <div class="course-actions">
                                    <a href="view_skill.php?id=<?php echo $course['Skill_ID']; ?>" class="btn btn-secondary">View Details</a>
                                    <?php if ($course['Status'] == 'Active'): ?>
                                        <button class="btn btn-primary" onclick="updateProgress(<?php echo $course['Enrollment_ID']; ?>)">Update Progress</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Teaching Courses Tab -->
            <div id="teaching-tab" class="tab-content">
                <?php if (empty($teaching_courses)): ?>
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                        <h2>No Teaching Courses</h2>
                        <p>You haven't created any courses yet. Share your skills and start earning!</p>
                        <a href="offer_skill.php" class="btn btn-primary">Create Course</a>
                    </div>
                <?php else: ?>
                    <div class="courses-grid">
                        <?php foreach ($teaching_courses as $course): ?>
                            <div class="course-card teaching">
                                <div class="course-header">
                                    <h3><?php echo htmlspecialchars($course['Skill_Name']); ?></h3>
                                    <div class="course-price">₹<?php echo number_format($course['Price'], 0); ?></div>
                                </div>
                                
                                <div class="course-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $course['Current_Enrollments']; ?>/<?php echo $course['Max_Students']; ?></div>
                                        <div class="stat-label">Students</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">₹<?php echo number_format($course['Total_Earnings'] ?? 0, 0); ?></div>
                                        <div class="stat-label">Earnings</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $course['Duration_Hours']; ?>h</div>
                                        <div class="stat-label">Duration</div>
                                    </div>
                                </div>

                                <div class="course-description">
                                    <?php echo nl2br(htmlspecialchars(substr($course['Description'], 0, 150))); ?>
                                    <?php if (strlen($course['Description']) > 150): ?>...<?php endif; ?>
                                </div>

                                <div class="course-actions">
                                    <a href="view_skill.php?id=<?php echo $course['Skill_ID']; ?>" class="btn btn-secondary">View Course</a>
                                    <a href="manage_students.php?skill_id=<?php echo $course['Skill_ID']; ?>" class="btn btn-primary">Manage Students</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <style>
    .course-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 1px solid var(--border);
    }

    .tab-btn {
        background: none;
        border: none;
        padding: 15px 20px;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 2px solid transparent;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .course-card {
        background: var(--card);
        border-radius: var(--radius-md);
        padding: 20px;
        border: 1px solid var(--border);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--shadow);
    }

    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .course-header h3 {
        margin: 0;
        color: var(--text);
        font-size: 1.2rem;
    }

    .course-status {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .course-status.active {
        background: var(--success);
        color: white;
    }

    .course-status.completed {
        background: var(--primary);
        color: white;
    }

    .course-price {
        color: var(--primary);
        font-weight: bold;
        font-size: 1.1rem;
    }

    .course-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .course-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 15px;
        padding: 15px;
        background: var(--surface);
        border-radius: var(--radius-sm);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--text);
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .progress-section {
        margin-bottom: 15px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .progress-bar {
        height: 6px;
        background: var(--surface);
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: var(--primary);
        transition: width 0.3s ease;
    }

    .course-description {
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 15px;
    }

    .course-actions {
        display: flex;
        gap: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state svg {
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h2 {
        margin-bottom: 10px;
        color: var(--text);
    }

    .empty-state p {
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
        
        .course-actions {
            flex-direction: column;
        }
        
        .course-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    </style>

    <script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').classList.add('active');
        
        // Add active class to clicked button
        event.target.classList.add('active');
    }

    function updateProgress(enrollmentId) {
        const progress = prompt('Enter your progress percentage (0-100):');
        if (progress !== null && progress >= 0 && progress <= 100) {
            // In a real application, you would send this to the server
            alert('Progress updated to ' + progress + '%');
            location.reload();
        }
    }
    </script>

<?php include 'includes/footer.php'; ?>