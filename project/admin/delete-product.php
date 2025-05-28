<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid product ID";
    header('Location: products.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Get product image path before deleting
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $product = $result->fetch_assoc();
    $image_path = '../' . $product['image'];
    
    // Delete product from database
    $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_stmt->bind_param("i", $product_id);
    
    if ($delete_stmt->execute()) {
        // Delete image file if exists
        if (file_exists($image_path) && !is_dir($image_path)) {
            unlink($image_path);
        }
        
        $_SESSION['success_message'] = "Product deleted successfully";
    } else {
        $_SESSION['error_message'] = "Failed to delete product: " . $conn->error;
    }
    
    $delete_stmt->close();
} else {
    $_SESSION['error_message'] = "Product not found";
}

$stmt->close();

// Redirect back to products page
header('Location: products.php');
exit;
?>