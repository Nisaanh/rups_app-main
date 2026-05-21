<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('tindaklanjut.show_arahan', $tindaklanjut->arahan_id) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            {{-- Alert Info Revisi --}}
            @if($tindaklanjut->status === 'rejected')
            @php
            $rejectionNote = $tindaklanjut->approvals()
            ->where('status', 'rejected')
            ->whereNotNull('note')
            ->latest()
            ->first();
            @endphp
            @if($rejectionNote)
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 bg-rose-100 rounded-lg flex-shrink-0">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-[10px] font-black text-rose-600 uppercase tracking-wider">Catatan Revisi</p>
                            <span class="text-[9px] text-rose-300">•</span>
                            <p class="text-[10px] text-rose-400 font-medium">{{ $rejectionNote->approver->name ?? '-' }}</p>
                        </div>
                        <p class="text-xs text-rose-700 mt-1 italic leading-relaxed line-clamp-2">"{{ $rejectionNote->note }}"</p>
                        <p class="text-[10px] text-rose-400">{{ $rejectionNote->approved_at ? $rejectionNote->approved_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Header --}}
                <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-8 text-white">
                    <div class="relative z-10">
                        <h2 class="text-xl font-black uppercase tracking-tight">
                            {{ $tindaklanjut->status === 'rejected' ? 'Revisi Laporan' : 'Edit Laporan' }}
                        </h2>
                        <p class="text-slate-300 text-sm mt-1">
                            {{ $tindaklanjut->status === 'rejected' ? 'Perbaiki laporan sesuai catatan revisi' : 'Perbarui data laporan tindak lanjut' }}
                        </p>
                    </div>
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-48 h-48 bg-sky-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-36 h-36 bg-indigo-500/10 rounded-full blur-2xl"></div>
                </div>

                <form action="{{ route('tindaklanjut.update', $tindaklanjut->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="arahan_id" value="{{ $tindaklanjut->arahan_id }}">

                    {{-- Error Messages --}}
                    @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl">
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

                    {{-- Informasi Arahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Butir Arahan
                            </label>
                            <div class="p-4 bg-indigo-50 rounded-xl text-sm font-medium text-indigo-800 border border-indigo-100 italic leading-relaxed">
                                "{{ $tindaklanjut->arahan->strategi }}"
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Target Tanggal
                            </label>
                            <div class="p-4 bg-amber-50 rounded-xl text-sm font-bold text-amber-800 border border-amber-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $tindaklanjut->arahan->tanggal_target ? $tindaklanjut->arahan->tanggal_target->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- Unit Kerja --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Unit Kerja Pelaksana <span class="text-rose-500">*</span>
                        </label>
                        <select name="unit_kerja_id"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('unit_kerja_id') border-rose-300 @enderror"
                            required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                            <option value="{{ $unit->id }}"
                                {{ old('unit_kerja_id', $tindaklanjut->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1.5">Pilih unit kerja yang melaksanakan tindak lanjut ini.</p>
                    </div>

                    {{-- Periode --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Bulan Progres <span class="text-rose-500">*</span>
                            </label>
                            <select disabled
                                class="w-full px-5 py-4 bg-slate-100 border border-slate-200 rounded-2xl text-sm font-bold text-slate-500 cursor-not-allowed">
                                @php
                                $bulanIndonesia = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                @endphp
                                @foreach($bulanIndonesia as $key => $bulan)
                                <option value="{{ $key }}" {{ old('periode_bulan', $tindaklanjut->periode_bulan) == $key ? 'selected' : '' }}>
                                    {{ $bulan }}
                                </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="periode_bulan" value="{{ old('periode_bulan', $tindaklanjut->periode_bulan) }}">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Tahun <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                value="{{ old('periode_tahun', $tindaklanjut->periode_tahun) }}"
                                class="w-full px-5 py-4 bg-slate-100 border border-slate-200 rounded-2xl text-sm font-bold text-slate-500 cursor-not-allowed"
                                readonly>
                            <input type="hidden" name="periode_tahun" value="{{ old('periode_tahun', $tindaklanjut->periode_tahun) }}">
                        </div>
                    </div>

                    {{-- Uraian Tindak Lanjut --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Uraian Tindak Lanjut <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="tindak_lanjut" rows="5"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-400 @error('tindak_lanjut') border-rose-300 @enderror"
                            placeholder="Tuliskan uraian tindak lanjut secara detail..."
                            required>{{ old('tindak_lanjut', $tindaklanjut->tindak_lanjut) }}</textarea>
                    </div>

                    {{-- Kendala --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Kendala
                        </label>
                        <textarea name="kendala" rows="3"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-rose-400 focus:ring-2 focus:ring-rose-400/20 transition-all placeholder:text-slate-400"
                            placeholder="Tuliskan kendala yang dihadapi...">{{ old('kendala', $tindaklanjut->kendala) }}</textarea>
                    </div>

                    {{-- Keterangan Tambahan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Keterangan Tambahan
                        </label>
                        <textarea name="keterangan" rows="3"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 transition-all placeholder:text-slate-400"
                            placeholder="Informasi tambahan jika ada...">{{ old('keterangan', $tindaklanjut->keterangan) }}</textarea>
                    </div>

                    {{-- Evidence Upload --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Evidence / Bukti Pendukung
                        </label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-indigo-300 hover:bg-indigo-50/30 transition-all cursor-pointer">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <input type="file" name="evidence" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 file:transition">
                            <p class="text-[10px] text-slate-400 mt-2">PDF, JPG, PNG — maks. 5MB</p>
                        </div>

                        @if($tindaklanjut->evidence_url)
                        <div class="mt-3 p-4 bg-emerald-50 rounded-xl flex items-center justify-between border border-emerald-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-bold text-emerald-700">File evidence tersimpan</span>
                            </div>
                            <a href="{{ Storage::url($tindaklanjut->evidence_url) }}" target="_blank"
                                class="text-xs font-black text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 bg-emerald-100 px-3 py-1.5 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Lihat
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-6 border-t border-slate-100 flex justify-end items-center gap-3">

                        <a href="{{ route('tindaklanjut.show_arahan', $tindaklanjut->arahan_id) }}"
                            class="inline-flex items-center gap-1 px-4 py-2 text-[11px] font-semibold text-slate-500 uppercase hover:text-slate-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-1 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-[11px] font-bold uppercase tracking-wide hover:from-indigo-700 hover:to-sky-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            {{ $tindaklanjut->status === 'rejected'
            ? 'Simpan & Ajukan Ulang'
            : 'Simpan Perubahan' }}
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>