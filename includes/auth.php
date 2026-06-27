<?php

function register_user(string $name, string $email, string $password): bool
{
    $db = get_db();

    // Nunca salvo a senha em texto puro. O password_hash gera um hash seguro
    // com salt automático. PASSWORD_DEFAULT usa o melhor algoritmo disponível
    // na versão atual do PHP (hoje é bcrypt).
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Uso prepared statement para evitar SQL injection.
    // Se eu concatenasse os valores direto na query, alguém poderia passar
    // algo como: ' OR '1'='1 e comprometer o banco inteiro.
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    return $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed]);
}

function is_email_taken(string $email): bool
{
    $db = get_db();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);

    // fetch() retorna false se não encontrar nenhum registro,
    // então basta verificar se o retorno é diferente de false
    return $stmt->fetch() !== false;
}

function login_user(string $email, string $password): bool
{
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // password_verify compara a senha digitada com o hash salvo no banco.
    // Não tem como reverter o hash — ele só verifica se os dois batem.
    if ($user && password_verify($password, $user['password'])) {
        // Salvo o id e o nome na sessão para não precisar ir ao banco
        // em toda requisição só para saber quem está logado
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    }

    return false;
}

function logout_user(): void
{
    // session_destroy() apaga todos os dados da sessão no servidor.
    // Assim o usuário perde o acesso imediatamente ao clicar em Sair.
    session_destroy();
}

function is_logged_in(): bool
{
    // Se user_id existe na sessão, significa que o login_user() foi bem-sucedido
    return isset($_SESSION['user_id']);
}
