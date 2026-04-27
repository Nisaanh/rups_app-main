<x-app-layout>
    <div class="space-y-6">

        {{-- Daftar Tindak Lanjut dengan Progress Cards (Desain Asli) --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Daftar Tindak Lanjut</h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Monitoring & Evaluasi Arahan</p>
                    </div>
                </div>
            </div>

            {{-- Progress Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                <div class="p-6 bg-gradient-to-br from-amber-50/30 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Belum Ditindaklanjuti</p>
                            <p class="text-3xl font-black text-amber-700 mt-1">{{ $stats['pending'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gradient-to-br from-blue-50/30 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">In Approval</p>
                            <p class="text-3xl font-black text-blue-600 mt-1">{{ $stats['in_approval'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gradient-to-br from-orange-50/30 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest">Perlu Revisi</p>
                            <p class="text-3xl font-black text-orange-600 mt-1">{{ $stats['revisi'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gradient-to-br from-emerald-50/30 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Approved/Selesai</p>
                            <p class="text-3xl font-black text-emerald-700 mt-1">{{ $stats['approved'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Bar --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Daftar Arahan</h3>
                    <p class="text-[9px] text-slate-400 font-bold mt-0.5">Menampilkan {{ $arahan->firstItem() ?? 1 }}-{{ $arahan->lastItem() ?? $arahan->count() }} dari {{ $arahan->total() ?? $stats['total'] }} arahan</p>
                </div>

                <form action="{{ route('tindaklanjut.index') }}" method="GET" class="flex gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari strategi arahan..."
                            class="pl-4 pr-10 py-2 border-slate-200 rounded-xl w-64 text-xs font-bold focus:ring-slate-900 focus:border-slate-900">
                    </div>
                    <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl font-black text-xs uppercase hover:bg-slate-800 transition shadow-lg shadow-slate-200">Cari</button>
                    @if(request('search'))
                    <a href="{{ route('tindaklanjut.index') }}" class="bg-rose-50 text-rose-600 px-4 py-2 rounded-xl font-black text-xs uppercase hover:bg-rose-100 transition">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase text-[9px] font-black tracking-widest border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">BIDANG</th>
                            <th class="px-6 py-4">STRATEGI/ARAHAN</th>
                            <th class="px-6 py-4">TANGGAL TARGET</th>
                            <th class="px-6 py-4 text-center">STATUS</th>
                            <th class="px-6 py-4 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($arahan as $item)
                        @php
                            $userUnitKerjaId = Auth::user()->unit_kerja_id;

                            // Tindak lanjut milik unit user yang sedang login
                            $tlMilikUser = $item->tindakLanjut
                                ->where('unit_kerja_id', $userUnitKerjaId)
                                ->sortByDesc('created_at')
                                ->first();

                            // Untuk tampilan status kolom (berdasarkan semua unit)
                            $latestTl = $item->tindakLanjut->sortByDesc('created_at')->first();
                            $count = $item->tindakLanjut->count();
                            $hasInput = $count > 0;

                            $isGlobalTD = ($item->status ?? '') === 'td';

                            $isFullyApproved = $latestTl
                                ? $latestTl->approvals()->where('stage', 5)->where('status', 'approved')->exists()
                                : false;
                            $isRevisi    = $latestTl && $latestTl->status === 'rejected';
                            $isInApproval = $latestTl && $latestTl->status === 'in_approval';

                            // Kondisi khusus PER UNIT USER
                            $sudahInputUnitIni   = !is_null($tlMilikUser);
                            $isRevisiUnitIni     = $tlMilikUser && $tlMilikUser->status === 'rejected';
                            $isApprovedUnitIni   = $tlMilikUser && $tlMilikUser->status === 'approved';
                            $isInApprovalUnitIni = $tlMilikUser && $tlMilikUser->status === 'in_approval';

                            // Status badge (tampilan kolom status)
                            if ($isGlobalTD) {
                                $statusCode  = 'TD';
                                $statusText  = 'Tidak Ditindaklanjuti';
                                $statusColor = 'slate';
                            } elseif ($isFullyApproved) {
                                $statusCode  = 'S';
                                $statusText  = 'Selesai';
                                $statusColor = 'emerald';
                            } elseif ($isRevisi) {
                                $statusCode  = 'BS';
                                $statusText  = 'Perlu Revisi';
                                $statusColor = 'orange';
                            } elseif ($isInApproval) {
                                $statusCode  = 'BS';
                                $statusText  = 'Dalam Approval';
                                $statusColor = 'blue';
                            } elseif ($latestTl) {
                                $statusCode  = 'BS';
                                $statusText  = 'Sedang Berjalan';
                                $statusColor = 'amber';
                            } else {
                                $statusCode  = 'BS';
                                $statusText  = 'Belum Ditindaklanjuti';
                                $statusColor = 'rose';
                            }

                            $revisiCount = $latestTl
                                ? $latestTl->approvals()->where('status', 'rejected')->count()
                                : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-600">{{ $item->bidang->name ?? $item->bidang ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-md">
                                    <p class="font-bold text-slate-800 text-sm leading-snug group-hover:text-blue-600 transition">
                                        {{ $item->strategi ?? $item->arahan ?? '-' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-slate-500">
                                    {{ $item->tanggal_target
                                        ? \Carbon\Carbon::parse($item->tanggal_target)->locale('id')->translatedFormat('d F Y')
                                        : ($item->target_date
                                            ? \Carbon\Carbon::parse($item->target_date)->locale('id')->translatedFormat('d F Y')
                                            : '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 border border-{{ $statusColor }}-100 rounded-full text-[10px] font-black uppercase tracking-tighter">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $statusColor }}-600"></span>
                                        {{ $statusCode }} - {{ $statusText }}
                                    </span>
                                    @if($hasInput)
                                    <span class="text-[9px] text-slate-400 font-bold italic">({{ $count }} Laporan)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    {{-- Tombol Lihat Detail selalu muncul --}}
                                    <a href="{{ route('tindaklanjut.show_arahan', $item->id) }}"
                                        class="px-4 py-2 bg-slate-50 text-slate-600 hover:text-blue-600 rounded-xl transition hover:bg-blue-50 text-xs font-black uppercase tracking-wider">
                                        Lihat Detail
                                    </a>

                                    @can('create_tindak_lanjut')
                                        @if(!$isGlobalTD)
                                            @if($isRevisiUnitIni && $tlMilikUser)
                                                {{-- Unit ini punya laporan yang perlu direvisi --}}
                                                <a href="{{ route('tindaklanjut.edit', $tlMilikUser->id) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition shadow-lg shadow-orange-200">
                                                    Revisi Laporan
                                                </a>
                                            @elseif(!$sudahInputUnitIni)
                                                {{-- Unit ini belum pernah input sama sekali --}}
                                                <a href="{{ route('tindaklanjut.create', ['arahan_id' => $item->id]) }}"
                                                    class="inline-flex items-center px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                                                    Isi Tindak Lanjut
                                                </a>
                                            @elseif($isApprovedUnitIni)
                                                {{-- Unit ini sudah selesai, tampilkan badge --}}
                                                <span class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl font-black text-[10px] uppercase tracking-widest">
                                                    ✓ Sudah Selesai
                                                </span>
                                            @elseif($isInApprovalUnitIni)
                                                {{-- Unit ini sedang dalam proses approval --}}
                                                <span class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl font-black text-[10px] uppercase tracking-widest">
                                                    Dalam Approval
                                                </span>
                                            @endif
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-slate-400 italic text-sm font-bold">Tidak ada arahan ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-50 bg-slate-50/30">
                <div class="flex justify-between items-center">
                    <p class="text-[10px] font-black text-slate-400">Menampilkan {{ $arahan->firstItem() ?? 1 }}-{{ $arahan->lastItem() ?? $arahan->count() }} dari {{ $arahan->total() ?? $stats['total'] }} arahan</p>
                    {{ $arahan->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>