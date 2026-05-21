<x-app-layout>
    <div class="py-2 bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl shadow-2xl p-8 text-white">
                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="text-center md:text-left space-y-2">
                            <h2 class="text-4xl font-black mb-2 tracking-tight">
                                Selamat Datang, <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-400">{{ Auth::user()->name }}</span>!
                            </h2>
                            <p class="text-slate-300 text-lg max-w-2xl">
                                Sistem Monitoring dan Tindak Lanjut Keputusan RUPS - Kelola data dengan presisi dan efisiensi tinggi.
                            </p>
                            @if(Auth::user()->unitKerja)
                            <div class="flex items-center gap-2 text-slate-400 mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-sm font-medium">{{ Auth::user()->unitKerja->name }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="mt-6 md:mt-0 flex flex-col sm:flex-row gap-4">
                            <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-300 mb-1">Tanggal</p>
                                <p class="text-xl font-bold">{{ now()->translatedFormat('d F Y') }}</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md px-6 py-4 rounded-2xl border border-white/20">
                                <p class="text-xs font-semibold uppercase tracking-widest text-slate-300 mb-1">Jam</p>
                                <p class="text-xl font-bold" id="liveTime">{{ now()->format('H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-72 h-72 bg-indigo-500/10 rounded-full blur-2xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-violet-500/5 rounded-full blur-2xl"></div>
            </div>

            {{-- Quick Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card Total Keputusan --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>

                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-2">{{ $totalKeputusan }}</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Total Keputusan RUPS</p>
                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400">Periode terbaru</span>
                            <span class="text-xs font-bold text-amber-700">{{ $periodeTermbaru }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card Total Arahan --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        @if($arahanBelumTL > 0)
                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-full text-xs font-bold">
                            {{ $arahanBelumTL }} Perlu Tindak Lanjut
                        </span>
                        @endif
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-2">{{ $totalArahan }}</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Total Arahan</p>
                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400">Belum ada tindak lanjut</span>
                            <span class="text-xs font-bold {{ $arahanBelumTL > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $arahanBelumTL }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Tindak Lanjut --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 hover:shadow-xl transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <!-- <div class="text-right">
                            <span class="text-2xl font-black text-slate-900">{{ number_format($completionRate, 1) }}%</span>
                            <p class="text-xs text-slate-400">Completion Rate</p>
                        </div> -->
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-4">{{ $totalTindakLanjut }}</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Total Tindak Lanjut</p>

                    {{-- Detail Status --}}
                    <div class="grid grid-cols-4 gap-3">
                        {{-- Selesai --}}
                        <div class="text-center bg-emerald-50 rounded-xl p-2.5">
                            <div class="text-lg font-black text-emerald-600">{{ $tindakLanjutStats['approved'] ?? 0 }}</div>
                            <div class="text-[10px] text-slate-500 flex items-center justify-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                            </div>
                        </div>
                        {{-- Approval --}}
                        <div class="text-center bg-blue-50 rounded-xl p-2.5">
                            <div class="text-lg font-black text-blue-600">{{ $tindakLanjutStats['in_approval'] ?? 0 }}</div>
                            <div class="text-[10px] text-slate-500 flex items-center justify-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Proses Approval
                            </div>
                        </div>
                        {{-- Reject --}}
                        <div class="text-center bg-rose-50 rounded-xl p-2.5">
                            <div class="text-lg font-black text-rose-600">{{ $tindakLanjutStats['rejected'] ?? 0 }}</div>
                            <div class="text-[10px] text-slate-500 flex items-center justify-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Ditolak
                            </div>
                        </div>
                        {{-- TD --}}
                        <div class="text-center bg-amber-50 rounded-xl p-2.5">
                            <div class="text-lg font-black text-amber-600">{{ $tindakLanjutStats['td'] ?? 0 }}</div>
                            <div class="text-[10px] text-slate-500 flex items-center justify-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>TD
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Chart: Progress Per Unit Kerja --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Progress per Unit Kerja</h3>
                            <p class="text-xs text-slate-400 mt-1">Distribusi tindak lanjut berdasarkan unit</p>
                        </div>
                        @if($is_global && $unitKerjaList->count() > 1)
                        <select id="approvalFilter" class="text-xs font-bold border-slate-200 rounded-xl bg-slate-50 px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="all">Semua Unit</option>
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

                {{-- Chart: Monthly Trend --}}
                <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-slate-800">Tren Bulanan {{ date('Y') }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Pergerakan tindak lanjut per bulan</p>
                    </div>
                    <div class="h-80 relative">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Pending Approval Table --}}
            @if($pendingApprovals->count() > 0)
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Antrian Persetujuan
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">Menunggu keputusan Anda untuk diproses</p>
                        </div>
                        <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-xs font-black uppercase shadow-sm">
                            {{ $pendingApprovals->count() }} Menunggu
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 bg-slate-50/50">
                                <th class="px-8 py-4">Unit Kerja</th>
                                <th class="px-8 py-4">Uraian Tindak Lanjut</th>
                                <th class="px-8 py-4 text-center">Arahan</th>

                                <th class="px-8 py-4 text-center">Tanggal</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">

                            @foreach($pendingApprovals as $approval)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-4">
                                    <div class="font-bold text-slate-700 text-sm">
                                        {{ $approval->tindakLanjut->unitKerja->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="text-slate-600 text-sm max-w-xs">
                                        {{ Str::limit($approval->tindakLanjut->tindak_lanjut, 80) }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 max-w-xs">
                                    <span class="block text-xs text-slate-500 truncate">
                                        {{ $approval->tindakLanjut->arahan->strategi ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-8 py-4 text-center">
                                    <div class="text-xs font-medium text-slate-600">
                                        {{ $approval->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $approval->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <a href="{{ route('approval.index') }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-sky-600 text-white rounded-xl text-xs font-bold hover:from-indigo-700 hover:to-sky-700 transition-all shadow-md hover:shadow-lg active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
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

            {{-- Recent Activities --}}
            @if($recentActivities->count() > 0)
            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Aktivitas Terbaru</h3>
                            <p class="text-xs text-slate-400 mt-1">5 tindak lanjut terakhir</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100 bg-slate-50/50">
                                <th class="px-8 py-4">Unit Kerja</th>
                                <th class="px-8 py-4">Uraian</th>
                                <th class="px-8 py-4 text-center">Status</th>
                                <th class="px-8 py-4 text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentActivities as $activity)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-4">
                                    <div class="font-bold text-slate-700 text-sm">
                                        {{ $activity->unitKerja->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="text-slate-600 text-sm">
                                        {{ Str::limit($activity->tindak_lanjut, 80) }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    @php
                                    $statusColors = [
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-rose-100 text-rose-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'in_approval' => 'bg-blue-100 text-blue-700',
                                    ];
                                    $statusLabels = [
                                    'approved' => 'Selesai',
                                    'rejected' => 'Ditolak',
                                    'pending' => 'Menunggu',
                                    'in_approval' => 'Proses Approval',
                                    ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $statusColors[$activity->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusLabels[$activity->status] ?? ucfirst($activity->status) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center text-xs text-slate-400">
                                    {{ $activity->created_at->format('d M Y H:i') }}
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
        // Live clock
        function updateTime() {
            const now = new Date();
            document.getElementById('liveTime').textContent =
                now.getHours().toString().padStart(2, '0') + ':' +
                now.getMinutes().toString().padStart(2, '0') + ':' +
                now.getSeconds().toString().padStart(2, '0');
        }
        setInterval(updateTime, 1000);

        // Unit chart
        let approvalChart = null;
        const unitKerjaFull = JSON.parse('{!! addslashes(json_encode($unitKerjaStats)) !!}');

        function buildApprovalChart(filterId = 'all') {
            const ctx = document.getElementById('approvalChart');
            if (!ctx) return;
            if (approvalChart) approvalChart.destroy();

            let filtered = unitKerjaFull;
            if (filterId !== 'all') filtered = unitKerjaFull.filter(item => item.id == filterId);
            if (filtered.length === 0) filtered = [{
                id: 0,
                name: 'Tidak ada data',
                total: 0,
                approved: 0,
                pending: 0,
                rejected: 0
            }];

            approvalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: filtered.map(d => d.name.length > 15 ? d.name.substring(0, 15) + '...' : d.name),
                    datasets: [{
                            label: 'Selesai',
                            data: filtered.map(d => d.approved || 0),
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        },
                        {
                            label: 'Pending',
                            data: filtered.map(d => d.pending || 0),
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        },
                        {
                            label: 'Ditolak',
                            data: filtered.map(d => d.rejected || 0),
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.dataset.label}: ${ctx.raw} TL`
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                font: {
                                    size: 9
                                },
                                maxRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: '#e2e8f0'
                            }
                        }
                    }
                }
            });
        }

        // Monthly trend chart
        let monthlyChart = null;
        const monthlyData = JSON.parse('{!! addslashes(json_encode($monthlyTL)) !!}');

        function buildMonthlyChart() {
            const ctx = document.getElementById('monthlyTrendChart');
            if (!ctx) return;
            if (monthlyChart) monthlyChart.destroy();

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const labels = monthlyData.map(d => months[d.month - 1]);
            const totals = monthlyData.map(d => d.total);
            const approved = monthlyData.map(d => d.approved);
            const rejected = monthlyData.map(d => d.rejected);

            monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total',
                            data: totals,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#6366f1',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        },
                        {
                            label: 'Selesai',
                            data: approved,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Ditolak',
                            data: rejected,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#ef4444',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 2,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: '#e2e8f0'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('approvalChart')) buildApprovalChart('all');
            if (document.getElementById('monthlyTrendChart')) buildMonthlyChart();

            const filterSelect = document.getElementById('approvalFilter');
            if (filterSelect) filterSelect.addEventListener('change', e => buildApprovalChart(e.target.value));
        });
    </script>
    @endpush
</x-app-layout>