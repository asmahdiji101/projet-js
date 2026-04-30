document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach((element) => {
        element.addEventListener('click', (event) => {
            event.preventDefault();

            const message = element.getAttribute('data-confirm') || 'Are you sure?';
            const href = element.getAttribute('href');

            const backdrop = document.createElement('div');
            backdrop.className = 'confirm-dialog-backdrop';
            backdrop.innerHTML = `
                <div class="confirm-dialog" role="dialog" aria-modal="true" aria-label="Confirmation">
                    <h3>Confirmation</h3>
                    <p>${message}</p>
                    <div class="confirm-dialog-actions">
                        <button type="button" class="button button-secondary" data-action-cancel>Annuler</button>
                        <button type="button" class="button" data-action-confirm>Confirmer</button>
                    </div>
                </div>
            `;

            const closeDialog = () => backdrop.remove();

            backdrop.addEventListener('click', (clickEvent) => {
                if (clickEvent.target === backdrop) {
                    closeDialog();
                }
            });

            backdrop.querySelector('[data-action-cancel]')?.addEventListener('click', closeDialog);
            backdrop.querySelector('[data-action-confirm]')?.addEventListener('click', () => {
                closeDialog();
                if (href) {
                    window.location.href = href;
                }
            });

            document.body.appendChild(backdrop);
        });
    });
});
