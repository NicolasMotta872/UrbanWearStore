<?php
require_once '../config/database.php';

// Get search term from query string
$term = isset($_GET['term']) ? trim($_GET['term']) : '';

// Return empty response if term is empty or too short
if (empty($term) || strlen($term) < 2) {
    echo json_encode(['products' => []]);
    exit;
}

// Prepare query to search products
$search_param = "%{$term}%";
$query = "SELECT p.id, p.name, p.price, p.sale_price, p.image, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? 
          ORDER BY p.name 
          LIMIT 8";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['sale_price'] ? $row['sale_price'] : $row['price'],
            'image' => $row['image'],
            'category_name' => $row['category_name']
        ];
    }
}

$stmt->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['products' => $products]);
?>