<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Section --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <h2 class="text-3xl font-black tracking-tight">
                                Daftar <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">Keputusan RUPS</span>
                            </h2>
                            <p class="text-slate-300 mt-2 text-sm font-medium max-w-xl">
                                Manajemen dan arsip keputusan rapat umum pemegang saham. Kelola dengan presisi dan efisiensi tinggi.
                            </p>
                        </div>

                        @can('create_keputusan')
                            @php
                                $tahunSekarang = date('Y');
                                $existing = \App\Models\Keputusan::where('periode_year', $tahunSekarang)->first();
                            @endphp

                            <div class="flex items-center">
                                @if($existing)
                                    <a href="{{ route('arahan.create', ['keputusan_id' => $existing->id]) }}"
                                        class="group inline-flex items-center px-6 py-3 bg-white/10 backdrop-blur-md text-white rounded-2xl font-bold text-sm transition-all hover:bg-white/20 border border-white/20 shadow-lg hover:shadow-xl active:scale-95">
                                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Arahan {{ $tahunSekarang }}
                                    </a>
                                @else
                                    <a href="{{ route('keputusan.create') }}"
                                        class="group inline-flex items-center px-6 py-3 bg-white text-slate-900 rounded-2xl font-bold text-sm transition-all hover:bg-sky-50 hover:shadow-xl active:scale-95">
                                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Buat Keputusan {{ $tahunSekarang }}
                                    </a>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Stats Summary --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @php
                    $totalKeputusan = $keputusan->total();
                    $statusCounts = [
                        'BD' => \App\Models\Keputusan::where('status', 'BD')->count(),
                        'BS' => \App\Models\Keputusan::where('status', 'BS')->count(),
                        'S' => \App\Models\Keputusan::where('status', 'S')->count(),
                        'TD' => \App\Models\Keputusan::where('status', 'TD')->count(),
                    ];
                @endphp
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-amber-50">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $totalKeputusan }}</div>
                            <div class="text-xs text-slate-400">Total Keputusan</div>
                        </div>
                    </div>
                </div>
              
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $statusCounts['BS'] }}</div>
                            <div class="text-xs text-slate-400">Aktif</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-5 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900">{{ $statusCounts['S'] + $statusCounts['TD'] }}</div>
                            <div class="text-xs text-slate-400">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-800">Daftar Keputusan</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $keputusan->total() }} keputusan tercatat</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Periode</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Dibuat Oleh</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($keputusan as $item)
                            <tr class="group hover:bg-slate-50/50 transition-colors duration-200">
                                
                                {{-- Periode --}}
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                       
                                        <span class="text-base font-bold text-slate-700">{{ $item->periode_year }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-8 py-5">
                                    @if($item->status === 'BD')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2 animate-pulse"></span> Draft
                                        </span>
                                    @elseif($item->status === 'BS')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2 animate-pulse"></span> Aktif
                                        </span>
                                    @elseif($item->status === 'S')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> Selesai
                                        </span>
                                    @elseif($item->status === 'TD')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span> TD
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-50 text-slate-500 border border-slate-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span> {{ $item->status }}
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Creator --}}
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->creator->name ?? 'System' }}</span>
                                        <span class="text-[11px] text-slate-400 mt-0.5">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        {{-- View --}}
                                        <a href="{{ route('keputusan.show', $item) }}" 
                                           class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all"
                                           title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @can('edit_keputusan')
                                            @if($item->status === 'BD')
                                                <a href="{{ route('keputusan.edit', $item) }}"
                                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-xs font-bold hover:from-indigo-700 hover:to-sky-700 transition-all shadow-sm"
                                                   title="Lanjutkan Isi Arahan">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Lanjutkan
                                                </a>
                                            @else
                                                <a href="{{ route('keputusan.edit', $item) }}"
                                                   class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all"
                                                   title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        @endcan

                                        @can('delete_keputusan')
                                            <form action="{{ route('keputusan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus keputusan ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-400 font-bold">Belum ada data keputusan</p>
                                        <p class="text-xs text-slate-300 mt-1">Klik tombol di atas untuk membuat keputusan baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($keputusan->hasPages())
                <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                    {{ $keputusan->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>