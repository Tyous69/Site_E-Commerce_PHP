<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $mysqli->real_escape_string($_POST['nom']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $auteur_id = $_SESSION['user_id'];

    $target_dir = "uploads/";
    $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        $error = "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql_article = "INSERT INTO articles (nom, description, prix, image, auteur_id) 
                            VALUES ('$nom', '$description', '$prix', '$filename', '$auteur_id')";
            
            if ($mysqli->query($sql_article)) {
                $article_id = $mysqli->insert_id;

                $sql_stock = "INSERT INTO stock (article_id, nombre) VALUES ('$article_id', '$stock')";
                $mysqli->query($sql_stock);

                $message = "Votre montre a été mise en vente avec succès !";
            } else {
                $error = "Erreur SQL : " . $mysqli->error;
            }
        } else {
            $error = "Désolé, une erreur est survenue lors de l'envoi de l'image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vendre une montre - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="auth-page"> <div class="auth-container" style="max-width: 600px;"> <h1>Mettre en vente</h1>
            
            <?php if($message): ?><p style="color: #d4af37;"><?php echo $message; ?></p><?php endif; ?>
            <?php if($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>

            <form action="vente.php" method="POST" enctype="multipart/form-data">
                
                <label style="text-align:left; display:block; color:#aaa;">Nom du modèle</label>
                <input type="text" name="nom" placeholder="Ex: Rolex Submariner" required>

                <label style="text-align:left; display:block; color:#aaa;">Description</label>
                <textarea name="description" rows="5" placeholder="Détails, état, année..." required style="width: 100%; background: #1a1a1a; color: white; border: 1px solid #333; margin-bottom: 15px; padding: 10px;"></textarea>

                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Prix (€)</label>
                        <input type="number" step="0.01" name="prix" placeholder="0.00" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Stock</label>
                        <input type="number" name="stock" placeholder="1" value="1" required>
                    </div>
                </div>

                <label style="text-align:left; display:block; color:#aaa;">Photo de la montre</label>
                <input type="file" name="image" required accept="image/*" style="padding-top: 10px;">

                <button type="submit">Publier l'annonce</button>
            </form>
        </div>
    </main>

</body>
</html>