<?php
/**
 * Get all categories from the database
 * 
 * @param mysqli $conn Database connection
 * @return array Array of categories
 */
function getAllCategories($conn) {
    $query = "SELECT * FROM categories ORDER BY name";
    $result = $conn->query($query);
    
    $categories = [];
    if ($result && $result->num_rows > 0) {
        $categories = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    return $categories;
}

/**
 * Get featured products
 * 
 * @param mysqli $conn Database connection
 * @param int $limit Number of products to retrieve
 * @return array Array of products
 */
function getFeaturedProducts($conn, $limit = 8) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              ORDER BY p.created_at DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    return $products;
}

/**
 * Get filtered products based on category, search term, and sale status
 * 
 * @param mysqli $conn Database connection
 * @param int|null $category_id Category ID to filter by
 * @param string $search_term Search term to filter by
 * @param int $sale_only Whether to show only products on sale
 * @return array Array of filtered products
 */
function getFilteredProducts($conn, $category_id = null, $search_term = '', $sale_only = 0) {
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
        $query .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }
    
    if ($sale_only) {
        $query .= " AND p.sale_price IS NOT NULL AND p.sale_price < p.price";
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    return $products;
}

/**
 * Get product by ID
 * 
 * @param mysqli $conn Database connection
 * @param int $product_id Product ID
 * @return array|null Product data or null if not found
 */
function getProductById($conn, $product_id) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Parse gallery images if available
        if (!empty($product['gallery'])) {
            $product['gallery'] = explode(',', $product['gallery']);
        } else {
            $product['gallery'] = [];
        }
        
        // Add sample data for demonstration
        $product['sizes'] = ['S', 'M', 'L', 'XL'];
        $product['colors'] = [
            'black' => '#000000',
            'white' => '#FFFFFF',
            'blue' => '#3E7CB1',
            'red' => '#D23C3C'
        ];
        $product['tags'] = ['Trendy', 'Casual', 'Urban'];
        
        return $product;
    }
    
    $stmt->close();
    return null;
}

/**
 * Get related products based on category
 * 
 * @param mysqli $conn Database connection
 * @param int $product_id Current product ID to exclude
 * @param int $category_id Category ID to match
 * @param int $limit Number of products to retrieve
 * @return array Array of related products
 */
function getRelatedProducts($conn, $product_id, $category_id, $limit = 4) {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.category_id = ? AND p.id != ? 
              ORDER BY RAND() 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $category_id, $product_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt->close();
    return $products;
}