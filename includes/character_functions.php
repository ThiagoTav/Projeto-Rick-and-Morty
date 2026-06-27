<?php

function get_character_by_api_id(int $api_id): array|false
{
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM characters WHERE api_id = :api_id");
    $stmt->execute([':api_id' => $api_id]);
    return $stmt->fetch();
}

function get_character_by_id(int $id): array|false
{
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM characters WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

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

function delete_character(int $id): bool
{
    $db   = get_db();
    $stmt = $db->prepare("DELETE FROM characters WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}
