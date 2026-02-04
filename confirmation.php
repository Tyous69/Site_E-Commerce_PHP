<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

$user_req = $mysqli->query("SELECT solde FROM users WHERE id = $user_id");
$user_info = $user_req->fetch_assoc();
$solde_actuel = floatval($user_info['solde']);

$sql_total = "SELECT SUM(articles.prix * cart.quantite) as total 
              FROM cart 
              JOIN articles ON cart.article_id = articles.id 
              WHERE cart.user_id = $user_id";
$res_total = $mysqli->query($sql_total);
$row_total = $res_total->fetch_assoc();
$montant_total = floatval($row_total['total']);

if ($montant_total == 0) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adresse = $mysqli->real_escape_string($_POST['adresse']);
    $ville = $mysqli->real_escape_string($_POST['ville']);
    $cp = $mysqli->real_escape_string($_POST['cp']);

    if ($solde_actuel < $montant_total) {
        $error = "Solde insuffisant ! Il vous manque " . number_format($montant_total - $solde_actuel, 2) . " €.";
    } else {
        $mysqli->begin_transaction();

        try {
            $nouveau_solde = $solde_actuel - $montant_total;
            $mysqli->query("UPDATE users SET solde = $nouveau_solde WHERE id = $user_id");

            $sql_invoice = "INSERT INTO invoices (user_id, montant, adresse_facturation, ville, code_postal) 
                            VALUES ('$user_id', '$montant_total', '$adresse', '$ville', '$cp')";
            $mysqli->query($sql_invoice);

            $cart_items = $mysqli->query("SELECT article_id, quantite FROM cart WHERE user_id = $user_id");
            while ($item = $cart_items->fetch_assoc()) {
                $aid = $item['article_id'];
                $qty = $item['quantite'];
                $mysqli->query("UPDATE stock SET nombre = nombre - $qty WHERE article_id = $aid");
            }

            $mysqli->query("DELETE FROM cart WHERE user_id = $user_id");

            $mysqli->commit();
            
            header("Location: account.php?success_order=1");
            exit();

        } catch (Exception $e) {
            $mysqli->rollback();
            $error = "Une erreur est survenue lors de la transaction.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation commande - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="auth-page">
        <div class="auth-container" style="max-width: 600px;">
            <h1>Validation de commande</h1>

            <div class="order-summary">
                <p>Montant total à payer : <strong class="gold-text"><?php echo number_format($montant_total, 2); ?> €</strong></p>
                <p>Votre solde actuel : <strong><?php echo number_format($solde_actuel, 2); ?> €</strong></p>
                
                <?php if ($solde_actuel < $montant_total): ?>
                    <p class="error-msg" style="margin-top: 10px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Solde insuffisant. 
                        <a href="account.php" style="color: inherit; text-decoration: underline;">Recharger mon compte</a>.
                    </p>
                <?php else: ?>
                    <p style="color: #4CAF50;"><i class="fa-solid fa-check"></i> Solde suffisant pour cet achat.</p>
                <?php endif; ?>
            </div>

            <?php if($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>

            <form action="confirmation.php" method="POST" style="margin-top: 20px;">
                <h3 style="color: var(--white); text-align: left; border-bottom: 1px solid #333; padding-bottom: 10px;">Adresse de facturation</h3>
                
                <label style="text-align:left; display:block; color:#aaa;">Adresse (Rue, Numéro)</label>
                <input type="text" name="adresse" placeholder="123 Avenue du Temps" required>

                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Ville</label>
                        <input type="text" name="ville" placeholder="Paris" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="text-align:left; display:block; color:#aaa;">Code Postal</label>
                        <input type="text" name="cp" placeholder="75000" required>
                    </div>
                </div>

                <?php if ($solde_actuel >= $montant_total): ?>
                    <button type="submit" class="btn-confirm">
                        <i class="fa-solid fa-lock"></i> Payer et Valider
                    </button>
                <?php else: ?>
                    <button type="button" disabled style="background: #555; cursor: not-allowed;">Solde Insuffisant</button>
                <?php endif; ?>
            </form>
            
            <p style="margin-top: 20px;">
                <a href="cart.php">Retour au panier</a>
            </p>
        </div>
    </main>

</body>
</html>