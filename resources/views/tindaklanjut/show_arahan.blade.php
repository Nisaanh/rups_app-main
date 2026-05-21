<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            {{-- Arahan Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10 space-y-4">
                    {{-- Meta Badges --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white/90 rounded-lg text-[10px] font-black uppercase tracking-widest border border-white/20">
                            {{ $arahan->bidang->name ?? 'Umum' }}
                        </span>
                        @if($arahan->tanggal_target)
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-md text-white/90 rounded-lg text-[10px] font-black uppercase tracking-widest border border-white/20 flex items-center gap-1.5">
                            <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Target: {{ $arahan->tanggal_target->translatedFormat('d F Y') }}
                        </span>
                        @endif
                    </div>

                    <h1 class="text-sm md:text-base font-bold text-white leading-relaxed">
                        {{ $arahan->strategi ?? $arahan->arahan }}
                    </h1>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- PIC Cards --}}
            @if($arahan->pics->count() > 0)
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-50 rounded-xl">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">PIC Terkait</h3>
                        <p class="text-[10px] text-slate-400">{{ $arahan->pics->count() }} penanggung jawab</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($arahan->pics as $pic)
                    <div class="inline-flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition group">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-400 to-sky-400 flex items-center justify-center text-white text-[10px] font-black shadow-sm">
                            {{ strtoupper(substr($pic->name, 0, 1)) }}
                        </div>
                        <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-700 transition">
                            {{ $pic->name }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Daftar Penugasan Unit --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Section Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Daftar Penugasan Unit</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Klik "Lihat Detail" untuk melihat laporan lengkap</p>
                        </div>
                        @if($tlPerUnit->count() > 0)
                        <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-xs font-black uppercase border border-indigo-100">
                            {{ $tlPerUnit->count() }} Unit
                        </span>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @if($tlPerUnit->count() > 0)
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Unit Kerja</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status Laporan</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($tlPerUnit as $unitId => $tlList)
                            @php
                            $firstTl = $tlList->sortByDesc('created_at')->first();
                            $unitName = $firstTl->unitKerja->name ?? 'Unit #'.$unitId;
                            $tlStatus = $firstTl->status ?? 'pending';

                            $badgeStyles = match($tlStatus) {
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                            'in_approval' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'td' => 'bg-slate-100 text-slate-600 border-slate-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-100'
                            };
                            $badgeText = match($tlStatus) {
                            'approved' => 'Selesai',
                            'rejected' => 'Perlu Revisi',
                            'in_approval' => 'Proses Approval',
                            'td' => 'Tidak Ditindaklanjuti',
                            default => 'Menunggu'
                            };
                            $dotColor = match($tlStatus) {
                            'approved' => 'bg-emerald-500',
                            'rejected' => 'bg-rose-500',
                            'in_approval' => 'bg-blue-500',
                            'td' => 'bg-slate-400',
                            default => 'bg-amber-500'
                            };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition group">
                                {{-- Unit Kerja --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-black text-xs shadow-sm">
                                            {{ strtoupper(substr($unitName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $unitName }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                                                {{ $firstTl->created_at->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeStyles }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                        {{ $badgeText }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="showDetail({{ $loop->index }})"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat Detail
                                        </button>

                                        @can('create_tindak_lanjut')
                                        @if($firstTl && $firstTl->status === 'rejected' && $firstTl->unit_kerja_id === Auth::user()->unit_kerja_id)
                                        <a href="{{ route('tindaklanjut.edit', $firstTl->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all"
                                            title="Revisi Laporan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    {{-- Empty State --}}
                    <div class="py-16 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-400">Belum Ada Laporan</h4>
                        <p class="text-xs text-slate-300 mt-1 max-w-md mx-auto">
                            Belum ada unit kerja yang melaporkan tindak lanjut untuk arahan ini.
                        </p>
                        <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full border border-slate-100">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Input Unit Kerja</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-slate-900 to-slate-800 flex-shrink-0">
                <h3 id="modalTitle" class="text-lg font-black text-white uppercase tracking-wider">Detail Laporan Unit</h3>
                <button onclick="closeDetail()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-slate-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="overflow-y-auto flex-1 p-6">
                {{-- Diisi JavaScript --}}
            </div>
        </div>
    </div>

    @php
    $laporanDataArray = [];
    foreach($tlPerUnit as $index => $tlList) {
    $tlSorted = $tlList->sortByDesc('created_at');
    $latestTl = $tlSorted->first();
    $unitName = $latestTl->unitKerja->name ?? 'Unit #' . $index;
    $bulanIndonesia = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $bulan = $latestTl->periode_bulan ? ($bulanIndonesia[$latestTl->periode_bulan] ?? '-') : '-';

    $tdApproval = $latestTl->approvals()
    ->where('status', 'rejected')
    ->where('note', 'like', '%Ditetapkan sebagai TD%')
    ->latest()->first();

    $revisiNote = $latestTl->approvals()
    ->where('status', 'rejected')
    ->where('note', 'not like', '%Ditetapkan sebagai TD%')
    ->whereNotNull('note')
    ->latest()->first();

    $approvalsArray = [];
    foreach($latestTl->approvals()->with('approver')->orderBy('stage')->get() as $a) {
    $approvalsArray[] = [
    'stage' => $a->stage,
    'status' => $a->status,
    'approved_at' => $a->approved_at ? $a->approved_at->format('d M Y H:i') : null,
    'approver' => $a->approver->name ?? null,
    'note' => $a->note ?? null,
    'is_td' => str_contains($a->note ?? '', 'Ditetapkan sebagai TD')
    ];
    }

    $laporanDataArray[] = [
    'unitName' => $unitName,
    'arahanText' => $arahan->strategi ?? $arahan->arahan ?? '-',  // FIXED: Added proper field name
    'periode' => $bulan . ' ' . $latestTl->periode_tahun,
    'tindakLanjut' => $latestTl->tindak_lanjut,
    'kendala' => $latestTl->kendala ?? '',
    'keterangan' => $latestTl->keterangan ?? '',
    'evidenceUrl' => $latestTl->evidence_url ? Storage::url($latestTl->evidence_url) : '',
    'createdAt' => $latestTl->created_at->format('d M Y H:i'),
    'creator' => $latestTl->creator->name ?? '-',
    'status' => $latestTl->status,
    'tdNote' => $tdApproval->note ?? '',
    'tdBy' => $tdApproval->approver->name ?? '',
    'tdAt' => $tdApproval && $tdApproval->approved_at ? $tdApproval->approved_at->format('d M Y H:i') : '',
    'revisiNote' => $revisiNote->note ?? '',
    'revisiBy' => $revisiNote->approver->name ?? '',
    'revisiAt' => $revisiNote && $revisiNote->approved_at ? $revisiNote->approved_at->format('d M Y H:i') : '',
    'approvals' => $approvalsArray
    ];
    }
    @endphp

    <script>
        'use strict';  // ADDED: Strict mode

        const laporanData = JSON.parse('{!! addslashes(json_encode($laporanDataArray)) !!}');

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function showDetail(idx) {
            const data = laporanData[idx];
            if (!data) return;

            document.getElementById('modalTitle').textContent = `Detail Laporan - ${data.unitName}`;

            // Approval Timeline
            let approvalsHtml = '';
            const stages = ['Atasan Auditi', 'Tim Monitoring', 'Pengendali Teknis', 'Pengendali Mutu', 'Penanggung Jawab'];
            
            stages.forEach(function(stageName, stageNum) {
                const approval = data.approvals?.find(function(a) { return a.stage === stageNum + 1; });
                const status = approval?.status || 'pending';
                const isApproved = status === 'approved';
                const isRejected = status === 'rejected';
                const isPending = status === 'pending';
                const isTD = approval?.is_td || false;

                let circleClass = 'bg-white border-slate-200';
                let iconHtml = '<span class="text-[10px] font-black text-slate-400">' + (stageNum + 1) + '</span>';

                if (isApproved) {
                    circleClass = 'bg-emerald-500 border-emerald-500';
                    iconHtml = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
                } else if (isRejected && isTD) {
                    circleClass = 'bg-slate-500 border-slate-500';
                    iconHtml = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>';
                } else if (isRejected) {
                    circleClass = 'bg-rose-500 border-rose-500';
                    iconHtml = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';
                } else if (isPending) {
                    circleClass = 'bg-slate-900 border-slate-900';
                    iconHtml = '<div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>';
                }

                let lineLeft = stageNum > 0 ? '<div class="flex-1 h-0.5 ' + (isApproved ? 'bg-emerald-400' : 'bg-slate-200') + '"></div>' : '';
                let lineRight = stageNum < 4 ? '<div class="flex-1 h-0.5 bg-slate-200"></div>' : '';

                approvalsHtml += `
                <div class="flex-1 flex flex-col items-center">
                    <div class="relative flex items-center w-full">
                        ${lineLeft}
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center border-2 ${circleClass}">
                            ${iconHtml}
                        </div>
                        ${lineRight}
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wide ${isApproved ? 'text-emerald-600' : (isPending ? 'text-slate-900' : 'text-slate-400')}">${stageName}</p>
                        ${approval?.approved_at ? '<p class="text-[8px] text-slate-400 mt-0.5">' + approval.approved_at + '</p>' : ''}
                        ${isRejected && approval?.note ? '<p class="text-[9px] ' + (isTD ? 'text-slate-500' : 'text-rose-500') + ' mt-1 italic truncate max-w-[80px]">"' + escapeHtml(approval.note.substring(0, 30)) + '"</p>' : ''}
                    </div>
                </div>`;
            });

            // Status Badge
            let statusBadge = '';
            if (data.status === 'approved') {
                statusBadge = '<span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[10px] font-black uppercase">✓ Selesai</span>';
            } else if (data.status === 'rejected') {
                statusBadge = '<span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[10px] font-black uppercase">↺ Perlu Revisi</span>';
            } else if (data.status === 'in_approval') {
                statusBadge = '<span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-black uppercase">⏳ Proses Approval</span>';
            } else if (data.status === 'td') {
                statusBadge = '<span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded-full text-[10px] font-black uppercase">✗ TD</span>';
            } else {
                statusBadge = '<span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px] font-black uppercase">⏰ Menunggu</span>';
            }

            // Catatan
            let catatanHtml = '';
            if (data.tdNote) {
                catatanHtml = `
                <div class="mb-4 p-4 bg-slate-100 border border-slate-200 rounded-xl">
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-wider mb-2">Keputusan Final: TD</p>
                    <p class="text-sm text-slate-700 italic mb-2">"${escapeHtml(data.tdNote)}"</p>
                    <p class="text-[10px] text-slate-500">— ${escapeHtml(data.tdBy)}, ${data.tdAt}</p>
                </div>`;
            } else if (data.revisiNote) {
                catatanHtml = `
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-2">Catatan Revisi</p>
                    <p class="text-sm text-rose-700 italic mb-2">"${escapeHtml(data.revisiNote)}"</p>
                    <p class="text-[10px] text-rose-400">— ${escapeHtml(data.revisiBy)}, ${data.revisiAt}</p>
                </div>`;
            }

            let evidenceHtml = '';
            if (data.evidenceUrl) {
                evidenceHtml = `
                <div class="mb-4">
                    <a href="${data.evidenceUrl}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold hover:bg-blue-100 transition border border-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Lihat Evidence
                    </a>
                </div>`;
            }

            let kendalaHtml = '';
            if (data.kendala) {
                kendalaHtml = `
                <div class="mb-4">
                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-wider mb-1">Kendala</p>
                    <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                        <p class="text-sm text-slate-700">${escapeHtml(data.kendala)}</p>
                    </div>
                </div>`;
            }

            let keteranganHtml = '';
            if (data.keterangan) {
                keteranganHtml = `
                <div class="mb-4">
                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-wider mb-1">Keterangan</p>
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-sm text-slate-700">${escapeHtml(data.keterangan)}</p>
                    </div>
                </div>`;
            }

            document.getElementById('modalContent').innerHTML = `
                <div class="grid grid-cols-2 gap-5 mb-6">
                    <div class="mb-5 p-4 bg-indigo-50 rounded-xl border border-indigo-100 col-span-2">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-wider mb-1">Butir Arahan</p>
                        <p class="text-sm font-bold text-indigo-800 leading-relaxed">${escapeHtml(data.arahanText)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Unit Kerja</p>
                        <p class="text-sm font-bold text-slate-800">${escapeHtml(data.unitName)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Periode</p>
                        <p class="text-sm font-bold text-slate-800">${escapeHtml(data.periode)}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Tanggal Input</p>
                        <p class="text-sm font-bold text-slate-800">${data.createdAt}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Status</p>
                        ${statusBadge}
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Uraian Tindak Lanjut</p>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-sm text-slate-700 leading-relaxed">${escapeHtml(data.tindakLanjut)}</p>
                    </div>
                </div>
                ${kendalaHtml}
                ${keteranganHtml}
                ${evidenceHtml}
                ${catatanHtml}
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-4">Timeline Approval</p>
                    <div class="flex items-start gap-0">
                        ${approvalsHtml}
                    </div>
                </div>
                <div class="pt-4 text-right border-t border-slate-100 mt-4">
                    <p class="text-[10px] text-slate-400">Diinput oleh: ${escapeHtml(data.creator)}</p>
                </div>
            `;

            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetail();
        });
    </script>
</x-app-layout>