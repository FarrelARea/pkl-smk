/**
 * Admin CRUD utility functions
 */

// ── Modal ──────────────────────────────────────────────
export function showModal(id = 'crud-modal') {
    document.getElementById(id)?.classList.remove('hidden');
}

export function hideModal(id = 'crud-modal') {
    document.getElementById(id)?.classList.add('hidden');
}

// ── Toast ──────────────────────────────────────────────
export function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = type === 'success'
        ? 'bg-tertiary-fixed text-on-tertiary-fixed-variant'
        : 'bg-error-container text-on-error-container';
    toast.className = `fixed bottom-6 right-6 z-[100] px-6 py-3 rounded-lg shadow-lg text-sm font-semibold ${colors} transition-opacity duration-300`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ── Confirm Delete ─────────────────────────────────────
export function confirmDelete(name) {
    return confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`);
}

// ── Render Table ───────────────────────────────────────
export function renderTable(tbodyId, rows, columns, actions) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    if (rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${columns.length + (actions ? 1 : 0)}" class="px-6 py-12 text-center text-on-surface-variant">No data found</td></tr>`;
        return;
    }
    tbody.innerHTML = rows.map(row => {
        const cells = columns.map(col => {
            const val = typeof col.render === 'function' ? col.render(row) : (row[col.key] ?? '—');
            return `<td class="px-6 py-4 text-sm">${val}</td>`;
        }).join('');
        const actionCell = actions ? `<td class="px-6 py-4 text-right">${actions(row)}</td>` : '';
        return `<tr class="hover:bg-surface-container-high transition-colors border-b border-surface-container">${cells}${actionCell}</tr>`;
    }).join('');
}

// ── Pagination ─────────────────────────────────────────
export function renderPagination(containerId, meta, onPageChange) {
    const el = document.getElementById(containerId);
    if (!el || !meta) return;
    const current = meta.current_page || 1;
    const last = meta.last_page || 1;
    if (last <= 1) { el.innerHTML = ''; return; }

    const buttons = [];
    buttons.push(`<button class="px-3 py-1.5 rounded-md text-xs font-bold ${current <= 1 ? 'text-outline cursor-not-allowed' : 'text-primary hover:bg-primary-fixed'}" data-page="${current - 1}" ${current <= 1 ? 'disabled' : ''}>Prev</button>`);
    for (let i = 1; i <= last; i++) {
        const active = i === current ? 'bg-primary text-white' : 'text-on-surface hover:bg-surface-container-high';
        buttons.push(`<button class="px-3 py-1.5 rounded-md text-xs font-bold ${active}" data-page="${i}">${i}</button>`);
    }
    buttons.push(`<button class="px-3 py-1.5 rounded-md text-xs font-bold ${current >= last ? 'text-outline cursor-not-allowed' : 'text-primary hover:bg-primary-fixed'}" data-page="${current + 1}" ${current >= last ? 'disabled' : ''}>Next</button>`);

    el.innerHTML = `<div class="flex items-center gap-1 justify-center py-4">${buttons.join('')}</div>`;
    el.querySelectorAll('button:not([disabled])').forEach(btn => {
        btn.addEventListener('click', () => onPageChange(parseInt(btn.dataset.page)));
    });
}

// ── Populate Select ────────────────────────────────────
export async function populateSelect(selectId, endpoint, labelKey = 'name', valueKey = 'id') {
    try {
        const separator = endpoint.includes('?') ? '&' : '?';
        const res = await Auth.apiFetch(endpoint + separator + 'per_page=1000');
        const json = await res.json();
        const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
        const select = document.getElementById(selectId);
        if (!select) return;
        const placeholder = select.querySelector('option[value=""]');
        select.innerHTML = '';
        if (placeholder) select.appendChild(placeholder);
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[valueKey];
            opt.textContent = item[labelKey];
            select.appendChild(opt);
        });
    } catch (e) {
        console.error(`Failed to populate ${selectId}:`, e);
    }
}

// ── Status Badge ───────────────────────────────────────
export function statusBadge(status) {
    const styles = {
        active: 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        completed: 'bg-primary-fixed text-on-primary-fixed-variant',
        cancelled: 'bg-error-container text-on-error-container',
        present: 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        absent: 'bg-error-container text-on-error-container',
        sick: 'bg-secondary-fixed text-on-secondary-fixed-variant',
        permission: 'bg-primary-fixed text-on-primary-fixed-variant',
        pending: 'bg-surface-container-highest text-on-surface-variant',
    };
    const cls = styles[status] || 'bg-surface-container-highest text-on-surface-variant';
    return `<span class="px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest ${cls}">${status}</span>`;
}

// ── Action Buttons ─────────────────────────────────────
export function editBtn(id) {
    return `<button onclick="editItem(${id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>`;
}

export function deleteBtn(id, name) {
    return `<button onclick="deleteItem(${id}, '${name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>`;
}

window.AdminUtils = { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, populateSelect, statusBadge, editBtn, deleteBtn };
