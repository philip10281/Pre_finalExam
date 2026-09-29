<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$stmt = $pdo->query(
    "SELECT id, item_number, category, category_name, name, description, price, icon, color, image
     FROM items
     ORDER BY category, item_number"
);

$rows = $stmt->fetchAll();

$items = array_map(function ($row) {
    return [
        'dbId'         => (int) $row['id'],
        'id'           => (int) $row['item_number'],
        'category'     => $row['category'],
        'categoryName' => $row['category_name'],
        'name'         => $row['name'],
        'desc'         => $row['description'],
        'price'        => (float) $row['price'],
        'icon'         => $row['icon'],
        'color'        => $row['color'],
        'img'          => $row['image'],
    ];
}, $rows);

echo json_encode($items);
