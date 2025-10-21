<?php
/**
 * SkillSwap Payment Page
 * Handle course payments in Indian Rupees
 */

$page_title = "Payment";
$body_class = "theme-dark";

// Include header
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit;
}

require_once 'config/connect.php';

$user_id = $_SESSION['user_id'];
$skill_id = $_GET['skill_id'] ?? 0;
$error_message = "";
$success_message = "";

// Fetch skill details
$sql = "SELECT s.*, u.Name as Instructor_Name, u.User_ID as Instructor_ID,
               (SELECT COUNT(*) FROM Enrollments e WHERE e.Skill_ID = s.Skill_ID AND e.Status = 'Active') as Current_Enrollments
        FROM Skills s 
        JOIN Users u ON s.User_ID = u.User_ID 
        WHERE s.Skill_ID = ? AND s.Skill_Type = 'Offering'";

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
$stmt->close();

// Check if course is full
$is_full = $skill['Current_Enrollments'] >= $skill['Max_Students'];

// Check if it's user's own skill
$is_own_skill = $skill['Instructor_ID'] == $user_id;

if ($is_enrolled || $is_full || $is_own_skill) {
    header("location: view_skill.php?id=" . $skill_id);
    exit;
}

// Handle payment processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'] ?? '';
    $upi_id = $_POST['upi_id'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $card_name = $_POST['card_name'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';

    if (empty($payment_method)) {
        $error_message = "Please select a payment method";
    } else {
        // Simulate payment processing
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        
        // In a real application, you would integrate with payment gateways like:
        // - Razorpay, PayU, CCAvenue, Paytm, etc.
        // For demo purposes, we'll simulate a successful payment
        
        $conn->begin_transaction();
        
        try {
            // Insert payment record
            $payment_sql = "INSERT INTO Payments (Student_User_ID, Instructor_User_ID, Skill_ID, Amount, Payment_Status, Payment_Method, Transaction_ID) 
                           VALUES (?, ?, ?, ?, 'Completed', ?, ?)";
            $stmt = $conn->prepare($payment_sql);
            $stmt->bind_param("iiidss", $user_id, $skill['Instructor_ID'], $skill_id, $skill['Price'], $payment_method, $transaction_id);
            $stmt->execute();
            $payment_id = $conn->insert_id;
            $stmt->close();
            
            // Create enrollment
            $enrollment_sql = "INSERT INTO Enrollments (Student_User_ID, Skill_ID, Payment_ID) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($enrollment_sql);
            $stmt->bind_param("iii", $user_id, $skill_id, $payment_id);
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            
            // Redirect to success page
            header("location: payment_success.php?payment_id=" . $payment_id);
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Payment processing failed. Please try again.";
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
        <div class="payment-container">
            <div class="payment-header">
                <h1>Complete Your Payment</h1>
                <p>Secure payment for your skill enrollment</p>
            </div>

            <div class="payment-content">
                <!-- Order Summary -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="course-info">
                        <h3><?php echo htmlspecialchars($skill['Skill_Name']); ?></h3>
                        <p>Instructor: <?php echo htmlspecialchars($skill['Instructor_Name']); ?></p>
                        <p>Duration: <?php echo $skill['Duration_Hours']; ?> hours</p>
                    </div>
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Course Fee</span>
                            <span>₹<?php echo number_format($skill['Price'], 2); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Platform Fee</span>
                            <span>₹0.00</span>
                        </div>
                        <div class="price-row total">
                            <span>Total Amount</span>
                            <span>₹<?php echo number_format($skill['Price'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="payment-form-container">
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

                    <form action="payment.php?skill_id=<?php echo $skill_id; ?>" method="post" class="payment-form" id="paymentForm">
                        <h2>Select Payment Method</h2>
                        
                        <!-- Payment Method Selection -->
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="UPI" required>
                                <div class="method-card">
                                    <div class="method-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"></path>
                                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"></path>
                                            <path d="M12 3c0 1-1 3-3 3s-3-2-3-3 1-3 3-3 3 2 3 3"></path>
                                            <path d="M12 21c0-1 1-3 3-3s3 2 3 3-1 3-3 3-3-2-3-3"></path>
                                        </svg>
                                    </div>
                                    <div class="method-info">
                                        <h3>UPI Payment</h3>
                                        <p>Pay using Google Pay, PhonePe, Paytm, etc.</p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="Card" required>
                                <div class="method-card">
                                    <div class="method-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                            <line x1="1" y1="10" x2="23" y2="10"></line>
                                        </svg>
                                    </div>
                                    <div class="method-info">
                                        <h3>Credit/Debit Card</h3>
                                        <p>Visa, Mastercard, RuPay accepted</p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="NetBanking" required>
                                <div class="method-card">
                                    <div class="method-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                            <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                        </svg>
                                    </div>
                                    <div class="method-info">
                                        <h3>Net Banking</h3>
                                        <p>All major Indian banks supported</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- UPI Details -->
                        <div id="upi-details" class="payment-details" style="display: none;">
                            <h3>UPI Details</h3>
                            <div class="form-group">
                                <label for="upi_id">UPI ID</label>
                                <input type="text" id="upi_id" name="upi_id" placeholder="yourname@paytm">
                            </div>
                        </div>

                        <!-- Card Details -->
                        <div id="card-details" class="payment-details" style="display: none;">
                            <h3>Card Details</h3>
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text" id="card_name" name="card_name" placeholder="Name on card">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_expiry">Expiry Date</label>
                                    <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="card_cvv">CVV</label>
                                    <input type="text" id="card_cvv" name="card_cvv" placeholder="123" maxlength="3">
                                </div>
                            </div>
                        </div>

                        <!-- Net Banking Details -->
                        <div id="netbanking-details" class="payment-details" style="display: none;">
                            <h3>Select Your Bank</h3>
                            <div class="form-group">
                                <select name="bank" id="bank">
                                    <option value="">Select Bank</option>
                                    <option value="SBI">State Bank of India</option>
                                    <option value="HDFC">HDFC Bank</option>
                                    <option value="ICICI">ICICI Bank</option>
                                    <option value="AXIS">Axis Bank</option>
                                    <option value="PNB">Punjab National Bank</option>
                                    <option value="BOB">Bank of Baroda</option>
                                    <option value="CANARA">Canara Bank</option>
                                    <option value="KOTAK">Kotak Mahindra Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="payment-actions">
                            <a href="view_skill.php?id=<?php echo $skill_id; ?>" class="btn btn-secondary">Back to Course</a>
                            <button type="submit" class="btn btn-primary">
                                <span>Pay ₹<?php echo number_format($skill['Price'], 0); ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4"></path>
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const upiDetails = document.getElementById('upi-details');
        const cardDetails = document.getElementById('card-details');
        const netbankingDetails = document.getElementById('netbanking-details');

        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                // Hide all details
                upiDetails.style.display = 'none';
                cardDetails.style.display = 'none';
                netbankingDetails.style.display = 'none';

                // Show relevant details
                if (this.value === 'UPI') {
                    upiDetails.style.display = 'block';
                } else if (this.value === 'Card') {
                    cardDetails.style.display = 'block';
                } else if (this.value === 'NetBanking') {
                    netbankingDetails.style.display = 'block';
                }
            });
        });

        // Format card number
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function() {
                let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                this.value = formattedValue;
            });
        }

        // Format expiry date
        const cardExpiryInput = document.getElementById('card_expiry');
        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value;
            });
        }
    });
    </script>

<?php include 'includes/footer.php'; ?>