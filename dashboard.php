<?php
/**
 * SkillSwap Dashboard
 * Main dashboard for logged-in users
 */

$page_title = "Dashboard";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

// Add performance CSS for dashboard
echo '<link rel="stylesheet" href="dashboard_performance.css">';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_name = $_SESSION['user_name'];

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$skill_type_filter = $_GET['skill_type'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Show only 10 skills per page for better performance
$offset = ($page - 1) * $limit;

$sql = "SELECT s.Skill_ID, s.Skill_Name, s.Skill_Type, s.Description, s.Date_Posted, s.Price, s.Duration_Hours, s.Max_Students, u.Name as User_Name,
               (SELECT COUNT(*) FROM Enrollments e WHERE e.Skill_ID = s.Skill_ID AND e.Status = 'Active') as Current_Enrollments
        FROM Skills s
        JOIN Users u ON s.User_ID = u.User_ID";

$conditions = [];
if (!empty($search)) {
    $conditions[] = "(s.Skill_Name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR s.Description LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
}
if (!empty($category_filter)) {
    $conditions[] = "s.Skill_ID IN (SELECT sc.Skill_ID FROM SkillCategories sc WHERE sc.Category_ID = " . (int)$category_filter . ")";
}
if (!empty($skill_type_filter)) {
    $conditions[] = "s.Skill_Type = '" . mysqli_real_escape_string($conn, $skill_type_filter) . "'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY s.Date_Posted DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM Skills s JOIN Users u ON s.User_ID = u.User_ID";
if (!empty($conditions)) {
    $count_sql .= " WHERE " . implode(" AND ", $conditions);
}
$count_result = $conn->query($count_sql);
$total_skills = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_skills / $limit);

// Fetch categories for filter dropdown
$categories_sql = "SELECT Category_ID, Category_Name FROM Categories ORDER BY Category_Name";
$categories_result = $conn->query($categories_sql);
?>

<?php include 'includes/navbar.php'; ?>

    <div class="page-wrapper">
        <h2 style="margin-bottom:12px;">SkillSwap Dashboard</h2>

        <div class="filter-form">
            <form action="dashboard.php" method="get">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">

                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $category['Category_ID']; ?>" <?php echo ($category_filter == $category['Category_ID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['Category_Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="skill_type">Type:</label>
                <select id="skill_type" name="skill_type">
                    <option value="">All Types</option>
                    <option value="Offering" <?php echo ($skill_type_filter == 'Offering') ? 'selected' : ''; ?>>Offering</option>
                    <option value="Seeking" <?php echo ($skill_type_filter == 'Seeking') ? 'selected' : ''; ?>>Seeking</option>
                </select>

                <input type="submit" value="Filter">
                <a href="dashboard.php">Reset</a>
            </form>
        </div>

        <div class="skill-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="skill-card">
                        <div class="skill-header">
                            <h3><a href="view_skill.php?id=<?php echo $row['Skill_ID']; ?>"><?php echo htmlspecialchars($row['Skill_Name']); ?></a></h3>
                            <div class="skill-type <?php echo strtolower($row['Skill_Type']); ?>"><?php echo htmlspecialchars($row['Skill_Type']); ?></div>
                        </div>
                        
                        <?php if ($row['Skill_Type'] == 'Offering' && $row['Price'] > 0): ?>
                            <div class="skill-pricing">
                                <div class="price">₹<?php echo number_format($row['Price'], 0); ?></div>
                                <div class="course-details">
                                    <span><?php echo $row['Duration_Hours']; ?> hours</span>
                                    <span><?php echo $row['Current_Enrollments']; ?>/<?php echo $row['Max_Students']; ?> enrolled</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <p class="skill-description"><?php echo nl2br(htmlspecialchars(substr($row['Description'], 0, 150))); ?><?php if (strlen($row['Description']) > 150): ?>...<?php endif; ?></p>
                        
                        <div class="skill-meta">
                            <span class="instructor">By <?php echo htmlspecialchars($row['User_Name']); ?></span>
                            <span class="date"><?php echo date('M j, Y', strtotime($row['Date_Posted'])); ?></span>
                        </div>
                        
                        <div class="skill-actions">
                            <a href="view_skill.php?id=<?php echo $row['Skill_ID']; ?>" class="btn btn-primary">View Details</a>
                            <?php if ($row['Skill_Type'] == 'Offering' && $row['Price'] > 0): ?>
                                <a href="payment.php?skill_id=<?php echo $row['Skill_ID']; ?>" class="btn btn-secondary">Enroll Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No skills found.</p>
            <?php endif; ?>
        </div>

        <!-- Skills Counter -->
        <div class="skills-counter">
            Showing <?php echo (($page - 1) * $limit) + 1; ?>-<?php echo min($page * $limit, $total_skills); ?> of <?php echo number_format($total_skills); ?> skills
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>&skill_type=<?php echo urlencode($skill_type_filter); ?>" class="btn">« First</a>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>&skill_type=<?php echo urlencode($skill_type_filter); ?>" class="btn">‹ Previous</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>&skill_type=<?php echo urlencode($skill_type_filter); ?>" class="btn">Next ›</a>
                    <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>&skill_type=<?php echo urlencode($skill_type_filter); ?>" class="btn">Last »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>
