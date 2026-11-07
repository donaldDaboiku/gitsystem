<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$pageTitle = 'Products';
$message = '';
$error = '';

$categories = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY display_order")->fetchAll();

// Delete product (POST + CSRF only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    require_csrf_from_post();
    $id = (int)$_POST['delete_id'];
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT image_path, webp_path FROM product_images WHERE product_id = ?');
        $stmt->execute([$id]);
        foreach ($stmt->fetchAll() as $img) {
            deleteImage($img['image_path'], $img['webp_path'] ?? null);
        }
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        logActivity($pdo, $_SESSION['admin_id'], 'delete_product', "Deleted product #$id");
        redirect('products.php?msg=deleted');
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Product deleted successfully.';
}

// Add or edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    require_csrf_from_post();
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
    $is_external_app = isset($_POST['is_external_app']) ? 1 : 0;
    $learn_url = trim($_POST['learn_url'] ?? '');
    $launch_url = trim($_POST['launch_url'] ?? '');
    $logo_url = trim($_POST['logo_url'] ?? '');
    $brand_color = trim($_POST['brand_color'] ?? '#109d48') ?: '#109d48';
    $price_label = sanitize($_POST['price_label'] ?? '');
    $slug = generateSlug($name);

    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $brand_color)) {
        $brand_color = '#109d48';
    }

    if (empty($name) || $category_id <= 0 || ($price < 0) || (!$is_external_app && $price <= 0)) {
        $error = 'Name, category, and a valid price are required.';
    } else {
        try {
            if ($id) {
                $stmt = $pdo->prepare("
                    UPDATE products SET category_id=?, name=?, slug=?, short_description=?, description=?,
                    price=?, original_price=?, stock_quantity=?, badge=?, is_featured=?, is_active=?,
                    is_external_app=?, learn_url=?, launch_url=?, logo_url=?, brand_color=?, price_label=?
                    WHERE id=?
                ");
                $stmt->execute([
                    $category_id, $name, $slug, $short_description, $description,
                    $price, $original_price, $stock_quantity, $badge ?: null, $is_featured, $is_active,
                    $is_external_app, $learn_url ?: null, $launch_url ?: null, $logo_url ?: null,
                    $brand_color, $price_label ?: null, $id
                ]);
                $productId = $id;
                logActivity($pdo, $_SESSION['admin_id'], 'update_product', "Updated product: $name");
                $message = 'Product updated successfully.';
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO products (category_id, name, slug, short_description, description, price, original_price, stock_quantity, badge, is_featured, is_active, is_external_app, learn_url, launch_url, logo_url, brand_color, price_label)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $category_id, $name, $slug, $short_description, $description,
                    $price, $original_price, $stock_quantity, $badge ?: null, $is_featured, $is_active,
                    $is_external_app, $learn_url ?: null, $launch_url ?: null, $logo_url ?: null,
                    $brand_color, $price_label ?: null
                ]);
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
                        $stmt = $pdo->prepare('INSERT INTO product_images (product_id, image_path, webp_path, is_primary, display_order) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([$productId, $result['path'], $result['webp_path'] ?? null, $isFirst ? 1 : 0, $i]);
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
                : (APP_DEBUG ? $e->getMessage() : 'Failed to save product. Please try again.');
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

$perPage = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalPages = max(1, (int)ceil($totalProducts / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name,
           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

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
        <?= csrf_field() ?>
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
                    <?php foreach (['New', 'Sale', 'Popular', 'Live'] as $b): ?>
                        <option value="<?= $b ?>" <?= ($editProduct['badge'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card" style="background: rgba(16,157,72,0.06); border: 1px solid var(--border); margin-bottom: 1.25rem; padding: 1.25rem;">
            <h3 style="margin-bottom: 0.5rem; color: var(--primary); font-size: 1rem;">Launch App (like GITInvoice / ChurchMS)</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                Turn this product into an external app card with Learn more + Launch buttons. No code changes needed for future apps.
            </p>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_external_app" value="1" id="is_external_app" <?= ($editProduct['is_external_app'] ?? 0) ? 'checked' : '' ?>>
                    This is an external launch app
                </label>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="learn_url">Learn more URL</label>
                    <input type="url" id="learn_url" name="learn_url" placeholder="https://your-app.vercel.app/" value="<?= htmlspecialchars($editProduct['learn_url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="launch_url">Launch app URL</label>
                    <input type="url" id="launch_url" name="launch_url" placeholder="https://your-app.vercel.app/app" value="<?= htmlspecialchars($editProduct['launch_url'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="logo_url">Logo image URL (optional)</label>
                    <input type="url" id="logo_url" name="logo_url" placeholder="https://…/icon.png" value="<?= htmlspecialchars($editProduct['logo_url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="brand_color">Brand color</label>
                    <input type="text" id="brand_color" name="brand_color" placeholder="#109d48" value="<?= htmlspecialchars($editProduct['brand_color'] ?? '#109d48') ?>">
                </div>
                <div class="form-group">
                    <label for="price_label">Price / CTA label</label>
                    <input type="text" id="price_label" name="price_label" placeholder="Launch app" value="<?= htmlspecialchars($editProduct['price_label'] ?? '') ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_featured" value="1" <?= ($editProduct['is_featured'] ?? 0) ? 'checked' : '' ?>>
                Featured product
            </label>
            &nbsp;&nbsp;
            <label>
                <input type="checkbox" name="is_active" value="1" <?= !isset($editProduct) || ($editProduct['is_active'] ?? 1) ? 'checked' : '' ?>>
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
    <h2>All Products (<?= $totalProducts ?>)</h2>
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
                <?php
                    $hasLaunch = trim((string)($p['learn_url'] ?? '')) !== '' || trim((string)($p['launch_url'] ?? '')) !== '';
                    $isLive = strcasecmp((string)($p['badge'] ?? ''), 'Live') === 0 && (empty($p['is_external_app']) || $hasLaunch);
                ?>
                <tr class="<?= $isLive ? '' : 'product-not-live' ?>">
                    <td>
                        <?= htmlspecialchars($p['name']) ?>
                        <?php if ($p['is_featured']): ?><span class="badge badge-new">Featured</span><?php endif; ?>
                        <?php if (!empty($p['is_external_app'])): ?><span class="badge badge-resolved">Launch app</span><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                    <td><?= formatPrice($p['price']) ?></td>
                    <td><?= (int)$p['stock_quantity'] ?></td>
                    <td><?= $p['is_active'] ? '<span class="badge badge-resolved">Active</span>' : '<span class="badge badge-spam">Inactive</span>' ?></td>
                    <td class="actions">
                        <a href="products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <form method="POST" class="form-inline" data-confirm="Delete this product?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Product pages">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-sm">← Previous</a>
            <?php endif; ?>
            <span class="pagination-info">Page <?= $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-sm btn-primary">Next →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
