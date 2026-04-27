@props(['tl', 'isTD' => false, 'isFirst' => false])

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden {{ $isTD ? 'opacity-80' : '' }} mb-4">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-400 text-lg">description</span>
                <h3 class="text-sm font-bold text-slate-700">
                    Laporan {{ $tl->created_at->format('d M Y H:i') }}
                </h3>
                @if($isFirst)
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full text-[9px] font-bold">Terbaru</span>
                @endif
            </div>
            <span class="text-xs text-slate-400">{{ $tl->created_at->diffForHumans() }}</span>
        </div>
    </div>
    <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Unit Kerja</p>
                <p class="text-sm font-semibold text-slate-800 flex items-center gap-1">
                    <span class="material-symbols-outlined text-slate-400 text-base">business</span>
                    {{ $tl->unitKerja->name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Periode Laporan</p>
                <p class="text-sm font-semibold text-slate-800 flex items-center gap-1">
                    <span class="material-symbols-outlined text-slate-400 text-base">calendar_month</span>
                    @php
                        $bulanIndonesia = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        $bulan = $tl->periode_bulan ? ($bulanIndonesia[$tl->periode_bulan] ?? '-') : '-';
                    @endphp
                    {{ $bulan }} {{ $tl->periode_tahun ?? '-' }}
                </p>
            </div>
        </div>

        <div>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tindak Lanjut</p>
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-700">{{ $tl->tindak_lanjut }}</p>
            </div>
        </div>

        @if($tl->kendala)
        <div>
            <p class="text-[9px] font-bold text-rose-400 uppercase tracking-wider mb-1">Kendala</p>
            <div class="p-3 bg-rose-50 rounded-lg">
                <p class="text-sm text-slate-700">{{ $tl->kendala }}</p>
            </div>
        </div>
        @endif

        @if($tl->keterangan)
        <div>
            <p class="text-[9px] font-bold text-blue-400 uppercase tracking-wider mb-1">Keterangan</p>
            <div class="p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-slate-700">{{ $tl->keterangan }}</p>
            </div>
        </div>
        @endif

        @if($tl->evidence_url)
        <div>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bukti Pendukung</p>
            <a href="{{ Storage::url($tl->evidence_url) }}" target="_blank" 
               class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 rounded-lg text-xs font-semibold text-blue-600 hover:bg-blue-50 transition">
                <span class="material-symbols-outlined text-sm">download</span>
                {{ basename($tl->evidence_url) }}
            </a>
        </div>
        @endif

        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">person</span>
                <span>{{ $tl->creator->name ?? '-' }}</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">schedule</span>
                <span>{{ $tl->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>