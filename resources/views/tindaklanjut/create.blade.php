<x-app-layout>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-10" bg-[#f5f7f8]">
        <!-- Page Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <nav class="flex items-center gap-2 text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">

                    <a class="hover:text-[#003b71] transition-colors" href="{{ route('tindaklanjut.index') }}">Tindak Lanjut</a>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-[#003b71]">Input Tindak Lanjut</span>
                </nav>
                <h2 class="text-[24px] leading-[32px] font-bold text-[#191c20]">Input Tindak Lanjut Baru</h2>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">TANGGAL HARI INI</p>
                <p class="text-sm font-bold text-[#191c20]">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>

        <div class="max-w-5xl">
            <!-- ALERT ERROR VALIDASI -->
            @if ($errors->any())
            <div class="bg-[#ffdad6] border-l-4 border-[#ba1a1a] text-[#93000a] px-5 py-4 rounded-lg mb-6">
                <p class="font-bold text-xs uppercase tracking-wide mb-2">Gagal menyimpan:</p>
                <ul class="text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-[#ba1a1a] flex-shrink-0"></span>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Section 1: Read-only Directive Info -->
                <div class="p-6 bg-[#f2f3fa] border-b border-slate-200">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-[#003b71] text-xl">info</span>
                        <h3 class="text-[18px] leading-[28px] font-semibold text-[#191c20]">Informasi</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- <div class="col-span-1">
                            <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-500 mb-1 block">NO. REGISTRASI</label>
                            <p class="text-sm font-bold text-[#003b71]">
                                @php
                                    $selectedArahan = $arahanList->firstWhere('id', $selectedArahanId);
                                @endphp
                                {{ $selectedArahan ? ($selectedArahan->no_registrasi ?? $selectedArahan->keputusan->nomor_keputusan ?? '-') : '-' }}
                            </p>
                        </div> -->
                        <div class="col-span-3">
                            <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-500 mb-1 block">STRATEGI / ARAHAN</label>
                            <p class="text-sm font-medium text-[#424751] leading-relaxed">
                                {{ $selectedArahan ? $selectedArahan->strategi : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Input Fields -->
                <form action="{{ route('tindaklanjut.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <!-- Hidden Arahan ID -->
                    <input type="hidden" name="arahan_id" value="{{ $selectedArahanId }}">

                   <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- UNIT KERJA -->
    <div class="space-y-2">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            Unit Kerja <span class="text-[#ba1a1a]">*</span>
        </label>
        <select name="unit_kerja_id"
            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 bg-white shadow-sm focus:ring-2 focus:ring-[#003b71]/20 focus:border-[#003b71] transition"
            required>
            <option disabled selected value="">Pilih Unit Kerja</option>
            @foreach($unitKerja as $unit)
                <option value="{{ $unit->id }}" {{ old('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- PERIODE -->
    <div class="space-y-2">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            Periode
        </label>

        <div class="w-full flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 text-slate-700 shadow-sm">
            <span class="material-symbols-outlined text-slate-400 text-[18px]">calendar_month</span>
            <span class="text-sm font-semibold">
                {{ now()->locale('id')->translatedFormat('F Y') }}
            </span>
        </div>

        <!-- hidden input -->
        <input type="hidden" name="periode_bulan" value="{{ date('n') }}">
        <input type="hidden" name="periode_tahun" value="{{ date('Y') }}">
    </div>

</div>

                    <!-- TINDAK LANJUT -->
                    <div class="space-y-2">
                        <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-600 block">
                            TINDAK LANJUT <span class="text-[#ba1a1a]">*</span>
                        </label>
                        <textarea name="tindak_lanjut" rows="6"
                            class="w-full border-slate-300 rounded-lg text-[14px] leading-[20px] font-normal focus:ring-[#003b71] focus:border-[#003b71] placeholder:text-slate-400"
                            placeholder="Deskripsikan secara detail langkah-langkah tindak lanjut yang telah dilakukan..."
                            required>{{ old('tindak_lanjut') }}</textarea>

                    </div>

                    <!-- KENDALA -->
                    <div class="space-y-2">
                        <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-600 block">
                            KENDALA / KEKURANGAN
                        </label>
                        <textarea name="kendala" rows="4"
                            class="w-full border-slate-300 rounded-lg text-[14px] leading-[20px] font-normal focus:ring-[#003b71] focus:border-[#003b71] placeholder:text-slate-400"
                            placeholder="Sebutkan kendala teknis atau administratif yang dihadapi selama proses tindak lanjut...">{{ old('kendala') }}</textarea>
                    </div>

                    <!-- KETERANGAN TAMBAHAN -->
                    <div class="space-y-2">
                        <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-600 block">
                            KETERANGAN TAMBAHAN
                        </label>
                        <textarea name="keterangan" rows="3"
                            class="w-full border-slate-300 rounded-lg text-[14px] leading-[20px] font-normal focus:ring-[#003b71] focus:border-[#003b71] placeholder:text-slate-400"
                            placeholder="Informasi tambahan jika ada...">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Upload Evidence -->
                    <div class="space-y-3">
                        <label class="text-[10px] leading-[16px] tracking-[0.1em] font-bold text-slate-600 block">
                            UPLOAD BUKTI <span class="text-[#ba1a1a]"></span>
                        </label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-[#003b71] hover:bg-slate-50 transition-all cursor-pointer group"
                            onclick="document.getElementById('file-upload').click()">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-[#d4e3ff] transition">
                                <span class="material-symbols-outlined text-slate-400 group-hover:text-[#003b71]">upload_file</span>
                            </div>
                            <h4 class="text-sm font-bold text-[#191c20] mb-1">Klik untuk upload atau drag and drop</h4>
                            <p class="text-xs text-slate-500">PDF, JPG, PNG, atau DOCX (Maks. 5MB)</p>
                            <input id="file-upload" type="file" name="evidence" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.docx">
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-[#f9f9ff] border border-slate-100 rounded-lg">
                            <span class="material-symbols-outlined text-[#ba1a1a] text-lg">info</span>
                            <p class="text-[11px] text-slate-500 font-medium">Pastikan dokumen valid dan telah ditandatangani oleh atasan unit terkait.</p>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="pt-8 border-t border-slate-100 flex justify-end items-center gap-4">
                        <a href="{{ route('tindaklanjut.index') }}"
                            class="px-6 py-2.5 rounded-lg border border-slate-300 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all active:scale-95">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-8 py-2.5 rounded-lg bg-[#003b71] text-white font-bold text-sm shadow-md hover:bg-[#005299] transition-all active:scale-95 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'wght' 600;">send</span>
                            Kirim Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>


        </div>
    </div>

    @push('scripts')
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @endpush
</x-app-layout>