<?php
/**
 * SkillSwap Receipt Download
 * Generate and download payment receipt
 */

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];
$payment_id = $_GET['payment_id'] ?? 0;

// Fetch payment details
$sql = "SELECT p.*, s.Skill_Name, s.Duration_Hours, u.Name as Instructor_Name, 
               student.Name as Student_Name, student.Email as Student_Email
        FROM Payments p
        JOIN Skills s ON p.Skill_ID = s.Skill_ID
        JOIN Users u ON p.Instructor_User_ID = u.User_ID
        JOIN Users student ON p.Student_User_ID = student.User_ID
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

// Set headers for PDF download (in a real app, you'd use a PDF library)
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - <?php echo $payment['Transaction_ID']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            color: #333;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-header h1 {
            color: #22c55e;
            margin: 0;
        }
        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-section h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .info-section p {
            margin: 5px 0;
            color: #666;
        }
        .payment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .receipt-footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        .print-btn {
            background: #22c55e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Receipt</button>
    
    <div class="receipt-header">
        <h1>SkillSwap</h1>
        <p>Payment Receipt</p>
    </div>

    <div class="receipt-info">
        <div class="info-section">
            <h3>Bill To:</h3>
            <p><strong><?php echo htmlspecialchars($payment['Student_Name']); ?></strong></p>
            <p><?php echo htmlspecialchars($payment['Student_Email']); ?></p>
        </div>
        <div class="info-section">
            <h3>Receipt Details:</h3>
            <p><strong>Receipt #:</strong> <?php echo htmlspecialchars($payment['Transaction_ID']); ?></p>
            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($payment['Payment_Date'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($payment['Payment_Status']); ?></p>
        </div>
    </div>

    <div class="payment-details">
        <h3>Course Details</h3>
        <div class="detail-row">
            <span>Course Name:</span>
            <span><?php echo htmlspecialchars($payment['Skill_Name']); ?></span>
        </div>
        <div class="detail-row">
            <span>Instructor:</span>
            <span><?php echo htmlspecialchars($payment['Instructor_Name']); ?></span>
        </div>
        <div class="detail-row">
            <span>Duration:</span>
            <span><?php echo $payment['Duration_Hours']; ?> hours</span>
        </div>
        <div class="detail-row">
            <span>Payment Method:</span>
            <span><?php echo htmlspecialchars($payment['Payment_Method']); ?></span>
        </div>
        <div class="detail-row">
            <span>Course Fee:</span>
            <span>₹<?php echo number_format($payment['Amount'], 2); ?></span>
        </div>
        <div class="detail-row">
            <span>Platform Fee:</span>
            <span>₹0.00</span>
        </div>
        <div class="detail-row">
            <span>Total Amount Paid:</span>
            <span>₹<?php echo number_format($payment['Amount'], 2); ?></span>
        </div>
    </div>

    <div class="receipt-footer">
        <p>Thank you for choosing SkillSwap!</p>
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p>For any queries, please contact us at support@skillswap.com</p>
    </div>
</body>
</html>