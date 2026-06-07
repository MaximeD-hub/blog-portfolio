<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Suppression d'un article (DOIT être avant tout HTML)
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM comments WHERE id_article = :id")->execute([':id' => $id]);
    $pdo->prepare("DELETE FROM articles WHERE id = :id")->execute([':id' => $id]);
    header('Location: index.php');
    exit();
}

// Récupération de tous les articles
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.created_at, c.name AS category
    FROM articles a
    JOIN categories c ON a.id_category = c.id
    ORDER BY a.created_at DESC
");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back-office — Mon Blog</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">Mon Blog</a>
            <nav class="nav">
                <a href="../index.php">Voir le blog</a>
                <a href="article-form.php" class="btn btn--primary">+ Nouvel article</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Tableau de bord</h1>
        <p style="color:var(--color-text-light);margin-bottom:2rem">
            Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋
        </p>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['title']) ?></td>
                        <td><?= htmlspecialchars($article['category']) ?></td>
                        <td><?= formatDate($article['created_at']) ?></td>
                        <td class="admin-table__actions">
                            <a href="article-form.php?id=<?= $article['id'] ?>" 
                               class="btn btn--primary">Modifier</a>
                            <a href="?delete=<?= $article['id'] ?>" 
                               class="btn btn--danger"
                               onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Mon Blog Portfolio</p>
        </div>
    </footer>

</body>
</html>