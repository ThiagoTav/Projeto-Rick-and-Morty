<?php

// Busca um personagem pelo id que ele tem na API do Rick and Morty.
// Uso isso para checar se o personagem já foi salvo antes de mostrar o botão "Salvar".
function get_character_by_api_id(int $api_id): array|false
{
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM characters WHERE api_id = :api_id");
    $stmt->execute([':api_id' => $api_id]);
    return $stmt->fetch();
}

// Busca pelo id interno do banco (gerado pelo AUTOINCREMENT).
// Uso quando o usuário vem da página de personagens salvos, onde o id na URL é o do banco.
function get_character_by_id(int $id): array|false
{
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM characters WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Salva um personagem da API no banco local.
// Guardo o api_id junto para conseguir identificar se esse personagem já foi salvo
// quando o usuário visitar a página de detalhes vindo da home.
function save_character(int $api_id, string $name, string $species, string $image, string $url): bool
{
    $db   = get_db();
    $stmt = $db->prepare("
        INSERT INTO characters (api_id, name, species, image, url)
        VALUES (:api_id, :name, :species, :image, :url)
    ");
    return $stmt->execute([
        ':api_id'  => $api_id,
        ':name'    => $name,
        ':species' => $species,
        ':image'   => $image,
        ':url'     => $url,
    ]);
}

// Atualiza os dados de um personagem já salvo.
// Atualizo o updated_at manualmente porque o SQLite não faz isso automático como o MySQL.
function update_character(int $id, string $name, string $species, string $image, string $url): bool
{
    $db   = get_db();
    $stmt = $db->prepare("
        UPDATE characters
        SET name = :name, species = :species, image = :image, url = :url, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    return $stmt->execute([
        ':id'      => $id,
        ':name'    => $name,
        ':species' => $species,
        ':image'   => $image,
        ':url'     => $url,
    ]);
}

// Retorna todos os personagens salvos, do mais recente para o mais antigo.
// Aqui não preciso de prepared statement porque não tem nenhuma entrada do usuário na query.
function get_all_characters(): array
{
    $db = get_db();
    return $db->query("SELECT * FROM characters ORDER BY created_at DESC")->fetchAll();
}

function delete_character(int $id): bool
{
    $db   = get_db();
    $stmt = $db->prepare("DELETE FROM characters WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}
