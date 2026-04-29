<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="space-y-8">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                        Daftar Keputusan <span class="text-blue-600">RUPS</span>
                    </h2>
                    <p class="text-slate-500 mt-1 text-sm font-medium italic">Manajemen dan arsip keputusan rapat umum pemegang saham.</p>
                </div>

                @can('create_keputusan')
                    @php
                        $tahunSekarang = date('Y');
                        $existing = \App\Models\Keputusan::where('periode_year', $tahunSekarang)->first();
                    @endphp

                    <div class="flex items-center">
                        @if($existing)
                            {{-- Sudah ada → tambah arahan (Warna Biru Modern) --}}
                            <a href="{{ route('arahan.create', ['keputusan_id' => $existing->id]) }}"
                                class="group inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-200 active:scale-95">
                                <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Arahan
                            </a>
                        @else
                            {{-- Belum ada → buat keputusan (Warna Dark Premium) --}}
                            <a href="{{ route('keputusan.create') }}"
                                class="group inline-flex items-center px-6 py-3 bg-slate-900 text-white rounded-2xl font-bold text-sm transition-all hover:bg-black hover:shadow-xl hover:shadow-slate-300 active:scale-95">
                                <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Buat Keputusan Baru
                            </a>
                        @endif
                    </div>
                @endcan
            </div>

            {{-- Table Container --}}
            <div class="bg-white border border-slate-200/60 rounded-[2.5rem] shadow-xl shadow-slate-200/40 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100">
                                <th class="px-10 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Periode</th>
                                <th class="px-10 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-10 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Dibuat Oleh</th>
                                <th class="px-10 py-5 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($keputusan as $item)
                            <tr class="group hover:bg-blue-50/30 transition-colors duration-300">
                                
                                <td class="px-10 py-6">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mr-4 group-hover:scale-110 transition-transform">
                                            <span class="font-black text-xs">{{ substr($item->periode_year, -2) }}</span>
                                        </div>
                                        <span class="text-base font-bold text-slate-700 tracking-tight">{{ $item->periode_year }}</span>
                                    </div>
                                </td>

                                <td class="px-10 py-6">
                                    @if($item->status === 'BD')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 animate-pulse"></span> Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> Dikirim
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 leading-tight">{{ $item->creator->name ?? 'System' }}</span>
                                        <span class="text-[11px] font-medium text-slate-400 mt-1 uppercase tracking-tighter">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                </td>

                                <td class="px-10 py-6 text-right">
                                    <div class="flex justify-end items-center space-x-3">
                                        {{-- View --}}
                                        <a href="{{ route('keputusan.show', $item) }}" 
                                           class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"
                                           title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @can('edit_keputusan')
                                            @if($item->status === 'BD')
                                                {{-- Lanjutkan Action --}}
                                                <a href="{{ route('keputusan.edit', $item) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all font-bold text-xs border border-blue-100 shadow-sm"
                                                   title="Lanjutkan Isi Arahan">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Lanjutkan
                                                </a>
                                            @else
                                                <a href="{{ route('keputusan.edit', $item) }}"
                                                   class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all"
                                                   title="Edit Data">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        @endcan

                                        @can('delete_keputusan')
                                            <form action="{{ route('keputusan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
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
                                <td colspan="5" class="px-10 py-32 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-400 font-bold italic tracking-tight">Belum ada data keputusan yang tercatat.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($keputusan->hasPages())
                <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100">
                    {{ $keputusan->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>