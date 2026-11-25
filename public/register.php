
<?php
require_once __DIR__ . '/../src/db.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validate username
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $errors[] = "Username must be 3-30 characters (letters, numbers, underscores).";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate password
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    if (!$errors) {
        $pdo = db();

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM account_user
            WHERE username = :username
               OR email = :email
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $email
        ]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Username or email already exists.";
        }
    }

    // Register the user
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO account_user
                (username, email, password_hash)
            VALUES
                (:username, :email, :password_hash)
        ");

        try {
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $hash
            ]);

            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                $errors[] = "Username or email already exists.";
            } else {
                error_log($e->getMessage());
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Fantasy Football Manager</title>

    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="login-page">

    <div class="login-box">

        <div class="login-header">
            <header>Create Account</header>
        </div>

        <?php foreach ($errors as $error): ?>
            <p style="color: #ff6b6b; text-align: center;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endforeach; ?>

        <form method="POST" action="register.php">

            <div class="input-box">
                <input
                    type="text"
                    name="username"
                    class="input-field"
                    placeholder="Username"
                    value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="input-box">
                <input
                    type="email"
                    name="email"
                    class="input-field"
                    placeholder="Email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="input-box">
                <input
                    type="password"
                    name="password"
                    class="input-field"
                    placeholder="Password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="input-box">
                <input
                    type="password"
                    name="confirm"
                    class="input-field"
                    placeholder="Confirm Password"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="input-submit">
                <button type="submit" class="submit-btn">
                    Sign Up
                </button>
            </div>

        </form>

        <div class="sign-up-link">
            <p>
                Already have an account?
                <a href="login.php">Sign In</a>
            </p>
        </div>

    </div>

</body>
</html>
