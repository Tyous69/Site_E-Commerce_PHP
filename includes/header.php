<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="main-header">
    <div class="header-container">
        <a href="index.php" class="logo">
            <img src="assets/img/E-Chronos_Logo_Full.png" alt="E-Chronos Logo">
        </a>
        
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="vente.php">Vendre</a></li>
                    <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Panier</a></li>
                    <li><a href="account.php">Mon Compte</a></li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN'): ?>
                        <li><a href="admin/index.php" style="color: var(--gold); border: 1px solid var(--gold); padding: 5px 10px; border-radius: 4px;">ADMIN</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" style="color: #ff6b6b;">Déconnexion</a></li>
                
                <?php else: ?>
                    <li><a href="login.php">Se connecter</a></li>
                    <li><a href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>