<?php
include 'includes/db.php';
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$article_id = intval($_GET['id']);
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $quantite_demandee = intval($_POST['quantite']);
    $stock_dispo = intval($_POST['stock_max']);

    if ($quantite_demandee > $stock_dispo) {
        $error = "Stock insuffisant !";
    } else {
        $check_cart = $mysqli->query("SELECT id, quantite FROM cart WHERE user_id = $user_id AND article_id = $article_id");
        
        if ($check_cart->num_rows > 0) {
            $row = $check_cart->fetch_assoc();
            $new_qty = $row['quantite'] + $quantite_demandee;
            
            if ($new_qty > $stock_dispo) {
                $error = "Vous ne pouvez pas ajouter plus que le stock disponible.";
            } else {
                $mysqli->query("UPDATE cart SET quantite = $new_qty WHERE id = " . $row['id']);
                $msg = "Panier mis à jour !";
            }
        } else {
            $mysqli->query("INSERT INTO cart (user_id, article_id, quantite) VALUES ($user_id, $article_id, $quantite_demandee)");
            $msg = "Article ajouté au panier !";
        }
    }
}

$sql = "SELECT articles.*, users.username, stock.nombre as stock_actuel 
        FROM articles 
        LEFT JOIN users ON articles.auteur_id = users.id 
        LEFT JOIN stock ON articles.id = stock.article_id 
        WHERE articles.id = $article_id";

$result = $mysqli->query($sql);

if ($result->num_rows == 0) {
    echo "Article introuvable.";
    exit();
}

$article = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article['nom']); ?> - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="detail-container">
        <div class="detail-image">
            <?php 
            $imgSrc = !empty($article['image']) ? 'uploads/' . $article['image'] : 'assets/img/no-image.png';
            ?>
            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($article['nom']); ?>">
        </div>

        <div class="detail-info">
            <h1><?php echo htmlspecialchars($article['nom']); ?></h1>
            <p class="author">Vendu par : <strong><?php echo htmlspecialchars($article['username']); ?></strong></p>
            
            <p class="price-tag"><?php echo number_format($article['prix'], 2); ?> €</p>
            
            <div class="description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($article['description'])); ?></p>
            </div>

            <div class="stock-info">
                <?php if($article['stock_actuel'] > 0): ?>
                    <span class="in-stock"><i class="fa-solid fa-check"></i> En stock (<?php echo $article['stock_actuel']; ?> disponibles)</span>
                <?php else: ?>
                    <span class="out-stock"><i class="fa-solid fa-xmark"></i> Rupture de stock</span>
                <?php endif; ?>
            </div>

            <?php if($msg): ?><p class="success-msg"><?php echo $msg; ?></p><?php endif; ?>
            <?php if($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>

            <?php if($article['stock_actuel'] > 0): ?>
                <form action="detail.php?id=<?php echo $article_id; ?>" method="POST" class="add-cart-form">
                    <input type="hidden" name="stock_max" value="<?php echo $article['stock_actuel']; ?>">
                    
                    <label>Quantité :</label>
                    <input type="number" name="quantite" value="1" min="1" max="<?php echo $article['stock_actuel']; ?>">
                    
                    <button type="submit" name="add_to_cart" class="btn-add">
                        <i class="fa-solid fa-cart-plus"></i> Ajouter au panier
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>