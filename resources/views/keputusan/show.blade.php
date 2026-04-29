<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">

            {{-- Navigation & Actions --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <a href="{{ route('keputusan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-600 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>

                <div class="flex gap-3">
                    @if(in_array($keputusan->status, ['BD', 'S']))
                    <a href="{{ route('arahan.create', ['keputusan_id' => $keputusan->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-wider hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Arahan
                    </a>
                    @endif
                </div>
            </div>

            {{-- Main Info Header Card --}}
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-8 py-8 md:px-10 md:py-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-white/10 border border-white/20 rounded-full text-[10px] font-black uppercase tracking-wider text-white/70">
                                    {{ $keputusan->status === 'BD' ? 'Draft' : ($keputusan->status === 'S' ? 'Selesai' : 'Aktif') }}
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                                Keputusan RUPS Tahun {{ $keputusan->periode_year }}
                            </h1>
                            <div class="flex items-center gap-2 mt-3 text-slate-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs font-medium">Oleh: {{ $keputusan->creator->name }}</span>
                                <span class="text-xs">•</span>
                                <span class="text-xs">{{ $keputusan->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Arahan</p>
                                <p class="text-2xl font-black text-white">{{ $keputusan->arahan->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Arahan Per Bidang --}}
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Daftar Arahan</h2>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
                        {{ $keputusan->arahan->count() }} Arahan
                    </span>
                </div>

                @forelse($keputusan->arahan->sortByDesc('created_at')->groupBy('bidang_id') as $bidangId => $kumpulanArahan)
                <div class="space-y-4">
                    {{-- Header Bidang --}}
                    <div class="sticky top-16 z-20">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-xl">
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                {{ $kumpulanArahan->first()->bidang->name ?? 'Umum' }}
                            </h3>
                            <span class="text-[9px] font-bold text-slate-400">({{ $kumpulanArahan->count() }})</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 ml-4 md:ml-8">
                        @foreach($kumpulanArahan as $index => $arahan)
                        @php
                            $lastTL = $arahan->tindakLanjut->sortByDesc('created_at')->first();
                            $isDeadlineNear = $arahan->tanggal_target && now()->diffInDays($arahan->tanggal_target, false) <= 7 && now()->diffInDays($arahan->tanggal_target, false) > 0;
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group">
                            <div class="p-5">
                                <div class="flex flex-col lg:flex-row justify-between gap-5">
                                    <div class="flex-1">
                                        {{-- Meta Tags --}}
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            @foreach($arahan->pics as $pic)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[9px] font-black uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $pic->name }}
                                            </span>
                                            @endforeach
                                            
                                            @if($isDeadlineNear && !$lastTL)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-lg text-[9px] font-black uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Deadline Mendekat
                                            </span>
                                            @endif
                                        </div>

                                        {{-- Isi Arahan --}}
                                        <h4 class="text-slate-800 text-base font-bold leading-relaxed group-hover:text-blue-600 transition-colors">
                                            {{ $arahan->strategi }}
                                        </h4>

                                        {{-- Target Date --}}
                                        <div class="flex items-center gap-2 mt-3">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase">Target: {{ \Carbon\Carbon::parse($arahan->tanggal_target)->format('d M Y') }}</span>
                                        </div>

                                        {{-- Progres Terakhir --}}
                                        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tindak Lanjut</span>
                                                @if($lastTL)
                                                <span class="text-[9px] text-slate-400">{{ $lastTL->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                            @if($lastTL)
                                            <p class="text-xs text-slate-600 leading-relaxed">
                                                {{ Str::limit($lastTL->tindak_lanjut, 120) }}
                                            </p>
                                            @else
                                            <div class="flex items-center gap-2 text-slate-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-[11px] italic">Belum ada tindak lanjut</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex flex-row lg:flex-col gap-2 lg:self-start">
                                        <a href="{{ route('tindaklanjut.show_arahan', $arahan) }}" 
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all"
                                           title="Lihat Detail Tindak Lanjut">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Detail</span>
                                        </a>

                                        @can('edit_arahan')
                                       @if($arahan->status === 'draft' && !$lastTL)
                                        <a href="{{ route('arahan.edit', $arahan) }}" 
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-100 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all"
                                           title="Edit Arahan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Edit</span>
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
                <div class="py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm font-bold text-slate-400">Belum ada butir arahan</p>
                    <p class="text-xs text-slate-300 mt-1">Klik tombol "Tambah Arahan" untuk mulai menambahkan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>