<x-app-layout>
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    {{-- Header Section --}}
    <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Badge Bidang: Dibuat sangat kecil & ringkas --}}
                <span class="px-2 py-0.5 bg-white/10 text-white/90 rounded-md text-[10px] font-bold uppercase tracking-widest border border-white/10">
                    {{ $arahan->bidang->name ?? '-' }}
                </span>

                {{-- Badge Target: Dibuat lebih tipis --}}
                <span class="text-[10px] text-white/60 font-medium tracking-wide">
                    Target: <span class="text-white/90 font-bold ml-1">{{ $arahan->tanggal_target ? $arahan->tanggal_target->format('d M Y') : '-' }}</span>
                </span>
            </div>

            
           <p class="text-m font-medium text-white/90 leading-relaxed">
                {{ $arahan->strategi }}
            </p>
        </div>
    </div>

    {{-- PIC Section --}}
    <div class="px-6 py-4 bg-white">
        <div class="flex flex-col gap-3">
            {{-- Label: Sangat kecil --}}
            <h3 class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">PIC Terkait</h3>

            <div class="flex flex-wrap gap-2">
                @forelse($arahan->pics as $pic)
                <div class="inline-flex items-center gap-2 px-2.5 py-1.5 bg-slate-50 border border-slate-100 rounded-lg">
                    {{-- Avatar: Diperkecil (w-5 h-5) --}}
                    <div class="w-5 h-5 bg-slate-200 rounded flex items-center justify-center text-slate-600 text-[9px] font-black">
                        {{ strtoupper(substr($pic->name, 0, 1)) }}
                    </div>
                    {{-- Nama PIC: Diperkecil ke text-xs --}}
                    <span class="text-xs font-bold text-slate-600 tracking-tight">
                        {{ $pic->name }}
                    </span>
                </div>
                @empty
                <span class="text-[11px] text-slate-400 italic">Belum ada PIC</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

        {{-- TABEL DAFTAR UNIT KERJA --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Daftar Penugasan Unit</h3>
                        <p class="text-[9px] text-slate-400 font-bold mt-0.5">Klik "Lihat Detail" untuk melihat laporan lengkap</p>
                    </div>
                    @if($tlPerUnit->count() > 0)
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
                            {{ $tlPerUnit->count() }} Unit
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($tlPerUnit->count() > 0)
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase text-[9px] font-black tracking-widest border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">UNIT KERJA</th>
                            <th class="px-6 py-4 text-center">STATUS LAPORAN</th>
                            <th class="px-6 py-4 text-right">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($tlPerUnit as $unitId => $tlList)
                        @php
                        $firstTl = $tlList->sortByDesc('created_at')->first();
                        $unitName = $firstTl->unitKerja->name ?? 'Unit #'.$unitId;

                        $tlStatus = $firstTl->status ?? 'pending';

                        $badgeColor = match($tlStatus) {
                        'approved' => 'emerald',
                        'rejected' => 'orange',
                        'in_approval' => 'blue',
                        'td' => 'slate',
                        default => 'amber'
                        };
                        $badgeText = match($tlStatus) {
                        'approved' => 'Selesai',
                        'rejected' => 'Perlu Revisi',
                        'in_approval' => 'Dalam Approval',
                        'td' => 'Tidak Ditindaklanjuti',
                        default => 'Pending'
                        };
                        $badgeIcon = match($tlStatus) {
                        'approved' => '✓',
                        'rejected' => '↺',
                        'in_approval' => '⏳',
                        'td' => '✗',
                        default => '⏰'
                        };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-black text-sm shadow-sm">
                                        {{ substr($unitName, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $unitName }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $firstTl->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $badgeColor }}-50 text-{{ $badgeColor }}-700 border border-{{ $badgeColor }}-100 rounded-full text-[10px] font-black uppercase tracking-tighter">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $badgeColor }}-600"></span>
                                    {{ $badgeIcon }} {{ $badgeText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="showDetail({{ $loop->index }})" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-600 hover:text-blue-600 rounded-xl transition hover:bg-blue-50 text-xs font-black uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                {{-- Empty State yang Lebih Bagus --}}
                <div class="py-16 px-4 text-center">
                    <div class="max-w-sm mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full animate-pulse"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-lg font-black text-slate-700 mb-2">Belum Ada Laporan</h4>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            Belum ada unit kerja yang melaporkan tindak lanjut<br>
                            untuk arahan ini.
                        </p>
                        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Input dari Unit Kerja</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL LAPORAN --}}
    <div id="detailModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-900 flex-shrink-0">
                <h3 id="modalTitle" class="text-sm font-black text-white uppercase tracking-wider">Detail Laporan Unit</h3>
                <button onclick="closeDetail()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 text-slate-400 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="overflow-y-auto flex-1 p-6">
                {{-- Content diisi JavaScript --}}
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

    // Ambil TD Approval (keputusan TD)
    $tdApproval = $latestTl->approvals()
    ->where('status', 'rejected')
    ->where('note', 'like', '%Ditetapkan sebagai TD%')
    ->latest()
    ->first();

    // Ambil Revisi Note (bukan TD)
    $revisiNote = $latestTl->approvals()
    ->where('status', 'rejected')
    ->where('note', 'not like', '%Ditetapkan sebagai TD%')
    ->whereNotNull('note')
    ->latest()
    ->first();

    $approvalsArray = [];
    $approvals = $latestTl->approvals()->with('approver')->orderBy('stage')->get();
    foreach($approvals as $a) {
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
        const laporanData = @json($laporanDataArray);

        function showDetail(idx) {
            const data = laporanData[idx];
            if (!data) return;

            document.getElementById('modalTitle').innerHTML = `Detail Laporan - ${data.unitName}`;

            let approvalsHtml = '';
            const stages = ['Atasan Auditi', 'Tim Monitoring', 'Pengendali Teknis', 'Pengendali Mutu', 'Penanggung Jawab'];
            stages.forEach((stageName, stageNum) => {
                const approval = data.approvals?.find(a => a.stage === stageNum + 1);
                const status = approval?.status || 'pending';
                const isApproved = status === 'approved';
                const isRejected = status === 'rejected';
                const isPending = status === 'pending';
                const approvedAt = approval?.approved_at || '';
                const note = approval?.note || '';
                const isTD = approval?.is_td || false;

                approvalsHtml += `
                <div class="flex-1 flex flex-col items-center">
                    <div class="relative flex items-center w-full">
                        ${stageNum > 0 ? `<div class="flex-1 h-px ${isApproved ? 'bg-emerald-400' : 'bg-slate-200'}"></div>` : ''}
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center border-2
                            ${isApproved ? 'bg-emerald-500 border-emerald-500' : (isRejected && isTD ? 'bg-slate-500 border-slate-500' : (isRejected ? 'bg-rose-500 border-rose-500' : (isPending ? 'bg-slate-900 border-slate-900' : 'bg-white border-slate-200')))}">
                            ${isApproved ? '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : (isRejected && isTD ? '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>' : (isRejected ? '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>' : (isPending ? '<div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>' : `<span class="text-[10px] font-black text-slate-400">${stageNum + 1}</span>`)))}
                        </div>
                        ${stageNum < 4 ? `<div class="flex-1 h-px bg-slate-200"></div>` : ''}
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wide ${isApproved ? 'text-emerald-600' : (isPending ? 'text-slate-900' : 'text-slate-400')}">${stageName}</p>
                        ${approvedAt ? `<p class="text-[8px] text-slate-400 mt-0.5">${approvedAt}</p>` : ''}
                        ${isRejected && note ? `<p class="text-[9px] ${isTD ? 'text-slate-500' : 'text-rose-500'} mt-1 italic truncate max-w-[80px]">"${escapeHtml(note.substring(0, 30))}"</p>` : ''}
                    </div>
                </div>`;
            });

            // Status Badge untuk modal berdasarkan status per unit
            const statusBadge = data.status === 'approved' ? '<span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded text-[10px] font-bold">✓ Selesai</span>' :
                (data.status === 'rejected' ? '<span class="px-2 py-1 bg-orange-50 text-orange-700 rounded text-[10px] font-bold">↺ Perlu Revisi</span>' :
                    (data.status === 'in_approval' ? '<span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-[10px] font-bold">⏳ Dalam Approval</span>' :
                        (data.status === 'td' ? '<span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[10px] font-bold">✗ TD - Tidak Ditindaklanjuti</span>' :
                            '<span class="px-2 py-1 bg-amber-50 text-amber-700 rounded text-[10px] font-bold">⏰ Pending</span>')));

            // Catatan TD atau Revisi
            let catatanHtml = '';
            if (data.tdNote) {
                catatanHtml = `
                <div class="mb-4 p-4 bg-slate-100 border border-slate-300 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-[9px] font-black text-slate-600 uppercase tracking-wider">Keputusan Final: Tidak Dapat Ditindaklanjuti (TD)</p>
                    </div>
                    <p class="text-sm text-slate-700 italic mb-2">"${escapeHtml(data.tdNote)}"</p>
                    <p class="text-[10px] text-slate-500 mt-1">— ${data.tdBy}, ${data.tdAt}</p>
                </div>`;
            } else if (data.revisiNote) {
                catatanHtml = `
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <p class="text-[9px] font-black text-rose-500 uppercase tracking-wider">Catatan Revisi</p>
                    </div>
                    <p class="text-sm text-rose-700 italic mb-2">"${escapeHtml(data.revisiNote)}"</p>
                    <p class="text-[10px] text-rose-400 mt-1">— ${data.revisiBy}, ${data.revisiAt}</p>
                </div>`;
            }

            document.getElementById('modalContent').innerHTML = `
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Unit Kerja</p>
                        <p class="text-sm font-bold text-slate-800">${escapeHtml(data.unitName)}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Periode Laporan</p>
                        <p class="text-sm font-bold text-slate-800">${escapeHtml(data.periode)}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Tanggal Input</p>
                        <p class="text-sm font-bold text-slate-800">${data.createdAt}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Status Laporan</p>
                        ${statusBadge}
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-1">Uraian Tindak Lanjut</p>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-sm text-slate-700 leading-relaxed">${escapeHtml(data.tindakLanjut)}</p>
                    </div>
                </div>
                ${data.kendala ? `
                <div class="mb-4">
                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-wider mb-1">Kendala</p>
                    <div class="p-4 bg-rose-50 rounded-xl border border-rose-100">
                        <p class="text-sm text-slate-700">${escapeHtml(data.kendala)}</p>
                    </div>
                </div>` : ''}
                ${data.keterangan ? `
                <div class="mb-4">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-wider mb-1">Keterangan Tambahan</p>
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-sm text-slate-700">${escapeHtml(data.keterangan)}</p>
                    </div>
                </div>` : ''}
                ${data.evidenceUrl ? `
                <div class="mb-4">
                    <a href="${data.evidenceUrl}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Lihat Bukti Pendukung
                    </a>
                </div>` : ''}
                ${catatanHtml}
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider mb-4">Riwayat Approval</p>
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

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
        }

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetail();
        });
    </script>
</x-app-layout>