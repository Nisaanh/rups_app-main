<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-3">
                <a class="hover:text-[#003b71] transition-colors" href="{{ route('tindaklanjut.index') }}">Tindak Lanjut</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-[#003b71]">Input Tindak Lanjut</span>
            </nav>
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Input Tindak Lanjut Baru</h2>
                    <div class="h-1 w-16 bg-[#003b71] rounded-full mt-2"></div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">TANGGAL HARI INI</p>
                    <p class="text-sm font-bold text-slate-800">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
        </div>

        <!-- ALERT ERROR VALIDASI -->
        @if ($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 px-5 py-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-bold text-xs uppercase tracking-wide">Gagal menyimpan:</p>
            </div>
            <ul class="text-sm space-y-1 pl-7">
                @foreach ($errors->all() as $error)
                <li class="flex items-start gap-2">
                    <span class="mt-1.5 w-1 h-1 rounded-full bg-rose-500 flex-shrink-0"></span>
                    {{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <!-- Section 1: Read-only Directive Info -->
            <div class="p-6 bg-gradient-to-r from-[#003b71] to-[#005299] text-white">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-base font-bold">Informasi Arahan</h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-wider text-blue-200 block mb-1">STRATEGI / ARAHAN</label>
                        <p class="text-sm font-medium text-white/90 leading-relaxed">
                            {{ $selectedArahan ? $selectedArahan->strategi : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Input Fields -->
            <form action="{{ route('tindaklanjut.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="arahan_id" value="{{ $selectedArahanId }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- UNIT KERJA -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                            Unit Kerja <span class="text-rose-500">*</span>
                        </label>
                        <select name="unit_kerja_id"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 bg-white shadow-sm focus:ring-2 focus:ring-[#003b71]/20 focus:border-[#003b71] transition"
                            required>
                            <option disabled selected value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PERIODE -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                            Periode Laporan
                        </label>
                        <div class="w-full flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 bg-slate-50 text-slate-700 shadow-sm">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-bold text-slate-700">
                                {{ now()->locale('id')->translatedFormat('F Y') }}
                            </span>
                        </div>
                        <input type="hidden" name="periode_bulan" value="{{ date('n') }}">
                        <input type="hidden" name="periode_tahun" value="{{ date('Y') }}">
                    </div>
                </div>

                <!-- TINDAK LANJUT -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Tindak Lanjut <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="tindak_lanjut" rows="6"
                        class="w-full border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#003b71]/20 focus:border-[#003b71] placeholder:text-slate-400 p-4 transition"
                        placeholder="Deskripsikan secara detail langkah-langkah tindak lanjut yang telah dilakukan..." required>{{ old('tindak_lanjut') }}</textarea>
                </div>

                <!-- KENDALA -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Kendala / Kekurangan
                    </label>
                    <textarea name="kendala" rows="4"
                        class="w-full border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#003b71]/20 focus:border-[#003b71] placeholder:text-slate-400 p-4 transition"
                        placeholder="Sebutkan kendala teknis atau administratif yang dihadapi...">{{ old('kendala') }}</textarea>
                </div>

                <!-- KETERANGAN TAMBAHAN -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-[#003b71]/20 focus:border-[#003b71] placeholder:text-slate-400 p-4 transition"
                        placeholder="Informasi tambahan jika ada...">{{ old('keterangan') }}</textarea>
                </div>

                <!-- Upload Evidence dengan Preview -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Upload Bukti Pendukung
                    </label>
                    
                    <!-- Drop Zone -->
                    <div class="relative border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-[#003b71] hover:bg-blue-50/30 transition-all cursor-pointer group"
                        onclick="document.getElementById('file-upload').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-[#003b71]', 'bg-blue-50/50')"
                        ondragleave="event.preventDefault(); this.classList.remove('border-[#003b71]', 'bg-blue-50/50')"
                        ondrop="handleDrop(event)">
                        <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-100 transition">
                            <svg class="w-7 h-7 text-slate-400 group-hover:text-[#003b71]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700 mb-1">Klik untuk upload atau drag and drop</h4>
                        <p class="text-xs text-slate-400">PDF, JPG, PNG, DOCX (Maks. 5MB)</p>
                        <input id="file-upload" type="file" name="evidence" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.docx,.doc" onchange="previewFile(this)">
                    </div>

                    <!-- Preview Area -->
                    <div id="filePreviewArea" class="hidden"></div>

                    <!-- Info Box -->
                    <div class="flex items-start gap-3 p-3 bg-blue-50/50 border border-blue-100 rounded-xl">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-[11px] text-slate-600 font-medium">Pastikan dokumen valid dan sesuai dengan unit terkait.</p>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-6 border-t border-slate-100 flex justify-end items-center gap-3">
                    <a href="{{ route('tindaklanjut.index') }}"
                        class="px-6 py-2.5 rounded-xl border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all active:scale-95">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-[#003b71] to-[#005299] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Tindak Lanjut
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // File Preview Function
        function previewFile(input) {
            const previewArea = document.getElementById('filePreviewArea');
            
            if (!input.files || !input.files[0]) {
                previewArea.classList.add('hidden');
                previewArea.innerHTML = '';
                return;
            }

            const file = input.files[0];
            const fileSize = file.size / 1024 / 1024; // MB
            const maxSize = 5;

            if (fileSize > maxSize) {
                alert(`Ukuran file terlalu besar! Maksimal ${maxSize}MB.`);
                input.value = '';
                previewArea.classList.add('hidden');
                return;
            }

            let previewHtml = '';
            const fileExt = file.name.split('.').pop().toLowerCase();
            const isImage = file.type.startsWith('image/');

            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewHtml = `
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-start gap-4">
                                <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border border-slate-200 shadow-sm">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm font-bold text-slate-700">${file.name}</p>
                                    </div>
                                    <p class="text-[10px] text-slate-400">${(file.size / 1024).toFixed(2)} KB</p>
                                    <button type="button" onclick="removeFile()" class="mt-2 text-xs text-rose-500 hover:text-rose-700 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus File
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    previewArea.innerHTML = previewHtml;
                    previewArea.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                // Icon berdasarkan tipe file
                let fileIcon = '';
                if (fileExt === 'pdf') {
                    fileIcon = `<svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>`;
                } else if (fileExt === 'doc' || fileExt === 'docx') {
                    fileIcon = `<svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>`;
                } else {
                    fileIcon = `<svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>`;
                }

                previewHtml = `
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white rounded-xl shadow-sm">
                                ${fileIcon}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-bold text-slate-700">${file.name}</p>
                                </div>
                                <p class="text-[10px] text-slate-400">${(file.size / 1024).toFixed(2)} KB</p>
                                <button type="button" onclick="removeFile()" class="mt-2 text-xs text-rose-500 hover:text-rose-700 font-bold flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus File
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                previewArea.innerHTML = previewHtml;
                previewArea.classList.remove('hidden');
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('file-upload');
            const previewArea = document.getElementById('filePreviewArea');
            
            fileInput.value = '';
            previewArea.classList.add('hidden');
            previewArea.innerHTML = '';
        }

        function handleDrop(event) {
            event.preventDefault();
            const dropZone = event.currentTarget;
            dropZone.classList.remove('border-[#003b71]', 'bg-blue-50/50');
            
            const file = event.dataTransfer.files[0];
            if (file) {
                const fileInput = document.getElementById('file-upload');
                
                // Check file type
                const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!validTypes.includes(file.type)) {
                    alert('Tipe file tidak didukung! Gunakan PDF, JPG, PNG, atau DOCX.');
                    return;
                }
                
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB.');
                    return;
                }
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                
                // Trigger preview
                previewFile(fileInput);
            }
        }
    </script>
    @endpush
</x-app-layout>