<?php
/**
 * SkillSwap Payment Success Page
 * Confirmation page after successful payment
 */

$page_title = "Payment Successful";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];
$payment_id = $_GET['payment_id'] ?? 0;

// Fetch payment details
$sql = "SELECT p.*, s.Skill_Name, s.Duration_Hours, u.Name as Instructor_Name, e.Enrollment_ID
        FROM Payments p
        JOIN Skills s ON p.Skill_ID = s.Skill_ID
        JOIN Users u ON p.Instructor_User_ID = u.User_ID
        LEFT JOIN Enrollments e ON p.Payment_ID = e.Payment_ID
        WHERE p.Payment_ID = ? AND p.Student_User_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $payment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("location: dashboard.php");
    exit;
}

$payment = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<?php include 'includes/navbar.php'; ?>

    <div class="page-wrapper">
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                
                <h1>Payment Successful!</h1>
                <p class="success-message">Congratulations! You have successfully enrolled in the course.</p>
                
                <div class="payment-details">
                    <h2>Payment Details</h2>
                    <div class="detail-row">
                        <span class="label">Transaction ID:</span>
                        <span class="value"><?php echo htmlspecialchars($payment['Transaction_ID']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Course:</span>
                        <span class="value"><?php echo htmlspecialchars($payment['Skill_Name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Instructor:</span>
                        <span class="value"><?php echo htmlspecialchars($payment['Instructor_Name']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Duration:</span>
                        <span class="value"><?php echo $payment['Duration_Hours']; ?> hours</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Amount Paid:</span>
                        <span class="value">₹<?php echo number_format($payment['Amount'], 2); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Payment Method:</span>
                        <span class="value"><?php echo htmlspecialchars($payment['Payment_Method']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Payment Date:</span>
                        <span class="value"><?php echo date('F j, Y g:i A', strtotime($payment['Payment_Date'])); ?></span>
                    </div>
                </div>

                <div class="next-steps">
                    <h2>What's Next?</h2>
                    <div class="steps-list">
                        <div class="step-item">
                            <div class="step-icon">1</div>
                            <div class="step-content">
                                <h3>Check Your Email</h3>
                                <p>You'll receive a confirmation email with course details</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">2</div>
                            <div class="step-content">
                                <h3>Contact Your Instructor</h3>
                                <p>Reach out to schedule your learning sessions</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">3</div>
                            <div class="step-content">
                                <h3>Start Learning</h3>
                                <p>Begin your skill development journey</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="my_courses.php" class="btn btn-primary">
                        <span>View My Courses</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12,5 19,12 12,19"></polyline>
                        </svg>
                    </a>
                    <a href="dashboard.php" class="btn btn-secondary">Browse More Courses</a>
                </div>

                <div class="receipt-download">
                    <p>Need a receipt? <a href="download_receipt.php?payment_id=<?php echo $payment_id; ?>" target="_blank">Download Receipt</a></p>
                </div>
            </div>
        </div>
    </div>

    <style>
    .success-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .success-card {
        background: var(--card);
        border-radius: var(--radius-lg);
        padding: 40px;
        max-width: 600px;
        width: 100%;
        text-align: center;
        box-shadow: 0 10px 30px var(--shadow);
    }

    .success-icon {
        color: var(--success);
        margin-bottom: 20px;
    }

    .success-card h1 {
        color: var(--success);
        margin-bottom: 10px;
        font-size: 2rem;
    }

    .success-message {
        color: var(--text-muted);
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    .payment-details {
        background: var(--surface);
        border-radius: var(--radius-md);
        padding: 20px;
        margin: 30px 0;
        text-align: left;
    }

    .payment-details h2 {
        margin-bottom: 15px;
        color: var(--text);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .label {
        color: var(--text-muted);
        font-weight: 500;
    }

    .value {
        color: var(--text);
        font-weight: 600;
    }

    .next-steps {
        margin: 30px 0;
        text-align: left;
    }

    .next-steps h2 {
        text-align: center;
        margin-bottom: 20px;
        color: var(--text);
    }

    .steps-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .step-icon {
        background: var(--primary);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }

    .step-content h3 {
        margin: 0 0 5px 0;
        color: var(--text);
    }

    .step-content p {
        margin: 0;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin: 30px 0;
        flex-wrap: wrap;
    }

    .receipt-download {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .receipt-download a {
        color: var(--primary);
        text-decoration: none;
    }

    .receipt-download a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .success-card {
            padding: 20px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .detail-row {
            flex-direction: column;
            gap: 5px;
        }
    }
    </style>

<?php include 'includes/footer.php'; ?>