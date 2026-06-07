<?php
function isAdmin(): bool {
    return isset($_SESSION['user_id']);
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: /blog-portfolio/admin/login.php');
        exit();
    }
}