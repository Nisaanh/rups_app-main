<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="space-y-8">

            {{-- Navigation & Actions --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <a href="{{ route('keputusan.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm tracking-wide">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>

                <div class="flex gap-3">
                    {{-- Tombol tambah hanya muncul jika status keputusan memungkinkan --}}
                    @if(in_array($keputusan->status, ['BD', 'S']))
                    <a href="{{ route('arahan.create', ['keputusan_id' => $keputusan->id]) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-xs tracking-wide hover:bg-blue-700 shadow-lg shadow-blue-100 transition active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Arahan
                    </a>
                    @endif
                </div>
            </div>

            {{-- Main Info Header Card --}}
            <div class="bg-slate-900 rounded-[2rem] shadow-xl overflow-hidden relative group">
                <div class="absolute -right-10 -top-10 w-64 h-64 bg-blue-600/10 rounded-full blur-[80px]"></div>
                <div class="p-8 md:p-10 relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="px-2.5 py-0.5 bg-blue-500/20 border border-blue-500/30 rounded text-[10px] font-bold uppercase tracking-wider text-blue-400">
                                    Dokumen Resmi
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight leading-tight uppercase">
                                RUPS PERIODE {{ $keputusan->periode_year }}
                            </h1>
                            <p class="text-slate-400 text-xs mt-3 font-medium flex flex-wrap items-center gap-y-2">

                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Oleh: <span class="text-slate-200 ml-1">{{ $keputusan->creator->name }}</span>
                            </p>
                        </div>

                        {{-- LOGIKA STATUS UTAMA --}}
                        {{-- Ganti bagian status utama dengan kode ini --}}
<div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 min-w-[200px]">
    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Utama</p>
    
    @php
        // Trigger hitung status secara real-time untuk tampilan
        $arahanList = $keputusan->arahan;
        $statuses = $arahanList->map(fn($a) => $a->getAggregateStatus());
        
        $finalStatus = 'BS'; // Default
        if ($arahanList->isEmpty()) {
            $finalStatus = 'BD';
        } elseif ($statuses->every(fn($s) => $s === 'S')) {
            $finalStatus = 'S';
        } elseif ($statuses->every(fn($s) => $s === 'td')) {
            $finalStatus = 'td';
        } elseif ($statuses->contains(fn($s) => in_array($s, ['BS', 'BD']))) {
            $finalStatus = 'BS';
        }
    @endphp

    <p class="text-lg font-bold 
        @if($finalStatus === 'BD') text-rose-400 
        @elseif($finalStatus === 'BS') text-amber-400 
        @elseif($finalStatus === 'S') text-emerald-400 
        @elseif($finalStatus === 'td') text-slate-400 
        @else text-white @endif">
        
        @switch($finalStatus)
            @case('BD') Belum Ditindaklanjuti @break
            @case('BS') Belum Selesai @break
            @case('S') Selesai @break
            @case('td') Tidak Dapat Ditindaklanjuti @break
            @default {{ $finalStatus }}
        @endswitch
    </p>
</div>
                    </div>
                </div>
            </div>

            {{-- Section: Arahan Per Bidang --}}
            <div class="space-y-10 pb-20">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight flex items-center">
                        <span class="w-1 h-5 bg-blue-600 rounded-full mr-3"></span>
                        Struktur Arahan
                    </h2>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-1.5 rounded-lg">
                        Total: {{ $keputusan->arahan->count() }}
                    </span>
                </div>

                @forelse($keputusan->arahan->sortByDesc('created_at')->groupBy('bidang_id') as $bidangId => $kumpulanArahan)
                <div class="relative">
                    {{-- Sticky Header Bidang --}}
                    <div class="sticky top-6 z-20 mb-6">
                        <div class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-xl">
                            <span class="w-2 h-2 bg-blue-600 rounded-full mr-2"></span>
                            <h3 class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                {{ $kumpulanArahan->first()->bidang->name ?? 'Umum' }}
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 ml-2 md:ml-6 border-l border-slate-200 pl-6">
                        @foreach($kumpulanArahan as $index => $arahan)
                        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex flex-col lg:flex-row justify-between gap-6">
                                <div class="flex-1">
                                    {{-- Meta & Badge Status Dinamis --}}
                                    <div class="flex flex-wrap items-center gap-3 mb-4">
                                        @php
                                        $lastTL = $arahan->tindakLanjut->sortByDesc('created_at')->first();

                                        // Prioritaskan status arahan (TD), baru fallback ke status tindak lanjut
                                        if ($arahan->status === 'td') {
                                        $currentStatus = 'td';
                                        } elseif ($lastTL) {
                                        $currentStatus = match($lastTL->status) {
                                        'approved' => 'S',
                                        'rejected' => 'BS',
                                        'in_approval' => 'BS',
                                        'pending' => 'BS',
                                        default => 'BD',
                                        };
                                        } else {
                                        $currentStatus = 'BD';
                                        }
                                        @endphp

                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border
                                        @if($currentStatus === 'BD') bg-rose-50 border-rose-100 text-rose-400 
                                        @elseif($currentStatus === 'BS') bg-amber-50 border-amber-100 text-amber-400 
                                        @elseif($currentStatus === 'S') bg-emerald-50 border-emerald-100 text-emerald-400 
                                        @elseif($currentStatus === 'td') bg-slate-50 border-slate-200 text-slate-400 
                                        @else bg-slate-50 border-slate-200 text-slate-400 @endif">

                                            @if($currentStatus === 'BD') Belum Ditindaklanjuti
                                            @elseif($currentStatus === 'BS') Belum Selesai
                                            @elseif($currentStatus === 'S') Selesai
                                            @elseif($currentStatus === 'td') Tidak Dapat Ditindaklanjuti
                                            @else {{ $currentStatus }}
                                            @endif
                                        </span>

                                       <div class="flex flex-wrap gap-1">
                                        @foreach($arahan->pics as $pic)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[9px] font-bold uppercase">
                                            {{ $pic->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                        <span class="text-[9px] font-semibold text-slate-400 uppercase">
                                            Target: {{ \Carbon\Carbon::parse($arahan->tanggal_target)->format('d/m/Y') }}
                                        </span>
                                    </div>

                                    

                                    {{-- Isi Arahan --}}
                                    <h4 class="text-slate-700 text-base font-semibold leading-relaxed mb-5 group-hover:text-blue-600 transition-colors">
                                        "{{ $arahan->strategi }}"
                                    </h4>

                                    {{-- Progres Terakhir --}}
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                        @if($lastTL)
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">Tindak Lanjut</span>
                                            <span class="text-[9px] text-slate-400 italic">{{ $lastTL->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                                            {{ $lastTL->tindak_lanjut }}
                                        </p>
                                        @else
                                        <div class="flex items-center text-slate-400 italic">
                                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-[11px]">Menunggu progres tindak lanjut.</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex lg:flex-col gap-2 self-start">
                                    <a href="{{ route('tindaklanjut.show_arahan', $arahan) }}" class="p-2.5 bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-lg transition-all shadow-sm" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @can('edit_arahan')
                                    @if(!$lastTL)
                                    <a href="{{ route('arahan.edit', $arahan) }}" class="p-2.5 bg-white border border-slate-200 text-slate-400 hover:text-amber-600 hover:border-amber-200 rounded-lg transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <p class="text-sm text-slate-400 italic">Belum ada butir arahan yang ditambahkan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>