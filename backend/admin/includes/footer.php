            </div>
        </main>
    </div>
    <?php if (defined('CSP_NONCE')): ?>
    <script src="assets/admin.js" nonce="<?= htmlspecialchars(CSP_NONCE, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endif; ?>
</body>
</html>
