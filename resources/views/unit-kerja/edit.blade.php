<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
       <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Back Button --}}
            <a href="{{ route('unit-kerja.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            {{-- Form Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                {{-- Header --}}
                <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 p-8 text-white">
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black uppercase tracking-tight">Sinkronisasi Struktur</h3>
                            <p class="text-slate-300 text-sm mt-1">Mengubah unit: {{ $unitKerja->name }}</p>
                        </div>
                        <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sky-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute right-20 top-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                </div>

                @if ($errors->any())
                <div class="mx-6 mt-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl">
                    <p class="text-[10px] font-black uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Gagal menyimpan
                    </p>
                    <ul class="text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <span class="mt-1 w-1 h-1 rounded-full bg-rose-500 flex-shrink-0"></span>
                            {{ $error }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('unit-kerja.update', $unitKerja) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-6">
                        {{-- Nama Unit --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Nama Unit Kerja <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $unitKerja->name) }}"
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('name') border-rose-300 @enderror"
                                   placeholder="Contoh: Seksi Infrastruktur IT" required>
                            @error('name') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Level --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Level <span class="text-rose-500">*</span>
                                </label>
                                <select name="level" 
                                    class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('level') border-rose-300 @enderror"
                                    required>
                                    <option value="">Pilih Level</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl }}" {{ old('level', $unitKerja->level) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                    @endforeach
                                </select>
                                @error('level') <p class="text-rose-500 text-[9px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Atasan --}}
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Atasan (Parent Unit)
                                </label>
                                <select name="report_to" 
                                    class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                                    <option value="">-- Pilih Atasan --</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('report_to', $unitKerja->report_to) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }} ({{ $parent->level }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-8 py-6 bg-slate-50/80 border-t border-slate-100 flex justify-end gap-3">
                        <a href="{{ route('unit-kerja.index') }}" 
                            class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:from-emerald-600 hover:to-teal-600 shadow-lg shadow-emerald-200 transition-all active:scale-95">
                            Update Unit Kerja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>