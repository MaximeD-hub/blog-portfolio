<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Récupération des articles avec leur catégorie
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.slug, a.content, a.created_at, c.name AS category, c.slug AS category_slug
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
    <title>Mon Blog Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Mon Blog</a>
            <nav class="nav">
                <a href="index.php">Accueil</a>
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php">Back-office</a>
                    <a href="admin/logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="admin/login.php">Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Derniers articles</h1>

        <div class="filters">
            <button class="filter-btn active" data-category="all">Tous</button>
            <button class="filter-btn" data-category="javascript">JavaScript</button>
            <button class="filter-btn" data-category="php">PHP</button>
            <button class="filter-btn" data-category="css">CSS</button>
        </div>

        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <article class="article-card" data-category="<?= $article['category_slug'] ?>">
                    <div class="article-card__content">
                        <span class="article-card__category"><?= htmlspecialchars($article['category']) ?></span>
                        <h2 class="article-card__title">
                            <?= htmlspecialchars($article['title']) ?>
                        </h2>
                        <p class="article-card__excerpt">
                            <?= truncate(htmlspecialchars($article['content'])) ?>
                        </p>
                        <div class="article-card__footer">
                            <span class="article-card__date"><?= formatDate($article['created_at']) ?></span>
                            <a href="article.php?slug=<?= $article['slug'] ?>" class="btn btn--primary">Lire la suite</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Mon Blog Portfolio</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>