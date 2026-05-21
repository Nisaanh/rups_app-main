<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight">
                                Manajemen <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">Role & Permission</span>
                            </h2>
                            <p class="text-slate-300 mt-2 text-sm font-medium max-w-xl">
                                Kelola role, dan permission.
                            </p>
                        </div>

                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Role</p>
                            <p class="text-2xl font-black text-slate-800">{{ $totalRoles }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Izin</p>
                            <p class="text-2xl font-black text-emerald-600">{{ $totalPermissions }}</p>
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
                            <h3 class="text-xl font-bold text-slate-800">Daftar Role</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $roles->total() }} role terdaftar</p>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('roles.refresh-cache') }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Refresh cache permission?')"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-amber-100 transition"
                                    title="Refresh Cache">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Refresh Cache
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Search --}}
                    <form method="GET" class="flex gap-2 mt-4">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama role..."
                            class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                            Cari
                        </button>
                        @if($search)
                        <a href="{{ route('roles.index') }}"
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
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Role</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Permissions</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Users</th>
                                <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($roles as $role)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-sky-400 flex items-center justify-center text-white font-black text-xs shadow-sm">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm capitalize">{{ $role->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($role->permissions->take(3) as $permission)
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[8px] font-black rounded-md border border-blue-100 uppercase tracking-tight">
                                            {{ $permission->name }}
                                        </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                        <span class="px-2 py-1 bg-slate-100 text-slate-400 text-[8px] font-black rounded-md border border-slate-200">
                                            +{{ $role->permissions->count() - 3 }}
                                        </span>
                                        @endif
                                        @if($role->permissions->isEmpty())
                                        <span class="text-[10px] text-slate-300 italic">Tidak ada</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">
                                        {{ $role->users->count() }} User
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('roles.edit', $role) }}"
                                            class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if(!in_array($role->name, ['admin', 'Super Admin']))

<button 
    type="button"
    onclick="openDeleteModal('{{ route('roles.destroy', $role) }}', '{{ $role->name }}')"
    class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition"
    title="Hapus">

    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
    </svg>

</button>

@endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Data role tidak ditemukan</p>
                                        <p class="text-xs text-slate-300 mt-1">Coba ubah kata kunci atau tambahkan role baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($roles->hasPages())
                <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                    {{ $roles->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal"
        class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">

        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-[fadeIn_.2s_ease-out]">

            <div class="p-8 text-center">

                <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-lg font-black text-slate-800 mb-2 uppercase tracking-tight">
                    Konfirmasi Hapus
                </h3>

                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Apakah yakin ingin menghapus role
                    <span id="deleteRoleName" class="font-black text-slate-900"></span>?
                    <br>
                    Data yang dihapus tidak dapat dipulihkan.
                </p>

                <div class="flex justify-center gap-3">

                    <button onclick="closeDeleteModal()"
                        class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition">
                        Batal
                    </button>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')

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
        function openDeleteModal(action, roleName) {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');

            document.getElementById('deleteForm').action = action;
            document.getElementById('deleteRoleName').innerText = roleName;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }

        // close when click outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>