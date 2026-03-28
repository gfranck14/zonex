

@extends('layout')

@section('title', 'Dashboard')



@section('content')
  <main class="h-full w-full relative">
        <!-- Fond Blanc en Light, Noir en Dark -->
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
            
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
                            <a href="{{ route('dashboard') }}" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium {{ !$currentZone ? 'text-brand-blue' : 'text-gray-800 dark:text-white' }} flex items-center gap-2 transition">
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
                                <a href="{{ route('dashboard', ['zone_id' => $z->id]) }}" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium {{ $currentZone && $currentZone->id == $z->id ? 'text-brand-blue' : 'text-gray-800 dark:text-white' }} flex items-center gap-2 transition">
                                    <i class="fas {{ $stockInfo['icon'] }} {{ $stockInfo['color'] }}"></i>
                                    <span>{{ $z->nom_zone }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </header>

            <!-- SECTION 1: KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                
                <!-- KPI 1 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-brand-blue/30 transition-all cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-brand-green">
                            <i class="fas fa-ticket-alt text-lg"></i>
                        </div>
                        <span class="flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Ventes Jour</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $ticketsActifsCount }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets vendus aujourd'hui</p>
                </div>

                <!-- KPI 2 (Dark Accent) -->
                <div class="bg-brand-sidebarLight dark:bg-brand-blue p-5 rounded-3xl shadow-lg text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-green opacity-20 rounded-full blur-2xl group-hover:opacity-30 transition"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white">
                            <i class="fas fa-money-bill-wave text-lg"></i>
                        </div>
                        <span class="text-brand-green dark:text-white text-xs font-bold">Aujourd'hui</span>
                    </div>
                    <h3 class="text-3xl font-bold text-white relative z-10">{{ number_format($revenuJour, 0, ',', '.') }} <span class="text-sm text-blue-200 font-normal">F CFA</span></h3>
                    <p class="text-xs text-blue-200 font-medium mt-1 relative z-10">Revenu généré</p>
                </div>

                <!-- KPI 3 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <i class="fas fa-boxes text-lg"></i>
                        </div>
                        <a href="{{ route('wifizones') }}" class="text-[10px] font-bold text-gray-400 hover:text-brand-blue uppercase">Gérer</a>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stockTotal }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets en stock</p>
                </div>

                <!-- KPI 4 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                            <i class="fas fa-star text-lg"></i>
                        </div>
                        <span class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase">Top Vente</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white truncate">
                        {{ $topForfait ? $topForfait->nom : 'N/A' }}
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">
                        {{ $topForfait ? $topForfait->total . ' ventes aujourd\'hui' : 'Aucune vente' }}
                    </p>
                </div>
            </div>

            <!-- SECTION 2: Chart & Zones List -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-80 mb-6">
                <!-- Chart Area -->
                <div class="lg:col-span-2 bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Ventes de la semaine 📈</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Volume total de tickets vendus</p>
                        </div>
                    </div>
                    
                    <div class="flex-1 w-full relative flex items-end px-2 pb-2 mt-4">
                        @php
                            $maxAmount = collect($salesData)->max('amount') ?: 1000;
                            // Normaliser pour SVG (hauteur 50)
                            $points = "";
                            foreach($salesData as $index => $data) {
                                $x = ($index / 6) * 100;
                                $y = 50 - (($data['amount'] / $maxAmount) * 40); // Garder marge en haut
                                $points .= ($index === 0 ? "M" : " L") . "$x,$y";
                            }
                        @endphp
                        
                        <!-- Grille -->
                        <div class="absolute inset-0 flex flex-col justify-between text-[10px] text-gray-300 dark:text-slate-600 pointer-events-none pb-6 pr-2">
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full flex justify-between">
                                <span>{{ number_format($maxAmount, 0) }}</span> <span></span>
                            </div>
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full flex justify-between">
                                <span>{{ number_format($maxAmount / 2, 0) }}</span> <span></span>
                            </div>
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full flex justify-between">
                                <span>0</span> <span></span>
                            </div>
                        </div>
                        
                        <!-- Courbe Dynamique -->
                        <svg class="w-full h-full overflow-visible z-10" viewBox="0 0 100 50" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="salesGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#0EA5E9" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="#0EA5E9" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path d="{{ $points }} L100,50 L0,50 Z" fill="url(#salesGradient)" />
                            <path d="{{ $points }}" fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                            
                            @foreach($salesData as $index => $data)
                                @php
                                    $cx = ($index / 6) * 100;
                                    $cy = 50 - (($data['amount'] / $maxAmount) * 40);
                                @endphp
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="1.5" fill="#0EA5E9" stroke="white" stroke-width="0.5" />
                            @endforeach
                        </svg>
                        
                        <div class="absolute bottom-0 left-0 w-full flex justify-between text-[10px] text-gray-400 dark:text-gray-500 px-1">
                            @foreach($salesData as $data)
                                <span>{{ $data['day'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Stock par Zone</h3>
                        <a href="{{ route('forfait_ticket') }}#list" class="text-xs font-bold text-brand-blue hover:underline">Gérer</a>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-3 no-scrollbar pr-2">
                        @forelse($zonesStocks as $zs)
                            <a href="{{ route('dashboard', ['zone_id' => $zs->id]) }}" class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm text-gray-400">
                                        <i class="fas fa-wifi text-xs {{ $zs->tickets_count < 10 ? 'text-orange-500' : 'text-brand-blue' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $zs->nom_zone }}</p>
                                        <p class="text-[10px] {{ $zs->tickets_count < 10 ? 'text-orange-500' : 'text-brand-green' }} font-medium flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 {{ $zs->tickets_count < 10 ? 'bg-orange-500 animate-pulse' : 'bg-green-500' }} rounded-full"></span> 
                                            {{ $zs->tickets_count < 10 ? 'Stock Critique' : 'En ligne' }}
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

        </div>
    </main>

    @endsection

@section('scripts')
<script>
    function toggleDropdown() {
        const menu = document.getElementById('zone-dropdown-menu');
        menu.classList.toggle('hidden');
    }

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