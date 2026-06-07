<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Si déjà connecté, rediriger vers le dashboard
if (isAdmin()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin — Mon Blog</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">Mon Blog</a>
        </div>
    </header>

    <main class="container">
        <div class="login-box">
            <h1>Connexion Admin</h1>

            <?php if ($error): ?>
                <p class="alert alert--error"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           placeholder="admin@blog.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" 
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn--primary" style="width:100%">
                    Se connecter
                </button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Mon Blog Portfolio</p>
        </div>
    </footer>

</body>
</html>