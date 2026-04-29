<x-app-layout>
<div class="space-y-6">
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menunggu Anda</p>
                <p class="text-2xl font-black text-amber-600 leading-none mt-0.5">{{ $pendingCount ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Disetujui</p>
                <p class="text-2xl font-black text-emerald-600 leading-none mt-0.5">{{ $approvedCount ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Direvisi</p>
                <p class="text-2xl font-black text-rose-600 leading-none mt-0.5">{{ $rejectedCount ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         ANTREAN
    ═══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900">
            <div>
                <h3 class="text-sm font-black text-white uppercase tracking-wider">Antrean Persetujuan</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">
                    {{ $roleName }} — Stage {{ $currentStage }}
                </p>
            </div>
            @if(isset($pendingApprovals) && count($pendingApprovals) > 0)
            <span class="px-3 py-1 bg-amber-500 text-white rounded-full text-[10px] font-black uppercase tracking-wider">
                {{ count($pendingApprovals) }} Menunggu
            </span>
            @endif
        </div>

        {{-- List --}}
        <div class="divide-y divide-slate-50">
            @forelse($pendingApprovals as $approval)
            @php
                $bulanIndonesia = [
                    1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                    7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
                ];
                $bulan = $approval->tindakLanjut->periode_bulan
                    ? ($bulanIndonesia[$approval->tindakLanjut->periode_bulan] ?? '-') : '-';
            @endphp

            <div class="p-6 hover:bg-slate-50/40 transition-colors">
                <div class="flex flex-col lg:flex-row gap-5">

                    {{-- Left --}}
                    <div class="flex-1 min-w-0 space-y-4">

                        {{-- Top meta row --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-[9px] font-black uppercase tracking-wider border border-blue-100">
                                Stage {{ $approval->stage }}
                            </span>
                            @if($approval->stage == 5)
                            <span class="px-2.5 py-1 bg-violet-50 text-violet-600 rounded-lg text-[9px] font-black uppercase tracking-wider border border-violet-100">
                                Stage Final
                            </span>
                            @endif
                            <span class="text-[10px] text-slate-400 font-bold">
                                {{ $bulan }} {{ $approval->tindakLanjut->periode_tahun }}
                            </span>
                            <span class="text-slate-200 text-xs">·</span>
                            <span class="text-[10px] text-slate-400 font-bold">
                                Oleh: {{ $approval->tindakLanjut->creator->name ?? '-' }}
                            </span>
                            <span class="text-slate-200 text-xs">·</span>
                            <span class="text-[10px] text-slate-400 font-bold">
                                {{ $approval->tindakLanjut->created_at->format('d M Y') }}
                            </span>
                        </div>

                        {{-- Unit Name --}}
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Unit Kerja</p>
                            <p class="text-base font-black text-slate-800">
                                {{ $approval->tindakLanjut->unitKerja->name ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- Uraian --}}
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Uraian Tindak Lanjut</p>
                            <p class="text-sm text-slate-700 leading-relaxed line-clamp-3">
                                {{ $approval->tindakLanjut->tindak_lanjut }}
                            </p>
                            @if(strlen($approval->tindakLanjut->tindak_lanjut) > 200)
                            <button onclick="openDetailModal({{ $approval->tindakLanjut->id }})"
                                class="mt-2 text-[9px] font-black text-blue-600 uppercase tracking-wider hover:underline">
                                Lihat Selengkapnya →
                            </button>
                            @endif
                        </div>

                        {{-- Kendala + Keterangan --}}
                        @if($approval->tindakLanjut->kendala || $approval->tindakLanjut->keterangan)
                        <div class="flex flex-wrap gap-2">
                            @if($approval->tindakLanjut->kendala)
                            <button onclick="openDetailModal({{ $approval->tindakLanjut->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg text-[9px] font-black uppercase tracking-wider hover:bg-rose-100 transition">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Ada Kendala
                            </button>
                            @endif
                            @if($approval->tindakLanjut->keterangan)
                            <button onclick="openDetailModal({{ $approval->tindakLanjut->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg text-[9px] font-black uppercase tracking-wider hover:bg-blue-100 transition">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Ada Keterangan
                            </button>
                            @endif
                        </div>
                        @endif

                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex lg:flex-col gap-2 flex-shrink-0 lg:w-40 items-start lg:items-stretch">
                        {{-- Detail Button --}}
                        <button onclick="openDetailModal({{ $approval->tindakLanjut->id }})"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-slate-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </button>

                        {{-- Approve --}}
                        <button data-id="{{ $approval->tindakLanjut->id }}" data-stage="{{ $approval->stage }}"
                            onclick="openApproveModal(this.dataset.id, this.dataset.stage)"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-emerald-600 transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $approval->stage == 5 ? 'Selesaikan' : 'Setujui' }}
                        </button>

                        {{-- Revisi --}}
                        <button data-id="{{ $approval->tindakLanjut->id }}"
                            onclick="openRejectModal(this.dataset.id)"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white border border-rose-200 text-rose-600 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-rose-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Revisi
                        </button>

                        {{-- Evidence --}}
                        @if($approval->tindakLanjut->evidence_url)
                        <a href="{{ Storage::url($approval->tindakLanjut->evidence_url) }}" target="_blank"
                            class="flex-1 lg:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-blue-100 transition border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            Evidence
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="py-16 text-center">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-400">Semua antrean sudah diproses</p>
                <p class="text-xs text-slate-300 mt-1">Tidak ada laporan yang menunggu persetujuan Anda</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         RIWAYAT
    ═══════════════════════════════════════ --}}
    @if(isset($approvalHistory) && $approvalHistory->total() > 0)
    @php
        $bulanIndonesia = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Riwayat Keputusan</h3>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ $approvalHistory->total() }} entri
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100">
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Unit & Periode</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Keputusan</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Catatan</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                        <th class="px-6 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($approvalHistory as $history)
                    @php
                        $isTD = str_contains($history->note ?? '', 'Ditetapkan sebagai TD');
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800">{{ $history->tindakLanjut->unitKerja->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">
                                {{ $history->tindakLanjut->periode_bulan ? ($bulanIndonesia[$history->tindakLanjut->periode_bulan] ?? '-') : '-' }}
                                {{ $history->tindakLanjut->periode_tahun }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            @if($history->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[9px] font-black uppercase border border-emerald-100">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Disetujui
                            </span>
                            @elseif($isTD)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase border border-slate-200">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" clip-rule="evenodd"/></svg>
                                TD - Tidak Ditindaklanjuti
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg text-[9px] font-black uppercase border border-rose-100">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                Direvisi
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-slate-500 max-w-[200px] truncate">{{ $history->note ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-[10px] font-bold text-slate-500">{{ $history->updated_at->format('d M Y') }}</p>
                            <p class="text-[9px] text-slate-400">{{ $history->updated_at->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('tindaklanjut.show_arahan', $history->tindakLanjut->arahan_id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-[9px] font-black text-slate-500 uppercase tracking-wider hover:text-blue-600 transition rounded-lg hover:bg-blue-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-50">
            {{ $approvalHistory->links() }}
        </div>
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: DETAIL LAPORAN
═══════════════════════════════════════════════════ --}}
<div id="detailModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[85vh] flex flex-col overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-900 flex-shrink-0">
            <div>
                <h3 class="text-sm font-black text-white uppercase tracking-wider">Detail Laporan</h3>
                <p id="detailModalUnit" class="text-[10px] text-slate-400 mt-0.5 font-bold"></p>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 text-slate-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-6 space-y-4">
            <div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Uraian Tindak Lanjut</p>
                <p id="detailModalUraian" class="text-sm text-slate-700 leading-relaxed bg-slate-50 rounded-xl p-4 border border-slate-100"></p>
            </div>

            <div id="detailKendalaWrapper" class="hidden">
                <p class="text-[9px] font-bold text-rose-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Kendala
                </p>
                <p id="detailModalKendala" class="text-sm text-slate-700 leading-relaxed bg-rose-50 rounded-xl p-4 border border-rose-100"></p>
            </div>

            <div id="detailKeteranganWrapper" class="hidden">
                <p class="text-[9px] font-bold text-blue-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Keterangan Tambahan
                </p>
                <p id="detailModalKeterangan" class="text-sm text-slate-700 leading-relaxed bg-blue-50 rounded-xl p-4 border border-blue-100"></p>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60 flex justify-end flex-shrink-0">
            <button onclick="closeDetailModal()" class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xl hover:bg-slate-800 transition">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: APPROVE
═══════════════════════════════════════════════════ --}}
<div id="approveModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-100">
            <h3 id="approveModalTitle" class="text-base font-black text-slate-800 uppercase tracking-tight">Konfirmasi Persetujuan</h3>
            <p id="approveModalDesc" class="text-xs text-slate-400 font-medium mt-1">Pastikan laporan sudah sesuai standar.</p>
        </div>

        <form id="approveForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="space-y-2">
                <label class="flex items-center gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                    <input type="radio" id="optionLanjut" name="result" value="lanjut" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" onchange="toggleOtherOptions()">
                    <span id="labelLanjut" class="text-xs font-bold text-slate-700 uppercase tracking-wide">Lanjutkan ke Stage Berikutnya</span>
                </label>
               
                <label id="optionTDWrapper" class="flex items-center gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-slate-800 has-[:checked]:bg-slate-900/5">
                    <input type="radio" id="optionTD" name="result" value="td" class="w-4 h-4 text-slate-800 focus:ring-slate-800" onchange="toggleOtherOptions()">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">TD — Tidak Dapat Ditindaklanjuti</span>
                </label>
            </div>

            <div id="noteWrapper" class="hidden">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    Catatan Revisi <span class="text-rose-500">*</span>
                </label>
                <textarea name="note" id="approveNote" rows="3"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-400 text-sm font-medium resize-none"
                    placeholder="Jelaskan alasan revisi..."></textarea>
            </div>

            <div id="tdNoteWrapper" class="hidden">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    Alasan TD <span class="text-rose-500">*</span>
                </label>
                <textarea name="td_note" id="tdNote" rows="3"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-800 focus:border-slate-800 text-sm font-medium resize-none"
                    placeholder="Jelaskan mengapa tidak dapat ditindaklanjuti..."></textarea>
                <p id="tdNoteError" class="hidden mt-1 text-[10px] font-bold text-rose-500 uppercase">⚠ Alasan TD wajib diisi.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeApproveModal()"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="button" id="approveSubmitBtn" onclick="submitApproveForm()"
                    class="flex-[2] px-4 py-3 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-emerald-600 transition shadow-sm">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     MODAL: REJECT
═══════════════════════════════════════════════════ --}}
<div id="rejectModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        <div class="px-6 py-5 border-b border-rose-50 bg-rose-50/40">
            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Minta Revisi</h3>
            <p class="text-xs text-slate-400 font-medium mt-1">Berikan catatan agar unit dapat memperbaiki laporan.</p>
        </div>

        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    Alasan Revisi <span class="text-rose-500">*</span>
                </label>
                <textarea name="note" id="rejectNote" rows="4" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-400 text-sm font-medium resize-none"
                    placeholder="Sebutkan bagian yang perlu diperbaiki..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-slate-200 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-[2] px-4 py-3 bg-rose-600 text-white rounded-xl font-bold text-[10px] uppercase tracking-wider hover:bg-rose-700 transition shadow-sm">
                    Kirim Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tindakLanjutData = {
        @foreach($pendingApprovals as $approval)
        "{{ $approval->tindakLanjut->id }}": {
            unit: "{{ addslashes($approval->tindakLanjut->unitKerja->name ?? 'N/A') }}",
            uraian: "{{ addslashes($approval->tindakLanjut->tindak_lanjut) }}",
            kendala: "{{ addslashes($approval->tindakLanjut->kendala ?? '') }}",
            keterangan: "{{ addslashes($approval->tindakLanjut->keterangan ?? '') }}"
        },
        @endforeach
    };

    // Make functions globally accessible
    window.openDetailModal = function(id) {
        const data = tindakLanjutData[id];
        if (!data) {
            console.error('Data not found for id:', id);
            return;
        }

        document.getElementById('detailModalUnit').textContent = data.unit;
        document.getElementById('detailModalUraian').textContent = data.uraian;

        const kendalaWrapper = document.getElementById('detailKendalaWrapper');
        if (data.kendala && data.kendala.trim() !== '') {
            document.getElementById('detailModalKendala').textContent = data.kendala;
            kendalaWrapper.classList.remove('hidden');
        } else {
            kendalaWrapper.classList.add('hidden');
        }

        const keteranganWrapper = document.getElementById('detailKeteranganWrapper');
        if (data.keterangan && data.keterangan.trim() !== '') {
            document.getElementById('detailModalKeterangan').textContent = data.keterangan;
            keteranganWrapper.classList.remove('hidden');
        } else {
            keteranganWrapper.classList.add('hidden');
        }

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeDetailModal = function() {
        const modal = document.getElementById('detailModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    };

    // Approve Modal Functions
    window.openApproveModal = function(id, stage) {
        const isLast = parseInt(stage) === 5;
        const isFirst = parseInt(stage) === 1;

        const form = document.getElementById('approveForm');
        if (form) form.action = `/approval/${id}/approve`;

        const optionLanjut = document.getElementById('optionLanjut');
        const labelLanjut = document.getElementById('labelLanjut');
        const tdWrapper = document.getElementById('optionTDWrapper');
        const submitBtn = document.getElementById('approveSubmitBtn');
        const modalTitle = document.getElementById('approveModalTitle');
        const modalDesc = document.getElementById('approveModalDesc');

        // Reset
        if (optionLanjut) optionLanjut.checked = true;
        const optionTD = document.getElementById('optionTD');
        if (optionTD) optionTD.checked = false;
        
        const rejectedRadio = document.querySelector('input[name="result"][value="rejected"]');
        if (rejectedRadio) rejectedRadio.checked = false;
        
        const noteWrapper = document.getElementById('noteWrapper');
        const tdNoteWrapper = document.getElementById('tdNoteWrapper');
        if (noteWrapper) noteWrapper.classList.add('hidden');
        if (tdNoteWrapper) tdNoteWrapper.classList.add('hidden');
        
        const approveNote = document.getElementById('approveNote');
        const tdNote = document.getElementById('tdNote');
        if (approveNote) approveNote.value = '';
        if (tdNote) tdNote.value = '';
        
        const tdNoteError = document.getElementById('tdNoteError');
        if (tdNoteError) tdNoteError.classList.add('hidden');

        if (tdWrapper && isFirst) {
            tdWrapper.style.display = 'none';
        } else if (tdWrapper) {
            tdWrapper.style.display = '';
        }

        if (isLast && optionLanjut) {
            optionLanjut.value = 'selesai';
            if (labelLanjut) labelLanjut.textContent = 'Selesai — Tindak Lanjut Dinyatakan Tuntas';
            if (submitBtn) submitBtn.textContent = 'Konfirmasi Selesai';
            if (modalTitle) modalTitle.textContent = 'Finalisasi Laporan';
            if (modalDesc) modalDesc.textContent = 'Laporan akan dinyatakan selesai dan tidak dapat diubah lagi.';
        } else if (optionLanjut) {
            optionLanjut.value = 'lanjut';
            if (labelLanjut) labelLanjut.textContent = 'Lanjutkan ke Stage Berikutnya';
            if (submitBtn) submitBtn.textContent = 'Konfirmasi Setuju';
            if (modalTitle) modalTitle.textContent = 'Konfirmasi Persetujuan';
            if (modalDesc) modalDesc.textContent = 'Pastikan laporan sudah sesuai standar.';
        }

        const modal = document.getElementById('approveModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeApproveModal = function() {
        const modal = document.getElementById('approveModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    };

    window.toggleOtherOptions = function() {
        const selectedValue = document.querySelector('input[name="result"]:checked');
        if (!selectedValue) return;
        
        const noteWrapper = document.getElementById('noteWrapper');
        const tdNoteWrapper = document.getElementById('tdNoteWrapper');
        
        if (noteWrapper) noteWrapper.classList.add('hidden');
        if (tdNoteWrapper) tdNoteWrapper.classList.add('hidden');
        
        if (selectedValue.value === 'rejected' && noteWrapper) {
            noteWrapper.classList.remove('hidden');
        } else if (selectedValue.value === 'td' && tdNoteWrapper) {
            tdNoteWrapper.classList.remove('hidden');
        }
    };

    window.submitApproveForm = function() {
        const selectedValue = document.querySelector('input[name="result"]:checked');
        if (!selectedValue) {
            alert('Pilih salah satu opsi!');
            return;
        }
        
        if (selectedValue.value === 'rejected') {
            const note = document.getElementById('approveNote');
            if (!note || note.value.trim() === '') {
                alert('Catatan revisi wajib diisi!');
                if (note) note.focus();
                return;
            }
        }
        
        if (selectedValue.value === 'td') {
            const note = document.getElementById('tdNote');
            if (!note || note.value.trim() === '') {
                const error = document.getElementById('tdNoteError');
                if (error) error.classList.remove('hidden');
                if (note) note.focus();
                return;
            }
        }
        
        const error = document.getElementById('tdNoteError');
        if (error) error.classList.add('hidden');
        
        const form = document.getElementById('approveForm');
        if (form) form.submit();
    };

    // Reject Modal Functions
   // Reject Modal Functions - UPDATE
window.openRejectModal = function(id) {
    console.log('Opening reject modal for ID:', id); // Debug
    
    const noteField = document.getElementById('rejectNote');
    if (noteField) noteField.value = '';
    
    const form = document.getElementById('rejectForm');
    if (form) {
        form.action = `/approval/${id}/reject`;
        console.log('Form action set to:', form.action); // Debug
    }
    
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

// Tambahkan event listener untuk form submit
document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            console.log('Reject form submitted'); // Debug
            console.log('Form action:', this.action);
            console.log('Note value:', document.getElementById('rejectNote').value);
        });
    }
});
    window.closeRejectModal = function() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    };

    // Close on backdrop click
    const modals = ['detailModal', 'approveModal', 'rejectModal'];
    modals.forEach(modalId => {
        const el = document.getElementById(modalId);
        if (el) {
            el.addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'detailModal') window.closeDetailModal();
                    else if (modalId === 'approveModal') window.closeApproveModal();
                    else if (modalId === 'rejectModal') window.closeRejectModal();
                }
            });
        }
    });
});
</script>

</x-app-layout>