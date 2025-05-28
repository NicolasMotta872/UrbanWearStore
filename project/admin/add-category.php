<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Category name is required";
    }
    
    // Check if category name already exists
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "This category name already exists";
    }
    $stmt->close();
    
    // Process image upload
    $image = '';
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
            $upload_dir = '../uploads/categories/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . $_FILES['image']['name'];
            $destination = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = 'uploads/categories/' . $file_name;
            } else {
                $errors[] = "Failed to upload the image";
            }
        }
    } else {
        $errors[] = "Category image is required";
    }
    
    // If no errors, insert the category
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $description, $image);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category added successfully";
            header('Location: categories.php');
            exit;
        } else {
            $errors[] = "Failed to add the category: " . $conn->error;
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
    <title>Add Category - UrbanWear Admin</title>
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
                    <h1>Add New Category</h1>
                    <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
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

                <form action="add-category.php" method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" id="name" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Category Image *</label>
                        <div class="file-input">
                            <input type="file" id="image" name="image" accept="image/*" required>
                            <div class="file-preview">
                                <img id="imagePreview" src="#" alt="Image Preview" style="display: none;">
                            </div>
                        </div>
                        <p class="form-hint">Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Category</button>
                        <a href="categories.php" class="btn btn-secondary">Cancel</a>
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