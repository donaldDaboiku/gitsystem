document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        const msg = form.getAttribute('data-confirm') || 'Are you sure?';
        if (!window.confirm(msg)) {
            e.preventDefault();
        }
    });
});

document.querySelectorAll('select[data-auto-submit]').forEach((select) => {
    select.addEventListener('change', () => {
        select.form?.submit();
    });
});
