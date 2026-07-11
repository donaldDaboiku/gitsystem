<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once 'config.php';

$action = $_GET['action'] ?? 'products';
$category = $_GET['category'] ?? 'all';

try {
    if ($action === 'products') {
        // Build query
        $sql = "
            SELECT p.*, c.slug as category_slug,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
        ";
        
        if ($category !== 'all') {
            $sql .= " AND c.slug = :category";
        }
        
        $sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        
        if ($category !== 'all') {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        // Format products for frontend
        $formatted_products = [];
        foreach ($products as $product) {
            $formatted_products[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'category' => $product['category_slug'],
                'short_description' => $product['short_description'],
                'price' => (float)$product['price'],
                'original_price' => $product['original_price'] ? (float)$product['original_price'] : null,
                'badge' => $product['badge'],
                'image' => $product['primary_image'] ? UPLOAD_URL . $product['primary_image'] : null,
                'is_featured' => (bool)$product['is_featured']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $formatted_products,
            'count' => count($formatted_products)
        ]);
        
    } elseif ($action === 'product') {
        $id = $_GET['id'] ?? null;
        $slug = $_GET['slug'] ?? null;

        if ($slug) {
            $stmt = $pdo->prepare("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.is_active = 1
            ");
            $stmt->execute([$slug]);
        } elseif ($id) {
            $stmt = $pdo->prepare("
                SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? AND p.is_active = 1
            ");
            $stmt->execute([$id]);
        } else {
            throw new Exception('Product ID or slug required');
        }

        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception('Product not found');
        }

        $productId = (int)$product['id'];

        $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order, is_primary DESC");
        $stmt->execute([$productId]);
        $images = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT spec_name, spec_value FROM product_specs WHERE product_id = ? ORDER BY display_order");
        $stmt->execute([$productId]);
        $specs = $stmt->fetchAll();

        $stmt = $pdo->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.badge, p.short_description,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
            ORDER BY p.is_featured DESC, p.created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$product['category_id'], $productId]);
        $related = $stmt->fetchAll();

        $stmt = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        $stmt->execute([$productId]);

        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $productId,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'category' => $product['category_name'],
                'category_slug' => $product['category_slug'],
                'description' => $product['description'],
                'short_description' => $product['short_description'],
                'price' => (float)$product['price'],
                'original_price' => $product['original_price'] ? (float)$product['original_price'] : null,
                'stock' => (int)$product['stock_quantity'],
                'badge' => $product['badge'],
                'views' => (int)$product['views'] + 1,
                'images' => array_map(function($img) {
                    return UPLOAD_URL . $img['image_path'];
                }, $images),
                'specs' => $specs,
                'related' => array_map(function($r) {
                    return [
                        'id' => (int)$r['id'],
                        'name' => $r['name'],
                        'slug' => $r['slug'],
                        'price' => (float)$r['price'],
                        'badge' => $r['badge'],
                        'short_description' => $r['short_description'],
                        'image' => $r['primary_image'] ? UPLOAD_URL . $r['primary_image'] : null,
                    ];
                }, $related),
            ]
        ]);
        
    } elseif ($action === 'categories') {
        // Get all active categories
        $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order");
        $categories = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        
    } elseif ($action === 'settings') {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        echo json_encode([
            'success' => true,
            'data' => $settings
        ]);

    } elseif ($action === 'submit_inquiry') {
        // Handle contact form submission
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('POST request required');
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $name = sanitize($data['name'] ?? '');
        $email = sanitize($data['email'] ?? '');
        $phone = sanitize($data['phone'] ?? '');
        $service = sanitize($data['service'] ?? '');
        $message = sanitize($data['message'] ?? '');
        $product_id = !empty($data['product_id']) ? (int)$data['product_id'] : null;
        
        if (empty($name) || empty($email) || empty($message)) {
            throw new Exception('Name, email, and message are required');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, service, product_id, message, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $service, $product_id, $message, $ip]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your inquiry! We will get back to you soon.'
        ]);
        
    } else {
        throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
