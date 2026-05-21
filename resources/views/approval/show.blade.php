<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('approval.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Approval
            </a>

            {{-- Info Tindak Lanjut --}}
<div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="text-lg font-black uppercase tracking-wider">
                    Informasi Tindak Lanjut
                </h3>
                <p class="text-xs text-slate-300 mt-1">
                    Detail laporan tindak lanjut dan informasi pendukung
                </p>
            </div>

            @php
            $isTD = $tindakLanjut->status === 'td' || $tindakLanjut->approvals->contains(fn($a) => str_contains($a->note ?? '', 'Ditetapkan sebagai TD'));

            $statusBadge = match(true) {
            $isTD => 'bg-slate-100 text-slate-700 border-slate-200',
            $tindakLanjut->status === 'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            $tindakLanjut->status === 'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
            $tindakLanjut->status === 'in_approval' => 'bg-blue-100 text-blue-700 border-blue-200',
            default => 'bg-amber-100 text-amber-700 border-amber-200'
            };

            $statusLabel = match(true) {
            $isTD => 'TD - Tidak Ditindaklanjuti',
            $tindakLanjut->status === 'approved' => 'Disetujui',
            $tindakLanjut->status === 'rejected' => 'Ditolak',
            $tindakLanjut->status === 'in_approval' => 'Proses Approval',
            default => ucfirst($tindakLanjut->status)
            };

            $bulanIndo = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            @endphp

            <span class="inline-flex items-center px-4 py-2 rounded-2xl border text-[10px] font-black uppercase tracking-wider {{ $statusBadge }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Arahan --}}
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>

                <p class="text-xs font-black text-indigo-500 uppercase tracking-widest">
                    Arahan
                </p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-5 border border-indigo-100">
                <p class="text-sm font-semibold text-slate-700 leading-relaxed">
                    {{ $tindakLanjut->arahan->strategi ?? $tindakLanjut->arahan->arahan ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Informasi Detail --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Bidang --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    Bidang
                </p>
                <p class="text-sm font-bold text-slate-800 leading-snug">
                    {{ $tindakLanjut->arahan->bidang->name ?? 'Umum' }}
                </p>
            </div>

            {{-- Unit Kerja --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    Unit Kerja
                </p>
                <p class="text-sm font-bold text-slate-800 leading-snug">
                    {{ $tindakLanjut->unitKerja->name ?? '-' }}
                </p>
            </div>

            {{-- Periode --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    Periode
                </p>
                <p class="text-sm font-bold text-slate-800 leading-snug">
                    {{ $bulanIndo[$tindakLanjut->periode_bulan] ?? '-' }}
                    {{ $tindakLanjut->periode_tahun }}
                </p>
            </div>

            {{-- Dibuat Oleh --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    Dibuat Oleh
                </p>
                <p class="text-sm font-bold text-slate-800 leading-snug">
                    {{ $tindakLanjut->creator->name ?? '-' }}
                </p>
            </div>

        </div>

        {{-- Tanggal --}}
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                Tanggal Dibuat
            </p>
            <p class="text-sm font-bold text-slate-800">
                {{ $tindakLanjut->created_at->format('d M Y • H:i') }}
            </p>
        </div>

        {{-- Uraian --}}
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                Uraian Tindak Lanjut
            </p>

            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <p class="text-sm text-slate-700 leading-relaxed">
                    {{ $tindakLanjut->tindak_lanjut }}
                </p>
            </div>
        </div>

        {{-- Kendala --}}
        @if($tindakLanjut->kendala)
        <div>
            <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2">
                Kendala
            </p>

            <div class="bg-rose-50 rounded-2xl p-5 border border-rose-100">
                <p class="text-sm text-slate-700 leading-relaxed">
                    {{ $tindakLanjut->kendala }}
                </p>
            </div>
        </div>
        @endif

        {{-- Keterangan --}}
        @if($tindakLanjut->keterangan)
        <div>
            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">
                Keterangan
            </p>

            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                <p class="text-sm text-slate-700 leading-relaxed">
                    {{ $tindakLanjut->keterangan }}
                </p>
            </div>
        </div>
        @endif

        {{-- Evidence --}}
        @if($tindakLanjut->evidence_url)
        <div class="pt-2">
            <a href="{{ Storage::url($tindakLanjut->evidence_url) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-5 py-3 bg-blue-50 text-blue-700 rounded-2xl text-xs font-black uppercase tracking-wide hover:bg-blue-100 transition border border-blue-100 shadow-sm">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>

                Lihat Evidence
            </a>
        </div>
        @endif

    </div>
</div>

            {{-- Approval Timeline --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Timeline Persetujuan</h3>
                </div>

                <div class="p-6">
                    <div class="relative">
                        @php
                        $stages = [
                        1 => ['name' => 'Atasan Auditi', 'icon' => '👤'],
                        2 => ['name' => 'Unit Kerja', 'icon' => '🏢'],
                        3 => ['name' => 'Pengendali Teknis', 'icon' => '🔧'],
                        4 => ['name' => 'Pengendali Mutu', 'icon' => '✅'],
                        5 => ['name' => 'Penanggung Jawab', 'icon' => '👑']
                        ];

                        // Cek apakah ada TD
                        $tdApproval = $tindakLanjut->approvals->first(function($a) {
                        return str_contains($a->note ?? '', 'Ditetapkan sebagai TD');
                        });
                        $isTD = $tindakLanjut->status === 'td' || $tdApproval;
                        $tdStage = $tdApproval ? $tdApproval->stage : null;
                        @endphp

                        @foreach($stages as $stageNum => $stage)
                        @php
                        $approval = $tindakLanjut->approvals->where('stage', $stageNum)->first();
                        $status = $approval ? $approval->status : 'pending';

                        // Stage setelah TD: di-skip (tidak perlu menunggu)
                        $isAfterTD = $isTD && $tdStage && $stageNum > $tdStage;
                        $displayStatus = $isAfterTD ? 'skipped' : $status;
                        @endphp
                        <div class="relative flex items-start mb-8 last:mb-0">
                            {{-- Garis penghubung --}}
                            @if(!$loop->last)
                            <div class="absolute left-5 top-10 bottom-0 w-0.5 
                                {{ $displayStatus == 'approved' ? 'bg-emerald-400' : ($displayStatus == 'skipped' ? 'bg-slate-200' : 'bg-slate-200') }}"></div>
                            @endif

                            {{-- Icon Circle --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center border-2
                                {{ $displayStatus == 'approved' ? 'bg-emerald-500 border-emerald-500' : 
                                   ($displayStatus == 'rejected' && !$isTD ? 'bg-rose-500 border-rose-500' : 
                                   ($displayStatus == 'rejected' && $isTD ? 'bg-slate-500 border-slate-500' :
                                   ($displayStatus == 'skipped' ? 'bg-slate-100 border-slate-200' : 'bg-white border-slate-300'))) }}">
                                @if($displayStatus == 'approved')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                @elseif($displayStatus == 'rejected' && $isTD)
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                @elseif($displayStatus == 'rejected')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                @elseif($displayStatus == 'skipped')
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                                @else
                                <span class="text-[10px] font-black text-slate-400">{{ $stageNum }}</span>
                                @endif
                            </div>

                            <div class="ml-4 flex-1">
                                <div class="flex flex-wrap justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-black uppercase
                                            {{ $displayStatus == 'skipped' ? 'text-slate-300' : 'text-slate-800' }}">
                                            Stage {{ $stageNum }}: {{ $stage['name'] }}
                                        </h4>
                                    </div>
                                    <div>
                                        @if($displayStatus == 'approved')
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[9px] font-black uppercase border border-emerald-100">Disetujui</span>
                                        @elseif($displayStatus == 'rejected' && $isTD)
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase border border-slate-200">TD</span>
                                        @elseif($displayStatus == 'rejected')
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-lg text-[9px] font-black uppercase border border-rose-100">Ditolak</span>
                                        @elseif($displayStatus == 'skipped')
                                        <span class="px-2.5 py-1 bg-slate-50 text-slate-300 rounded-lg text-[9px] font-black uppercase border border-slate-100">Dilewati</span>
                                        @else
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[9px] font-black uppercase border border-amber-100">Menunggu</span>
                                        @endif
                                    </div>
                                </div>

                                @if($approval && $approval->approved_at && $displayStatus != 'skipped')
                                <div class="mt-2 text-xs text-slate-500">
                                    <p>Oleh: <span class="font-bold text-slate-700">{{ $approval->approver->name ?? '-' }}</span></p>
                                    <p>{{ $approval->approved_at->format('d M Y H:i') }}</p>
                                </div>
                                @endif

                                @if($approval && $approval->note && !str_contains($approval->note, 'Ditetapkan sebagai TD'))
                                <div class="mt-3 bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Catatan</p>
                                    <p class="text-xs text-slate-600">{{ $approval->note }}</p>
                                </div>
                                @endif

                                {{-- TD Note --}}
                                @if($approval && $approval->note && str_contains($approval->note, 'Ditetapkan sebagai TD'))
                                <div class="mt-3 bg-slate-100 rounded-xl p-4 border border-slate-300">
                                    <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Keputusan TD</p>
                                    <p class="text-xs text-slate-600 italic">"{{ str_replace(' (Ditetapkan sebagai TD)', '', $approval->note) }}"</p>
                                    <p class="text-[9px] text-slate-400 mt-1.5">Proses approval dihentikan. Stage selanjutnya dilewati.</p>
                                </div>
                                @endif

                                {{-- Skip notice --}}
                                @if($displayStatus == 'skipped')
                                <div class="mt-2 text-[10px] text-slate-300 italic">
                                    Dilewati karena proses dihentikan (TD)
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            @if($currentApproval && $currentApproval->status == 'pending' && !$isTD)
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Tindakan Anda</h3>
                </div>
                <div class="p-6 flex gap-4">
                    <button data-id="{{ $tindakLanjut->id }}" data-stage="{{ $currentStage }}"
                        onclick="openApproveModal(this.dataset.id, this.dataset.stage)"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $currentStage == 5 ? 'Selesaikan' : 'Setujui' }}
                    </button>
                    <button data-id="{{ $tindakLanjut->id }}"
                        onclick="openRejectModal(this.dataset.id)"
                        class="flex-1 px-6 py-3 bg-white border border-rose-200 text-rose-600 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-rose-50 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak
                    </button>
                </div>
            </div>
            @endif

            {{-- TD Notice --}}
            @if($isTD)
            <div class="bg-slate-100 rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-slate-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-600">Proses Dihentikan (TD)</p>
                    <p class="text-xs text-slate-400 mt-1">Tindak lanjut ini ditetapkan sebagai Tidak Dapat Ditindaklanjuti. Tidak ada tindakan lebih lanjut yang diperlukan.</p>
                </div>
            </div>
            @endif


            {{-- Approve Modal --}}
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
                                <input type="radio" id="optionLanjut" name="result" value="lanjut" checked class="w-4 h-4 text-emerald-600" onchange="toggleOtherOptions()">
                                <span id="labelLanjut" class="text-xs font-bold text-slate-700 uppercase">Lanjutkan ke Stage Berikutnya</span>
                            </label>
                            <label id="optionTDWrapper" class="flex items-center gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-slate-800 has-[:checked]:bg-slate-900/5">
                                <input type="radio" id="optionTD" name="result" value="td" class="w-4 h-4 text-slate-800" onchange="toggleOtherOptions()">
                                <span class="text-xs font-bold text-slate-700 uppercase">TD — Tidak Dapat Ditindaklanjuti</span>
                            </label>
                        </div>
                        <div id="tdNoteWrapper" class="hidden">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alasan TD <span class="text-rose-500">*</span></label>
                            <textarea name="td_note" id="tdNote" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-800 text-sm font-medium resize-none" placeholder="Jelaskan mengapa tidak dapat ditindaklanjuti..."></textarea>
                            <p id="tdNoteError" class="hidden mt-1 text-[10px] font-bold text-rose-500 uppercase">⚠ Alasan TD wajib diisi.</p>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="closeApproveModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase hover:bg-slate-200 transition">Batal</button>
                            <button type="button" id="approveSubmitBtn" onclick="submitApproveForm()" class="flex-[2] px-4 py-3 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase hover:bg-emerald-600 transition shadow-sm">Konfirmasi</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div id="rejectModal" class="fixed inset-0 bg-slate-900/70 hidden items-center justify-center z-[100] backdrop-blur-sm p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                    <div class="px-6 py-5 border-b border-rose-50 bg-rose-50/40">
                        <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Tolak Tindak Lanjut</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">Berikan catatan agar unit dapat memperbaiki laporan.</p>
                    </div>
                    <form id="rejectForm" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alasan <span class="text-rose-500">*</span></label>
                            <textarea name="note" id="rejectNote" rows="4" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium resize-none" placeholder="Sebutkan bagian yang perlu diperbaiki..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-[10px] uppercase hover:bg-slate-200 transition">Batal</button>
                            <button type="submit" class="flex-[2] px-4 py-3 bg-rose-600 text-white rounded-xl font-bold text-[10px] uppercase hover:bg-rose-700 transition shadow-sm">Kirim Tolak</button>
                        </div>
                    </form>
                </div>
            </div>

            @push('scripts')
            <script>
                function openApproveModal(id, stage) {
                    const isLast = parseInt(stage) === 5;
                    const isFirst = parseInt(stage) === 1;
                    const form = document.getElementById('approveForm');
                    if (form) form.action = `/approval/${id}/approve`;

                    document.getElementById('optionLanjut').checked = true;
                    document.getElementById('optionTD').checked = false;
                    document.getElementById('tdNoteWrapper').classList.add('hidden');
                    document.getElementById('tdNote').value = '';
                    document.getElementById('tdNoteError').classList.add('hidden');

                    const tdWrapper = document.getElementById('optionTDWrapper');
                    if (tdWrapper && isFirst) tdWrapper.style.display = 'none';
                    else if (tdWrapper) tdWrapper.style.display = '';

                    if (isLast) {
                        document.getElementById('optionLanjut').value = 'selesai';
                        document.getElementById('labelLanjut').textContent = 'Selesai — Tindak Lanjut Dinyatakan Tuntas';
                        document.getElementById('approveSubmitBtn').textContent = 'Konfirmasi Selesai';
                        document.getElementById('approveModalTitle').textContent = 'Finalisasi Laporan';
                        document.getElementById('approveModalDesc').textContent = 'Laporan akan dinyatakan selesai.';
                    } else {
                        document.getElementById('optionLanjut').value = 'lanjut';
                        document.getElementById('labelLanjut').textContent = 'Lanjutkan ke Stage Berikutnya';
                        document.getElementById('approveSubmitBtn').textContent = 'Konfirmasi';
                        document.getElementById('approveModalTitle').textContent = 'Konfirmasi Persetujuan';
                        document.getElementById('approveModalDesc').textContent = 'Pastikan laporan sudah sesuai standar.';
                    }

                    document.getElementById('approveModal').classList.remove('hidden');
                    document.getElementById('approveModal').classList.add('flex');
                }

                function closeApproveModal() {
                    document.getElementById('approveModal').classList.add('hidden');
                    document.getElementById('approveModal').classList.remove('flex');
                }

                function toggleOtherOptions() {
                    const val = document.querySelector('input[name="result"]:checked')?.value;
                    document.getElementById('tdNoteWrapper').classList.add('hidden');
                    if (val === 'td') document.getElementById('tdNoteWrapper').classList.remove('hidden');
                }

                function submitApproveForm() {
                    const val = document.querySelector('input[name="result"]:checked')?.value;
                    if (val === 'td') {
                        const note = document.getElementById('tdNote');
                        if (!note || note.value.trim() === '') {
                            document.getElementById('tdNoteError').classList.remove('hidden');
                            note.focus();
                            return;
                        }
                    }
                    document.getElementById('tdNoteError').classList.add('hidden');
                    document.getElementById('approveForm').submit();
                }

                function openRejectModal(id) {
                    document.getElementById('rejectNote').value = '';
                    document.getElementById('rejectForm').action = `/approval/${id}/reject`;
                    document.getElementById('rejectModal').classList.remove('hidden');
                    document.getElementById('rejectModal').classList.add('flex');
                }

                function closeRejectModal() {
                    document.getElementById('rejectModal').classList.add('hidden');
                    document.getElementById('rejectModal').classList.remove('flex');
                }

                ['approveModal', 'rejectModal'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.addEventListener('click', function(e) {
                        if (e.target === this) id === 'approveModal' ? closeApproveModal() : closeRejectModal();
                    });
                });
            </script>
            @endpush
</x-app-layout>