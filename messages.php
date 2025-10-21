<?php
/**
 * SkillSwap Messages Page
 * Display user messages
 */

$page_title = "Messages";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$loggedInUserId = $_SESSION['user_id'];

// Fetch all messages for the logged-in user (both sent and received)
$sql = "SELECT m.Message_ID, m.Message_Text, m.Timestamp,
               u_from.Name as From_User_Name, u_to.Name as To_User_Name,
               s.Skill_Name, s.Skill_ID
        FROM Messages m
        JOIN Users u_from ON m.From_User_ID = u_from.User_ID
        JOIN Users u_to ON m.To_User_ID = u_to.User_ID
        LEFT JOIN Skills s ON m.Skill_ID = s.Skill_ID
        WHERE m.From_User_ID = ? OR m.To_User_ID = ?
        ORDER BY m.Timestamp DESC";

$messages = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $loggedInUserId, $loggedInUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
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
            <h1>Your Messages</h1>
            <p>Stay connected with the SkillSwap community</p>
        </div>

        <div class="messages-container">
            <?php if (!empty($messages)): ?>
                <div class="messages-grid">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card">
                            <div class="message-header">
                                <div class="message-avatar">
                                    <?php
                                    $sender = htmlspecialchars($message['From_User_Name']);
                                    $receiver = htmlspecialchars($message['To_User_Name']);
                                    $skillName = htmlspecialchars($message['Skill_Name']);
                                    
                                    if ($message['From_User_ID'] == $loggedInUserId) {
                                        echo "To: " . $receiver;
                                    } else {
                                        echo "From: " . $sender;
                                    }
                                    ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('M j, Y g:i A', strtotime($message['Timestamp'])); ?>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <?php echo nl2br(htmlspecialchars($message['Message_Text'])); ?>
                            </div>
                            
                            <?php if (!empty($skillName)): ?>
                                <div class="message-skill">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                    </svg>
                                    <span>Regarding: <a href="view_skill.php?id=<?php echo $message['Skill_ID']; ?>"><?php echo $skillName; ?></a></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <h3>No messages yet</h3>
                    <p>Start connecting with other learners by viewing skills and sending messages!</p>
                    <a href="dashboard.php" class="cta-btn">Browse Skills</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
