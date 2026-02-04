<?php
include 'includes/db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $mysqli->real_escape_string($_POST['username']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $check = $mysqli->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        
        if ($check->num_rows > 0) {
            $error = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, email, password, solde, role) VALUES ('$username', '$email', '$hashed_password', 0, 'USER')";
            
            if ($mysqli->query($sql)) {
                header("Location: login.php?signup_success=1");
                exit();
            } else {
                $error = "Erreur technique lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - E-Chronos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="register-container">
            <h1>Rejoindre E-Chronos</h1>
            
            <?php if($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <input type="email" name="email" placeholder="Adresse Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                
                <div class="password-field">
                    <input type="password" name="password" id="pass" placeholder="Mot de passe" required>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePass('pass', this)"></i>
                </div>

                <div class="password-field">
                    <input type="password" name="confirm_password" id="conf_pass" placeholder="Confirmer mot de passe" required>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePass('conf_pass', this)"></i>
                </div>

                <button type="submit">S'inscrire</button>
            </form>
            <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
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