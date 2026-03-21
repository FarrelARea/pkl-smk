@extends('layouts.app')

@section('title', 'Evaluations & Assessments')

@section('content')
<!-- Header -->
<header class="mb-10">
    <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Evaluations & Assessments</h1>
    <p class="text-on-surface-variant max-w-2xl font-body">
        Manage teacher/supervisor evaluations and final assessments for internships.
    </p>
</header>

<!-- Tab Navigation -->
<div class="flex gap-4 mb-8 border-b border-outline-variant/20">
    <button id="tab-evaluations" onclick="switchTab('evaluations')" class="px-4 py-3 text-sm font-bold uppercase tracking-wider border-b-2 border-primary text-primary transition-all">Evaluations</button>
    <button id="tab-assessments" onclick="switchTab('assessments')" class="px-4 py-3 text-sm font-bold uppercase tracking-wider border-b-2 border-transparent text-on-surface-variant hover:text-on-surface transition-all">Final Assessments</button>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Evaluations Section -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div id="section-evaluations">
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="p-6 flex flex-wrap justify-between items-center gap-4 border-b border-surface-container">
            <h2 class="text-xl font-bold tracking-tight font-headline">Evaluations</h2>
            <div class="flex items-center gap-3">
                <select id="eval-filter-student" onchange="loadEvals()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Students</option>
                </select>
                <select id="eval-filter-type" onchange="loadEvals()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="teacher">Teacher</option>
                    <option value="supervisor">Supervisor</option>
                </select>
                <button onclick="editEval(null)" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">add</span> Add Evaluation
                </button>
            </div>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Student</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Internship</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Score</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Type</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Comments</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="eval-table" class="divide-y divide-surface-container">
                <tr><td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Final Assessments Section -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div id="section-assessments" class="hidden">
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="p-6 flex justify-between items-center border-b border-surface-container">
            <h2 class="text-xl font-bold tracking-tight font-headline">Final Assessments</h2>
            <button onclick="editAssessment(null)" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">add</span> Add Final Assessment
            </button>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Student</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Internship</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Score</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Comments</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="assessment-table" class="divide-y divide-surface-container">
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Evaluation Modal -->
<!-- ═══════════════════════════════════════════════════════════ -->
@component('partials.modal', ['id' => 'eval-modal', 'title' => 'Evaluation'])
    <form id="eval-form" onsubmit="event.preventDefault(); saveEval();">
        <input type="hidden" id="eval-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="eval-student_id">Student</label>
                <select id="eval-student_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select student...</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="eval-internship_id">Internship</label>
                <select id="eval-internship_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select internship...</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="eval-score">Score</label>
                <input id="eval-score" required type="number" min="0" max="100" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="eval-type">Type</label>
                <select id="eval-type" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select type...</option>
                    <option value="teacher">Teacher</option>
                    <option value="supervisor">Supervisor</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="eval-comments">Comments</label>
                <textarea id="eval-comments" rows="3" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('eval-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- Final Assessment Modal -->
<!-- ═══════════════════════════════════════════════════════════ -->
@component('partials.modal', ['id' => 'assessment-modal', 'title' => 'Final Assessment'])
    <form id="assessment-form" onsubmit="event.preventDefault(); saveAssessment();">
        <input type="hidden" id="assessment-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="assessment-student_id">Student</label>
                <select id="assessment-student_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select student...</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="assessment-internship_id">Internship</label>
                <select id="assessment-internship_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select internship...</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="assessment-score">Score</label>
                <input id="assessment-score" required type="number" min="0" max="100" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="assessment-comments">Comments</label>
                <textarea id="assessment-comments" rows="3" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('assessment-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, editBtn, deleteBtn } = AdminUtils;

    let studentsCache = [];
    let internshipsCache = [];

    // ── Tab Switching ─────────────────────────────────────
    function switchTab(tab) {
        document.getElementById('section-evaluations').classList.toggle('hidden', tab !== 'evaluations');
        document.getElementById('section-assessments').classList.toggle('hidden', tab !== 'assessments');

        document.getElementById('tab-evaluations').classList.toggle('border-primary', tab === 'evaluations');
        document.getElementById('tab-evaluations').classList.toggle('text-primary', tab === 'evaluations');
        document.getElementById('tab-evaluations').classList.toggle('border-transparent', tab !== 'evaluations');
        document.getElementById('tab-evaluations').classList.toggle('text-on-surface-variant', tab !== 'evaluations');

        document.getElementById('tab-assessments').classList.toggle('border-primary', tab === 'assessments');
        document.getElementById('tab-assessments').classList.toggle('text-primary', tab === 'assessments');
        document.getElementById('tab-assessments').classList.toggle('border-transparent', tab !== 'assessments');
        document.getElementById('tab-assessments').classList.toggle('text-on-surface-variant', tab !== 'assessments');
    }

    // ── Load Dropdown Data ────────────────────────────────
    async function loadDropdowns() {
        try {
            const [studentsRes, internshipsRes] = await Promise.all([
                Auth.apiFetch('/students'),
                Auth.apiFetch('/internships'),
            ]);
            const studentsJson = await studentsRes.json();
            const internshipsJson = await internshipsRes.json();

            studentsCache = Array.isArray(studentsJson.data) ? studentsJson.data : (studentsJson.data?.data || []);
            internshipsCache = Array.isArray(internshipsJson.data) ? internshipsJson.data : (internshipsJson.data?.data || []);

            const studentOptions = studentsCache.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            const internshipOptions = internshipsCache.map(i => `<option value="${i.id}">${i.id}</option>`).join('');

            // Populate filter dropdown
            document.getElementById('eval-filter-student').innerHTML = '<option value="">All Students</option>' + studentOptions;

            // Populate modal dropdowns
            ['eval-student_id', 'assessment-student_id'].forEach(id => {
                document.getElementById(id).innerHTML = '<option value="">Select student...</option>' + studentOptions;
            });
            ['eval-internship_id', 'assessment-internship_id'].forEach(id => {
                document.getElementById(id).innerHTML = '<option value="">Select internship...</option>' + internshipOptions;
            });
        } catch (e) {
            console.error(e);
            showToast('Failed to load dropdown data', 'error');
        }
    }

    // ═══════════════════════════════════════════════════════
    // EVALUATIONS
    // ═══════════════════════════════════════════════════════

    async function loadEvals() {
        try {
            const params = new URLSearchParams();
            const studentId = document.getElementById('eval-filter-student').value;
            const type = document.getElementById('eval-filter-type').value;
            if (studentId) params.set('student_id', studentId);
            if (type) params.set('type', type);

            const res = await Auth.apiFetch(`/evaluations?${params}`);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            const tbody = document.getElementById('eval-table');
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">No evaluations found.</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(row => `
                <tr class="hover:bg-surface-container-lowest/60 transition-colors">
                    <td class="px-6 py-4 text-sm text-on-surface">${row.student?.name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface">${row.internship?.id || '-'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface font-semibold">${row.score ?? '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${row.type === 'teacher' ? 'bg-primary/10 text-primary' : 'bg-tertiary/10 text-tertiary'}">${row.type || '-'}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant max-w-xs truncate">${row.comments ? row.comments.substring(0, 60) + (row.comments.length > 60 ? '...' : '') : '-'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            ${editBtn(row.id, 'editEval')}
                            ${deleteBtn(row.id, row.student?.name || 'this evaluation', 'deleteEval')}
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            console.error(e);
            showToast('Failed to load evaluations', 'error');
        }
    }

    async function editEval(id) {
        document.getElementById('eval-id').value = '';
        document.getElementById('eval-form').reset();
        document.getElementById('eval-modal-title').textContent = id ? 'Edit Evaluation' : 'Add Evaluation';

        if (id) {
            try {
                const res = await Auth.apiFetch(`/evaluations/${id}`);
                const json = await res.json();
                const item = json.data || json;

                document.getElementById('eval-id').value = item.id;
                document.getElementById('eval-student_id').value = item.student_id || '';
                document.getElementById('eval-internship_id').value = item.internship_id || '';
                document.getElementById('eval-score').value = item.score || '';
                document.getElementById('eval-type').value = item.type || '';
                document.getElementById('eval-comments').value = item.comments || '';
            } catch (e) {
                console.error(e);
                showToast('Failed to load evaluation details', 'error');
                return;
            }
        }

        showModal('eval-modal');
    }

    async function deleteEval(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`/evaluations/${id}`, { method: 'DELETE' });
            showToast('Evaluation deleted successfully');
            loadEvals();
        } catch (e) {
            console.error(e);
            showToast('Failed to delete evaluation', 'error');
        }
    }

    async function saveEval() {
        const id = document.getElementById('eval-id').value;
        const payload = {
            student_id: document.getElementById('eval-student_id').value,
            internship_id: document.getElementById('eval-internship_id').value,
            score: document.getElementById('eval-score').value,
            type: document.getElementById('eval-type').value,
            comments: document.getElementById('eval-comments').value,
        };

        try {
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/evaluations/${id}` : '/evaluations';
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Validation error');
            }

            hideModal('eval-modal');
            showToast(id ? 'Evaluation updated successfully' : 'Evaluation created successfully');
            loadEvals();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save evaluation', 'error');
        }
    }

    // ═══════════════════════════════════════════════════════
    // FINAL ASSESSMENTS
    // ═══════════════════════════════════════════════════════

    async function loadAssessments() {
        try {
            const res = await Auth.apiFetch('/final-assessments');
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            const tbody = document.getElementById('assessment-table');
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No final assessments found.</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(row => `
                <tr class="hover:bg-surface-container-lowest/60 transition-colors">
                    <td class="px-6 py-4 text-sm text-on-surface">${row.student?.name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface">${row.internship?.id || '-'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface font-semibold">${row.score ?? '-'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant max-w-xs truncate">${row.comments ? row.comments.substring(0, 60) + (row.comments.length > 60 ? '...' : '') : '-'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            ${editBtn(row.id, 'editAssessment')}
                            ${deleteBtn(row.id, row.student?.name || 'this assessment', 'deleteAssessment')}
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            console.error(e);
            showToast('Failed to load final assessments', 'error');
        }
    }

    async function editAssessment(id) {
        document.getElementById('assessment-id').value = '';
        document.getElementById('assessment-form').reset();
        document.getElementById('assessment-modal-title').textContent = id ? 'Edit Final Assessment' : 'Add Final Assessment';

        if (id) {
            try {
                const res = await Auth.apiFetch(`/final-assessments/${id}`);
                const json = await res.json();
                const item = json.data || json;

                document.getElementById('assessment-id').value = item.id;
                document.getElementById('assessment-student_id').value = item.student_id || '';
                document.getElementById('assessment-internship_id').value = item.internship_id || '';
                document.getElementById('assessment-score').value = item.score || '';
                document.getElementById('assessment-comments').value = item.comments || '';
            } catch (e) {
                console.error(e);
                showToast('Failed to load assessment details', 'error');
                return;
            }
        }

        showModal('assessment-modal');
    }

    async function deleteAssessment(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`/final-assessments/${id}`, { method: 'DELETE' });
            showToast('Final assessment deleted successfully');
            loadAssessments();
        } catch (e) {
            console.error(e);
            showToast('Failed to delete final assessment', 'error');
        }
    }

    async function saveAssessment() {
        const id = document.getElementById('assessment-id').value;
        const payload = {
            student_id: document.getElementById('assessment-student_id').value,
            internship_id: document.getElementById('assessment-internship_id').value,
            score: document.getElementById('assessment-score').value,
            comments: document.getElementById('assessment-comments').value,
        };

        try {
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/final-assessments/${id}` : '/final-assessments';
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Validation error');
            }

            hideModal('assessment-modal');
            showToast(id ? 'Final assessment updated successfully' : 'Final assessment created successfully');
            loadAssessments();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save final assessment', 'error');
        }
    }

    // ── Expose to window ─────────────────────────────────
    window.switchTab = switchTab;
    window.loadEvals = loadEvals;
    window.editEval = editEval;
    window.deleteEval = deleteEval;
    window.saveEval = saveEval;
    window.loadAssessments = loadAssessments;
    window.editAssessment = editAssessment;
    window.deleteAssessment = deleteAssessment;
    window.saveAssessment = saveAssessment;

    // ── Init ─────────────────────────────────────────────
    await loadDropdowns();
    loadEvals();
    loadAssessments();
</script>
@endpush
