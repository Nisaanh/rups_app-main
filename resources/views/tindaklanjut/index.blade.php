<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Page Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight">
                                Daftar <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">Tindak Lanjut</span>
                            </h2>
                            <p class="text-slate-300 mt-2 text-sm font-medium max-w-xl">
                                Monitoring dan evaluasi tindak lanjut arahan keputusan RUPS.
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/20">
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-wider">Total Arahan</p>
                                <p class="text-2xl font-black text-white">{{ $arahan->total() ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Table Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Daftar Arahan</h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Menampilkan {{ $arahan->firstItem() ?? 1 }}-{{ $arahan->lastItem() ?? $arahan->count() }} dari {{ $arahan->total() ?? 0 }} arahan
                            </p>
                        </div>
                        <form action="{{ route('tindaklanjut.index') }}" method="GET" class="flex gap-2">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}" 
                                    placeholder="Cari strategi arahan..."
                                    class="pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl w-64 text-xs font-bold focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                >
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button type="submit" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                                Cari
                            </button>
                            @if(request('search'))
                            <a href="{{ route('tindaklanjut.index') }}" 
                                class="px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-rose-100 transition">
                                Reset
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Bidang</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Strategi/Arahan</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Tanggal Target</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($arahan as $item)
                            @php
                            $userUnitKerjaId = Auth::user()->unit_kerja_id;

                            $tlMilikUser = $item->tindakLanjut
                                ->where('unit_kerja_id', $userUnitKerjaId)
                                ->sortByDesc('created_at')
                                ->first();

                            $latestTl = $item->tindakLanjut->sortByDesc('created_at')->first();
                            $count = $item->tindakLanjut->count();
                            $hasInput = $count > 0;

                            $isGlobalTD = ($item->status ?? '') === 'td';

                            $isFullyApproved = $latestTl
                                ? $latestTl->approvals()->where('stage', 5)->where('status', 'approved')->exists()
                                : false;
                            $isRevisi = $latestTl && $latestTl->status === 'rejected';
                            $isInApproval = $latestTl && $latestTl->status === 'in_approval';

                            $sudahInputUnitIni = !is_null($tlMilikUser);
                            $isRevisiUnitIni = $tlMilikUser && $tlMilikUser->status === 'rejected';
                            $isApprovedUnitIni = $tlMilikUser && $tlMilikUser->status === 'approved';
                            $isInApprovalUnitIni = $tlMilikUser && $tlMilikUser->status === 'in_approval';

                            // Status badge
                            if ($isGlobalTD) {
                                $statusText = 'Tidak Ditindaklanjuti';
                                $statusBg = 'bg-slate-100 text-slate-600 border-slate-200';
                            } elseif ($isFullyApproved) {
                                $statusText = 'Selesai';
                                $statusBg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                            } elseif ($isRevisi) {
                                $statusText = 'Perlu Revisi';
                                $statusBg = 'bg-rose-50 text-rose-600 border-rose-100';
                            } elseif ($isInApproval) {
                                $statusText = 'Dalam Approval';
                                $statusBg = 'bg-blue-50 text-blue-600 border-blue-100';
                            } elseif ($latestTl) {
                                $statusText = 'Menunggu';
                                $statusBg = 'bg-amber-50 text-amber-600 border-amber-100';
                            } else {
                                $statusText = 'Belum Ditindaklanjuti';
                                $statusBg = 'bg-slate-50 text-slate-500 border-slate-100';
                            }
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition group">
                                {{-- Bidang --}}
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-600">
                                        {{ $item->bidang->name ?? $item->bidang ?? '-' }}
                                    </span>
                                </td>

                                {{-- Strategi/Arahan --}}
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <p class="font-bold text-slate-800 text-sm leading-snug group-hover:text-indigo-600 transition">
                                            @php
                                                $text = $item->strategi ?? $item->arahan ?? '-';
                                                $maxLength = 100;
                                            @endphp
                                            {{ strlen($text) > $maxLength ? Str::limit($text, $maxLength) : $text }}
                                        </p>
                                       
                                    </div>
                                </td>

                                {{-- Tanggal Target --}}
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-slate-500">
                                        {{ $item->tanggal_target
                                            ? \Carbon\Carbon::parse($item->tanggal_target)->locale('id')->translatedFormat('d M Y')
                                            : ($item->target_date
                                                ? \Carbon\Carbon::parse($item->target_date)->locale('id')->translatedFormat('d M Y')
                                                : '-') }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Detail --}}
                                        <a href="{{ route('tindaklanjut.show_arahan', $item->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-2 bg-slate-100 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all"
                                            title="Lihat Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>

                                        @can('create_tindak_lanjut')
                                            @if($isRevisiUnitIni && $tlMilikUser)
                                            <a href="{{ route('tindaklanjut.edit', $tlMilikUser->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all"
                                                title="Revisi Laporan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            @elseif(!$sudahInputUnitIni)
                                            <a href="{{ route('tindaklanjut.create', ['arahan_id' => $item->id]) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-indigo-600 to-sky-600 text-white hover:from-indigo-700 hover:to-sky-700 rounded-xl transition-all shadow-sm"
                                                title="Isi Tindak Lanjut">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </a>
                                            @elseif($isApprovedUnitIni)
                                            <div class="flex items-center justify-center w-9 h-9 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100" title="Selesai">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            @elseif($isInApprovalUnitIni)
                                            <div class="flex items-center justify-center w-9 h-9 bg-blue-50 text-blue-500 rounded-xl" title="Menunggu Approval">
                                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Tidak ada arahan ditemukan</p>
                                        <p class="text-xs text-slate-300 mt-1">Coba ubah kata kunci pencarian</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-bold text-slate-400">
                            Menampilkan {{ $arahan->firstItem() ?? 1 }}-{{ $arahan->lastItem() ?? $arahan->count() }} dari {{ $arahan->total() ?? 0 }} arahan
                        </p>
                        {{ $arahan->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>