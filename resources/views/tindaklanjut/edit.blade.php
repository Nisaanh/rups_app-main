<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $tindaklanjut->status === 'rejected' ? 'Revisi Laporan Tindak Lanjut' : 'Edit Laporan Tindak Lanjut' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                    <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-rose-800">Catatan Revisi dari Approver:</p>
                                <p class="text-sm text-rose-700 mt-1 italic">"{{ $rejectionNote->note }}"</p>
                                <p class="text-xs text-rose-500 mt-2">Oleh: {{ $rejectionNote->approver->name ?? '-' }} pada {{ $rejectionNote->approved_at ? $rejectionNote->approved_at->format('d M Y H:i') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <form action="{{ route('tindaklanjut.update', $tindaklanjut->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Hidden data --}}
                    <input type="hidden" name="arahan_id" value="{{ $tindaklanjut->arahan_id }}">

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
                            <p class="font-bold text-xs uppercase tracking-wide mb-2">Gagal menyimpan:</p>
                            <ul class="text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Informasi Arahan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Butir Arahan
                            </label>
                            <div class="p-4 bg-slate-50 rounded-xl text-sm font-medium text-slate-700 border border-slate-200 italic">
                                "{{ $tindaklanjut->arahan->strategi }}"
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Target Tanggal
                            </label>
                            <div class="p-4 bg-slate-50 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200">
                                {{ $tindaklanjut->arahan->tanggal_target ? $tindaklanjut->arahan->tanggal_target->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- Unit Kerja Pelaksana (Bisa dipilih) --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Unit Kerja Pelaksana <span class="text-rose-500">*</span>
                        </label>
                        <select name="unit_kerja_id" 
                                class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-semibold text-slate-700 transition" 
                                required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            @foreach($unitKerja as $unit)
                                <option value="{{ $unit->id }}" 
                                    {{ old('unit_kerja_id', $tindaklanjut->unit_kerja_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">
                            Pilih unit kerja yang melaksanakan tindak lanjut ini.
                        </p>
                    </div>

                    {{-- Periode (Readonly) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Bulan Progres <span class="text-rose-500">*</span>
                            </label>
                            <select name="periode_bulan" 
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm font-semibold cursor-not-allowed" 
                                    disabled>
                                @php
                                    $bulanIndonesia = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach($bulanIndonesia as $key => $bulan)
                                    <option value="{{ $key }}" 
                                        {{ old('periode_bulan', $tindaklanjut->periode_bulan) == $key ? 'selected' : '' }}>
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
                                   class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 font-semibold cursor-not-allowed" 
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
                            class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-medium text-slate-700 p-4 placeholder:text-slate-400"
                            required>{{ old('tindak_lanjut', $tindaklanjut->tindak_lanjut) }}</textarea>
                    </div>

                    {{-- Kendala --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Kendala
                        </label>
                        <textarea name="kendala" rows="3"
                            class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-medium text-slate-700 p-4 placeholder:text-slate-400"
                            placeholder="Tuliskan kendala yang dihadapi...">{{ old('kendala', $tindaklanjut->kendala) }}</textarea>
                    </div>

                    {{-- Keterangan Tambahan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Keterangan Tambahan
                        </label>
                        <textarea name="keterangan" rows="3"
                            class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 font-medium text-slate-700 p-4 placeholder:text-slate-400"
                            placeholder="Informasi tambahan jika ada...">{{ old('keterangan', $tindaklanjut->keterangan) }}</textarea>
                    </div>

                    {{-- Evidence --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Evidence / Bukti Pendukung
                        </label>
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-blue-300 transition">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <input type="file" name="evidence" class="text-xs text-slate-500">
                            <p class="text-[10px] text-slate-400 mt-2">PDF, JPG, PNG — maks. 5MB</p>
                        </div>

                        @if($tindaklanjut->evidence_url)
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">File saat ini:</span>
                                </div>
                                <a href="{{ Storage::url($tindaklanjut->evidence_url) }}" target="_blank"
                                   class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    Lihat Evidence
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Buttons --}}
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="{{ route('tindaklanjut.show_arahan', $tindaklanjut->arahan_id) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 text-xs font-bold text-slate-500 uppercase hover:text-slate-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Batal
                        </a>

                        <button type="submit"
                            class="px-8 py-3 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg hover:bg-blue-700 transition active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>