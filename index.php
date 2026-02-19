<?php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>E-Chronos - Excellence Horlogère</title>
    <link rel="icon" type="image/png" href="E-Chronos_Logo_Blanc.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="home-content">
        <section class="hero">
            <h1>Découvrez l'exception à votre poignet</h1>
            <p>Une sélection de montres uniques pour des moments inoubliables.</p>
        </section>

        <section class="articles-grid">
            <?php
            $sql = "SELECT * FROM articles ORDER BY date_publication DESC";
            $result = $mysqli->query($sql);

            if ($result && $result->num_rows > 0):
                while($article = $result->fetch_assoc()): ?>
                    <div class="article-card">
                        <img src="<?php echo !empty($article['image']) ? 'uploads/' . $article['image'] : 'assets/img/no-image.png'; ?>" alt="<?php echo htmlspecialchars($article['nom']); ?>">
                        
                        <h3><?php echo htmlspecialchars($article['nom']); ?></h3>
                        <p class="price"><?php echo number_format($article['prix'], 2); ?> €</p>
                        <a href="detail.php?id=<?php echo $article['id']; ?>" class="btn-detail">Voir le produit</a>
                    </div>
                <?php endwhile;
            else: ?>
                <p class="no-articles">Aucune montre n'est encore en vente. Soyez le premier !</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>