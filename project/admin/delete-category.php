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
    $_SESSION['error_message'] = "Invalid category ID";
    header('Location: categories.php');
    exit;
}

$category_id = (int)$_GET['id'];

// Start transaction
$conn->begin_transaction();

try {
    // Get category image path before deleting
    $stmt = $conn->prepare("SELECT image FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $category = $result->fetch_assoc();
        $image_path = '../' . $category['image'];
        
        // Get product images for this category
        $product_stmt = $conn->prepare("SELECT image FROM products WHERE category_id = ?");
        $product_stmt->bind_param("i", $category_id);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        
        $product_images = [];
        while ($product = $product_result->fetch_assoc()) {
            $product_images[] = '../' . $product['image'];
        }
        
        // Delete products in this category
        $delete_products_stmt = $conn->prepare("DELETE FROM products WHERE category_id = ?");
        $delete_products_stmt->bind_param("i", $category_id);
        $delete_products_stmt->execute();
        
        // Delete the category
        $delete_category_stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $delete_category_stmt->bind_param("i", $category_id);
        $delete_category_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Delete image files
        if (file_exists($image_path) && !is_dir($image_path)) {
            unlink($image_path);
        }
        
        foreach ($product_images as $product_image) {
            if (file_exists($product_image) && !is_dir($product_image)) {
                unlink($product_image);
            }
        }
        
        $_SESSION['success_message'] = "Category and all its products deleted successfully";
    } else {
        throw new Exception("Category not found");
    }
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_message'] = "Failed to delete category: " . $e->getMessage();
}

// Redirect back to categories page
header('Location: categories.php');
exit;
?>