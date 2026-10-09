<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
// Handles user authentication and session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Returns logged in user or null if not logged in.
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

// Sends them back to login.php if not logged in, otherwise returns the user.
function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        header('Location: login.php');
        exit;
    }
    return $user;
}
// Logs in the user by storing their information in the session.
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

// helper for escaping output in HTML.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}