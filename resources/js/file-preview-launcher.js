let activeDialog = null;
let returnFocus = null;

function closePreview() {
    if (!activeDialog) return;
    activeDialog.close();
}

function openPreview(link) {
    returnFocus = link;
    const dialog = document.createElement('dialog');
    dialog.className = 'bk-file-preview-dialog';
    dialog.setAttribute('aria-label', 'File preview');
    dialog.innerHTML = `
        <div class="bk-file-preview-dialog-frame">
            <button type="button" class="bk-file-preview-dialog-close" aria-label="Close file preview">
                <span aria-hidden="true">×</span>
            </button>
            <iframe title="File preview" loading="eager"></iframe>
        </div>`;
    dialog.querySelector('iframe').src = link.href;
    dialog.querySelector('button').addEventListener('click', closePreview);
    dialog.addEventListener('click', event => {
        if (event.target === dialog) closePreview();
    });
    dialog.addEventListener('cancel', event => {
        event.preventDefault();
        closePreview();
    });
    dialog.addEventListener('close', () => {
        dialog.remove();
        activeDialog = null;
        returnFocus?.focus();
        returnFocus = null;
    });
    document.body.append(dialog);
    activeDialog = dialog;
    dialog.showModal();
    dialog.querySelector('button').focus();
}

document.addEventListener('click', event => {
    const link = event.target.closest('a[data-file-preview-overlay]');
    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    openPreview(link);
});

window.addEventListener('message', event => {
    if (event.origin === window.location.origin && event.data?.type === 'bootkode:close-file-preview') closePreview();
});
