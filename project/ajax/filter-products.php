<?php
require_once '../config/database.php';

// Check if request is AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Get filter parameters
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category_id = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
    
    // Prepare query
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($category_id) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }
    
    if (!empty($search_term)) {
        $search_param = "%{$search_term}%";
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }
    
    $query .= " ORDER BY p.name";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    $stmt->close();
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['products' => $products]);
    exit;
}

// If not AJAX request, redirect to products page
header('Location: ../admin/products.php');
exit;
?>