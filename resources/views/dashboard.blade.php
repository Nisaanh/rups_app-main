<x-app-layout>
    <div class="py-2 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Welcome Banner --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-3xl shadow-xl p-8 text-white">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-extrabold mb-2 tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-400 text-lg max-w-xl">Sistem Monitoring dan Tindak Lanjut Keputusan RUPS - Kelola data dengan presisi.</p>
                    </div>
                    <div class="mt-6 md:mt-0 bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-300 mb-1">Tanggal Hari Ini</p>
                        <p class="text-xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-64 h-64 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl"></div>
            </div>

            {{-- Quick Stats Area --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Card 1: Keputusan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Keputusan</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ array_sum($keputusanStats ?? []) }}</h3>
                        </div>
                    </div>
                   
                </div>

                {{-- Card 2: Arahan --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Arahan</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ $totalArahan ?? 0 }}</h3>
                        </div>
                    </div>
                    
                </div>

                {{-- Card 3: Tindak Lanjut --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tindak Lanjut</p>
                            <h3 class="text-3xl font-black text-slate-900">{{ array_sum($tindakLanjutStats ?? []) }}</h3>
                        </div>
                    </div>
                    
                </div>
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 gap-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-slate-800">Progress Tindak Lanjut Per Unit Kerja</h3>
                        @if($is_global && $unitKerjaList->count() > 1)
                        <select id="approvalFilter" class="text-xs font-bold border-slate-200 rounded-xl bg-slate-50 px-3 py-2">
                            <option value="all">Semua Unit Kerja</option>
                            @foreach($unitKerjaList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="h-80 relative">
                        <canvas id="approvalChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Pending Approval Table --}}
            @if($pendingApprovals->count() > 0)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Antrian Persetujuan</h3>
                    <p class="text-xs text-slate-400 mt-1">Menunggu keputusan Anda</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[700px]">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                                <th class="px-8 py-5">Unit Kerja</th>
                                <th class="px-8 py-5">Deskripsi</th>
                                <th class="px-8 py-5 text-center">Stage</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingApprovals as $approval)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-5 font-bold text-slate-700">{{ $approval->tindakLanjut->unitKerja->name ?? '-' }}</td>
                                <td class="px-8 py-5 text-slate-500 text-sm">{{ Str::limit($approval->tindakLanjut->tindak_lanjut, 60) }}</td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-4 py-1.5 bg-sky-50 text-sky-700 rounded-full text-[10px] font-black uppercase">
                                        Stage {{ $approval->stage }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('approval.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-sky-600 transition-all shadow-sm active:scale-95">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Proses
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Store chart instance
        let approvalChart = null;
        
        // Data from server
        const unitKerjaFull = @json($unitKerjaStats);
        
        function buildApprovalChart(filterId = 'all') {
            const ctx = document.getElementById('approvalChart');
            if (!ctx) return;
            
            // Destroy existing chart
            if (approvalChart) {
                approvalChart.destroy();
            }
            
            // Filter data
            let filtered = unitKerjaFull;
            if (filterId !== 'all') {
                filtered = unitKerjaFull.filter(item => item.id == filterId);
            }
            
            // If no data, show empty chart
            if (filtered.length === 0) {
                filtered = [{ id: 0, name: 'Tidak ada data', total: 0 }];
            }
            
            // Create new chart
            approvalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: filtered.map(d => d.name.length > 20 ? d.name.substring(0, 20) + '...' : d.name),
                    datasets: [{
                        label: 'Jumlah Tindak Lanjut',
                        data: filtered.map(d => d.total),
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 11, weight: 'bold' },
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Total: ${context.raw} tindak lanjut`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 10 }
                            },
                            grid: {
                                color: '#e2e8f0',
                                drawBorder: true
                            },
                            title: {
                                display: true,
                                text: 'Jumlah',
                                font: { size: 10, weight: 'bold' }
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 9 },
                                rotation: 0,
                                autoSkip: true,
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Unit Kerja',
                                font: { size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }
        
        // Initialize chart when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('approvalChart')) {
                buildApprovalChart('all');
            }
            
            // Add filter event listener
            const filterSelect = document.getElementById('approvalFilter');
            if (filterSelect) {
                filterSelect.addEventListener('change', (e) => {
                    buildApprovalChart(e.target.value);
                });
            }
        });
    </script>
    @endpush
</x-app-layout>