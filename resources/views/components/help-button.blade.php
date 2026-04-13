@props(['title' => 'Cara Menggunakan'])

@php $helpId = 'help-modal-' . uniqid(); @endphp

<button type="button" onclick="document.getElementById('{{ $helpId }}').classList.remove('hidden')"
    class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 inline-flex items-center justify-center text-sm font-bold transition-colors duration-200"
    title="Cara Menggunakan">
    ?
</button>

<div id="{{ $helpId }}" class="fixed inset-0 z-[999] flex items-center justify-center p-4 hidden">
    <div class="fixed inset-0 bg-black/40" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-6 z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
            <button onclick="document.getElementById('{{ $helpId }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="text-sm text-slate-600 leading-relaxed space-y-2">
            {{ $slot }}
        </div>
        <div class="mt-6 text-right">
            <button onclick="document.getElementById('{{ $helpId }}').classList.add('hidden')"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
