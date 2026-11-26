<?php

require_once __DIR__ . '/../src/db.php';

session_start();

$errors = [];
$identity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare("
        SELECT user_id, username, password_hash
        FROM account_user
        WHERE username = :username OR email = :email
        LIMIT 1
    ");

    $stmt->execute([
        'username' => $identity,
        'email' => $identity
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['password_hash'] !== null &&
        password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        unset($_SESSION['league_id']);

        header("Location: index.php");
        exit;

    } else {
        $errors[] = "Could not authenticate user.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Fantasy Football Manager</title>

    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="login-page">

    <div class="login-box">

        <div class="login-header">
            <header>Login</header>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <p style="color: green; text-align: center;">
                Account registered successfully. Please sign in.
            </p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p style="color: #ff6b6b; text-align: center;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endforeach; ?>

        <form method="POST" action="login.php">

            <div class="input-box">
                <input type="text"
                    name="identity"
                    class="input-field"
                    placeholder="Username or Email"
                    value="<?= htmlspecialchars($identity) ?>"
                    autocomplete="username"
                    required>
            </div>

            <div class="input-box">
                <input type="password"
                    name="password"
                    class="input-field"
                    placeholder="Password"
                    autocomplete="current-password"
                    required>
            </div>

            <div class="input-submit">
                <button type="submit"
                    class="submit-btn" id="submit">
                    Sign in
                </button>
            </div>

        </form>

        <div class="sign-up-link">
            <p>Don't have an account?
                <a href="register.php">Sign Up</a>
            </p>
        </div>

    </div>

</body>

</html>
