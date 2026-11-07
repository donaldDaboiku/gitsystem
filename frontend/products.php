<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Products';
$pageDescription = 'Browse GITSystem products. Each item has details, pricing, and an enquiry button.';
$navCurrent = 'products';
require __DIR__ . '/includes/page-start.php';
?>
    <section class="products">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Catalog</span>
                <h2>Products</h2>
                <p>Items come from the admin catalog. Grey cards are not live yet.</p>
            </div>
            <div class="product-filters">
                <button class="filter-btn active" data-category="all">All Products</button>
            </div>
            <div class="products-grid">
                <p class="products-loading">Loading products…</p>
            </div>
        </div>
    </section>
<?php require __DIR__ . '/includes/page-end.php'; ?>
