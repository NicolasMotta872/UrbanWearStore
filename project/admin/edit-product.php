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

// Get all categories for the dropdown
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_query);
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Get product data
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error_message'] = "Product not found";
    header('Location: products.php');
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $sku = trim($_POST['sku']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $category_id = (int)$_POST['category_id'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    
    if (empty($sku)) {
        $errors[] = "SKU is required";
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    
    if ($sale_price !== null && $sale_price >= $price) {
        $errors[] = "Sale price must be less than regular price";
    }
    
    if ($category_id <= 0) {
        $errors[] = "Please select a category";
    }
    
    // Check if SKU already exists (excluding current product)
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ? AND id != ?");
    $stmt->bind_param("si", $sku, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "This SKU already exists";
    }
    $stmt->close();
    
    // Process image upload if a new image is provided
    $image = $product['image']; // Use existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        }
        
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) { // 2MB
            $errors[] = "File size exceeds the limit (2MB)";
        }
        
        if (empty($errors)) {
            $upload_dir = '../uploads/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['image']['name'];
            $destination = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Delete old image if exists and different
                if (!empty($product['image']) && file_exists('../' . $product['image']) && !is_dir('../' . $product['image'])) {
                    unlink('../' . $product['image']);
                }
                
                $image = 'uploads/products/' . $file_name;
            } else {
                $errors[] = "Failed to upload the image";
            }
        }
    }
    
    // If no errors, update the product
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, sku = ?, description = ?, price = ?, sale_price = ?, category_id = ?, image = ?, stock_quantity = ? WHERE id = ?");
        $stmt->bind_param("sssddisii", $name, $sku, $description, $price, $sale_price, $category_id, $image, $stock_quantity, $product_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Product updated successfully";
            header('Location: products.php');
            exit;
        } else {
            $errors[] = "Failed to update the product: " . $conn->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - UrbanWear Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="admin-content">
            <?php include 'includes/header.php'; ?>

            <main>
                <div class="page-header">
                    <h1>Edit Product</h1>
                    <a href="products.php" class="btn btn-secondary">Back to Products</a>
                </div>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form action="edit-product.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Product Name *</label>
                            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sku">SKU *</label>
                            <input type="text" id="sku" name="sku" required value="<?= htmlspecialchars($product['sku']) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price ($) *</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($product['price']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sale_price">Sale Price ($)</label>
                            <input type="number" id="sale_price" name="sale_price" step="0.01" min="0" value="<?= $product['sale_price'] ? htmlspecialchars($product['sale_price']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= $category['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity *</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" required value="<?= htmlspecialchars($product['stock_quantity']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <div class="current-image">
                            <p>Current image:</p>
                            <img src="../<?= $product['image'] ?>" alt="<?= $product['name'] ?>" style="max-width: 200px; max-height: 200px; margin-bottom: 10px;">
                        </div>
                        <div class="file-input">
                            <input type="file" id="image" name="image" accept="image/*">
                            <div class="file-preview">
                                <img id="imagePreview" src="#" alt="New Image Preview" style="display: none;">
                            </div>
                        </div>
                        <p class="form-hint">Leave empty to keep the current image. Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>