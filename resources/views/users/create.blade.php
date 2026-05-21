<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('users.index') }}" 
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
                            <h3 class="text-xl font-black uppercase tracking-tight">Registrasi Akun Baru</h3>
                            <p class="text-slate-300 text-sm mt-1">Lengkapi informasi identitas dan hak akses user.</p>
                        </div>
                        <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sky-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                <div class="mx-6 mt-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl">
                    <p class="text-[10px] font-black uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Gagal menyimpan
                    </p>
                    <ul class="text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <span class="mt-1 w-1 h-1 rounded-full bg-rose-500 flex-shrink-0"></span>
                            {{ $error }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    {{-- Section 1: Identitas --}}
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                            Informasi Identitas
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Badge / NIP <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="badge" value="{{ old('badge') }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('badge') border-rose-300 @enderror"
                                    placeholder="Contoh: 123456" required>
                                @error('badge') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('name') border-rose-300 @enderror"
                                    placeholder="Nama lengkap user" required>
                                @error('name') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Alamat Email <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('email') border-rose-300 @enderror"
                                    placeholder="email@pusri.co.id" required>
                                @error('email') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Keamanan --}}
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Keamanan Akun</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('password') border-rose-300 @enderror"
                                    placeholder="••••••••" required>
                                @error('password') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Konfirmasi Password <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Penempatan --}}
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span>
                            Penempatan & Akses
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Unit Kerja</label>
                                <select name="unit_kerja_id" id="unit_kerja_id"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    @foreach($unitKerja as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Role <span class="text-rose-500">*</span>
                                </label>
                                <select name="role" id="role_select"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2 hidden" id="pic_wrapper">
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                    Atasan Langsung
                                    <span id="pic-loading" class="hidden ml-2 text-indigo-500 font-medium">Memuat...</span>
                                </label>
                                <div id="pic-hint-box" class="mt-1 p-3 bg-emerald-50 border border-emerald-200 rounded-xl hidden">
                                    <p id="pic-hint" class="text-emerald-700 text-xs font-bold"></p>
                                </div>
                                <div id="pic-error-box" class="mt-1 p-3 bg-rose-50 border border-rose-200 rounded-xl hidden">
                                    <p id="pic-error" class="text-rose-600 text-xs font-bold"></p>
                                </div>
                                <input type="hidden" name="pic_unit_kerja_id" id="pic_hidden">
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Status --}}
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Status Akun</h4>
                            <p class="text-xs text-slate-500">Tentukan apakah akun langsung aktif setelah dibuat.</p>
                        </div>
                        <div class="flex bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                            <label class="flex items-center cursor-pointer px-5 py-2 rounded-lg transition {{ old('status', 'active') == 'active' ? 'bg-emerald-500 text-white shadow-sm' : 'text-slate-400' }}">
                                <input type="radio" name="status" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }} class="hidden">
                                <span class="text-xs font-black uppercase">Aktif</span>
                            </label>
                            <label class="flex items-center cursor-pointer px-5 py-2 rounded-lg transition {{ old('status') == 'inactive' ? 'bg-rose-500 text-white shadow-sm' : 'text-slate-400' }}">
                                <input type="radio" name="status" value="inactive" {{ old('status') == 'inactive' ? 'checked' : '' }} class="hidden">
                                <span class="text-xs font-black uppercase">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('users.index') }}" 
                            class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const unitSelect = document.getElementById('unit_kerja_id');
        const roleSelect = document.getElementById('role_select');
        const picWrapper = document.getElementById('pic_wrapper');
        const picLoading = document.getElementById('pic-loading');
        const picHintBox = document.getElementById('pic-hint-box');
        const picHint = document.getElementById('pic-hint');
        const picErrorBox = document.getElementById('pic-error-box');
        const picError = document.getElementById('pic-error');
        const picHidden = document.getElementById('pic_hidden');

        function resetPicInfo() {
            picHintBox.classList.add('hidden');
            picErrorBox.classList.add('hidden');
            picHidden.value = '';
        }

        function fetchAndSetPic() {
            const unitId = unitSelect.value;
            const isAuditi = roleSelect.value === 'Auditi';

            // Sembunyikan section atasan kalau bukan Auditi
            if (!isAuditi) {
                picWrapper.classList.add('hidden');
                resetPicInfo();
                return;
            }

            picWrapper.classList.remove('hidden');
            resetPicInfo();

            if (!unitId) {
                picErrorBox.classList.remove('hidden');
                picError.textContent = 'Pilih Unit Kerja terlebih dahulu untuk menentukan atasan.';
                return;
            }

            picLoading.classList.remove('hidden');

            fetch(`/users/pic-by-unit/${unitId}`)
                .then(res => res.json())
                .then(users => {
                    picLoading.classList.add('hidden');

                    if (users.length === 0) {
                        picErrorBox.classList.remove('hidden');
                        picError.textContent = 'Tidak ditemukan atasan untuk unit kerja ini. Pastikan unit induk sudah memiliki Atasan Auditi.';
                        return;
                    }

                    // Auto-set atasan pertama
                    picHidden.value = users[0].id;
                    picHintBox.classList.remove('hidden');
                    picHint.textContent = '✓ Atasan otomatis: ' + users[0].name + ' (' + users[0].badge + ')';
                })
                .catch(() => {
                    picLoading.classList.add('hidden');
                    picErrorBox.classList.remove('hidden');
                    picError.textContent = 'Gagal memuat data atasan. Silakan coba lagi.';
                });
        }

        unitSelect.addEventListener('change', fetchAndSetPic);
        roleSelect.addEventListener('change', fetchAndSetPic);
    </script>
</x-app-layout>