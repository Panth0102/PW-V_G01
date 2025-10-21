<?php
$page_title = "Page Not Found";
$body_class = "theme-dark";
include 'includes/header.php';
?>

<div class="container">
    <div class="error-page">
        <div class="error-content">
            <h1>404</h1>
            <h2>Page Not Found</h2>
            <p>The page you're looking for doesn't exist or has been moved.</p>
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">Go to Login</a>
                <a href="dashboard.php" class="btn btn-secondary">Go to Dashboard</a>
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
    color: var(--primary);
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