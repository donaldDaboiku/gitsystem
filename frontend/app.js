let allProducts = [];
let activeCategory = 'all';

function isProductLive(product) {
    if ((product.badge || '').toLowerCase() !== 'live') return false;
    if (isExternalApp(product) || isGitInvoiceProduct(product.slug)) {
        const links = getExternalAppLinks(product);
        return Boolean(links.learn || links.launch);
    }
    return true;
}

function buildProductCard(product) {
    const slug = product.slug || `product-${product.id}`;
    const badgeClass = product.badge?.toLowerCase() === 'sale' ? 'sale' : '';
    const badgeHtml = product.badge
        ? `<div class="product-badge ${badgeClass}">${escapeHtml(product.badge)}</div>`
        : '';
    const imageHtml = product.image
        ? pictureHtml(product.image, product.image_webp, product.name)
        : '<div class="placeholder-image">📄</div>';
    const categoryLabel = product.category
        ? product.category.charAt(0).toUpperCase() + product.category.slice(1)
        : '';

    if (isExternalApp(product) || isGitInvoiceProduct(slug)) {
        const links = getExternalAppLinks(product);
        const color = links.color;
        if (!links.learn && !links.launch) {
            return `
            <article class="product-card product-card-external is-upcoming" data-category="${escapeHtml(product.category)}" style="--app-brand:${escapeHtml(color)}">
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

        const upcoming = isProductLive(product) ? '' : ' is-upcoming';
        return `
        <article class="product-card product-card-external ${isGit ? 'product-card-gitinvoice' : ''}${upcoming}" data-category="${escapeHtml(product.category)}" style="--app-brand:${escapeHtml(color)}">
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
    const upcoming = isProductLive(product) ? '' : ' is-upcoming';
    return `
        <article class="product-card${upcoming}" data-category="${escapeHtml(product.category)}">
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
                <div class="software-card software-card-external is-upcoming" style="--app-brand:${escapeHtml(color)}">
                    ${logoBlock}
                    <h3>${escapeHtml(product.name)}</h3>
                    <p>${escapeHtml(product.short_description || '')}</p>
                    <span class="software-link">Add Learn/Launch URLs in Admin →</span>
                </div>`;
            }

            const upcoming = isProductLive(product) ? '' : ' is-upcoming';
            return `
            <div class="software-card software-card-external ${isGit ? 'software-card-gitinvoice' : ''}${upcoming}" style="--app-brand:${escapeHtml(color)}">
                <div class="external-software-head">${isGit ? title : `${logoBlock}${title}`}</div>
                <p>${escapeHtml(product.short_description || '')}</p>
                <div class="software-actions">
                    <a href="${escapeHtml(links.learn)}" class="btn-external-ghost btn-sm" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Learn more</a>
                    <a href="${escapeHtml(links.launch)}" class="btn-external-primary btn-sm" style="--app-brand:${escapeHtml(color)}" target="_blank" rel="noopener noreferrer">Launch ${escapeHtml(product.name)}</a>
                </div>
            </div>`;
        }

        const upcoming = isProductLive(product) ? '' : ' is-upcoming';
        return `
        <a href="${productUrl(product.slug)}" class="software-card${upcoming}" target="_blank" rel="noopener noreferrer">
            <span class="software-icon">${icons[product.slug] || '💻'}</span>
            <h3>${escapeHtml(product.name)}</h3>
            <p>${escapeHtml(product.short_description || '')}</p>
            <span class="software-link">View landing page →</span>
        </a>`;
    }).join('');
}

async function loadProducts() {
    const grid = document.querySelector('.products-grid');
    const software = document.getElementById('softwareGrid');
    if (!grid && !software) return;

    if (grid) grid.innerHTML = '<p class="products-loading">Loading products…</p>';

    try {
        const productsRes = await fetch(`${API_BASE}?action=products`);
        if (!productsRes.ok) throw new Error(`HTTP ${productsRes.status}`);

        const productsData = await productsRes.json();

        let categoriesData = null;
        try {
            const categoriesRes = await fetch(`${API_BASE}?action=categories`);
            if (categoriesRes.ok) categoriesData = await categoriesRes.json();
        } catch { /* categories are optional */ }

        if (!productsData.success) {
            throw new Error(productsData.error || 'Products unavailable');
        }
        allProducts = productsData.data || [];
        const used = new Set(allProducts.map(p => p.category));
        const categories = (categoriesData?.data || []).filter(c => used.has(c.slug));
        if (grid && categories.length) renderCategoryFilters(categories);
    } catch {
        allProducts = [];
        if (grid) grid.innerHTML = '<p class="products-empty">Products could not be loaded. Please try again.</p>';
        if (software) software.innerHTML = '<p class="products-empty">Products could not be loaded. Please try again.</p>';
        return;
    }

    if (grid) {
        bindFilterButtons();
        renderProducts(allProducts);
    }
    if (software) renderSoftwareShowcase(allProducts);
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
