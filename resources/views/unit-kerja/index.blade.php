<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight">
                                Manajemen <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">Unit Kerja</span>
                            </h2>
                            <p class="text-slate-300 mt-2 text-sm font-medium max-w-xl">
                                Kelola struktur organisasi, unit kerja, dan bidang terkait.
                            </p>
                        </div>
                        
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Unit</p>
                            <p class="text-2xl font-black text-indigo-600">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
            
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total User</p>
                            <p class="text-2xl font-black text-emerald-600">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Table Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Daftar Unit Kerja</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $unitKerja->total() }} unit terdaftar</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('unit-kerja.create') }}" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Unit
                            </a>
                            <button onclick="openBidangModal()" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                Kelola Bidang
                            </button>
                        </div>
                    </div>

                    {{-- Search/Filter --}}
                    <form method="GET" class="flex gap-2 mt-4">
                        <select name="level" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua Level</option>
                            @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari unit kerja..."
                            class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        <button type="submit" 
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                            Cari
                        </button>
                        @if($search || request('level'))
                        <a href="{{ route('unit-kerja.index') }}" 
                            class="px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-rose-100 transition">
                            Reset
                        </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Unit Kerja</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Level</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Atasan</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">SDM</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($unitKerja as $unit)
                            @php
                                $levelColors = [
                                    'Direktorat' => 'bg-slate-900',
                                    'Kompartemen' => 'bg-indigo-600',
                                    'Departemen' => 'bg-slate-700',
                                    'Seksi' => 'bg-slate-500',
                                ];
                                $levelBadges = [
                                    'Direktorat' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'Kompartemen' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Departemen' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Seksi' => 'bg-purple-50 text-purple-700 border-purple-100',
                                ];
                                $avatarBg = $levelColors[$unit->level] ?? 'bg-slate-500';
                                $badgeStyle = $levelBadges[$unit->level] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl {{ $avatarBg }} flex items-center justify-center text-white font-black text-xs shadow-sm">
                                            {{ strtoupper(substr($unit->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $unit->name }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $unit->children->count() }} unit bawahan</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $badgeStyle }}">
                                        {{ $unit->level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-500">
                                        {{ $unit->parent ? $unit->parent->name : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-1.5">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-[9px] font-black uppercase">
                                            {{ $unit->users->count() }} User
                                        </span>
                                        
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('unit-kerja.show', $unit) }}" 
                                            class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition"
                                            title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('unit-kerja.edit', $unit) }}" 
                                            class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button type="button" data-id="{{ $unit->id }}" data-name="{{ $unit->name }}" 
                                            onclick="showDeleteModal(this)" 
                                            class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Tidak ada unit kerja ditemukan</p>
                                        <p class="text-xs text-slate-300 mt-1">Coba ubah filter atau tambahkan unit baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                    {{ $unitKerja->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL BIDANG --}}
    <div id="bidangModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[85vh]">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-black uppercase tracking-wider">Master Data Bidang</h3>
                <button onclick="closeBidangModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-slate-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
                <form id="bidangForm" method="POST" action="{{ route('bidang.store') }}" class="mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="name" id="bidangInputName" placeholder="Nama Bidang Baru..." required
                            class="flex-1 px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        <button type="submit" id="bidangSubmitBtn" 
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-black text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                            Simpan
                        </button>
                    </div>
                </form>

                <div class="space-y-2">
                    @foreach($bidang as $b)
                    <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-2xl hover:border-indigo-200 transition">
                        <span class="font-bold text-slate-700 text-sm">{{ $b->name }}</span>
                        <div class="flex gap-1.5">
                            <button onclick="editBidang('{{ $b->id }}', '{{ $b->name }}')" 
                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('bidang.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus bidang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="deleteModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2 uppercase tracking-tight">Konfirmasi Hapus</h3>
                <p class="text-sm text-slate-500 mb-6">
                    Hapus unit <span id="deleteUnitName" class="font-black text-slate-900"></span>? Tindakan ini akan gagal jika masih ada data terkait.
                </p>
                <div class="flex justify-center gap-3">
                    <button onclick="closeDeleteModal()" 
                        class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            class="px-6 py-3 bg-rose-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-rose-700 transition shadow-sm">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBidangModal() {
            document.getElementById('bidangModal').classList.remove('hidden');
            document.getElementById('bidangModal').classList.add('flex');
        }
        function closeBidangModal() {
            document.getElementById('bidangModal').classList.add('hidden');
            document.getElementById('bidangModal').classList.remove('flex');
            document.getElementById('bidangForm').action = "{{ route('bidang.store') }}";
            document.getElementById('bidangInputName').value = '';
            document.getElementById('bidangSubmitBtn').innerText = 'Simpan';
            const oldMethod = document.getElementById('bidangMethod');
            if(oldMethod) oldMethod.remove();
        }
        function editBidang(id, name) {
            document.getElementById('bidangInputName').value = name;
            document.getElementById('bidangSubmitBtn').innerText = 'Update';
            document.getElementById('bidangForm').action = "{{ route('bidang.update', ':id') }}".replace(':id', id);
            if(!document.getElementById('bidangMethod')) {
                const mi = document.createElement('input');
                mi.type = 'hidden'; mi.name = '_method'; mi.value = 'PUT'; mi.id = 'bidangMethod';
                document.getElementById('bidangForm').appendChild(mi);
            }
        }
        function showDeleteModal(btn) {
            document.getElementById('deleteUnitName').textContent = btn.dataset.name;
            document.getElementById('deleteForm').action = "{{ route('unit-kerja.destroy', ':id') }}".replace(':id', btn.dataset.id);
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        ['bidangModal', 'deleteModal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', function(e) { if (e.target === this) id === 'bidangModal' ? closeBidangModal() : closeDeleteModal(); });
        });
    </script>
</x-app-layout>