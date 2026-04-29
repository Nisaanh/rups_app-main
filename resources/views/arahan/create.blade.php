<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-8">

            {{-- Header Bar --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('keputusan.index') }}" class="group inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Daftar Keputusan
                    </a>
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Buat Arahan RUPS</h2>
                        <div class="h-1 w-12 bg-blue-500 rounded-full mt-1"></div>
                    </div>
                </div>
            </div>

            {{-- Konteks Keputusan Selected --}}
            @if(isset($keputusanSelected) && $keputusanSelected)
            <div class="bg-slate-900 rounded-3xl shadow-2xl p-6 md:p-8 text-white relative overflow-hidden group">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="flex h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400">Konteks Keputusan Aktif</p>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-black uppercase tracking-tight">RUPS {{ $keputusanSelected->periode_year }}</h3>
                    </div>
                    <div class="px-5 py-2.5 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl text-[10px] font-black uppercase tracking-widest self-start md:self-center">
                        Status: <span class="text-blue-300">{{ $keputusanSelected->status }}</span>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-600/20 rounded-full blur-[80px]"></div>
                <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-[50px]"></div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Form Input --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
                        <div class="p-6 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-800 uppercase text-xs tracking-widest flex items-center">
                                <span class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </span>
                                {{ $existingArahan->count() > 0 ? 'Tambah Arahan Berikutnya' : 'Input Arahan Pertama' }}
                            </h3>
                        </div>

                        <form action="{{ route('arahan.store') }}" method="POST" class="px-8 pb-8 pt-4 md:px-10 md:pb-10 md:pt-6 space-y-6">
                            @csrf
                            <input type="hidden" name="keputusan_id" value="{{ $keputusanSelected->id ?? '' }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                                {{-- Bidang --}}
                                <div class="space-y-2">
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Bidang <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="bidang_id" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-semibold text-slate-700 transition" required>
                                        <option value="">-- Pilih Bidang --</option>
                                        @foreach($bidang as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- PIC Multiple dengan Alpine.js --}}
                                <div class="space-y-2" x-data="picComponent()" x-init="init()">
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        PIC Penanggung Jawab <span class="text-rose-500">*</span>
                                        <span class="text-blue-500 normal-case font-normal">(bisa pilih banyak)</span>
                                    </label>

                                    <div class="relative">
                                        <button type="button"
                                            @click="dropdownOpen = !dropdownOpen"
                                            class="w-full bg-white rounded-xl border border-slate-200 font-semibold text-slate-700 p-3 text-left flex justify-between items-center hover:border-blue-300 transition">
                                            <span>
                                                <span x-show="selectedPics.length === 0" class="text-slate-400 text-sm">-- Klik untuk memilih PIC --</span>
                                                <span x-show="selectedPics.length > 0" class="text-sm">
                                                    <span class="font-bold text-blue-600" x-text="selectedPics.length"></span> PIC terpilih
                                                </span>
                                            </span>
                                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                                :class="{'rotate-180': dropdownOpen}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="dropdownOpen"
                                            @click.away="dropdownOpen = false"
                                            x-cloak
                                            class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl max-h-72 overflow-y-auto">
                                            <div class="sticky top-0 bg-white px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih PIC</span>
                                                <button type="button"
                                                    @click="resetAll()"
                                                    class="text-xs text-rose-500 hover:text-rose-700 font-bold">
                                                    Reset Semua
                                                </button>
                                            </div>
                                            <div class="p-2">
                                                @foreach($users as $u)
                                                <label class="flex items-center p-3 hover:bg-slate-50 rounded-lg cursor-pointer gap-3">
                                                    <input type="checkbox"
                                                        :checked="selectedPics.includes({{ $u->id }})"
                                                        @change="togglePic({{ $u->id }}, $event.target.checked)"
                                                        class="w-4 h-4 text-blue-600 rounded border-slate-300 flex-shrink-0">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-700">{{ $u->name }}</p>
                                                        <p class="text-xs text-slate-400">{{ $u->unitKerja->name ?? '-' }}</p>
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Hidden inputs untuk submit --}}
                                    <template x-for="picId in selectedPics" :key="picId">
                                        <input type="hidden" name="pic_unit_kerja_ids[]" :value="picId">
                                    </template>

                                    {{-- Badge PIC Terpilih --}}
                                    <div class="flex flex-wrap gap-2 mt-2" x-show="selectedPics.length > 0" x-cloak>
                                        <template x-for="picId in selectedPics" :key="picId">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-full text-xs">
                                                <span class="font-semibold text-blue-700" x-text="getName(picId)"></span>
                                                <button type="button" @click="removePic(picId)" class="text-blue-400 hover:text-rose-500 font-bold leading-none">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Tanggal Target --}}
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        Tanggal Target Arahan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="tanggal_target"
                                        value="{{ old('tanggal_target', date('Y-m-d')) }}"
                                        class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-semibold text-slate-700 transition"
                                        required>
                                </div>
                            </div>

                            {{-- Strategi --}}
                            <div class="space-y-2">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Strategi Pelaksanaan (Butir Arahan) <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="strategi" rows="5"
                                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-medium text-slate-700 transition p-4 md:p-6 placeholder:text-slate-400"
                                    placeholder="Tuliskan butir arahan secara detail dan terperinci..."
                                    required>{{ old('strategi') }}</textarea>
                            </div>

                            {{-- After Save Option --}}
                            <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest">Opsi Setelah Simpan:</span>
                                <div class="flex flex-wrap gap-4 md:gap-8">
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="after_save" value="continue" checked class="text-blue-600 focus:ring-blue-500 w-4 h-4 border-slate-300">
                                        <span class="ml-2 text-xs font-bold text-blue-700 uppercase tracking-tight">Input Arahan Lagi</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="after_save" value="finish" class="text-blue-600 focus:ring-blue-500 w-4 h-4 border-slate-300">
                                        <span class="ml-2 text-xs font-bold text-slate-500 uppercase tracking-tight">Kembali ke Detail</span>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" class="w-full md:w-auto px-10 py-4 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-[0.15em] hover:bg-blue-600 shadow-xl shadow-slate-200 hover:shadow-blue-200 transition-all active:scale-95 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Simpan Butir Arahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar Draft List --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden sticky top-6">
                        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
                            <h3 class="text-[10px] font-black uppercase tracking-widest flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Draft Tersimpan ({{ $existingArahan->count() }})
                            </h3>
                        </div>

                        <div class="max-h-[450px] overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                            @forelse($existingArahan as $ea)
                            <div class="p-5 hover:bg-slate-50 transition-colors group relative">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-tight border border-blue-100">
                                        {{ $ea->bidang->name ?? '-' }}
                                    </span>
                                    <button type="button" onclick="deleteArahan({{ $ea->id }})"
                                        class="p-1.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed font-medium line-clamp-3">"{{ $ea->strategi }}"</p>

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($ea->pics as $pic)
                                    <div class="inline-flex items-center px-2 py-1 bg-slate-100 rounded-full text-[9px] font-semibold text-slate-600">
                                        <div class="w-4 h-4 bg-slate-300 rounded-full flex items-center justify-center mr-1 text-[8px] font-black">
                                            {{ substr($pic->name, 0, 1) }}
                                        </div>
                                        {{ $pic->name }}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <div class="p-12 text-center">
                                <div class="mx-auto w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-400 font-medium italic">Belum ada butir arahan.</p>
                            </div>
                            @endforelse
                        </div>

                        @if($existingArahan->count() > 0 && isset($keputusanSelected))
                        <div class="p-6 bg-slate-50 border-t border-slate-100">
                            {{-- Tombol Finalisasi dengan Modal --}}
                            <button type="button" id="btnFinalize"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black py-4 rounded-xl shadow-lg shadow-emerald-100 transition-all uppercase tracking-[0.1em] flex items-center justify-center group">
                                <span class="mr-2 transform group-hover:scale-125 transition-transform">🚀</span>
                                Finalisasi & Kirim
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI FINALISASI --}}
    <div id="modalFinalize" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="modalFinalizeContent">
            
            {{-- Header dengan Icon --}}
            <div class="relative bg-gradient-to-r from-emerald-600 to-teal-600 rounded-t-3xl p-6 text-center">
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2">
                    <div class="w-16 h-16 bg-white rounded-2xl rotate-45 shadow-lg flex items-center justify-center">
                        <div class="-rotate-45">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-black text-white uppercase tracking-tight pt-2">Finalisasi Arahan</h3>
            </div>

            {{-- Body Modal --}}
            <div class="pt-12 pb-6 px-6 text-center">
                <p class="text-slate-600 text-sm mb-6">
                    Anda akan memfinalisasi <span class="font-black text-emerald-600 text-lg">{{ $existingArahan->count() }}</span> butir arahan untuk:
                </p>
                
                {{-- Ringkasan Keputusan --}}
                <div class="bg-slate-100 rounded-2xl p-4 mb-6">
                    <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Periode RUPS</div>
                    <div class="text-2xl font-black text-slate-800">{{ $keputusanSelected->periode_year ?? '-' }}</div>
                </div>

                {{-- Daftar Ringkas Arahan --}}
                <div class="text-left mb-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">📋 Butir Arahan yang akan dikirim:</p>
                    <div class="max-h-48 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                        @foreach($existingArahan->take(5) as $index => $ea)
                        <div class="flex items-start gap-2 text-xs p-2 bg-slate-50 rounded-lg">
                            <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-[10px] font-black">{{ $index + 1 }}</span>
                            <span class="text-slate-600 line-clamp-2">{{ Str::limit($ea->strategi, 60) }}</span>
                        </div>
                        @endforeach
                        @if($existingArahan->count() > 5)
                        <div class="text-center text-xs text-slate-400 italic pt-1">
                            + {{ $existingArahan->count() - 5 }} arahan lainnya
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Warning Box --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-left">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-[10px] text-amber-800">
                            <p class="font-bold">Perhatian!</p>
                            <p>Setelah finalisasi, arahan akan dikirim ke seluruh PIC dan tidak dapat diubah kembali.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="flex gap-3 p-6 bg-slate-50 rounded-b-3xl">
                <button type="button" id="modalFinalizeCancel" class="flex-1 px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition active:scale-95">
                    Batal
                </button>
                <form id="finalizeForm" action="{{ route('keputusan.finalize', $keputusanSelected->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="finalizeSubmitBtn" class="w-full px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl text-sm font-bold hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-200 active:scale-95">
                        Ya, Finalisasi & Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const usersData = @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]));

        function picComponent() {
            return {
                selectedPics: [],
                dropdownOpen: false,

                init() {
                    console.log('PIC Component initialized');
                },

                togglePic(id, checked) {
                    if (checked) {
                        if (!this.selectedPics.includes(id)) {
                            this.selectedPics.push(id);
                        }
                    } else {
                        this.selectedPics = this.selectedPics.filter(i => i !== id);
                    }
                },

                removePic(id) {
                    this.selectedPics = this.selectedPics.filter(i => i !== id);
                },

                resetAll() {
                    this.selectedPics = [];
                },

                getName(id) {
                    const user = usersData.find(u => u.id === id);
                    return user ? user.name : 'Unknown';
                }
            }
        }

        function deleteArahan(id) {
            if (confirm('Hapus arahan ini dari draft?')) {
                const form = document.getElementById('deleteArahanForm');
                form.action = `/arahan/${id}`;
                form.submit();
            }
        }

        // ========== MODAL FINALIZE ==========
        const modalFinalize = document.getElementById('modalFinalize');
        const modalFinalizeContent = document.getElementById('modalFinalizeContent');
        const btnFinalize = document.getElementById('btnFinalize');
        const modalFinalizeCancel = document.getElementById('modalFinalizeCancel');
        const finalizeForm = document.getElementById('finalizeForm');
        const finalizeSubmitBtn = document.getElementById('finalizeSubmitBtn');

        function showModal() {
            modalFinalize.classList.remove('hidden');
            modalFinalize.classList.add('flex');
            void modalFinalize.offsetHeight;
            modalFinalizeContent.classList.remove('scale-95', 'opacity-0');
            modalFinalizeContent.classList.add('scale-100', 'opacity-100');
        }

        function hideModal() {
            modalFinalizeContent.classList.remove('scale-100', 'opacity-100');
            modalFinalizeContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modalFinalize.classList.add('hidden');
                modalFinalize.classList.remove('flex');
            }, 200);
        }

        if (btnFinalize) {
            btnFinalize.addEventListener('click', showModal);
        }

        if (modalFinalizeCancel) {
            modalFinalizeCancel.addEventListener('click', hideModal);
        }

        modalFinalize.addEventListener('click', function(e) {
            if (e.target === modalFinalize) {
                hideModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalFinalize && !modalFinalize.classList.contains('hidden')) {
                hideModal();
            }
        });

        // Loading state saat submit finalisasi
        if (finalizeSubmitBtn) {
            finalizeForm.addEventListener('submit', function() {
                finalizeSubmitBtn.disabled = true;
                finalizeSubmitBtn.innerHTML = `
                    <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Memproses...
                `;
            });
        }
    </script>

    <form id="deleteArahanForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush
</x-app-layout>