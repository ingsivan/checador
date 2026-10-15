/**
 * Live timers.
 *
 * <span data-timer data-since="ISO date" data-format="clock|short" data-limit="60"></span>
 *
 * Counts up from data-since. With data-limit (minutes), the closest [data-timer-tone]
 * container gets data-tone="ok|near|over", and any [data-timer-ring] inside it fills up.
 */
const pad = (n) => String(n).padStart(2, '0');

function render(el, now) {
    const since = new Date(el.dataset.since);
    const offset = Number(el.dataset.offset || 0); // seconds already accumulated
    const seconds = Math.max(0, Math.floor((now - since) / 1000) + offset);
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;

    el.dataset.long = String(h > 0); // lets the view shrink the text for h:mm:ss
    el.textContent = el.dataset.format === 'short'
        ? (h ? `${h} h ${m} min` : `${m} min`)
        : (h ? `${h}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`);

    const limit = Number(el.dataset.limit || 0);
    if (!limit) return;

    const minutes = seconds / 60;
    const tone = minutes > limit ? 'over' : minutes > limit * 0.8 ? 'near' : 'ok';
    const box = el.closest('[data-timer-tone]');
    if (box) {
        box.dataset.tone = tone;
        box.querySelectorAll('[data-timer-ring]').forEach((ring) => {
            const length = Number(ring.getAttribute('pathLength') || 100);
            ring.style.strokeDashoffset = String(length - Math.min(1, minutes / limit) * length);
        });
        box.querySelectorAll('[data-timer-left]').forEach((left) => {
            const remaining = Math.ceil(limit - minutes);
            left.textContent = remaining > 0
                ? `Te quedan ${remaining} min`
                : `Te pasaste ${Math.floor(minutes - limit)} min`;
        });
    }
}

function tick() {
    const now = Date.now();
    document.querySelectorAll('[data-timer]').forEach((el) => render(el, now));
}

tick();
setInterval(tick, 1000);
