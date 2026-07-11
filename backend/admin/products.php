<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$pageTitle = 'Products';
$message = '';
$error = '';

$categories = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY display_order")->fetchAll();

// Delete product
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $img) {
        deleteImage($img['image_path']);
    }
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['admin_id'], 'delete_product', "Deleted product #$id");
    redirect('products.php?msg=deleted');
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Product deleted successfully.';
}

// Add or edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $name = sanitize($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $short_description = sanitize($_POST['short_description'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $badge = sanitize($_POST['badge'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $slug = generateSlug($name);

    if (empty($name) || $category_id <= 0 || $price <= 0) {
        $error = 'Name, category, and price are required.';
    } else {
        try {
            if ($id) {
                $stmt = $pdo->prepare("
                    UPDATE products SET category_id=?, name=?, slug=?, short_description=?, description=?,
                    price=?, original_price=?, stock_quantity=?, badge=?, is_featured=?, is_active=?
                    WHERE id=?
                ");
                $stmt->execute([$category_id, $name, $slug, $short_description, $description, $price, $original_price, $stock_quantity, $badge ?: null, $is_featured, $is_active, $id]);
                $productId = $id;
                logActivity($pdo, $_SESSION['admin_id'], 'update_product', "Updated product: $name");
                $message = 'Product updated successfully.';
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO products (category_id, name, slug, short_description, description, price, original_price, stock_quantity, badge, is_featured, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$category_id, $name, $slug, $short_description, $description, $price, $original_price, $stock_quantity, $badge ?: null, $is_featured, $is_active]);
                $productId = (int)$pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], 'add_product', "Added product: $name");
                $message = 'Product added successfully.';
            }

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $isFirst = true;
                foreach ($_FILES['images']['name'] as $i => $filename) {
                    if (empty($filename)) continue;
                    $file = [
                        'name' => $_FILES['images']['name'][$i],
                        'type' => $_FILES['images']['type'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'error' => $_FILES['images']['error'][$i],
                        'size' => $_FILES['images']['size'][$i],
                    ];
                    $result = uploadImage($file);
                    if ($result['success']) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary, display_order) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$productId, $result['path'], $isFirst ? 1 : 0, $i]);
                        $isFirst = false;
                    }
                }
            }

            if (!$id) {
                redirect('products.php?edit=' . $productId . '&msg=added');
            }
        } catch (PDOException $e) {
            $error = strpos($e->getMessage(), 'Duplicate') !== false
                ? 'A product with this name already exists.'
                : 'Failed to save product. Please try again.';
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'added') {
    $message = 'Product added successfully.';
}

$editProduct = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$products = $pdo->query("
    SELECT p.*, c.name as category_name,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
")->fetchAll();

$showForm = isset($_GET['action']) && $_GET['action'] === 'add' || $editProduct;

require __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($showForm): ?>
<div class="card">
    <h2><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= (int)$editProduct['id'] ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= ($editProduct['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="short_description">Short Description</label>
            <input type="text" id="short_description" name="short_description" maxlength="500" value="<?= htmlspecialchars($editProduct['short_description'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="description">Full Description</label>
            <textarea id="description" name="description"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="price">Price (₦) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="original_price">Original Price (₦)</label>
                <input type="number" id="original_price" name="original_price" step="0.01" min="0" value="<?= htmlspecialchars($editProduct['original_price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="stock_quantity">Stock</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?= htmlspecialchars($editProduct['stock_quantity'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label for="badge">Badge</label>
                <select id="badge" name="badge">
                    <option value="">None</option>
                    <?php foreach (['New', 'Sale', 'Popular'] as $b): ?>
                        <option value="<?= $b ?>" <?= ($editProduct['badge'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_featured" value="1" <?= ($editProduct['is_featured'] ?? 0) ? 'checked' : '' ?>>
                Featured product
            </label>
            &nbsp;&nbsp;
            <label>
                <input type="checkbox" name="is_active" value="1" <?= ($editProduct['is_active'] ?? 1) ? 'checked' : '' ?>>
                Active (visible on site)
            </label>
        </div>
        <div class="form-group">
            <label for="images">Product Images</label>
            <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            <small style="color: var(--text-muted);">First image becomes primary. Max 5MB each.</small>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary"><?= $editProduct ? 'Update Product' : 'Add Product' ?></button>
            <a href="products.php" class="btn" style="background: var(--dark); color: var(--text-muted); border: 1px solid var(--border);">Cancel</a>
        </div>
    </form>
</div>
<?php else: ?>
    <p style="margin-bottom: 1rem;"><a href="products.php?action=add" class="btn btn-primary">+ Add New Product</a></p>
<?php endif; ?>

<div class="card">
    <h2>All Products (<?= count($products) ?>)</h2>
    <?php if (empty($products)): ?>
        <p style="color: var(--text-muted);">No products yet. Add your first product above.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($p['name']) ?>
                        <?php if ($p['is_featured']): ?><span class="badge badge-new">Featured</span><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                    <td><?= formatPrice($p['price']) ?></td>
                    <td><?= (int)$p['stock_quantity'] ?></td>
                    <td><?= $p['is_active'] ? '<span class="badge badge-resolved">Active</span>' : '<span class="badge badge-spam">Inactive</span>' ?></td>
                    <td class="actions">
                        <a href="products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="products.php?delete=<?= (int)$p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
