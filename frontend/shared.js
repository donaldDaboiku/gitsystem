// Shared utilities and navigation (used on all pages)

function resolveApiBase() {
    const meta = document.querySelector('meta[name="api-base"]')?.content?.trim();
    if (!meta) return '/backend/data.php';
    if (meta.startsWith('http') || meta.startsWith('/')) return meta;
    return new URL(meta, window.location.href).href;
}

function resolveFrontendBase() {
    const meta = document.querySelector('meta[name="frontend-base"]')?.content?.trim();
    if (meta) return meta.replace(/\/$/, '');
    const path = window.location.pathname;
    const idx = path.indexOf('/frontend');
    if (idx !== -1) return path.substring(0, idx + '/frontend'.length);
    return '';
}

const API_BASE = resolveApiBase();
const FRONTEND_BASE = resolveFrontendBase();

function formatPrice(amount) {
    return '₦' + Number(amount).toLocaleString('en-NG');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function productUrl(slug) {
    if (!slug) return `${FRONTEND_BASE}/index.html#products`;
    return `${FRONTEND_BASE}/product/${slug}`;
}

function isExternalApp(product) {
    if (!product) return false;
    if (product.is_external_app) return true;
    return !!(product.learn_url || product.launch_url);
}

function getExternalAppLinks(product) {
    // Legacy GITInvoice fallback if DB fields not migrated yet
    if (isGitInvoiceProduct(product?.slug) && !product?.learn_url && !product?.launch_url) {
        const brand = getGitInvoiceBrand();
        return {
            learn: brand.learn,
            launch: brand.launch,
            logo: brand.logo,
            color: '#ff6b35',
            priceLabel: product?.price_label || 'Try free · 3 invoices',
        };
    }
    const learn = (product.learn_url || product.launch_url || '').trim();
    const launch = (product.launch_url || product.learn_url || '').trim();
    return {
        learn,
        launch,
        logo: (product.logo_url || product.image || '').trim(),
        color: product.brand_color || '#109d48',
        priceLabel: product.price_label || 'Launch app',
    };
}

const GITINVOICE_SLUGS = ['gitinvoice', 'invohub-invoicing-software', 'gitinvoice-invoicing-software'];

function isGitInvoiceProduct(slug) {
    return GITINVOICE_SLUGS.includes(slug);
}

function getGitInvoiceBrand() {
    const base = (document.querySelector('meta[name="gitinvoice-url"]')?.content?.trim()
        || 'https://git-invoice-three.vercel.app').replace(/\/$/, '');
    return {
        base,
        learn: `${base}/`,
        launch: `${base}/app`,
        logo: `${base}/icons/icon-192-maskable.png`,
        name: 'GITInvoice',
    };
}

function getGitInvoiceUrls() {
    const { learn, launch } = getGitInvoiceBrand();
    return { learn, launch };
}

function gitInvoiceLogoHtml(size = 40) {
    const { logo, name } = getGitInvoiceBrand();
    return `<img src="${escapeHtml(logo)}" alt="${escapeHtml(name)}" class="gitinvoice-logo" width="${size}" height="${size}" loading="lazy">`;
}

function gitInvoiceWordmarkHtml(logoSize = 40) {
    return `
        <span class="gitinvoice-wordmark">
            ${gitInvoiceLogoHtml(logoSize)}
            <span class="gitinvoice-name"><span class="gitinvoice-git">GIT</span><span class="gitinvoice-invoice">Invoice</span></span>
        </span>`;
}

function initGitInvoiceLinks() {
    const { learn, launch, logo } = getGitInvoiceBrand();
    document.querySelectorAll('[data-gitinvoice-learn]').forEach(el => { el.href = learn; });
    document.querySelectorAll('[data-gitinvoice-launch]').forEach(el => { el.href = launch; });
    document.querySelectorAll('[data-gitinvoice-logo]').forEach(el => {
        el.src = logo;
        el.alt = 'GITInvoice';
    });
}

function showToast(message, type = 'success') {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.className = `toast toast-${type} visible`;
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('visible'), 4000);
}

function initNavigation() {
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks = document.getElementById('navLinks');

    mobileToggle?.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        mobileToggle.classList.toggle('active');
    });

    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks?.classList.remove('active');
            mobileToggle?.classList.remove('active');
        });
    });

    const header = document.getElementById('header');
    window.addEventListener('scroll', () => {
        header?.classList.toggle('scrolled', window.scrollY > 60);
    });

    const scrollTop = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
        scrollTop?.classList.toggle('visible', window.scrollY > 300);
    });
    scrollTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const offset = target.getBoundingClientRect().top + window.pageYOffset - 80;
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }
        });
    });

    const year = document.getElementById('currentYear');
    if (year) year.textContent = new Date().getFullYear();
}

async function submitInquiry(form, productId = null) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending…';

    const formData = new FormData(form);
    const payload = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        service: formData.get('service') || 'product',
        message: formData.get('message'),
        product_id: formData.get('product_id') || productId || null
    };

    try {
        const res = await fetch(`${API_BASE}?action=submit_inquiry`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || 'Message sent successfully!');
            form.reset();
            return true;
        }
        showToast(data.error || 'Failed to send message.', 'error');
        return false;
    } catch {
        showToast('Unable to reach the server. Please try again.', 'error');
        return false;
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

initNavigation();
initGitInvoiceLinks();
loadSiteSettings();

async function loadSiteSettings() {
    try {
        const res = await fetch(`${API_BASE}?action=settings`);
        const json = await res.json();
        if (!json.success || !json.data) return;

        const s = json.data;

        ['site_name', 'site_address', 'site_phone', 'site_email'].forEach(key => {
            if (!s[key]) return;
            document.querySelectorAll(`[data-setting="${key}"]`).forEach(el => {
                el.textContent = s[key];
            });
        });

        if (s.site_email) {
            document.querySelectorAll('[data-setting="site_email_link"]').forEach(el => {
                el.textContent = s.site_email;
                if (el.tagName === 'A') el.href = `mailto:${s.site_email}`;
            });
        }

        if (s.site_phone) {
            document.querySelectorAll('[data-setting="site_phone_link"]').forEach(el => {
                el.textContent = s.site_phone;
                if (el.tagName === 'A') el.href = `tel:${s.site_phone.replace(/[^\d+]/g, '')}`;
            });
        }

        const socialMap = {
            facebook_url: '[data-social="facebook"]',
            instagram_url: '[data-social="instagram"]',
            twitter_url: '[data-social="twitter"]',
            linkedin_url: '[data-social="linkedin"]',
            whatsapp_number: '[data-social="whatsapp"]'
        };

        Object.entries(socialMap).forEach(([key, selector]) => {
            const val = s[key];
            if (!val || val === '#') return;
            document.querySelectorAll(selector).forEach(el => {
                if (key === 'whatsapp_number') {
                    const num = val.replace(/[^\d]/g, '');
                    el.href = `https://wa.me/${num}`;
                } else {
                    el.href = val;
                }
            });
        });
    } catch {
        /* settings optional */
    }
}
