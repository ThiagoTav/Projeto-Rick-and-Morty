<?php

function get_db(): PDO
{
    // O static faz a variável persistir entre chamadas na mesma requisição.
    // Sem isso, cada função que chama get_db() abriria uma nova conexão com o banco,
    // o que é desnecessário e mais pesado. Assim a conexão é criada uma vez só.
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);

        // ERRMODE_EXCEPTION faz o PDO lançar exceções em vez de retornar false silenciosamente.
        // Prefiro assim porque fica muito mais fácil identificar quando uma query deu errado.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // FETCH_ASSOC retorna os resultados como array associativo (ex: $row['name'])
        // em vez de array numérico (ex: $row[0]). Muito mais legível.
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        create_tables($pdo);
    }

    return $pdo;
}

function create_tables(PDO $pdo): void
{
    // IF NOT EXISTS garante que as tabelas só são criadas se ainda não existirem.
    // Assim não preciso me preocupar em rodar isso na primeira vez manualmente —
    // o próprio sistema cria o banco sozinho quando acessado pela primeira vez.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id        INTEGER PRIMARY KEY AUTOINCREMENT,
            name      TEXT    NOT NULL,
            email     TEXT    NOT NULL UNIQUE,
            password  TEXT    NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // SQLite não suporta ADD COLUMN IF NOT EXISTS, então verifico via PRAGMA
    // se a coluna já existe antes de tentar adicionar — evita erro em bancos antigos
    $columns = $pdo->query("PRAGMA table_info(users)")->fetchAll();
    $columnNames = array_column($columns, 'name');
    if (!in_array('profile_image', $columnNames)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_image TEXT");
    }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS characters (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            api_id     INTEGER,
            name       TEXT    NOT NULL,
            species    TEXT    NOT NULL,
            image      TEXT    NOT NULL,
            url        TEXT    NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}
