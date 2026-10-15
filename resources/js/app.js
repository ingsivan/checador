import './timers';
import './board';

// Fade out the success toast after a few seconds.
document.querySelectorAll('[data-toast]').forEach((toast) => {
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 4000);
});

// Ask before actions that can't be undone (e.g. leaving for the day).
document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

// Prevent double taps on the check-in button.
document.querySelectorAll('form[data-once]').forEach((form) => {
    form.addEventListener('submit', () => {
        form.querySelectorAll('button').forEach((b) => (b.disabled = true));
    });
});
