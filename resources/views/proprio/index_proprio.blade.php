

@extends('layout')

@section('title', 'Dashboard')



@section('content')
  <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
        <!-- Fond Blanc en Light, Noir en Dark -->
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            
            <header class="flex justify-between items-center mb-8 relative z-50">
                <div>
                    <!-- Texte Noir en Light, Blanc en Dark -->
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Bonjour, {{ Auth::guard('proprio')->user()->nom }} 👋</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                        @if($currentZone)
                            Voici ce qui se passe sur la zone <strong>{{ $currentZone->nom_zone }}</strong> aujourd'hui.
                        @else
                            Voici ce qui se passe sur toutes vos zones aujourd'hui.
                        @endif
                    </p>
                </div>
                
                <!-- Zone Selector (Custom Dropdown avec Icônes FA) -->
                <div class="relative z-[60] w-full md:w-72">
                    <div class="relative">
                        <!-- BOUTON SÉLECTEUR -->
                        <button onclick="toggleDropdown()" class="w-full flex items-center justify-between bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3.5 rounded-xl shadow-lg border border-transparent hover:brightness-110 transition">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-wifi text-white/80"></i>
                                <span id="selected-zone-name" class="font-bold text-sm">{{ $currentZone ? $currentZone->nom_zone : 'Toutes les zones' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-white/80 text-xs"></i>
                        </button>

                        <!-- MENU DÉROULANT -->
                        <div id="zone-dropdown-menu" class="absolute right-0 top-full mt-2 w-full bg-brand-cardLight dark:bg-brand-cardDark rounded-xl shadow-xl p-2 hidden border border-gray-100 dark:border-gray-700 z-[100] animate-fade-in">
                            <!-- OPTION : TOUTES LES ZONES -->
                            <a href="{{ route('proprio.dashboard') }}" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium {{ !$currentZone ? 'text-brand-blue' : 'text-gray-800 dark:text-white' }} flex items-center gap-2 transition">
                                <i class="fas fa-wifi text-gray-400"></i> Toutes les zones
                            </a>

                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>

                            <!-- OPTIONS : LES ZONES -->
                            @foreach($zones as $z)
                                @php
                                    $stockInfo = $zoneStockStatus[$z->id] ?? [
                                        'status' => 'empty',
                                        'icon' => 'fa-times-circle',
                                        'color' => 'text-red-500'
                                    ];
                                @endphp
                                <a href="{{ route('proprio.dashboard', ['zone_id' => $z->id]) }}" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium {{ $currentZone && $currentZone->id == $z->id ? 'text-brand-blue' : 'text-gray-800 dark:text-white' }} flex items-center gap-2 transition">
                                    <i class="fas {{ $stockInfo['icon'] }} {{ $stockInfo['color'] }}"></i>
                                    <span>{{ $z->nom_zone }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </header>

            @if($zones->isEmpty())
                <!-- ══ GUIDED EMPTY STATE ══ -->
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center animate-fade-in">
                    <div class="w-24 h-24 bg-gradient-to-br from-brand-blue/20 to-brand-green/20 rounded-3xl flex items-center justify-center mb-8 shadow-inner">
                        <i class="fas fa-wifi text-4xl text-brand-blue animate-pulse"></i>
                    </div>
                    
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-4">
                        Bienvenue sur WiFiProfit ! 🚀
                    </h3>
                    
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-10 leading-relaxed">
                        Pour commencer à générer des revenus, vous devez d'abord connecter votre équipement MikroTik à notre plateforme.
                    </p>
                    
                    <a href="{{ route('proprio.wifizones') }}" 
                       class="group relative inline-flex items-center gap-3 bg-brand-blue hover:bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-blue-500/20 transition-all hover:-translate-y-1 active:scale-95">
                        <i class="fas fa-plus-circle text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                        <span>Connecter votre premier WiFi Zone à WiFiPay</span>
                    </a>
                    
                    <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl w-full text-left">
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700">
                            <i class="fas fa-bolt text-brand-blue mb-2"></i>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">Synchro Rapide</h4>
                            <p class="text-[10px] text-gray-400">Importez vos forfaits en 1 clic.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700">
                            <i class="fas fa-shield-alt text-brand-green mb-2"></i>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">Paiements Sécurisés</h4>
                            <p class="text-[10px] text-gray-400">Vos fonds sont en sécurité.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700">
                            <i class="fas fa-chart-line text-purple-500 mb-2"></i>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">Stats en Temps Réel</h4>
                            <p class="text-[10px] text-gray-400">Suivez vos ventes en direct.</p>
                        </div>
                    </            @else
                <!-- SECTION 1: KPIs (Synchronized Multi-Period) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    
                    <!-- KPI 1: Ventes (CLICKABLE) -->
                    <div onclick="cycleKpis()" class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-brand-blue/30 transition-all cursor-pointer group active:scale-95">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-brand-green group-hover:scale-110 transition-transform">
                                <i class="fas fa-ticket-alt text-lg"></i>
                            </div>
                            <span id="kpi-sales-badge" class="flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase transition-all">Ventes {{ $kpiData['meta']['year'] }}</span>
                        </div>
                        <h3 id="kpi-sales-value" class="text-3xl font-bold text-gray-800 dark:text-white kpi-animate">{{ number_format($kpiData['sales']['year'], 0, ',', '.') }}</h3>
                        <p id="kpi-sales-label" class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets vendus en {{ $kpiData['meta']['year'] }}</p>
                    </div>

                    <!-- KPI 2: Revenu (CLICKABLE) -->
                    <div onclick="cycleKpis()" class="bg-brand-sidebarLight dark:bg-brand-blue p-5 rounded-3xl shadow-lg text-white relative overflow-hidden group cursor-pointer active:scale-95 transition-all">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-green opacity-20 rounded-full blur-2xl group-hover:opacity-30 transition"></div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white group-hover:rotate-12 transition-transform">
                                <i class="fas fa-money-bill-wave text-lg"></i>
                            </div>
                            <span id="kpi-revenue-badge" class="text-brand-green dark:text-white text-xs font-bold transition-all">{{ $kpiData['meta']['year'] }}</span>
                        </div>
                        <h3 class="text-3xl font-bold text-white relative z-10 kpi-animate">
                            <span id="kpi-revenue-value">{{ number_format($kpiData['revenue']['year'], 0, ',', '.') }}</span> 
                            <span class="text-sm text-blue-200 font-normal">F CFA</span>
                        </h3>
                        <p id="kpi-revenue-label" class="text-xs text-blue-200 font-medium mt-1 relative z-10">Revenu généré (Année)</p>
                    </div>

                    <!-- KPI 3: Stock (STATIC) -->
                    <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                <i class="fas fa-boxes text-lg"></i>
                            </div>
                            <a href="{{ route('proprio.wifizones') }}" class="text-[10px] font-bold text-gray-400 hover:text-brand-blue uppercase">Gérer</a>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stockTotal }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets en stock</p>
                    </div>

                    <!-- KPI 4: Top Vente (CLICKABLE) -->
                    <div onclick="cycleKpis()" class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all cursor-pointer group active:scale-95">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400 group-hover:scale-110 transition-transform">
                                <i class="fas fa-star text-lg"></i>
                            </div>
                            <span id="kpi-top-badge" class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase transition-all">Top {{ $kpiData['meta']['year'] }}</span>
                        </div>
                        <h3 id="kpi-top-value" class="text-xl font-bold text-gray-800 dark:text-white truncate kpi-animate">
                            {{ $kpiData['top']['year']['nom'] }}
                        </h3>
                        <p id="kpi-top-label" class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">
                            {{ $kpiData['top']['year']['total'] }} ventes en {{ $kpiData['meta']['year'] }}
                        </p>
                    </div>
                </div>

                <!-- SECTION 2: Chart & Zones List -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[400px] mb-6">
                    <!-- Chart Area -->
                    <div class="lg:col-span-2 bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 id="chart-title" class="font-bold text-lg text-gray-800 dark:text-white kpi-animate">Ventes de {{ $kpiData['meta']['year'] }} 📈</h3>
                                <p id="chart-subtitle" class="text-xs text-gray-400 mt-0.5 transition-all">Volume total de tickets vendus</p>
                            </div>
                            
                            <!-- Metric Selector (Pill Style) -->
                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-slate-700/50 p-1 rounded-xl border border-gray-200 dark:border-slate-600">
                                <button onclick="switchChartMetric('volume')" id="btn-metric-volume" 
                                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all bg-white dark:bg-brand-blue shadow-sm text-brand-blue dark:text-white">
                                    Tickets
                                </button>
                                <button onclick="switchChartMetric('revenue')" id="btn-metric-revenue" 
                                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-brand-blue">
                                    FCFA
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex-1 w-full relative mt-4 min-h-0 min-w-0" style="height: 320px;">
                            <div id="sales-chart-container" class="w-full h-full"></div>
                        </div>
                    </div>

                    <div class="bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Stock par Zone</h3>
                            <a href="{{ route('proprio.forfait_ticket') }}#list" class="text-xs font-bold text-brand-blue hover:underline">Gérer</a>
                        </div>

                        <div class="flex-1 overflow-y-auto space-y-3 no-scrollbar pr-2">
                            @forelse($zonesStocks as $zs)
                                <a href="{{ route('proprio.dashboard', ['zone_id' => $zs->id]) }}" class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm text-gray-400">
                                            <i class="fas fa-wifi text-xs {{ $zs->tickets_count < 10 ? 'text-orange-500' : 'text-brand-blue' }}"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $zs->nom_zone }}</p>
                                            <p class="text-[10px] {{ $zs->tickets_count < 10 ? 'text-orange-500' : 'text-brand-green' }} font-medium flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 {{ $zs->tickets_count < 10 ? 'bg-orange-500 animate-pulse' : 'bg-green-500' }} rounded-full"></span> 
                                                {{ $zs->tickets_count < 10 ? 'Stock Critique' : 'Stock Satisfaisant' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-gray-800 dark:text-white">{{ $zs->tickets_count }}</span>
                                        <span class="text-[10px] text-gray-400">tickets</span>
                                    </div>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-4">Aucune zone WiFi</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>

    @endsection

@section('scripts')
<style>
    .kpi-animate {
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;
    }
    .kpi-update {
        transform: scale(0.95);
        opacity: 0.5;
    }
</style>


<script>
    // Injection des données KPI calculées côté serveur
    const kpiData = @json($kpiData);
    let currentPeriod = 'year'; // Ordre: year -> month -> day -> year
    let currentMetric = 'volume'; // 'volume' (Tickets) ou 'revenue' (FCFA)
    let chart; // Instance ApexCharts

    function cycleKpis() {
        // Cycle: year -> month -> day
        const periods = ['year', 'month', 'day'];
        let idx = periods.indexOf(currentPeriod);
        currentPeriod = periods[(idx + 1) % periods.length];
        
        // Effet visuel immédiat
        const animElements = document.querySelectorAll('.kpi-animate');
        animElements.forEach(el => el.classList.add('kpi-update'));
        
        setTimeout(() => {
            updateKpiUI();
            updateChart();
            animElements.forEach(el => el.classList.remove('kpi-update'));
        }, 150);
    }

    function switchChartMetric(metric) {
        currentMetric = metric;
        
        // Mise à jour des boutons
        const btnVol = document.getElementById('btn-metric-volume');
        const btnRev = document.getElementById('btn-metric-revenue');
        
        if (metric === 'volume') {
            btnVol.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all bg-white dark:bg-brand-blue shadow-sm text-brand-blue dark:text-white";
            btnRev.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-brand-blue";
        } else {
            btnRev.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all bg-white dark:bg-brand-blue shadow-sm text-brand-blue dark:text-white";
            btnVol.className = "px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-brand-blue";
        }

        updateChart();
    }

    function updateKpiUI() {
        const meta = kpiData.meta[currentPeriod];
        const periodLabel = currentPeriod === 'year' ? 'Année' : (currentPeriod === 'month' ? 'Mois' : 'Jour');
        
        // 1. Ventes
        const salesBadgeText = currentPeriod === 'day' ? 'Ventes Aujourd\'hui' : 'Ventes ' + meta;
        document.getElementById('kpi-sales-value').innerText = kpiData.sales[currentPeriod].toLocaleString();
        document.getElementById('kpi-sales-badge').innerText = salesBadgeText;
        document.getElementById('kpi-sales-label').innerText = 'Tickets vendus (' + periodLabel + ')';

        // 2. Revenu
        const revenueBadgeText = meta;
        document.getElementById('kpi-revenue-value').innerText = kpiData.revenue[currentPeriod].toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        document.getElementById('kpi-revenue-badge').innerText = revenueBadgeText;
        document.getElementById('kpi-revenue-label').innerText = 'Revenu généré (' + periodLabel + ')';

        // 4. Top Vente
        const topBadgeText = 'Top ' + meta;
        document.getElementById('kpi-top-value').innerText = kpiData.top[currentPeriod].nom;
        document.getElementById('kpi-top-badge').innerText = topBadgeText;
        document.getElementById('kpi-top-label').innerText = kpiData.top[currentPeriod].total + ' ventes (' + periodLabel + ')';
    }

    function initChart() {
        const isDark = document.documentElement.classList.contains('dark');
        const options = {
            series: [{
                name: 'Tickets',
                data: kpiData.charts[currentPeriod][currentMetric]
            }],
            chart: {
                id: 'sales-chart-proprio',
                type: 'area',
                height: '100%',
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: { enabled: true, delay: 150 },
                    dynamicAnimation: { enabled: true, speed: 350 }
                },
                fontFamily: 'inherit',
                sparkline: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: ['#0EA5E9']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            markers: {
                size: 0,
                hover: { size: 5, strokeWidth: 2, strokeColors: '#fff' }
            },
            xaxis: {
                categories: kpiData.charts[currentPeriod].labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    offsetY: 0
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '10px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        if (currentMetric === 'revenue') {
                            return val >= 1000 ? (val/1000).toFixed(0) + 'k' : val;
                        }
                        return Math.floor(val);
                    }
                }
            },
            grid: {
                borderColor: isDark ? '#334155' : '#f1f5f9',
                strokeDashArray: 4,
                padding: { top: 0, right: 10, bottom: 0, left: 10 }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                x: { show: true },
                y: {
                    formatter: function(val) {
                        return currentMetric === 'revenue' 
                            ? val.toLocaleString() + ' FCFA' 
                            : val + ' tickets';
                    }
                }
            },
            colors: ['#0EA5E9']
        };

        chart = new ApexCharts(document.querySelector("#sales-chart-container"), options);
        chart.render();
    }

    function updateChart() {
        if (!chart) return;

        const chartData = kpiData.charts[currentPeriod];
        const data = chartData[currentMetric];
        const labels = chartData.labels;
        const meta = kpiData.meta[currentPeriod];
        
        // Titre et Sous-titre
        const titlePrefix = currentMetric === 'volume' ? 'Ventes' : 'Revenu';
        document.getElementById('chart-title').innerText = titlePrefix + ' de ' + meta + ' 📈';
        document.getElementById('chart-subtitle').innerText = currentMetric === 'volume' 
            ? 'Volume total de tickets vendus' 
            : 'Chiffre d\'affaires total généré';

        // Mise à jour via l'API ApexCharts (en une seule passe pour éviter les doublons/ bugs d'échelle)
        chart.updateOptions({
            series: [{
                name: currentMetric === 'volume' ? 'Tickets' : 'Revenu',
                data: data
            }],
            xaxis: { 
                categories: labels 
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        if (currentMetric === 'revenue') {
                            return val >= 1000 ? (val/1000).toFixed(0) + 'k' : val;
                        }
                        return Math.floor(val);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return currentMetric === 'revenue' 
                            ? val.toLocaleString() + ' FCFA' 
                            : val + ' tickets';
                    }
                }
            }
        }, true, true); // redrawPaths=true, animate=true
    }

    function toggleDropdown() {
        const menu = document.getElementById('zone-dropdown-menu');
        menu.classList.toggle('hidden');
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', () => {
        initChart();
    });

    // Fermer le menu si on clique ailleurs
    window.addEventListener('click', function(e) {
        const menu = document.getElementById('zone-dropdown-menu');
        const button = e.target.closest('button');
        
        if (menu && !menu.contains(e.target) && (!button || !button.onclick || !button.onclick.toString().includes('toggleDropdown'))) {
            menu.classList.add('hidden');
        }
    });
</script>
@endsection