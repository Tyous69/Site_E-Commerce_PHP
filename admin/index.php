<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['delete_user'])) {
        $id_to_delete = intval($_POST['user_id']);
        if ($id_to_delete != $_SESSION['user_id']) {
            $mysqli->query("DELETE FROM users WHERE id = $id_to_delete");
            $msg = "Utilisateur supprimé.";
        } else {
            $msg = "Vous ne pouvez pas supprimer votre propre compte ici.";
        }
    }

    if (isset($_POST['delete_article'])) {
        $id_art = intval($_POST['article_id']);
        $mysqli->query("DELETE FROM articles WHERE id = $id_art");
        $msg = "Article supprimé.";
    }

    if (isset($_POST['toggle_role'])) {
        $uid = intval($_POST['user_id']);
        
        if ($uid != $_SESSION['user_id']) {
            $current_role = $_POST['current_role'];
            $new_role = ($current_role == 'ADMIN') ? 'USER' : 'ADMIN';
            
            $mysqli->query("UPDATE users SET role = '$new_role' WHERE id = $uid");
            $msg = "Rôle mis à jour : $new_role";
        }
    }
}

$users = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
$articles = $mysqli->query("SELECT articles.*, users.username FROM articles LEFT JOIN users ON articles.auteur_id = users.id ORDER BY articles.id DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - E-Chronos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid var(--gold); padding-bottom: 10px; }
        .data-table { width: 100%; border-collapse: collapse; background: #252525; color: white; margin-bottom: 40px; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #444; text-align: left; }
        .data-table th { background: #1a1a1a; color: var(--gold); }
        .action-btn { padding: 5px 10px; border-radius: 4px; text-decoration: none; color: white; border: none; cursor: pointer; font-size: 0.9rem; }
        .btn-red { background: #ff4d4d; }
        .btn-blue { background: #3498db; }
        .btn-green { background: #2ecc71; }
        h2 { color: var(--gold); margin-top: 0; }
    </style>
</head>
<body>

    <div class="admin-container">
        <header class="admin-header">
            <h1><i class="fa-solid fa-user-shield"></i> Dashboard Admin</h1>
            <a href="../index.php" class="action-btn btn-blue">Retour au Site</a>
        </header>

        <?php if($msg): ?><p style="color: #4CAF50; background: #333; padding: 10px;"><?php echo $msg; ?></p><?php endif; ?>

        <section>
            <h2>Gestion des Utilisateurs</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Solde</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td>
                            <?php if(!empty($u['photo'])): ?>
                                <img src="../uploads/<?php echo $u['photo']; ?>" style="width:30px; height:30px; border-radius:50%; vertical-align:middle;">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($u['username']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        
                        <td>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form action="index.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <input type="hidden" name="current_role" value="<?php echo $u['role']; ?>">
                                    <button type="submit" name="toggle_role" class="action-btn <?php echo $u['role'] == 'ADMIN' ? 'btn-green' : 'btn-blue'; ?>" title="Changer le rôle">
                                        <?php echo $u['role']; ?> <i class="fa-solid fa-rotate"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="font-weight:bold; color: var(--gold);">ADMIN (Vous)</span>
                            <?php endif; ?>
                        </td>

                        <td><?php echo number_format($u['solde'], 2); ?> €</td>
                        
                        <td>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form action="index.php" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" name="delete_user" class="action-btn btn-red"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <span style="color:#777;">(Vous)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Gestion des Articles</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Vendeur</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($art = $articles->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $art['id']; ?></td>
                        <td>
                            <?php if($art['image']): ?>
                                <img src="../uploads/<?php echo $art['image']; ?>" style="width:50px; height:50px; object-fit:cover;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($art['nom']); ?></td>
                        <td><?php echo htmlspecialchars($art['username']); ?></td>
                        <td><?php echo number_format($art['prix'], 2); ?> €</td>
                        <td>
                            <a href="../edit.php?id=<?php echo $art['id']; ?>" class="action-btn btn-blue"><i class="fa-solid fa-pen"></i></a>
                            
                            <form action="index.php" method="POST" onsubmit="return confirm('Supprimer cet article ?');" style="display:inline;">
                                <input type="hidden" name="article_id" value="<?php echo $art['id']; ?>">
                                <button type="submit" name="delete_article" class="action-btn btn-red"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>