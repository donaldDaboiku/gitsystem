const CATEGORY_ICONS = {
    software: '💻',
    laptops: '💻',
    accessories: '🖱️',
    networking: '🌐',
    components: '⚡',
    peripherals: '🖥️'
};

const SOFTWARE_FEATURES = [
    { icon: '🚀', title: 'Fast Deployment', desc: 'Get up and running quickly with guided setup and onboarding.' },
    { icon: '🔒', title: 'Secure & Reliable', desc: 'Enterprise-grade security with regular updates and backups.' },
    { icon: '📊', title: 'Smart Analytics', desc: 'Real-time dashboards and reports to drive better decisions.' },
    { icon: '🤝', title: 'Dedicated Support', desc: 'Expert technical support and training for your team.' }
];

function getSlug() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('slug')) return params.get('slug');
    const match = window.location.pathname.match(/\/product\/([a-z0-9-]+)\/?$/i);
    return match ? match[1] : null;
}

function getCategoryIcon(slug) {
    return CATEGORY_ICONS[slug] || '📦';
}

function renderGallery(images, categorySlug, name) {
    if (images?.length) {
        const thumbs = images.map((img, i) =>
            `<button type="button" class="gallery-thumb ${i === 0 ? 'active' : ''}" data-src="${escapeHtml(img)}">
                <img src="${escapeHtml(img)}" alt="${escapeHtml(name)}">
            </button>`
        ).join('');
        return `
            <div class="product-gallery">
                <div class="gallery-main" id="galleryMain">
                    <img src="${escapeHtml(images[0])}" alt="${escapeHtml(name)}" id="galleryMainImg">
                </div>
                ${images.length > 1 ? `<div class="gallery-thumbs">${thumbs}</div>` : ''}
            </div>`;
    }
    return `
        <div class="product-gallery">
            <div class="gallery-main gallery-placeholder">
                <span class="gallery-icon">${getCategoryIcon(categorySlug)}</span>
            </div>
        </div>`;
}

function renderSpecs(specs, isSoftware) {
    if (specs?.length) {
        return `
            <section class="product-section">
                <h2>${isSoftware ? 'Features & Capabilities' : 'Specifications'}</h2>
                <div class="specs-grid">
                    ${specs.map(s => `
                        <div class="spec-card">
                            <span class="spec-label">${escapeHtml(s.spec_name)}</span>
                            <span class="spec-value">${escapeHtml(s.spec_value)}</span>
                        </div>
                    `).join('')}
                </div>
            </section>`;
    }
    if (isSoftware) {
        return `
            <section class="product-section">
                <h2>Why Choose This Solution</h2>
                <div class="features-grid">
                    ${SOFTWARE_FEATURES.map(f => `
                        <div class="feature-card">
                            <span class="feature-icon">${f.icon}</span>
                            <h3>${f.title}</h3>
                            <p>${f.desc}</p>
                        </div>
                    `).join('')}
                </div>
            </section>`;
    }
    return '';
}

function renderRelated(related) {
    if (!related?.length) return '';
    return `
        <section class="product-section">
            <h2>Related Products</h2>
            <div class="related-grid">
                ${related.map(p => `
                    <a href="${productUrl(p.slug)}" class="related-card" target="_blank" rel="noopener noreferrer">
                        <div class="related-image">
                            ${p.image
                                ? `<img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" loading="lazy">`
                                : `<span>${getCategoryIcon('software')}</span>`}
                        </div>
                        <div class="related-info">
                            <h3>${escapeHtml(p.name)}</h3>
                            <p>${escapeHtml(p.short_description || '')}</p>
                            <span class="related-price">${formatPrice(p.price)}</span>
                        </div>
                    </a>
                `).join('')}
            </div>
        </section>`;
}

function renderProductPage(product) {
    const isSoftware = product.category_slug === 'software';
    const badgeClass = product.badge?.toLowerCase() === 'sale' ? 'sale' : '';
    const badgeHtml = product.badge
        ? `<span class="product-hero-badge ${badgeClass}">${escapeHtml(product.badge)}</span>`
        : '';
    const priceHtml = product.original_price && product.original_price > product.price
        ? `<span class="price-original">${formatPrice(product.original_price)}</span>
           <span class="price-current">${formatPrice(product.price)}</span>`
        : `<span class="price-current">${formatPrice(product.price)}</span>`;
    const stockLabel = product.stock > 0 ? `${product.stock} in stock` : 'Available on request';
    const descParagraphs = (product.description || product.short_description || '')
        .split(/\n+/).filter(Boolean)
        .map(p => `<p>${escapeHtml(p)}</p>`).join('');

    document.title = `${product.name} — GITSYSTEMNG`;
    const metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) metaDesc.content = product.short_description || product.name;

    const main = document.getElementById('productMain');
    main.innerHTML = `
        <nav class="breadcrumb">
            <a href="index.html">Home</a>
            <span>/</span>
            <a href="index.html#products">Products</a>
            <span>/</span>
            <a href="index.html#products">${escapeHtml(product.category || '')}</a>
            <span>/</span>
            <span>${escapeHtml(product.name)}</span>
        </nav>

        <section class="product-hero">
            <div class="product-hero-grid">
                ${renderGallery(product.images, product.category_slug, product.name)}
                <div class="product-hero-info">
                    ${badgeHtml}
                    <p class="product-hero-category">${escapeHtml(product.category || '')}</p>
                    <h1>${escapeHtml(product.name)}</h1>
                    <p class="product-hero-tagline">${escapeHtml(product.short_description || '')}</p>
                    <div class="product-hero-price">${priceHtml}</div>
                    <p class="product-hero-stock">${stockLabel}</p>
                    <div class="product-hero-actions">
                        <button type="button" class="btn btn-primary btn-lg" id="btnBuy">Request Quote / Buy</button>
                        <button type="button" class="btn btn-secondary btn-lg" id="btnContact">Contact Sales</button>
                    </div>
                    <div class="product-trust">
                        <span>✓ Expert setup</span>
                        <span>✓ Warranty included</span>
                        <span>✓ Nigeria-wide support</span>
                    </div>
                </div>
            </div>
        </section>

        ${isSoftware ? `
        <section class="product-banner">
            <div class="banner-inner">
                <h2>Built for Nigerian businesses</h2>
                <p>Scalable, secure, and supported by the GITSYSTEMNG team from deployment to daily operations.</p>
            </div>
        </section>` : ''}

        <section class="product-section product-about">
            <h2>Overview</h2>
            <div class="about-content">${descParagraphs || '<p>Contact us for full product details and a personalized demo.</p>'}</div>
        </section>

        ${renderSpecs(product.specs, isSoftware)}

        <section class="product-section product-cta-section" id="productContact">
            <div class="cta-split">
                <div class="cta-text">
                    <h2>Ready to get started?</h2>
                    <p>Fill out the form and our team will reach out with pricing, demo access, and next steps within 24 hours.</p>
                    <ul class="cta-benefits">
                        <li>Free consultation for software products</li>
                        <li>Flexible payment options</li>
                        <li>Installation & training available</li>
                    </ul>
                </div>
                <form class="product-inquiry-form" id="productInquiryForm">
                    <input type="hidden" name="product_id" value="${product.id}">
                    <input type="hidden" name="service" value="product">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required>I am interested in ${escapeHtml(product.name)}. Please send me pricing and availability.</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">Send Inquiry</button>
                </form>
            </div>
        </section>

        ${renderRelated(product.related)}
    `;

    bindProductEvents(product);
}

function bindProductEvents(product) {
    const scrollToForm = () => {
        document.getElementById('productContact')?.scrollIntoView({ behavior: 'smooth' });
        document.getElementById('name')?.focus();
    };

    document.getElementById('btnBuy')?.addEventListener('click', scrollToForm);
    document.getElementById('btnContact')?.addEventListener('click', scrollToForm);
    document.getElementById('stickyBuy')?.addEventListener('click', scrollToForm);
    document.getElementById('stickyContact')?.addEventListener('click', scrollToForm);

    document.getElementById('productInquiryForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        await submitInquiry(e.target, product.id);
    });

    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', () => {
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            const mainImg = document.getElementById('galleryMainImg');
            if (mainImg) mainImg.src = thumb.dataset.src;
        });
    });

    const stickyBar = document.getElementById('stickyBar');
    const stickyName = document.getElementById('stickyName');
    const stickyPrice = document.getElementById('stickyPrice');
    if (stickyName) stickyName.textContent = product.name;
    if (stickyPrice) stickyPrice.textContent = formatPrice(product.price);

    const hero = document.querySelector('.product-hero');
    if (hero && stickyBar) {
        const observer = new IntersectionObserver(([entry]) => {
            stickyBar.hidden = entry.isIntersecting;
        }, { threshold: 0 });
        observer.observe(hero);
    }
}

function renderNotFound() {
    document.getElementById('productMain').innerHTML = `
        <div class="product-not-found">
            <h1>Product Not Found</h1>
            <p>The product you're looking for doesn't exist or has been removed.</p>
            <a href="index.html#products" class="btn btn-primary">Browse Products</a>
        </div>`;
}

async function loadProduct() {
    const slug = getSlug();
    if (!slug) {
        renderNotFound();
        return;
    }

    if (isGitInvoiceProduct(slug)) {
        const { learn, launch } = getGitInvoiceUrls();
        window.location.replace(launch);
        return;
    }

    try {
        const res = await fetch(`${API_BASE}?action=product&slug=${encodeURIComponent(slug)}`);
        const data = await res.json();
        if (data.success && data.data) {
            renderProductPage(data.data);
            return;
        }
    } catch { /* fallback below */ }

    const fallback = FALLBACK_PRODUCT_DETAILS[slug];
    if (fallback) {
        renderProductPage(fallback);
    } else {
        renderNotFound();
    }
}

const FALLBACK_PRODUCT_DETAILS = {
    'invohub-invoicing-software': {
        id: 1, name: 'invoHub - Invoicing Software', slug: 'invohub-invoicing-software',
        category: 'Software', category_slug: 'software',
        short_description: 'Invoicing software for small businesses',
        description: 'Complete invoicing solution for small businesses.\n\nCreate professional invoices, track payments, manage clients, and generate financial reports — all in one place. Perfect for freelancers, SMEs, and growing businesses in Nigeria.',
        price: 35000, original_price: null, stock: 99, badge: 'New', images: [],
        specs: [
            { spec_name: 'Invoicing', spec_value: 'Unlimited invoices & receipts' },
            { spec_name: 'Payments', spec_value: 'Track paid & pending' },
            { spec_name: 'Reports', spec_value: 'PDF & Excel export' },
            { spec_name: 'Support', spec_value: 'Email & phone support' }
        ],
        related: []
    }
};

loadProduct();
