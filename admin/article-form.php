<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Génération token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Mode édition si un ID est passé
$article = null;
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$article) {
        header('Location: index.php');
        exit();
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(htmlspecialchars($_POST['title'] ?? ''));
    $content     = trim(htmlspecialchars($_POST['content'] ?? ''));
    $id_category = (int) ($_POST['id_category'] ?? 0);

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Requête invalide.';
    } elseif (empty($title) || empty($content) || !$id_category) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        $slug = slugify($title);

        if ($id) {
            $stmt = $pdo->prepare("
                UPDATE articles
                SET title = :title, slug = :slug, content = :content, id_category = :id_category
                WHERE id = :id
            ");
            $stmt->execute([
                ':title'       => $title,
                ':slug'        => $slug,
                ':content'     => $content,
                ':id_category' => $id_category,
                ':id'          => $id
            ]);
            $success = 'Article mis à jour !';
            $article['title']       = $title;
            $article['content']     = $content;
            $article['id_category'] = $id_category;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO articles (title, slug, content, id_user, id_category)
                VALUES (:title, :slug, :content, :id_user, :id_category)
            ");
            $stmt->execute([
                ':title'       => $title,
                ':slug'        => $slug,
                ':content'     => $content,
                ':id_user'     => $_SESSION['user_id'],
                ':id_category' => $id_category
            ]);
            header('Location: index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Modifier' : 'Nouvel' ?> article — Mon Blog</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="../index.php" class="logo">Mon Blog</a>
            <nav class="nav">
                <a href="index.php">← Tableau de bord</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1><?= $id ? 'Modifier l\'article' : 'Nouvel article' ?></h1>

        <?php if ($error): ?>
            <p class="alert alert--error"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="alert alert--success"><?= $success ?></p>
        <?php endif; ?>

        <div class="article-form-box">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title"
                           value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                           placeholder="Titre de l'article" required>
                </div>

                <div class="form-group">
                    <label for="id_category">Catégorie</label>
                    <select id="id_category" name="id_category" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= isset($article['id_category']) && $article['id_category'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea id="content" name="content" rows="12"
                              placeholder="Contenu de l'article..." required><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn--secondary">Annuler</a>
                    <button type="submit" class="btn btn--primary">
                        <?= $id ? 'Mettre à jour' : 'Publier' ?>
                    </button>
                </div>
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