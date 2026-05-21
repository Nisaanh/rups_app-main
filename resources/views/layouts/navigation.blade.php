<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">

            {{-- Left: Logo & Title --}}
            <div class="flex items-center gap-3">
                {{-- Logo/Icon --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-80 transition">
                </a>
            </div>

            {{-- Right: User Menu --}}
            <div class="flex items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-1.5 border border-slate-200 text-sm rounded-xl text-slate-500 bg-white hover:text-slate-700 hover:border-slate-300 focus:outline-none transition gap-2">
                            {{-- Avatar --}}
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-400 to-sky-400 text-white flex items-center justify-center text-[10px] font-black shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            {{-- Nama & Role --}}
                            <div class="text-left hidden sm:block">
                                <div class="text-xs font-bold text-slate-800 leading-none">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[8px] font-black uppercase tracking-wider text-slate-400 mt-0.5">
                                    {{ Auth::user()->getRoleNames()->first() ?? 'User' }}
                                </div>
                            </div>

                            {{-- Chevron --}}
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Mobile User Info --}}
                        <div class="px-4 py-2.5 border-b border-slate-100 sm:hidden">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Akun</p>
                            <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-[9px] text-slate-500">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</p>
                        </div>

                        {{-- Menu Items --}}
                        <x-dropdown-link :href="route('profile.edit')" class="text-xs">
                            <svg class="w-3.5 h-3.5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </x-dropdown-link>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                class="text-xs text-rose-600 font-bold"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>