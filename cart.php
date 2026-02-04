<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['delete_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $mysqli->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
        $msg = "Article retiré du panier.";
    }

    if (isset($_POST['update_qty'])) {
        $cart_id = intval($_POST['cart_id']);
        $new_qty = intval($_POST['quantite']);
        $article_id = intval($_POST['article_id']);

        $stock_check = $mysqli->query("SELECT nombre FROM stock WHERE article_id = $article_id");
        $stock_data = $stock_check->fetch_assoc();

        if ($new_qty <= 0) {
            $mysqli->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
        } elseif ($new_qty > $stock_data['nombre']) {
            $error = "Désolé, il n'y a que " . $stock_data['nombre'] . " exemplaires en stock.";
        } else {
            $mysqli->query("UPDATE cart SET quantite = $new_qty WHERE id = $cart_id AND user_id = $user_id");
            $msg = "Panier mis à jour.";
        }
    }
}

$sql = "SELECT cart.id as cart_id, cart.quantite, cart.article_id,
               articles.nom, articles.prix, articles.image,
               stock.nombre as stock_max
        FROM cart
        JOIN articles ON cart.article_id = articles.id
        LEFT JOIN stock ON articles.id = stock.article_id
        WHERE cart.user_id = $user_id";

$result = $mysqli->query($sql);
$total_general = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="cart-page">
        <div class="cart-container">
            <h1><i class="fa-solid fa-cart-shopping"></i> Votre Panier</h1>

            <?php if($msg): ?><p class="success-msg"><?php echo $msg; ?></p><?php endif; ?>
            <?php if($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Prix Unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $result->fetch_assoc()): 
                            $total_ligne = $item['prix'] * $item['quantite'];
                            $total_general += $total_ligne;
                        ?>
                            <tr>
                                <td class="item-info">
                                    <img src="<?php echo !empty($item['image']) ? 'uploads/'.$item['image'] : 'assets/img/no-image.png'; ?>" alt="Montre">
                                    <span><?php echo htmlspecialchars($item['nom']); ?></span>
                                </td>
                                <td><?php echo number_format($item['prix'], 2); ?> €</td>
                                
                                <td>
                                    <form action="cart.php" method="POST" class="qty-form">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="hidden" name="article_id" value="<?php echo $item['article_id']; ?>">
                                        
                                        <input type="number" name="quantite" value="<?php echo $item['quantite']; ?>" min="1" max="<?php echo $item['stock_max']; ?>">
                                        <button type="submit" name="update_qty" class="btn-refresh" title="Mettre à jour">
                                            <i class="fa-solid fa-rotate"></i>
                                        </button>
                                    </form>
                                </td>
                                
                                <td class="price-col"><?php echo number_format($total_ligne, 2); ?> €</td>
                                
                                <td>
                                    <form action="cart.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" name="delete_item" class="btn-delete" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <h2>Total : <span class="gold-text"><?php echo number_format($total_general, 2); ?> €</span></h2>
                    <a href="confirmation.php" class="btn-checkout">Passer la commande <i class="fa-solid fa-arrow-right"></i></a>
                </div>

            <?php else: ?>
                <div class="empty-cart">
                    <p>Votre panier est vide.</p>
                    <a href="index.php" class="btn-detail">Découvrir nos montres</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>