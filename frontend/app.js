const FALLBACK_PRODUCTS = [
    { id: 1, slug: 'gitinvoice', name: 'GITInvoice', category: 'software', short_description: 'Online invoicing & payment tracking for businesses', price: 35000, badge: 'Live', is_external_app: true, learn_url: 'https://git-invoice-three.vercel.app/', launch_url: 'https://git-invoice-three.vercel.app/app', logo_url: 'https://git-invoice-three.vercel.app/icons/icon-192-maskable.png', brand_color: '#ff6b35', price_label: 'Try free · 3 invoices' },
    { id: 20, slug: 'churchms', name: 'ChurchMS', category: 'software', short_description: 'Church management system for congregations', price: 1, badge: 'Live', is_external_app: true, learn_url: '', launch_url: '', logo_url: '', brand_color: '#109d48', price_label: 'Launch ChurchMS' },
    { id: 2, slug: 'sms-school-management-system', name: 'SMS - School Management System', category: 'software', short_description: 'School management software for schools', price: 850000, badge: 'New' },
    { id: 3, slug: 'hms-hospital-management-system', name: 'HMS - Hospital Management System', category: 'software', short_description: 'Hospital management software for hospitals', price: 1200000, badge: 'New' },
    { id: 4, slug: 'fsm-fashion-management-system', name: 'FSM - Fashion Management System', category: 'software', short_description: 'Fashion management software for fashion businesses', price: 500000, badge: 'New' },
    { id: 5, slug: 'dell-latitude-5420', name: 'Dell Latitude 5420', category: 'laptops', short_description: 'Intel Core i5, 16GB RAM, 512GB SSD', price: 450000, badge: 'New' },
    { id: 6, slug: 'hp-probook-450-g8', name: 'HP ProBook 450 G8', category: 'laptops', short_description: 'Intel Core i7, 16GB RAM, 1TB SSD', price: 580000, badge: null },
    { id: 7, slug: 'lenovo-thinkpad-t14', name: 'Lenovo ThinkPad T14', category: 'laptops', short_description: 'AMD Ryzen 7, 32GB RAM, 512GB SSD', price: 520000, badge: 'Sale' },
    { id: 8, slug: 'logitech-mx-master-3s', name: 'Logitech MX Master 3S', category: 'accessories', short_description: 'Ergonomic, 8K DPI, Multi-device', price: 45000, badge: null },
    { id: 9, slug: 'keychron-k8-pro', name: 'Keychron K8 Pro', category: 'accessories', short_description: 'Hot-swappable, RGB, Wireless', price: 65000, badge: 'Popular' },
    { id: 10, slug: 'sony-wh-1000xm5', name: 'Sony WH-1000XM5', category: 'accessories', short_description: 'Premium audio, 30hr battery', price: 180000, badge: null },
    { id: 11, slug: 'unifi-dream-machine', name: 'UniFi Dream Machine', category: 'networking', short_description: 'Enterprise router with WiFi 6', price: 250000, badge: null },
    { id: 12, slug: 'tp-link-omada-eap660-hd', name: 'TP-Link Omada EAP660 HD', category: 'networking', short_description: '3.6 Gbps, PoE, Ceiling Mount', price: 95000, badge: 'New' },
    { id: 13, slug: 'cisco-sg350-28p', name: 'Cisco SG350-28P', category: 'networking', short_description: '28-port Gigabit with 24 PoE+', price: 380000, badge: null },
    { id: 14, slug: 'nvidia-rtx-4060-ti', name: 'NVIDIA RTX 4060 Ti', category: 'components', short_description: '8GB GDDR6, Ray Tracing', price: 320000, badge: 'Sale' },
    { id: 15, slug: 'intel-core-i7-13700k', name: 'Intel Core i7-13700K', category: 'components', short_description: '16-core, 24-thread, 5.4GHz', price: 280000, badge: null },
    { id: 16, slug: 'samsung-990-pro', name: 'Samsung 990 PRO', category: 'components', short_description: '2TB, 7450MB/s read speed', price: 145000, badge: 'Popular' },
    { id: 17, slug: 'lg-ultragear-27-4k', name: 'LG UltraGear 27" 4K', category: 'peripherals', short_description: '144Hz, 1ms, HDR400', price: 380000, badge: null },
    { id: 18, slug: 'shure-mv7', name: 'Shure MV7', category: 'peripherals', short_description: 'Podcast & streaming quality', price: 125000, badge: 'New' },
    { id: 19, slug: 'logitech-c920-pro-hd', name: 'Logitech C920 Pro HD', category: 'peripherals', short_description: '1080p, auto-focus, stereo audio', price: 42000, badge: null }
];

let allProducts = [];
let activeCategory = 'all';

function buildProductCard(product) {
    const slug = product.slug || `product-${product.id}`;
    const badgeClass = product.badge?.toLowerCase() === 'sale' ? 'sale' : '';
    const badgeHtml = product.badge
        ? `<div class="product-badge ${badgeClass}">${escapeHtml(product.badge)}</div>`
        : '';
    const imageHtml = product.image
        ? `<img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}" loading="lazy">`
        : '<div class="placeholder-image">📄</div>';
    const categoryLabel = product.category
        ? product.category.charAt(0).toUpperCase() + product.category.slice(1)
        : '';

    if (isExternalApp(product) || isGitInvoiceProduct(slug)) {
        const links = getExternalAppLinks(product);
        const color = links.color;
        if (!links.learn && !links.launch) {
            // External app configured but URLs not set yet
            return `
            <article class="product-card product-card-external" data-category="${escapeHtml(product.category)}" style="--app-brand:${escapeHtml(color)}">
                <div class="product-card-link">
                    <div class="product-badge" style="background:${escapeHtml(color)}">${escapeHtml(product.badge || 'Soon')}</div>
                    <div class="product-image product-image-external">
                        <div class="external-card-visual external-card-visual-empty"><span>${escapeHtml(product.name.charAt(0))}</span></div>
                    </div>
                    <div class="product-info">
                        <p class="product-category" style="color:${escapeHtml(color)}">Software</p>
                        <h3>${escapeHtml(product.name)}</h3>
                        <p class="product-desc">${escapeHtml(product.short_description || '')}</p>
                        <div class="product-footer">
                            <span class="product-price" style="color:${escapeHtml(color)}">Coming soon</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-inquire-quick" data-product-id="${product.id}" data-product-name="${escapeHtml(product.name)}" data-product-slug="${escapeHtml(slug)}">Quick Inquiry</button>
            </article>`;
        }
        const isGit = isGitInvoiceProduct(slug);
        const titleHtml = isGit
            ? gitInvoiceWordmarkHtml(32)
            : escapeHtml(product.name);
        const visual = links.logo
            ? `<div class="external-card-visual"><img src="${escapeHtml(links.logo)}" alt="${escapeHtml(product.name)}" class="external-app-logo" loading="lazy"></div>`
            : `<div class="external-card-visual external-card-visual-empty"><span>${escapeHtml(product.name.charAt(0))}</span></div>`;
        const liveBadge = product.badge
            ? `<div class="product-badge" style="background:${escapeHtml(color)}">${escapeHtml(product.badge)}</div>`
            : `<div class="product-badge" style="background:${escapeHtml(color)}">Live</div>`;

        return `
        <article class="product-card product-card-external ${isGit ? 'product-card-gitinvoice' : ''}" data-category="${escapeHtml(product.category)}" style="--app-brand:${escapeHtml(color)}">
            <a href="${escapeHtml(links.learn)}" class="product-card-link" target="_blank" rel="noopener noreferrer">
                ${liveBadge}
                <div class="product-image product-image-external">${visual}
                    <div class="product-overlay product-overlay-external">
                        <span class="view-details">Learn more →</span>
                    </div>
                </div>
                <div class="product-info">
                    <p class="product-category" style="color:${escapeHtml(color)}">Software</p>
                    <h3 class="external-app-title">${titleHtml}</h3>
                    <p class="product-desc">${escapeHtml(product.short_description || '')}</p>
                    <div class="product-footer">
                        <span class="product-price" style="color:${escapeHtml(color)}">${escapeHtml(links.priceLabel)}</span>
                    </div>
                </div>
            </a>
            <div class="product-external-actions">
                <a href="${escapeHtml(links.learn)}" class="btn-external-ghost" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Learn more</a>
                <a href="${escapeHtml(links.launch)}" class="btn-external-primary" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Launch app</a>
            </div>
        </article>`;
    }

    const url = productUrl(slug);
    return `
        <article class="product-card" data-category="${escapeHtml(product.category)}">
            <a href="${url}" class="product-card-link" target="_blank" rel="noopener noreferrer">
                ${badgeHtml}
                <div class="product-image">${imageHtml}
                    <div class="product-overlay">
                        <span class="view-details">View Details →</span>
                    </div>
                </div>
                <div class="product-info">
                    <p class="product-category">${escapeHtml(categoryLabel)}</p>
                    <h3>${escapeHtml(product.name)}</h3>
                    <p class="product-desc">${escapeHtml(product.short_description || '')}</p>
                    <div class="product-footer">
                        <span class="product-price">${formatPrice(product.price)}</span>
                    </div>
                </div>
            </a>
            <button type="button" class="btn-inquire-quick" data-product-id="${product.id}" data-product-name="${escapeHtml(product.name)}" data-product-slug="${escapeHtml(slug)}">Quick Inquiry</button>
        </article>`;
}

function renderProducts(products) {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;

    const filtered = activeCategory === 'all'
        ? products
        : products.filter(p => p.category === activeCategory);

    if (filtered.length === 0) {
        grid.innerHTML = '<p class="products-empty">No products in this category yet.</p>';
        return;
    }

    grid.innerHTML = filtered.map(buildProductCard).join('');

    grid.querySelectorAll('.btn-inquire-quick').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            inquireProduct(btn.dataset.productId, btn.dataset.productName);
        });
    });
}

function renderCategoryFilters(categories) {
    const container = document.querySelector('.product-filters');
    if (!container || !categories?.length) return;

    container.innerHTML = [
        '<button class="filter-btn active" data-category="all">All Products</button>',
        ...categories.map(c =>
            `<button class="filter-btn" data-category="${escapeHtml(c.slug)}">${escapeHtml(c.name)}</button>`
        )
    ].join('');
    container.dataset.bound = '0';
    bindFilterButtons();
}

function bindFilterButtons() {
    const container = document.querySelector('.product-filters');
    if (!container || container.dataset.bound === '1') return;
    container.dataset.bound = '1';

    container.addEventListener('click', (e) => {
        const btn = e.target.closest('.filter-btn');
        if (!btn) return;
        container.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeCategory = btn.dataset.category;
        renderProducts(allProducts);
    });
}

function renderSoftwareShowcase(products) {
    const grid = document.getElementById('softwareGrid');
    if (!grid) return;

    const software = products.filter(p => p.category === 'software');
    if (!software.length) {
        grid.innerHTML = '<p class="products-empty">No software products yet.</p>';
        return;
    }

    const icons = {
        'sms-school-management-system': '🎓',
        'hms-hospital-management-system': '🏥',
        'fsm-fashion-management-system': '👗',
        churchms: '⛪',
        gitinvoice: '🧾',
    };

    grid.innerHTML = software.map(product => {
        if (isExternalApp(product) || isGitInvoiceProduct(product.slug)) {
            const links = getExternalAppLinks(product);
            const color = links.color;
            const isGit = isGitInvoiceProduct(product.slug);
            const title = isGit
                ? gitInvoiceWordmarkHtml(40)
                : `<h3 style="margin:0">${escapeHtml(product.name)}</h3>`;
            const logoBlock = links.logo
                ? `<img src="${escapeHtml(links.logo)}" alt="" class="external-app-logo" width="48" height="48" loading="lazy">`
                : `<span class="software-icon">${icons[product.slug] || '💻'}</span>`;

            // Hide launch/learn if URLs not set yet (e.g. ChurchMS before admin fills them)
            if (!links.learn && !links.launch) {
                return `
                <div class="software-card software-card-external" style="--app-brand:${escapeHtml(color)}">
                    ${logoBlock}
                    <h3>${escapeHtml(product.name)}</h3>
                    <p>${escapeHtml(product.short_description || '')}</p>
                    <span class="software-link">Add Learn/Launch URLs in Admin →</span>
                </div>`;
            }

            return `
            <div class="software-card software-card-external ${isGit ? 'software-card-gitinvoice' : ''}" style="--app-brand:${escapeHtml(color)}">
                <div class="external-software-head">${isGit ? title : `${logoBlock}${title}`}</div>
                <p>${escapeHtml(product.short_description || '')}</p>
                <div class="software-actions">
                    <a href="${escapeHtml(links.learn)}" class="btn-external-ghost btn-sm" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Learn more</a>
                    <a href="${escapeHtml(links.launch)}" class="btn-external-primary btn-sm" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Launch ${escapeHtml(product.name)}</a>
                </div>
            </div>`;
        }

        return `
        <a href="${productUrl(product.slug)}" class="software-card" target="_blank" rel="noopener noreferrer">
            <span class="software-icon">${icons[product.slug] || '💻'}</span>
            <h3>${escapeHtml(product.name)}</h3>
            <p>${escapeHtml(product.short_description || '')}</p>
            <span class="software-link">View landing page →</span>
        </a>`;
    }).join('');
}

async function loadProducts() {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;

    grid.innerHTML = '<p class="products-loading">Loading products…</p>';

    try {
        const productsRes = await fetch(`${API_BASE}?action=products`);
        if (!productsRes.ok) throw new Error(`HTTP ${productsRes.status}`);

        const productsData = await productsRes.json();

        let categoriesData = null;
        try {
            const categoriesRes = await fetch(`${API_BASE}?action=categories`);
            if (categoriesRes.ok) categoriesData = await categoriesRes.json();
        } catch { /* categories are optional */ }

        if (productsData.success && productsData.data?.length) {
            allProducts = productsData.data;
            if (categoriesData?.success) renderCategoryFilters(categoriesData.data);
        } else {
            allProducts = FALLBACK_PRODUCTS;
        }
    } catch (err) {
        console.warn('Using fallback products:', err.message);
        allProducts = FALLBACK_PRODUCTS;
    }

    bindFilterButtons();
    renderProducts(allProducts);
    renderSoftwareShowcase(allProducts);
}

function inquireProduct(productId, productName) {
    const productField = document.getElementById('product_id');
    const messageField = document.getElementById('message');
    if (productField) productField.value = productId;
    if (messageField && productName) {
        messageField.value = `I'm interested in: ${productName}\n\n`;
        messageField.focus();
    }
    document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' });
}

const contactForm = document.getElementById('contactForm');
contactForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitInquiry(contactForm);
});

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.service-card, .tech-card, .feature-box, .section-header').forEach(el => {
    el.classList.add('reveal-on-scroll');
    observer.observe(el);
});

function animateCounter(element, target, duration = 2000) {
    let current = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + '+';
    }, 16);
}

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            document.querySelectorAll('.stat-number').forEach(stat => {
                const raw = stat.dataset.target || stat.textContent.replace('+', '');
                if (raw === '24' || raw.includes('24')) {
                    stat.textContent = '24/7';
                } else {
                    animateCounter(stat, parseInt(raw, 10));
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

const statsSection = document.querySelector('.stats');
if (statsSection) statsObserver.observe(statsSection);

bindFilterButtons();
document.addEventListener('DOMContentLoaded', loadProducts);
if (document.readyState !== 'loading') loadProducts();
