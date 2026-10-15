import Sortable from 'sortablejs';

const board = document.querySelector('[data-board]');
const dialog = document.getElementById('task-dialog');

if (board) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const refreshCounts = () => {
        board.querySelectorAll('[data-column]').forEach((column) => {
            column.closest('section').querySelector('[data-count]').textContent =
                column.querySelectorAll('[data-task]').length;
        });
    };

    board.querySelectorAll('[data-column]').forEach((column) => {
        Sortable.create(column, {
            group: 'board',
            animation: 180,
            delay: 180,
            delayOnTouchOnly: true, // on phones: press and hold to drag, so swiping still scrolls
            ghostClass: 'opacity-40',
            chosenClass: 'rotate-2',
            onEnd: async (event) => {
                const to = event.to;
                refreshCounts();
                const response = await fetch(`/tareas/${event.item.dataset.task}/mover`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({
                        status: to.dataset.column,
                        order: [...to.querySelectorAll('[data-task]')].map((el) => Number(el.dataset.task)),
                    }),
                });
                if (!response.ok) {
                    window.location.reload();
                    return;
                }
                const task = JSON.parse(event.item.dataset.json);
                task.status = to.dataset.column;
                event.item.dataset.json = JSON.stringify(task);
                event.item.querySelector('p.text-\\[15px\\]')?.classList.toggle('line-through', task.status === 'done');
            },
        });
    });

    board.addEventListener('click', (event) => {
        const card = event.target.closest('[data-task]');
        if (card) openDialog(JSON.parse(card.dataset.json));
    });
    board.addEventListener('keydown', (event) => {
        const card = event.target.closest('[data-task]');
        if (card && (event.key === 'Enter' || event.key === ' ')) {
            event.preventDefault();
            openDialog(JSON.parse(card.dataset.json));
        }
    });
}

document.querySelectorAll('[data-task-new]').forEach((button) =>
    button.addEventListener('click', () => openDialog(null)),
);

function openDialog(task) {
    if (!dialog) return;
    const form = dialog.querySelector('form');
    const field = (name) => form.elements.namedItem(name);

    form.reset();
    dialog.querySelector('[data-dialog-title]').textContent = task ? 'Editar tarea' : 'Nueva tarea';
    dialog.querySelector('[data-delete]').classList.toggle('hidden', !task);

    if (task) {
        form.action = `${dialog.dataset.update}/${task.id}`;
        field('_method').value = 'PUT';
        field('title').value = task.title;
        field('description').value = task.description ?? '';
        field('project_id').value = task.project_id;
        field('user_id').value = task.user_id ?? '';
        field('status').value = task.status;
        field('due_date').value = task.due_date ?? '';
        document.getElementById('task-delete').action = `${dialog.dataset.update}/${task.id}`;
    } else {
        form.action = dialog.dataset.store;
        field('_method').value = 'POST';
    }

    dialog.showModal();
    if (!task) field('title').focus();
}

dialog?.querySelectorAll('[data-close]').forEach((b) => b.addEventListener('click', () => dialog.close()));
dialog?.addEventListener('click', (event) => {
    if (event.target === dialog) dialog.close(); // tap outside the sheet
});
