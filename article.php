<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Récupération de l'article
$slug = htmlspecialchars($_GET['slug'] ?? '');

$stmt = $pdo->prepare("
    SELECT a.*, c.name AS category, c.slug AS category_slug, u.username AS author
    FROM articles a
    JOIN categories c ON a.id_category = c.id
    JOIN users u ON a.id_user = u.id
    WHERE a.slug = :slug
");
$stmt->execute([':slug' => $slug]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: index.php');
    exit();
}

// Récupération des commentaires
$stmt2 = $pdo->prepare("
    SELECT * FROM comments
    WHERE id_article = :id
    ORDER BY created_at ASC
");
$stmt2->execute([':id' => $article['id']]);
$comments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire de commentaire
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Requête invalide.';
    } else {
        $author_name = trim(htmlspecialchars($_POST['author_name'] ?? ''));
        $content = trim(htmlspecialchars($_POST['content'] ?? ''));

        if (empty($author_name) || empty($content)) {
            $error = 'Tous les champs sont obligatoires.';
        } elseif (strlen($author_name) > 50) {
            $error = 'Le nom est trop long (50 caractères max).';
        } else {
            $stmt3 = $pdo->prepare("
                INSERT INTO comments (author_name, content, id_article)
                VALUES (:author, :content, :id_article)
            ");
            $stmt3->execute([
                ':author'     => $author_name,
                ':content'    => $content,
                ':id_article' => $article['id']
            ]);
            $success = 'Commentaire ajouté !';
            header("Location: article.php?slug=" . $article['slug']);
            exit();
        }
    }
}

// Génération token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> — Mon Blog</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Mon Blog</a>
            <nav class="nav">
                <?php if (isAdmin()): ?>
                    <a href="admin/index.php">Back-office</a>
                    <a href="admin/logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="admin/login.php">Connexion</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">

        <article class="article-full">
            <div class="article-full__meta">
                <span class="article-card__category"><?= htmlspecialchars($article['category']) ?></span>
                <span class="article-full__date"><?= formatDate($article['created_at']) ?></span>
                <span class="article-full__author">par <?= htmlspecialchars($article['author']) ?></span>
            </div>

            <h1 class="article-full__title"><?= htmlspecialchars($article['title']) ?></h1>

            <div class="article-full__content">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>

            <?php if (isAdmin()): ?>
                <div class="article-full__actions">
                    <a href="admin/article-form.php?id=<?= $article['id'] ?>" class="btn btn--primary">Modifier</a>
                </div>
            <?php endif; ?>
        </article>

        <section class="comments">
            <h2>Commentaires (<?= count($comments) ?>)</h2>

            <?php if (empty($comments)): ?>
                <p class="comments__empty">Aucun commentaire pour le moment. Soyez le premier !</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment__header">
                            <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                            <span><?= formatDate($comment['created_at']) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="comment-form">
                <h3>Laisser un commentaire</h3>

                <?php if ($error): ?>
                    <p class="alert alert--error"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" id="comment-form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="form-group">
                        <label for="author_name">Votre nom</label>
                        <input type="text" id="author_name" name="author_name" 
                               placeholder="Jean Dupont" required maxlength="50">
                    </div>

                    <div class="form-group">
                        <label for="content">Votre commentaire</label>
                        <textarea id="content" name="content" rows="4" 
                                  placeholder="Votre message..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn--primary">Envoyer</button>
                </form>
            </div>
        </section>

    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Mon Blog Portfolio</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>