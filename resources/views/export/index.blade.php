<x-app-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Monitoring & Export</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Role: <span class="font-bold text-slate-600">{{ auth()->user()->getRoleNames()->first() }}</span>
                    @if(!$isAdmin)
                    — <span class="font-bold text-slate-600">{{ auth()->user()->unitKerja->name ?? '' }}</span>
                    @endif
                </p>
            </div>

            {{-- Tombol Export --}}
            @can('export_report')
            <div class="flex gap-3">
                <a href="{{ route('export.download', array_merge(request()->query(), ['jenis' => 'tindaklanjut'])) }}"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel ({{ $total }} data)
                </a>
            </div>
            @endcan
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
            <form method="GET" action="{{ route('export.index') }}" class="flex flex-wrap gap-3 items-end">

                @if($isAdmin && $unitKerjaList->isNotEmpty())
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Unit Kerja</label>
                    <select name="unit_kerja_id" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
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
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Keputusan</label>
                    <select name="keputusan_id" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Keputusan</option>
                        @foreach($keputusanList as $kep)
                        <option value="{{ $kep->id }}" {{ request('keputusan_id') == $kep->id ? 'selected' : '' }}>
                            {{ $kep->nomor_keputusan }} ({{ $kep->periode_year }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[130px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Bulan</label>
                    <select name="periode_bulan" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                        <option value="{{ $i + 1 }}" {{ request('periode_bulan') == $i + 1 ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[110px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun</label>
                    <select name="periode_tahun" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('periode_tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-slate-900 focus:border-slate-900">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="in_approval" {{ request('status') == 'in_approval' ? 'selected' : '' }}>Sedang DiApproval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Revisi / TD</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 transition shadow-lg shadow-slate-200 transform hover:scale-105">
                        Filter
                    </button>
                    @if(request()->hasAny(['unit_kerja_id','keputusan_id','periode_bulan','periode_tahun','status']))
                    <a href="{{ route('export.index') }}"
                        class="px-4 py-2.5 bg-rose-50 text-rose-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-rose-100 transition transform hover:scale-105">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-slate-700 uppercase text-xs tracking-widest">
                        Hasil: {{ $total }} data ditemukan
                    </h3>
                    @if(request()->hasAny(['unit_kerja_id','keputusan_id','periode_bulan','periode_tahun','status']))
                    <p class="text-[9px] text-slate-400 mt-1">Filter aktif</p>
                    @endif
                </div>
                @if($isAdmin)
                <span class="text-[10px] font-bold text-slate-400 uppercase bg-slate-100 px-3 py-1.5 rounded-full">
                    Semua Unit Kerja
                </span>
                @else
                <span class="text-[10px] font-bold text-slate-400 uppercase bg-slate-100 px-3 py-1.5 rounded-full">
                    {{ auth()->user()->unitKerja->name ?? '' }}
                </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[1200px]">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-slate-400 uppercase text-[10px] font-black tracking-widest">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            @if($isAdmin)
                            <th class="px-4 py-3">Unit Kerja</th>
                            @endif
                            <th class="px-4 py-3">Keputusan</th>
                            <th class="px-4 py-3">Arahan</th>
                            <th class="px-4 py-3 text-center w-20">Periode</th>
                            <th class="px-4 py-3">Tindak Lanjut</th>
                            <th class="px-4 py-3 w-32">Kendala</th>
                            <th class="px-4 py-3 w-32">Keterangan</th>
                            <th class="px-4 py-3 text-center w-24">Status</th>
                            @if($isAdmin)
                            <th class="px-4 py-3 text-center w-32">Approval</th>
                            @endif
                            <th class="px-4 py-3">Dibuat Oleh</th>
                            <th class="px-4 py-3 text-center w-28">Tanggal</th>
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
                                $tl->status === 'approved' => ['bg-emerald-50 text-emerald-700 border-emerald-100', '✓ Selesai'],
                                $tl->status === 'in_approval' => ['bg-blue-50 text-blue-700 border-blue-100', '⟳ Dalam Approval'],
                                $isTD => ['bg-slate-900 text-white border-slate-900', '⊘ TD'],
                                $tl->status === 'rejected' => ['bg-rose-50 text-rose-700 border-rose-100', '✎ Perlu Revisi'],
                                default => ['bg-amber-50 text-amber-700 border-amber-100', '○ Menunggu'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 text-center text-slate-400 text-xs font-bold">
                                {{ ($data->currentPage() - 1) * $data->perPage() + $i + 1 }}
                            </td>
                            @if($isAdmin)
                            <td class="px-4 py-3">
                                <span class="text-[10px] font-black text-slate-600 bg-slate-100 px-2 py-1 rounded-lg whitespace-nowrap">
                                    {{ $tl->unitKerja->name ?? '-' }}
                                </span>
                            </td>
                            @endif
                            <td class="px-4 py-3">
                               
                                <p class="text-xs font-black text-slate-700">{{ $tl->arahan->keputusan->periode_year ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 max-w-[200px]">
                                <p class="text-xs text-slate-600 font-medium line-clamp-2" title="{{ $tl->arahan->strategi ?? '-' }}">
                                    {{ Str::limit($tl->arahan->strategi ?? '-', 60) }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded-lg whitespace-nowrap">
                                    {{ $tl->periode_bulan }}/{{ $tl->periode_tahun }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-[200px]">
                                <p class="text-xs text-slate-700 font-medium line-clamp-2" title="{{ $tl->tindak_lanjut }}">
                                    {{ Str::limit($tl->tindak_lanjut, 60) }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                @if($tl->kendala)
                                <div class="flex items-start gap-1">
                                    <svg class="w-3 h-3 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    <p class="text-[10px] text-rose-600 font-medium line-clamp-2" title="{{ $tl->kendala }}">
                                        {{ Str::limit($tl->kendala, 40) }}
                                    </p>
                                </div>
                                @else
                                <span class="text-[10px] text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($tl->keterangan)
                                <div class="flex items-start gap-1">
                                    <svg class="w-3 h-3 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                                    </svg>
                                    <p class="text-[10px] text-blue-600 font-medium line-clamp-2" title="{{ $tl->keterangan }}">
                                        {{ Str::limit($tl->keterangan, 40) }}
                                    </p>
                                </div>
                                @else
                                <span class="text-[10px] text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-black uppercase border whitespace-nowrap {{ $statusConfig[0] }}">
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
                                        <div class="relative group">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[8px] font-black transition-all
                                                @if($approvedStage)
                                                    bg-emerald-500 text-white shadow-sm
                                                @elseif($isCurrentStage)
                                                    bg-amber-500 text-white ring-2 ring-amber-300
                                                @else
                                                    bg-slate-100 text-slate-400
                                                @endif
                                            ">
                                                {{ $s }}
                                            </span>
                                            <div class="absolute bottom-full mb-1 left-1/2 transform -translate-x-1/2 hidden group-hover:block z-10">
                                                <div class="bg-slate-800 text-white text-[8px] font-bold px-1.5 py-0.5 rounded whitespace-nowrap">
                                                    @if($approvedStage)
                                                        ✅ Disetujui
                                                    @elseif($isCurrentStage)
                                                        ⏳ Menunggu
                                                    @else
                                                        ⭕ Belum
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </td>
                            @endif
                            <td class="px-4 py-3">
                                <p class="text-xs font-bold text-slate-600">{{ $tl->creator->name ?? '-' }}</p>
                                <p class="text-[9px] text-slate-400">{{ $tl->creator->getRoleNames()->first() ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[10px] font-medium text-slate-600 whitespace-nowrap">
                                    {{ $tl->created_at->format('d/m/Y') }}
                                </span>
                                <span class="text-[9px] text-slate-400 ml-1 whitespace-nowrap">
                                    {{ $tl->created_at->format('H:i') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 12 : 10 }}" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-bold text-sm">Tidak ada data ditemukan</p>
                                    <p class="text-slate-400 text-xs mt-1">Coba ubah filter atau reset filter</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $data->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>