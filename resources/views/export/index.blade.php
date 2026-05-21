<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight">
                                Monitoring & <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">Export</span>
                            </h2>
                            <p class="text-slate-300 mt-2 text-sm font-medium">
                                Role: <span class="font-bold text-white">{{ auth()->user()->getRoleNames()->first() }}</span>
                                @if(!$isAdmin)
                                — <span class="font-bold text-white">{{ auth()->user()->unitKerja->name ?? '' }}</span>
                                @endif
                            </p>
                        </div>

                        @can('export_report')
                        <a href="{{ route('export.download', array_merge(request()->query(), ['jenis' => 'tindaklanjut'])) }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-2xl font-bold text-xs uppercase tracking-wider hover:bg-emerald-600 shadow-lg shadow-emerald-200 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export Excel ({{ $total }} data)
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Filter Bar --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6">
               <form method="GET" action="{{ route('export.index') }}" class="flex flex-wrap items-end justify-between gap-4">
                    @if($isAdmin && $unitKerjaList->isNotEmpty())
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Unit Kerja</label>
                        <select name="unit_kerja_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua Unit</option>
                            @foreach($unitKerjaList as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_kerja_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Keputusan</label>
                        <select name="keputusan_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua Keputusan</option>
                            @foreach($keputusanList as $kep)
                            <option value="{{ $kep->id }}" {{ request('keputusan_id') == $kep->id ? 'selected' : '' }}>
                                {{ $kep->nomor_keputusan }} ({{ $kep->periode_year }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="min-w-[130px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Bulan</label>
                        <select name="periode_bulan" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua</option>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                            <option value="{{ $i + 1 }}" {{ request('periode_bulan') == $i + 1 ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="min-w-[110px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Tahun</label>
                        <select name="periode_tahun" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua</option>
                            @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('periode_tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="min-w-[160px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="in_approval" {{ request('status') == 'in_approval' ? 'selected' : '' }}>Dalam Approval</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Revisi / TD</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:from-indigo-700 hover:to-sky-700 transition shadow-sm">
                            Filter
                        </button>
                        @if(request()->hasAny(['unit_kerja_id','keputusan_id','periode_bulan','periode_tahun','status']))
                        <a href="{{ route('export.index') }}"
                            class="px-4 py-3 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-rose-100 transition">
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Table Header --}}
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">{{ $total }} data ditemukan</h3>
                            @if(request()->hasAny(['unit_kerja_id','keputusan_id','periode_bulan','periode_tahun','status']))
                            <p class="text-xs text-slate-400 mt-0.5">Filter aktif</p>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-full uppercase">
                            {{ $isAdmin ? 'Semua Unit' : (auth()->user()->unitKerja->name ?? '') }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[1200px]">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center w-10">No</th>
                                @if($isAdmin)
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Unit Kerja</th>
                                @endif
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Keputusan</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Arahan</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center w-20">Periode</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Tindak Lanjut</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest w-28">Kendala</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest w-28">Keterangan</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center w-24">Status</th>
                                @if($isAdmin)
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center w-28">Approval</th>
                                @endif
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Dibuat Oleh</th>
                                <th class="px-4 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center w-24">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($data as $i => $tl)
                            @php
                                $tdApproval = $tl->approvals->first(function($a) {
                                    return str_contains(strtolower($a->note ?? ''), 'td');
                                });
                                $isTD = ($tl->arahan->status ?? '') === 'td' || $tdApproval;
                                
                                $statusConfig = match(true) {
                                    $tl->status === 'approved' => ['bg-emerald-50 text-emerald-700 border-emerald-100', 'Selesai'],
                                    $tl->status === 'in_approval' => ['bg-blue-50 text-blue-700 border-blue-100', 'Approval'],
                                    $isTD => ['bg-slate-100 text-slate-600 border-slate-200', 'TD'],
                                    $tl->status === 'rejected' => ['bg-rose-50 text-rose-700 border-rose-100', 'Revisi'],
                                    default => ['bg-amber-50 text-amber-700 border-amber-100', 'Menunggu'],
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-center text-xs font-bold text-slate-400">
                                    {{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}
                                </td>
                                @if($isAdmin)
                                <td class="px-4 py-3">
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                                        {{ $tl->unitKerja->name ?? '-' }}
                                    </span>
                                </td>
                                @endif
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-slate-700">{{ $tl->arahan->keputusan->periode_year ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 max-w-[180px]">
                                    <p class="text-xs text-slate-600 font-medium line-clamp-2">
                                        {{ Str::limit($tl->arahan->strategi ?? '-', 60) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded-lg">
                                        {{ $tl->periode_bulan }}/{{ $tl->periode_tahun }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 max-w-[180px]">
                                    <p class="text-xs text-slate-700 font-medium line-clamp-2">
                                        {{ Str::limit($tl->tindak_lanjut, 60) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($tl->kendala)
                                    <p class="text-[10px] text-rose-600 font-medium line-clamp-2">{{ Str::limit($tl->kendala, 35) }}</p>
                                    @else
                                    <span class="text-[10px] text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($tl->keterangan)
                                    <p class="text-[10px] text-blue-600 font-medium line-clamp-2">{{ Str::limit($tl->keterangan, 35) }}</p>
                                    @else
                                    <span class="text-[10px] text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase border {{ $statusConfig[0] }}">
                                            {{ $statusConfig[1] }}
                                        </span>
                                    </div>
                                </td>
                                @if($isAdmin)
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-1">
                                        @for($s = 1; $s <= 5; $s++)
                                            @php
                                                $approvedStage = $tl->approvals->where('stage', $s)->where('status', 'approved')->first();
                                                $isCurrentStage = $tl->approvals->where('stage', $s)->where('status', 'pending')->first();
                                            @endphp
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-black
                                                {{ $approvedStage ? 'bg-emerald-500 text-white' : ($isCurrentStage ? 'bg-amber-500 text-white ring-2 ring-amber-300' : 'bg-slate-100 text-slate-400') }}">
                                                {{ $s }}
                                            </span>
                                        @endfor
                                    </div>
                                </td>
                                @endif
                                <td class="px-4 py-3">
                                    <p class="text-xs font-bold text-slate-600">{{ $tl->creator->name ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[10px] font-medium text-slate-600">{{ $tl->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[9px] text-slate-400 ml-1">{{ $tl->created_at->format('H:i') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 12 : 10 }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-400">Tidak ada data ditemukan</p>
                                        <p class="text-xs text-slate-300 mt-1">Coba ubah atau reset filter</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($data->hasPages())
                <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                    {{ $data->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>