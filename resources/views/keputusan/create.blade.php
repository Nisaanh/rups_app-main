<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('keputusan.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>

            <div class="max-w-2xl mx-auto">
                {{-- Form Card --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                    {{-- Header --}}
                    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-8 text-white text-center">
                        <div class="relative z-10">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 mb-4">
                                <svg class="w-8 h-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-black uppercase tracking-tight">Buat Keputusan RUPS</h3>
                            <p class="text-slate-300 text-sm mt-1">Inisiasi periode baru untuk monitoring arahan</p>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-48 h-48 bg-sky-500/20 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-36 h-36 bg-indigo-500/10 rounded-full blur-2xl"></div>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('keputusan.store') }}" method="POST">
                        @csrf
                        <div class="p-8 space-y-6">
                            {{-- Periode Tahun --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Periode Tahun RUPS <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="periode_year"
                                    value="{{ old('periode_year', date('Y')) }}"
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold text-lg focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all @error('periode_year') border-rose-300 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                    min="2000"
                                    max="{{ date('Y') + 5 }}"
                                    required
                                    placeholder="Masukkan tahun RUPS">
                                @error('periode_year')
                                <p class="text-rose-500 text-[10px] font-bold mt-1.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Info Cards --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-[10px] font-bold text-amber-700 uppercase">Perhatian</p>
                                    </div>
                                    <p class="text-xs text-amber-600 mt-1.5 leading-relaxed">
                                        Setiap tahun hanya dapat memiliki <strong>satu</strong> keputusan RUPS aktif.
                                    </p>
                                </div>
                                <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-[10px] font-bold text-indigo-700 uppercase">Langkah Selanjutnya</p>
                                    </div>
                                    <p class="text-xs text-indigo-600 mt-1.5 leading-relaxed">
                                        Setelah membuat keputusan, Anda dapat langsung menambahkan arahan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="px-8 py-6 bg-slate-50/80 border-t border-slate-100 flex justify-center">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-slate-900 to-indigo-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:from-slate-800 hover:to-indigo-800 shadow-xl shadow-slate-200 transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                Buat & Lanjut Input Arahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>