<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
    exit;
}

// Basahin ang data (support both JSON body at normal form POST)
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$category     = trim($input['category'] ?? '');
$categoryName = trim($input['categoryName'] ?? '');
$name         = trim($input['name'] ?? '');
$desc         = trim($input['desc'] ?? '');
$price        = $input['price'] ?? null;
$icon         = trim($input['icon'] ?? 'fa-box');
$color        = trim($input['color'] ?? 'from-slate-500/20 to-slate-400/20 text-slate-600');
$image        = trim($input['img'] ?? '');

// Simpleng validation
$errors = [];
if ($category === '')      $errors[] = 'Category is required.';
if ($categoryName === '')  $errors[] = 'Category name is required.';
if ($name === '')          $errors[] = 'Name is required.';
if (!is_numeric($price))   $errors[] = 'Price must be a number.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Kunin ang susunod na item_number para sa category na ito (auto increment per category)
$stmt = $pdo->prepare("SELECT COALESCE(MAX(item_number), 0) + 1 AS next_num FROM items WHERE category = ?");
$stmt->execute([$category]);
$nextNum = (int) $stmt->fetch()['next_num'];

$insert = $pdo->prepare(
    "INSERT INTO items (item_number, category, category_name, name, description, price, icon, color, image)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$insert->execute([
    $nextNum,
    $category,
    $categoryName,
    $name,
    $desc,
    (float) $price,
    $icon,
    $color,
    $image,
]);

echo json_encode([
    'success' => true,
    'message' => 'Item added successfully.',
    'id'      => (int) $pdo->lastInsertId(),
    'item_number' => $nextNum,
]);
