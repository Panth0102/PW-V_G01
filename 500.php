<?php
$page_title = "Server Error";
$body_class = "theme-dark";
include 'includes/header.php';
?>

<div class="container">
    <div class="error-page">
        <div class="error-content">
            <h1>500</h1>
            <h2>Server Error</h2>
            <p>Something went wrong on our end. Please try again later.</p>
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">Go to Login</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    text-align: center;
}

.error-content h1 {
    font-size: 6rem;
    color: var(--danger);
    margin-bottom: 1rem;
}

.error-content h2 {
    color: var(--text);
    margin-bottom: 1rem;
}

.error-content p {
    color: var(--text-muted);
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

@media (max-width: 768px) {
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>