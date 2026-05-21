<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Navigation & Actions --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <a href="{{ route('keputusan.index') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar
                </a>

                @if(in_array($keputusan->status, ['BD', 'S']))
                <a href="{{ route('arahan.create', ['keputusan_id' => $keputusan->id]) }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-lg shadow-indigo-200 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Arahan
                </a>
                @endif
            </div>

            {{-- Main Info Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                @php
                                    $statusColors = [
                                        'BD' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                        'BS' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        'S' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                        'TD' => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
                                    ];
                                @endphp
                                <span class="px-3 py-1 {{ $statusColors[$keputusan->status] ?? 'bg-white/10 text-white/70 border-white/20' }} border rounded-full text-[10px] font-black uppercase tracking-wider">
                                    {{ $keputusan->status === 'BD' ? 'Draft' : ($keputusan->status === 'BS' ? 'Aktif' : ($keputusan->status === 'S' ? 'Selesai' : ($keputusan->status === 'TD' ? 'Tidak Ditindaklanjuti' : $keputusan->status))) }}
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-black tracking-tight">
                                Keputusan RUPS Tahun <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">{{ $keputusan->periode_year }}</span>
                            </h1>
                            <div class="flex items-center gap-3 mt-3 text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-xs font-medium">{{ $keputusan->creator->name }}</span>
                                </div>
                                <span class="text-slate-600">•</span>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs font-medium">{{ $keputusan->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-center bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20">
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-wider">Total Arahan</p>
                                <p class="text-3xl font-black text-white">{{ $keputusan->arahan->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Section: Daftar Arahan --}}
            <div class="space-y-6">
                {{-- Section Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-xl">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">Daftar Arahan</h2>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $keputusan->arahan->count() }} arahan tercatat</p>
                        </div>
                    </div>
                </div>

                @forelse($keputusan->arahan->sortByDesc('created_at')->groupBy('bidang_id') as $bidangId => $kumpulanArahan)
                <div class="space-y-4">
                    {{-- Header Bidang --}}
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                {{ $kumpulanArahan->first()->bidang->name ?? 'Umum' }}
                            </h3>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                {{ $kumpulanArahan->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 ml-4 border-l-2 border-indigo-100 pl-6">
                        @foreach($kumpulanArahan as $arahan)
                        @php
                            $lastTL = $arahan->tindakLanjut->sortByDesc('created_at')->first();
                            $tlCount = $arahan->tindakLanjut->count();
                            $approvedCount = $arahan->tindakLanjut->where('status', 'approved')->count();
                            $isDeadlineNear = $arahan->tanggal_target && now()->diffInDays($arahan->tanggal_target, false) <= 7 && now()->diffInDays($arahan->tanggal_target, false) > 0;
                           $isOverdue = $arahan->tanggal_target && now()->startOfDay()->gt($arahan->tanggal_target);
                        @endphp
                        
                        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition-all duration-300 overflow-hidden group">
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row justify-between gap-5">
                                    <div class="flex-1 space-y-4">
                                        {{-- Meta Tags --}}
                                        <div class="flex flex-wrap items-center gap-2">
                                            {{-- PICs --}}
                                            @foreach($arahan->pics as $pic)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[9px] font-black uppercase border border-indigo-100">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ $pic->name }}
                                            </span>
                                            @endforeach
                                            
                                            {{-- Deadline Badge --}}
                                            @if($isOverdue && !$lastTL)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-[9px] font-black uppercase border border-rose-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                </svg>
                                                Terlambat
                                            </span>
                                            @elseif($isDeadlineNear && !$lastTL)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-lg text-[9px] font-black uppercase border border-amber-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Deadline {{ now()->diffInDays($arahan->tanggal_target) }} hari
                                            </span>
                                            @endif
                                        </div>

                                        {{-- Isi Arahan --}}
                                        <h4 class="text-slate-800 text-base font-bold leading-relaxed group-hover:text-indigo-600 transition-colors">
                                            {{ $arahan->strategi ?? $arahan->arahan }}
                                        </h4>

                                        {{-- Target Date --}}
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase">
                                                Target: {{ $arahan->tanggal_target ? \Carbon\Carbon::parse($arahan->tanggal_target)->translatedFormat('d F Y') : '-' }}
                                            </span>
                                        </div>

                                        {{-- Tindak Lanjut Terakhir --}}
                                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tindak Lanjut Terakhir</span>
                                                @if($lastTL)
                                                <span class="text-[9px] text-slate-400">{{ $lastTL->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                            @if($lastTL)
                                            <p class="text-sm text-slate-600 leading-relaxed">
                                                {{ Str::limit($lastTL->tindak_lanjut, 150) }}
                                            </p>
                                            @else
                                            <div class="flex items-center gap-2 text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-xs italic">Belum ada tindak lanjut</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex flex-row lg:flex-col gap-2 lg:self-start">
                                        <a href="{{ route('tindaklanjut.show_arahan', $arahan) }}" 
                                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all"
                                           title="Lihat Detail Tindak Lanjut">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>

                                        @can('edit_arahan')
                                        @if($arahan->status === 'draft' && !$lastTL)
                                        <a href="{{ route('arahan.edit', $arahan) }}" 
                                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all"
                                           title="Edit Arahan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                {{-- Empty State --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                    <div class="py-16 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada butir arahan</p>
                        <p class="text-xs text-slate-300 mt-1">Klik tombol "Tambah Arahan" untuk mulai menambahkan</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>