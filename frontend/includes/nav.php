<?php
$navCurrent = $navCurrent ?? '';
$navItems = [
    'home' => ['/', 'Home'],
    'about' => ['/about', 'About'],
    'services' => ['/services', 'Services'],
    'products' => ['/products', 'Products'],
    'software' => ['/software', 'Software'],
    'academy' => ['/academy', 'Academy'],
    'contact' => ['/contact', 'Contact'],
];
?>
<header id="header">
    <nav>
        <a href="/" class="logo" aria-label="GITSYSTEM home">
            <img src="/frontend/assets/gitsystem-logo.png" alt="GITSYSTEM — Greenpasture Integrated Technology" class="logo-img" width="220" height="44">
        </a>
        <ul class="nav-links" id="navLinks">
            <?php foreach ($navItems as $key => [$href, $label]): ?>
                <li><a href="<?= frontend_h($href) ?>"<?= $navCurrent === $key ? ' class="active" aria-current="page"' : '' ?>><?= frontend_h($label) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="mobile-toggle" id="mobileToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>
