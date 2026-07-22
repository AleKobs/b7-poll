// Comportamentos globais da interface: dispensa automática de toasts.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toast]').forEach(function (toast) {
        window.setTimeout(function () {
            toast.dataset.leaving = '';
            toast.addEventListener('animationend', function () {
                toast.remove();
            }, { once: true });
        }, 4000);
    });
});
