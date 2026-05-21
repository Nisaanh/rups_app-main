<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('tindaklanjut.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            {{-- Error Messages --}}
            @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
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

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Header --}}
                <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-8 text-white">
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black uppercase tracking-tight">Input Tindak Lanjut</h2>
                                <p class="text-slate-300 text-sm mt-1">Laporkan progres tindak lanjut arahan</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tanggal</p>
                                <p class="text-sm font-bold text-white">{{ now()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-48 h-48 bg-sky-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-36 h-36 bg-indigo-500/10 rounded-full blur-2xl"></div>
                </div>

                {{-- Arahan Info --}}
                <div class="px-8 pt-6 pb-2">
                    <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Arahan</p>
                        <p class="text-sm font-bold text-indigo-800 leading-relaxed">
                            {{ $selectedArahan ? $selectedArahan->strategi : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('tindaklanjut.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="arahan_id" value="{{ $selectedArahanId }}">

                    {{-- Unit Kerja & Periode --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Unit Kerja --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Unit Kerja <span class="text-rose-500">*</span>
                            </label>
                            <select name="unit_kerja_id"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('unit_kerja_id') border-rose-300 @enderror"
                                required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach($unitKerja as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Periode --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Periode Laporan
                            </label>
                            <div class="flex items-center gap-3 px-5 py-4 bg-slate-100 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ now()->translatedFormat('F Y') }}</span>
                            </div>
                            <input type="hidden" name="periode_bulan" value="{{ date('n') }}">
                            <input type="hidden" name="periode_tahun" value="{{ date('Y') }}">
                        </div>
                    </div>

                    {{-- Uraian Tindak Lanjut --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Uraian Tindak Lanjut <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="tindak_lanjut" rows="5"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-400 @error('tindak_lanjut') border-rose-300 @enderror"
                            placeholder="Deskripsikan langkah-langkah tindak lanjut yang telah dilakukan..."
                            required>{{ old('tindak_lanjut') }}</textarea>
                    </div>

                    {{-- Kendala --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Kendala
                        </label>
                        <textarea name="kendala" rows="3"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition-all placeholder:text-slate-400"
                            placeholder="Sebutkan kendala yang dihadapi...">{{ old('kendala') }}</textarea>
                    </div>

                    {{-- Keterangan Tambahan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Keterangan Tambahan
                        </label>
                        <textarea name="keterangan" rows="3"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 transition-all placeholder:text-slate-400"
                            placeholder="Informasi tambahan jika ada...">{{ old('keterangan') }}</textarea>
                    </div>

                    {{-- Upload Evidence --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Upload Bukti Pendukung
                        </label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-indigo-300 hover:bg-indigo-50/30 transition-all cursor-pointer"
                            onclick="document.getElementById('file-upload').click()"
                            ondragover="event.preventDefault(); this.classList.add('border-indigo-400', 'bg-indigo-50/50')"
                            ondragleave="event.preventDefault(); this.classList.remove('border-indigo-400', 'bg-indigo-50/50')"
                            ondrop="handleDrop(event)">
                            <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700 mb-1">Klik untuk upload atau drag & drop</h4>
                            <p class="text-xs text-slate-400">PDF, JPG, PNG, DOCX (Maks. 5MB)</p>
                            <input id="file-upload" type="file" name="evidence" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.docx,.doc" onchange="previewFile(this)">
                        </div>

                        {{-- Preview Area --}}
                        <div id="filePreviewArea" class="hidden mt-3"></div>

                        {{-- Info --}}
                        <div class="flex items-start gap-2 mt-3 p-3 bg-blue-50 rounded-xl border border-blue-100">
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[11px] text-blue-700 font-medium">Pastikan dokumen valid dan sesuai dengan unit terkait.</p>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end items-center gap-3">

                        <a href="{{ route('tindaklanjut.index') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-[10px] font-semibold uppercase text-slate-500 hover:text-slate-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-wide hover:from-indigo-700 hover:to-sky-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">

                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>

                            Kirim Tindak Lanjut
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewFile(input) {
            const previewArea = document.getElementById('filePreviewArea');

            if (!input.files || !input.files[0]) {
                previewArea.classList.add('hidden');
                previewArea.innerHTML = '';
                return;
            }

            const file = input.files[0];
            const fileSize = file.size / 1024 / 1024;

            if (fileSize > 5) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                input.value = '';
                previewArea.classList.add('hidden');
                return;
            }

            const isImage = file.type.startsWith('image/');
            let previewHtml = '';

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewHtml = `
                        <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                            <div class="flex items-start gap-4">
                                <img src="${e.target.result}" class="w-16 h-16 object-cover rounded-xl border border-slate-200">
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-slate-700">${file.name}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">${(file.size / 1024).toFixed(2)} KB</p>
                                    <button type="button" onclick="removeFile()" class="mt-2 text-[10px] font-black text-rose-500 hover:text-rose-600 uppercase flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    previewArea.innerHTML = previewHtml;
                    previewArea.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                const fileExt = file.name.split('.').pop().toLowerCase();
                const iconColors = {
                    pdf: 'text-rose-500',
                    doc: 'text-blue-500',
                    docx: 'text-blue-500'
                };
                const iconColor = iconColors[fileExt] || 'text-slate-500';

                previewHtml = `
                    <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-slate-50 rounded-xl">
                                <svg class="w-8 h-8 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-700">${file.name}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">${(file.size / 1024).toFixed(2)} KB</p>
                                <button type="button" onclick="removeFile()" class="mt-2 text-[10px] font-black text-rose-500 hover:text-rose-600 uppercase flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>`;
                previewArea.innerHTML = previewHtml;
                previewArea.classList.remove('hidden');
            }
        }

        function removeFile() {
            document.getElementById('file-upload').value = '';
            document.getElementById('filePreviewArea').classList.add('hidden');
            document.getElementById('filePreviewArea').innerHTML = '';
        }

        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('border-indigo-400', 'bg-indigo-50/50');

            const file = event.dataTransfer.files[0];
            if (file) {
                const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!validTypes.includes(file.type)) {
                    alert('Tipe file tidak didukung! Gunakan PDF, JPG, PNG, atau DOCX.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB.');
                    return;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('file-upload').files = dt.files;
                previewFile(document.getElementById('file-upload'));
            }
        }
    </script>
    @endpush
</x-app-layout>