<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ═══════════════════════════════
         BREADCRUMB + HEADER
    ═══════════════════════════════ --}}
        <div class="mb-8">
            <nav class="flex text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3 gap-2 items-center">
                <a class="hover:text-slate-700 transition-colors" href="{{ route('dashboard') }}">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a class="hover:text-slate-700 transition-colors" href="{{ route('tindaklanjut.index') }}">Tindak Lanjut</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-700">Detail Arahan</span>
            </nav>

            @php

            $aggregateStatus = $arahan->getAggregateStatus();

            $isGlobalTD = $aggregateStatus === 'td';
            $isGlobalSelesai = $aggregateStatus === 'S';
            $isGlobalBelumSelesai = $aggregateStatus === 'BS';
            @endphp

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        @if($isGlobalTD)
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-wider">TD — Tidak Ditindaklanjuti</span>
                        @elseif($isGlobalSelesai)
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-wider">✓ Selesai</span>
                        @else
                        {{-- Ini akan otomatis muncul jika ada unit yang masih pending/revisi --}}
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-wider">Belum Selesai</span>
                        @endif
                        <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $arahan->bidang->name ?? '-' }}</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 leading-snug max-w-2xl">{{ $arahan->strategi }}</h1>
                    <p class="text-xs text-slate-400 font-bold mt-2">Target: {{ $arahan->tanggal_target ? $arahan->tanggal_target->format('d M Y') : '-' }}</p>
                </div>
            </div>
        </div>

        @if($isGlobalTD)
        <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3">
            <div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-700">Proses dihentikan — Status TD</p>
                <p class="text-xs text-slate-500">Arahan ini telah ditetapkan Tidak Dapat Ditindaklanjuti. Tidak ada input atau revisi baru yang dapat dilakukan.</p>
            </div>
        </div>
        @endif

        {{-- ═══════════════════════════════
         LAYOUT: LEFT (tabs) + RIGHT (info)
    ═══════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ─────────────────────────────
             LEFT: TAB PER UNIT KERJA
        ───────────────────────────── --}}
            <div class="lg:col-span-2">

                @php
                // Kelompokkan tindak lanjut per unit kerja
                $tlPerUnit = $arahan->tindakLanjut->groupBy('unit_kerja_id');
                $unitList = $tlPerUnit->keys();
                @endphp

                @if($tlPerUnit->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400">Belum ada laporan yang masuk</p>
                    <p class="text-xs text-slate-300 mt-1">Unit kerja belum melakukan input tindak lanjut</p>
                </div>
                @else

                {{-- Tab Navigation --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

                    {{-- Tab Headers --}}
                    <div class="border-b border-slate-100 px-4 pt-4 flex gap-1 overflow-x-auto" id="unit-tabs">
                        @foreach($tlPerUnit as $unitId => $tlList)
                        @php
                        $firstTl = $tlList->sortByDesc('created_at')->first();
                        $unitName = $firstTl->unitKerja->name ?? 'Unit #'.$unitId;
                        $unitShort = Str::limit($unitName, 20);
                        $tlStatus = $firstTl->status ?? 'pending';
                        $isApprovedUnit = $tlStatus === 'approved';
                        $isRejectedUnit = $tlStatus === 'rejected';
                        $isInApprovalUnit = $tlStatus === 'in_approval';
                        $tabIdx = $loop->index;
                        @endphp
                        <button onclick="switchTab({{ $tabIdx }})"
                            id="tab-btn-{{ $tabIdx }}"
                            class="tab-btn flex-shrink-0 flex items-center gap-2 px-4 py-2.5 rounded-t-xl text-[11px] font-black uppercase tracking-wider transition-all border-b-2
                            {{ $tabIdx === 0 ? 'border-slate-900 text-slate-900 bg-slate-50' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                            <span class="w-2 h-2 rounded-full flex-shrink-0
                            {{ $isApprovedUnit ? 'bg-emerald-500' : ($isRejectedUnit ? 'bg-rose-500' : ($isInApprovalUnit ? 'bg-blue-500' : 'bg-amber-400')) }}">
                            </span>
                            {{ $unitShort }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Tab Content --}}
                    @foreach($tlPerUnit as $unitId => $tlList)
                    @php
                    $tabIdx = $loop->index;
                    $tlSorted = $tlList->sortByDesc('created_at');
                    $latestTlUnit = $tlSorted->first();
                    $unitName = $latestTlUnit->unitKerja->name ?? '-';
                    @endphp
                    <div id="tab-content-{{ $tabIdx }}" class="tab-content {{ $tabIdx !== 0 ? 'hidden' : '' }}">

                        {{-- Unit Header --}}
                        <div class="px-6 py-4 bg-slate-50/60 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Unit Kerja</p>
                                <p class="text-sm font-black text-slate-800">{{ $unitName }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                $tlStatus = $latestTlUnit->status ?? 'pending';
                                $isApprovedUnit = $tlStatus === 'approved';
                                $isRejectedUnit = $tlStatus === 'rejected';
                                $isInApprovalUnit = $tlStatus === 'in_approval';
                                @endphp
                                @if($isApprovedUnit)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[9px] font-black uppercase">✓ Selesai</span>
                                @elseif($isRejectedUnit)
                                <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[9px] font-black uppercase">Perlu Revisi</span>
                                @elseif($isInApprovalUnit)
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-[9px] font-black uppercase">Dalam Approval</span>
                                @else
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[9px] font-black uppercase">Pending</span>
                                @endif
                                <span class="text-[9px] font-bold text-slate-400">{{ $tlSorted->count() }} laporan</span>
                            </div>
                        </div>

                        {{-- Laporan list untuk unit ini --}}
                        <div class="divide-y divide-slate-50">
                            @foreach($tlSorted as $tl)
                            @php
                            $bulanIndonesia = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                            $bulan = $tl->periode_bulan ? ($bulanIndonesia[$tl->periode_bulan] ?? '-') : '-';
                            $rejectedNote = $tl->approvals()->where('status','rejected')->whereNotNull('note')->latest()->first();
                            $isLatest = $loop->first;
                            @endphp
                            <div class="p-6 {{ !$isLatest ? 'opacity-60' : '' }}">

                                {{-- Laporan badge + meta --}}
                                <div class="flex items-center gap-2 mb-4">
                                    @if($isLatest)
                                    <span class="px-2.5 py-1 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-wider">Terbaru</span>
                                    @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-wider">Laporan {{ $loop->iteration }}</span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-bold">{{ $bulan }} {{ $tl->periode_tahun }}</span>
                                    <span class="text-slate-200 text-xs">·</span>
                                    <span class="text-[10px] text-slate-400 font-bold">{{ $tl->created_at->format('d M Y') }}</span>
                                    <span class="text-slate-200 text-xs">·</span>
                                    <span class="text-[10px] text-slate-400 font-bold">{{ $tl->creator->name ?? '-' }}</span>
                                </div>

                                {{-- Tindak Lanjut --}}
                                <div class="mb-4">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Uraian Tindak Lanjut</p>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                        <p class="text-sm text-slate-700 leading-relaxed">{{ $tl->tindak_lanjut }}</p>
                                    </div>
                                </div>

                                {{-- Kendala --}}
                                @if($tl->kendala)
                                <div class="mb-4">
                                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Kendala
                                    </p>
                                    <div class="bg-rose-50 rounded-xl p-4 border border-rose-100">
                                        <p class="text-sm text-slate-700 leading-relaxed">{{ $tl->kendala }}</p>
                                    </div>
                                </div>
                                @endif

                                {{-- Keterangan --}}
                                @if($tl->keterangan)
                                <div class="mb-4">
                                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        Keterangan Tambahan
                                    </p>
                                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                        <p class="text-sm text-slate-700 leading-relaxed">{{ $tl->keterangan }}</p>
                                    </div>
                                </div>
                                @endif

                                {{-- Evidence + Catatan Revisi --}}
                                <div class="flex flex-wrap gap-3">
                                    @if($tl->evidence_url)
                                    <a href="{{ Storage::url($tl->evidence_url) }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-blue-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Lihat Evidence
                                    </a>
                                    @endif

                                    @if($isLatest && $tl->status === 'rejected')
                                    @can('edit_tindak_lanjut')
                                    <a href="{{ route('tindaklanjut.edit', $tl->id) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-rose-700 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Revisi Laporan
                                    </a>
                                    @endcan
                                    @endif
                                </div>

                                {{-- Catatan Revisi --}}
                                @if($isLatest && $rejectedNote && $tl->status === 'rejected')
                                <div class="mt-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                                    <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mb-1">Catatan Revisi</p>
                                    <p class="text-sm text-rose-700 italic">"{{ $rejectedNote->note }}"</p>
                                    <p class="text-[10px] text-rose-400 mt-1">— {{ $rejectedNote->approver->name ?? '-' }}, {{ $rejectedNote->approved_at?->format('d M Y H:i') }}</p>
                                </div>
                                @endif

                            </div>
                            @endforeach
                        </div>

                        {{-- Approval Timeline untuk unit ini --}}
                        <div class="px-6 pb-6">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Riwayat Approval</p>
                            @php
                            $approvals = $latestTlUnit->approvals()->with('approver')->orderBy('stage')->get();
                            $stages = [
                            1 => 'Atasan Auditi',
                            2 => 'Tim Monitoring',
                            3 => 'Pengendali Teknis',
                            4 => 'Pengendali Mutu',
                            5 => 'Penanggung Jawab',
                            ];
                            $activeStage = null;
                            foreach($stages as $num => $name) {
                            $appr = $approvals->firstWhere('stage', $num);
                            if($appr && $appr->status === 'pending') { $activeStage = $num; break; }
                            }
                            @endphp
                            <div class="flex items-start gap-0">
                                @foreach($stages as $stageNum => $stageName)
                                @php
                                $appr = $approvals->firstWhere('stage', $stageNum);
                                $st = $appr ? $appr->status : null;
                                $isActive = ($stageNum === $activeStage);
                                $isLast = $loop->last;
                                @endphp
                                <div class="flex-1 flex flex-col items-center">
                                    {{-- Circle --}}
                                    <div class="relative flex items-center w-full">
                                        @if(!$loop->first)
                                        <div class="flex-1 h-px {{ $st === 'approved' ? 'bg-emerald-400' : 'bg-slate-200' }}"></div>
                                        @endif
                                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center border-2
                                        {{ $st === 'approved' ? 'bg-emerald-500 border-emerald-500' :
                                           ($st === 'rejected' ? 'bg-rose-500 border-rose-500' :
                                           ($isActive ? 'bg-slate-900 border-slate-900' : 'bg-white border-slate-200')) }}">
                                            @if($st === 'approved')
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            @elseif($st === 'rejected')
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            @elseif($isActive)
                                            <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                                            @else
                                            <span class="text-[9px] font-black text-slate-400">{{ $stageNum }}</span>
                                            @endif
                                        </div>
                                        @if(!$isLast)
                                        <div class="flex-1 h-px bg-slate-200"></div>
                                        @endif
                                    </div>
                                    {{-- Label --}}
                                    <div class="mt-2 text-center px-1">
                                        <p class="text-[8px] font-black uppercase tracking-wide leading-tight
                                        {{ $st === 'approved' ? 'text-emerald-600' : ($isActive ? 'text-slate-900' : 'text-slate-400') }}">
                                            {{ Str::limit($stageName, 12) }}
                                        </p>
                                        @if($appr && $appr->approved_at)
                                        <p class="text-[7px] text-slate-400 mt-0.5">{{ $appr->approved_at->format('d/m') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    @endforeach

                </div>
                @endif
            </div>

            {{-- ─────────────────────────────
             RIGHT: INFO ARAHAN (sticky)
        ───────────────────────────── --}}
            <div class="space-y-4 lg:sticky lg:top-6">

                {{-- Info Card --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-slate-900 flex items-center justify-between">
                        <p class="text-[10px] font-black text-white uppercase tracking-wider">Informasi Arahan</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Bidang</p>
                            <p class="text-sm font-bold text-slate-800">{{ $arahan->bidang->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Target</p>
                            <p class="text-sm font-bold text-slate-800">{{ $arahan->tanggal_target ? $arahan->tanggal_target->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">PIC Unit Kerja</p>
                            <div class="space-y-1">
                                @forelse($arahan->pics as $pic)
                                <p class="text-sm font-bold text-slate-800">{{ $pic->name }}</p>
                                @empty
                                <p class="text-sm text-slate-400">-</p>
                                @endforelse
                            </div>
                        </div>
                        {{-- Di bagian Sidebar Kanan --}}
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Arahan</p>
                            @if($isGlobalTD)
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-black uppercase">TD - Tidak Ditindaklanjuti</span>
                            @elseif($isGlobalSelesai)
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[9px] font-black uppercase">✓ Selesai</span>
                            @else
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[9px] font-black uppercase">Belum Selesai</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ringkasan per Unit --}}
                @if($tlPerUnit->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <p class="text-[10px] font-black text-slate-800 uppercase tracking-wider">Ringkasan Unit</p>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @foreach($tlPerUnit as $unitId => $tlList)
                        @php
                        $firstTl = $tlList->sortByDesc('created_at')->first();
                        $unitName = $firstTl->unitKerja->name ?? 'Unit #'.$unitId;
                        $tlStatus = $firstTl->status ?? 'pending';
                        $tabIdx = $loop->index;
                        @endphp
                        <button onclick="switchTab({{ $tabIdx }})"
                            class="w-full px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition text-left">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-2 h-2 rounded-full flex-shrink-0
                                {{ $tlStatus === 'approved' ? 'bg-emerald-500' : ($tlStatus === 'rejected' ? 'bg-rose-500' : ($tlStatus === 'in_approval' ? 'bg-blue-500' : 'bg-amber-400')) }}">
                                </div>
                                <span class="text-xs font-bold text-slate-700 truncate">{{ $unitName }}</span>
                            </div>
                            <span class="text-[9px] font-black uppercase ml-2 flex-shrink-0
                            {{ $tlStatus === 'approved' ? 'text-emerald-600' : ($tlStatus === 'rejected' ? 'text-rose-600' : ($tlStatus === 'in_approval' ? 'text-blue-600' : 'text-amber-600')) }}">
                               {{ $tlStatus === 'approved' ? 'Selesai' : ($tlStatus === 'rejected' ? 'Revisi' : ($tlStatus === 'in_approval' ? 'Dalam Approval' : 'Pending')) }}
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>

    <script>
        function switchTab(idx) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-slate-900', 'text-slate-900', 'bg-slate-50');
                el.classList.add('border-transparent', 'text-slate-400');
            });
            document.getElementById('tab-content-' + idx).classList.remove('hidden');
            const btn = document.getElementById('tab-btn-' + idx);
            btn.classList.remove('border-transparent', 'text-slate-400');
            btn.classList.add('border-slate-900', 'text-slate-900', 'bg-slate-50');
        }
    </script>
</x-app-layout>