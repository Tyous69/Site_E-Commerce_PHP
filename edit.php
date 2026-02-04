<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$article_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'USER';
$msg = "";
$error = "";

$sql = "SELECT articles.*, stock.nombre as stock_actuel 
        FROM articles 
        LEFT JOIN stock ON articles.id = stock.article_id 
        WHERE articles.id = $article_id";
$result = $mysqli->query($sql);

if ($result->num_rows == 0) {
    die("Article introuvable.");
}

$article = $result->fetch_assoc();

if ($article['auteur_id'] != $user_id && $user_role != 'ADMIN') {
    die("Vous n'avez pas le droit de modifier cet article.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['delete_article'])) {
        $mysqli->query("DELETE FROM articles WHERE id = $article_id");
        header("Location: account.php?msg=deleted");
        exit();
    }

    $nom = $mysqli->real_escape_string($_POST['nom']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $prix = floatval($_POST['prix']);
    $new_stock = intval($_POST['stock']);

    $sql_update_img = "";
    if (!empty($_FILES['image']['name'])) {
        $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $filename)) {
            $sql_update_img = ", image = '$filename'";
        }
    }

    $update_art = "UPDATE articles SET nom='$nom', description='$description', prix='$prix' $sql_update_img WHERE id=$article_id";
    $update_stock = "UPDATE stock SET nombre=$new_stock WHERE article_id=$article_id";

    if ($mysqli->query($update_art) && $mysqli->query($update_stock)) {
        $msg = "Article modifié avec succès !";
        $article['nom'] = $nom;
        $article['description'] = $description;
        $article['prix'] = $prix;
        $article['stock_actuel'] = $new_stock;
        if($sql_update_img) $article['image'] = $filename;
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier <?php echo htmlspecialchars($article['nom']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="auth-page" style="min-height: auto; padding: 50px 0;">
        <div class="auth-container" style="max-width: 600px;">
            <h1>Modifier l'article</h1>
            
            <?php if($msg): ?><p class="success-msg"><?php echo $msg; ?></p><?php endif; ?>
            <?php if($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>

            <form action="edit.php?id=<?php echo $article_id; ?>" method="POST" enctype="multipart/form-data">
                
                <label style="text-align:left; display:block; color:#aaa;">Nom</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($article['nom']); ?>" required>

                <label style="text-align:left; display:block; color:#aaa;">Description</label>
                <textarea name="description" rows="5" required style="width: 100%; background: #1a1a1a; color: white; border: 1px solid #333; margin-bottom: 15px; padding: 10px;"><?php echo htmlspecialchars($article['description']); ?></textarea>

                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Prix (€)</label>
                        <input type="number" step="0.01" name="prix" value="<?php echo $article['prix']; ?>" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Stock</label>
                        <input type="number" name="stock" value="<?php echo $article['stock_actuel']; ?>" required>
                    </div>
                </div>

                <label style="text-align:left; display:block; color:#aaa;">Changer l'image (optionnel)</label>
                <input type="file" name="image" accept="image/*" style="padding-top: 10px;">
                <?php if($article['image']): ?>
                    <p style="font-size:0.8rem; color:#aaa;">Image actuelle : <?php echo $article['image']; ?></p>
                <?php endif; ?>

                <button type="submit" style="margin-top: 20px;">Enregistrer les modifications</button>
            </form>

            <form action="edit.php?id=<?php echo $article_id; ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article définitivement ?');">
                <button type="submit" name="delete_article" class="btn-delete" style="margin-top: 20px; width: 100%; background-color: #ff4d4d;">
                    <i class="fa-solid fa-trash"></i> Supprimer l'annonce
                </button>
            </form>

            <p style="margin-top: 20px;"><a href="account.php">Retour à mon profil</a></p>
        </div>
    </main>

</body>
</html>