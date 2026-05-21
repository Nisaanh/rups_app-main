<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('roles.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Header --}}
                <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-8 text-white">
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black uppercase tracking-tight">{{ $role->name }}</h3>
                            <p class="text-slate-300 text-sm mt-1">Konfigurasi hak akses sistem</p>
                        </div>
                        <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-sky-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                </div>

                <form action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-6">
                        {{-- Input Name --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Nama Role
                            </label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                class="w-full px-5 py-4 rounded-xl border-slate-200 font-bold text-slate-800 transition {{ $isProtected ? 'bg-slate-100 cursor-not-allowed text-slate-400' : 'bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20' }}"
                                required {{ $isProtected ? 'readonly' : '' }}>
                            @if($isProtected)
                            <p class="text-[10px] text-amber-600 font-bold mt-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                Role Sistem: Nama tidak dapat dimodifikasi
                            </p>
                            @endif
                        </div>

                        {{-- Permissions --}}
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-5 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                                Konfigurasi Hak Akses (Permissions)
                            </h4>

                            @php
                            $permissionGroups = [
                                'keputusan' => 'Manajemen Keputusan RUPS',
                                'arahan' => 'Sistem Arahan & Notulensi',
                                'tindak' => 'Progress Tindak Lanjut',
                                'approve' => 'Approval & Persetujuan',
                                'report' => 'Dashboard & Export',
                                'manage' => 'Sistem Management',
                                'user' => 'Kontrol Pengguna',
                                'role' => 'Kontrol Role & Akses',
                                'unit' => 'Struktur Unit Kerja'
                            ];

                            $grouped = $permissions->groupBy(function($permission) {
                                if(str_contains($permission->name, 'keputusan')) return 'keputusan';
                                if(str_contains($permission->name, 'arahan')) return 'arahan';
                                if(str_contains($permission->name, 'tindak')) return 'tindak';
                                if(str_contains($permission->name, 'approve')) return 'approve';
                                if(str_contains($permission->name, 'dashboard') || str_contains($permission->name, 'export')) return 'report';
                                if(str_contains($permission->name, 'manage')) return 'manage';
                                if(str_contains($permission->name, 'user')) return 'user';
                                if(str_contains($permission->name, 'role')) return 'role';
                                if(str_contains($permission->name, 'unit')) return 'unit';
                                return 'other';
                            });
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($grouped as $group => $perms)
                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 hover:bg-white hover:shadow-md transition-all duration-200">
                                    <h5 class="text-[10px] font-black text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-200 pb-2">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                        {{ strtoupper($permissionGroups[$group] ?? $group) }}
                                    </h5>
                                    <div class="space-y-1.5">
                                        @foreach($perms as $permission)
                                        <label class="flex items-center cursor-pointer p-2 rounded-lg hover:bg-slate-100 transition">
                                            <input type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                            <span class="ml-2.5 text-[11px] font-bold text-slate-600 capitalize">
                                                {{ str_replace('_', ' ', $permission->name) }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-8 py-6 bg-slate-50/80 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('roles.index') }}" 
                            class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Box --}}
            <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-indigo-100 rounded-xl flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-indigo-800 uppercase tracking-wider">Peringatan Sinkronisasi</h4>
                        <p class="mt-1.5 text-[11px] text-indigo-700 leading-relaxed font-medium">
                            Setiap perubahan akan berdampak instan. Seluruh user dengan role <span class="font-black text-indigo-900">{{ $role->name }}</span> akan mendapatkan pembaruan hak akses pada request berikutnya. Pastikan konfigurasi sesuai SOP.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>