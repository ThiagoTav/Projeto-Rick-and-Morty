<?php

function register_user(string $name, string $email, string $password): bool
{
    $db = get_db();
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    return $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed]);
}

function is_email_taken(string $email): bool
{
    $db = get_db();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch() !== false;
}

function login_user(string $email, string $password): bool
{
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    }

    return false;
}

function logout_user(): void
{
    session_destroy();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}
