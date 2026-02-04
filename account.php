<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

$target_id = isset($_GET['user']) ? intval($_GET['user']) : $current_user_id;
$is_me = ($target_id === $current_user_id);

if ($is_me && $_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['add_money'])) {
        $amount = floatval($_POST['amount']);
        if ($amount > 0) {
            $mysqli->query("UPDATE users SET solde = solde + $amount WHERE id = $current_user_id");
            $msg = "Solde mis à jour avec succès !";
        }
    }

    if (isset($_POST['update_profile'])) {
        $new_email = $mysqli->real_escape_string($_POST['email']);
        $sql_update = "UPDATE users SET email = '$new_email'";

        if (!empty($_FILES['avatar']['name'])) {
            $filename = uniqid() . "_" . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], "uploads/" . $filename)) {
                $sql_update .= ", photo = '$filename'";
            }
        }

        if (!empty($_POST['password'])) {
            $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql_update .= ", password = '$hashed'";
        }

        $sql_update .= " WHERE id = $current_user_id";
        
        if ($mysqli->query($sql_update)) {
            $msg = "Profil mis à jour.";
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    }
}

$sql_user = "SELECT * FROM users WHERE id = $target_id";
$res_user = $mysqli->query($sql_user);

if ($res_user->num_rows == 0) {
    echo "Utilisateur introuvable.";
    exit();
}
$user = $res_user->fetch_assoc();

$sql_articles = "SELECT * FROM articles WHERE auteur_id = $target_id ORDER BY date_publication DESC";
$res_articles = $mysqli->query($sql_articles);

$res_invoices = null;
if ($is_me) {
    $sql_invoices = "SELECT * FROM invoices WHERE user_id = $current_user_id ORDER BY date_transaction DESC";
    $res_invoices = $mysqli->query($sql_invoices);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?php echo htmlspecialchars($user['username']); ?> - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main class="account-page">
        
        <?php if($msg): ?><div class="success-banner"><?php echo $msg; ?></div><?php endif; ?>
        <?php if($error): ?><div class="error-banner"><?php echo $error; ?></div><?php endif; ?>

        <section class="profile-header">
            <div class="profile-card">
                <?php 
                ?>
                <?php if(!empty($user['photo'])): ?>
                    <img src="uploads/<?php echo $user['photo']; ?>" class="profile-pic">
                <?php else: ?>
                    <div class="profile-pic-placeholder"><i class="fa-solid fa-user"></i></div>
                <?php endif; ?>

                <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="role-badge"><?php echo $user['role']; ?></p>
                
                <?php if ($is_me): ?>
                    <div class="balance-box">
                        <p>Mon Solde</p>
                        <h2><?php echo number_format($user['solde'], 2); ?> €</h2>
                        
                        <form action="account.php" method="POST" class="add-money-form">
                            <input type="number" name="amount" placeholder="Montant" min="1" required>
                            <button type="submit" name="add_money" title="Ajouter des fonds"><i class="fa-solid fa-plus"></i></button>
                        </form>
                    </div>

                    <div class="edit-section">
                        <h3>Modifier mes infos</h3>
                        <form action="account.php" method="POST" enctype="multipart/form-data" class="edit-form">
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            <input type="password" name="password" placeholder="Nouveau mot de passe (laisser vide si inchangé)">
                            <label>Changer de photo :</label>
                            <input type="file" name="avatar">
                            <button type="submit" name="update_profile">Enregistrer</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="user-content">
            <h2><i class="fa-solid fa-tag"></i> Montres mises en vente</h2>
            <div class="articles-grid">
                <?php if ($res_articles->num_rows > 0): ?>
                    <?php while($art = $res_articles->fetch_assoc()): ?>
                        <div class="article-card">
                            <img src="<?php echo !empty($art['image']) ? 'uploads/'.$art['image'] : 'assets/img/no-image.png'; ?>" alt="Montre">
                            <h3><?php echo htmlspecialchars($art['nom']); ?></h3>
                            <p class="price"><?php echo number_format($art['prix'], 2); ?> €</p>
                            <a href="detail.php?id=<?php echo $art['id']; ?>" class="btn-detail">Voir</a>
                            
                            <?php if($is_me): ?>
                                <a href="edit.php?id=<?php echo $art['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color:#777;">Aucune montre en vente pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($is_me): ?>
        <section class="user-content" style="margin-top: 50px;">
            <h2><i class="fa-solid fa-file-invoice-dollar"></i> Mes Commandes Passées</h2>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Adresse de livraison</th>
                        <th>ID Facture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res_invoices && $res_invoices->num_rows > 0): ?>
                        <?php while($inv = $res_invoices->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date("d/m/Y H:i", strtotime($inv['date_transaction'])); ?></td>
                                <td class="gold-text"><?php echo number_format($inv['montant'], 2); ?> €</td>
                                <td><?php echo htmlspecialchars($inv['adresse_facturation']) . ", " . htmlspecialchars($inv['ville']); ?></td>
                                <td>#<?php echo $inv['id']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Aucune commande effectuée.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>

    </main>

</body>
</html>