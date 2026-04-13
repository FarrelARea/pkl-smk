<div class="space-y-6">
    <x-help-button title="Panduan Dashboard Pembimbing">
        <p>Ini adalah halaman utama untuk pembimbing lapangan. Di sini kamu bisa:</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Lihat daftar siswa magang yang kamu bimbing</li>
            <li>Pantau kehadiran terakhir siswa</li>
        </ul>
    </x-help-button>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Siswa Magang</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kehadiran Terakhir</th>
                    </tr>
                </thead>
                <tbody id="supervisor-students-body">
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
