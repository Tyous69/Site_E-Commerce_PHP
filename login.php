<?php
include 'includes/db.php';
session_start();

$error = "";
$success_msg = "";

if (isset($_GET['signup_success'])) {
    $success_msg = "Compte créé avec succès ! Veuillez vous connecter.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $mysqli->real_escape_string($_POST['identifier']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$identifier' OR email = '$identifier'";
    $result = $mysqli->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Aucun compte trouvé avec cet identifiant.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h1>Connexion</h1>
            
            <?php if($success_msg): ?>
                <p style="color: #4CAF50; background: rgba(76, 175, 80, 0.1); padding: 10px; border-radius: 4px;">
                    <?php echo $success_msg; ?>
                </p>
            <?php endif; ?>

            <?php if($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="text" name="identifier" placeholder="Nom d'utilisateur ou Email" required>
                
                <div class="password-field">
                    <input type="password" name="password" id="login_pass" placeholder="Mot de passe" required>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePass('login_pass', this)"></i>
                </div>

                <button type="submit">Se connecter</button>
            </form>
            <p>Pas de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>

    <script>
        function togglePass(id, icon) {
            const field = document.getElementById(id);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>
</body>
</html>