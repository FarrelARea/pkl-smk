{{-- Reusable Modal --}}
<div id="{{ $id ?? 'crud-modal' }}" data-help-target="modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="AdminUtils.hideModal('{{ $id ?? 'crud-modal' }}')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
            <div class="flex justify-between items-center p-6 border-b border-surface-container">
                <h3 id="{{ ($id ?? 'crud-modal') }}-title" class="text-lg font-bold font-headline">{{ $title ?? 'Modal' }}</h3>
                <button onclick="AdminUtils.hideModal('{{ $id ?? 'crud-modal' }}')" class="p-1 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
