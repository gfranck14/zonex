@extends('layout')
@section('title', 'Paiements')

@section('content')
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
            <div id="header-container"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-fade-in">
                <div class="bg-brand-sidebarLight dark:bg-slate-800 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue opacity-10 rounded-full blur-3xl -translate-y-10 translate-x-20"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div></div>
                        </div>
                        <!-- Zone Selector -->
                        <div class="mb-4">
                            <form method="GET" action="{{ route('proprio.paiements') }}">
                                <select name="filter_zone" onchange="this.form.submit()" class="bg-white/10 dark:bg-slate-700 text-white border border-white/20 rounded-xl py-2 px-4 text-xs font-bold outline-none cursor-pointer hover:bg-white/20 transition w-full">
                                    <option value="">Toutes les zones</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ $filterZone == $zone->id ? 'selected' : '' }}>{{ $zone->nom_zone }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="flex justify-between items-end mb-2">
                            <div><p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">{{ $filterZone ? 'Recettes de la Zone' : 'Solde Disponible' }}</p><h3 id="solde-display" class="text-5xl font-bold tracking-tight text-white">{{ number_format($balance ?? 0, 0, ',', ' ') }} <span class="text-2xl text-brand-blue font-normal">F</span></h3></div>
                            <div class="bg-white/10 p-3 rounded-xl flex items-center justify-center"><i class="fas fa-wallet text-xl text-white"></i></div>
                        </div>
                        <p class="text-xs text-gray-400 mb-4">{{ $filterZone ? 'Total brut généré par cette seule zone' : 'Solde total de toutes vos zones' }}</p>

                        <!-- KPI Revenus par zone — Bento Grid -->
                        @if(!$filterZone && isset($zoneRevenues) && count($zoneRevenues) > 0)
                        @php
                            $sorted = collect($zoneRevenues)->sortByDesc('revenue')->values()->all();
                            $count = count($sorted);
                            $maxRevenue = $sorted[0]['revenue'] ?? 1;
                            $colors = [
                                ['from-blue-500/20', 'to-cyan-500/10', 'text-cyan-400', 'border-cyan-500/20'],
                                ['from-purple-500/20', 'to-pink-500/10', 'text-purple-400', 'border-purple-500/20'],
                                ['from-emerald-500/20', 'to-teal-500/10', 'text-emerald-400', 'border-emerald-500/20'],
                                ['from-amber-500/20', 'to-orange-500/10', 'text-amber-400', 'border-amber-500/20'],
                                ['from-rose-500/20', 'to-red-500/10', 'text-rose-400', 'border-rose-500/20'],
                                ['from-indigo-500/20', 'to-violet-500/10', 'text-indigo-400', 'border-indigo-500/20'],
                            ];
                            // Single row up to 5, mosaic 2 rows for 6+
                            $gridCols = match(true) {
                                $count <= 2 => 'grid-cols-2',
                                $count <= 3 => 'grid-cols-3',
                                $count <= 5 => 'grid-cols-' . $count,
                                default => 'grid-cols-3 sm:grid-cols-4',
                            };
                        @endphp
                        <div class="mt-4 pt-4 border-t border-white/10 relative z-20">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Revenus par zone</p>
                            <div class="grid gap-2 {{ $gridCols }}">
                                @foreach($sorted as $i => $zr)
                                @php
                                    $c = $colors[$i % count($colors)];
                                    $pct = $maxRevenue > 0 ? round(($zr['revenue'] / $maxRevenue) * 100) : 0;
                                @endphp
                                <div class="bg-gradient-to-br {{ $c[0] }} {{ $c[1] }} border {{ $c[3] }} rounded-xl p-2.5 flex flex-col justify-between hover:scale-[1.03] transition-transform cursor-default">
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <i class="fas fa-wifi {{ $c[2] }} text-[10px]"></i>
                                        <span class="text-gray-300 text-[10px] font-semibold truncate" title="{{ $zr['name'] }}">{{ $zr['name'] }}</span>
                                    </div>
                                    <p class="text-white font-bold text-sm leading-tight">{{ number_format($zr['revenue'], 0, ',', ' ') }} <span class="text-[9px] font-normal {{ $c[2] }}">F</span></p>
                                    <div class="mt-1.5 h-0.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r {{ $c[0] }} {{ $c[1] }} opacity-80" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <!-- Widget Retrait Rapide -->
                <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Demander un retrait</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Virement vers Mobile Money</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-brand-blue">
                            <i class="fas fa-arrow-down text-lg"></i>
                        </div>
                    </div>

                    <!-- Zone de sélection -->
                    <div class="space-y-4 mb-8">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Montant à retirer</label>
                            <div class="relative">
                                <select id="withdraw-select" onchange="updateWithdrawalFees()" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 pr-8 text-sm font-bold appearance-none focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer transition-all">
                                    <option value="10000">10 000 F</option>
                                    <option value="30000">30 000 F</option>
                                    <option value="50000">50 000 F</option>
                                    <option value="100000">100 000 F</option>
                                    <option value="150000">150 000 F</option>
                                    <option value="300000">300 000 F</option>
                                    <option value="500000">500 000 F</option>
                                </select>
                                <!-- Chevron custom -->
                                <div class="absolute right-3 top-0 bottom-0 flex items-center pointer-events-none text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Note Warning masquée par défaut -->
                        <div id="high-amount-note" class="hidden bg-orange-50 dark:bg-orange-900/20 p-2 rounded-lg flex items-center gap-2 border border-orange-100 dark:border-orange-800">
                            <i class="fas fa-exclamation-triangle text-xs text-orange-500 flex-shrink-0"></i>
                            <p class="text-[10px] text-orange-600 dark:text-orange-400 font-medium leading-tight">Délai de traitement : 24h pour ce montant.</p>
                        </div>

                        <!-- Frais de transaction et Commission -->
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Frais de transaction</span>
                                <span id="transaction-fees" class="text-sm font-bold text-gray-700 dark:text-gray-200">150 F</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Commission (10%)</span>
                                <span id="commission-amount" class="text-sm font-bold text-gray-700 dark:text-gray-200">1 000 F</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-slate-600 pt-2 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300">Montant net</span>
                                    <span id="net-amount" class="text-sm font-bold text-brand-blue">8 850 F</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Action -->
                    <button onclick="openWithdrawalModal()" class="w-full mt-6 bg-brand-sidebarLight dark:bg-brand-blue text-white py-4 rounded-2xl text-sm font-bold hover:brightness-110 transition shadow-lg flex justify-center items-center gap-2">
                        Configurer le virement
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden animate-fade-in">
                <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex flex-wrap justify-between items-center gap-4">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Historique de paiements</h3>
                    <form method="GET" action="{{ route('proprio.paiements') }}" class="flex gap-3">
                        <select name="filter_zone" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                            <option value="">Toutes les Zones</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('filter_zone') == $zone->id ? 'selected' : '' }}>{{ $zone->nom_zone }}</option>
                            @endforeach
                        </select>
                        <select name="filter_status" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                            <option value="">Tous statuts</option>
                            <option value="reussi" {{ request('filter_status') == 'reussi' ? 'selected' : '' }}>Succès</option>
                            <option value="en_attente" {{ request('filter_status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="echoue" {{ request('filter_status') == 'echoue' ? 'selected' : '' }}>Échoué</option>
                            <option value="annule" {{ request('filter_status') == 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        <input type="date" name="filter_date" value="{{ request('filter_date') }}" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                        @if(request('filter_zone') || request('filter_status') || request('filter_date'))
                            <a href="{{ route('proprio.paiements') }}" class="p-2 text-red-500 flex items-center hover:bg-red-50 rounded-xl transition">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider"><tr><th class="p-5">ID Transaction</th><th class="p-5">Date & Heure</th><th class="p-5">Client</th><th class="p-5">Zone</th><th class="p-5">Forfait</th><th class="p-5">Montant</th><th class="p-5">Statut</th></tr></thead>
                        <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                            @forelse($paiements as $p)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition cursor-pointer">
                                    <td class="p-5 font-mono text-gray-500 dark:text-gray-300 text-xs">
                                        {{ $p->fedapay_transaction_id ?? '-' }}
                                    </td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ $p->created_at->format('d M, H:i') }}</td>
                                    <td class="p-5 font-medium text-gray-800 dark:text-white">
                                        {{ $p->client->nom_complet ?? 'Inconnu' }}
                                    </td>
                                    <td class="p-5">
                                        <span class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 px-2 py-1 rounded-lg text-[10px] font-bold">
                                            {{ $p->forfait->wifizone->nom_zone ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-lg text-[10px] font-bold">
                                            {{ $p->forfait->nom ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-5 font-bold text-gray-800 dark:text-white">{{ number_format($p->montant, 0, ',', ' ') }} F</td>
                                    <td class="p-5">
                                        @if($p->statut === 'reussi')
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold">SUCCÈS</span>
                                        @elseif($p->statut === 'en_attente')
                                            <span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">⏳ EN ATTENTE</span>
                                        @elseif($p->statut === 'annule')
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-[10px] font-bold">ANNULÉ</span>
                                        @else
                                            <span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-[10px] font-bold">ÉCHEC</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-10 text-center text-gray-400">Aucun achat trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            
                            <!-- GAUCHE : Info Affichage (Achats) -->
                            <div class="flex items-center gap-2 order-2 md:order-1">
                                <span class="text-xs text-gray-400">Affichage de</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $paiements->firstItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">à</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $paiements->lastItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">sur</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $paiements->total() }}</span>
                                <span class="text-xs text-gray-400">achats</span>
                            </div>
                            
                            <!-- MILIEU : Sélecteur lignes -->
                            <div class="flex items-center gap-2 order-3 md:order-2">
                                <span class="text-xs text-gray-400">Afficher</span>
                                <form method="GET" action="{{ route('proprio.paiements') }}" class="inline-block">
                                    @if(request('filter_zone')) <input type="hidden" name="filter_zone" value="{{ request('filter_zone') }}"> @endif
                                    @if(request('filter_status')) <input type="hidden" name="filter_status" value="{{ request('filter_status') }}"> @endif
                                    @if(request('filter_date')) <input type="hidden" name="filter_date" value="{{ request('filter_date') }}"> @endif
                                    <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold p-1 px-2 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 lignes</option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 lignes</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 lignes</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 lignes</option>
                                    </select>
                                </form>
                            </div>
                            
                            <!-- DROITE : Pagination Séquentielle -->
                            <div class="flex items-center gap-2 order-1 md:order-3">
                                <!-- Bouton Précédent -->
                                @if ($paiements->onFirstPage())
                                    <button disabled class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-300 cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                @else
                                    <a href="{{ $paiements->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif
                                
                                <!-- NUMÉROS DE PAGES -->
                                <div class="flex gap-1">
                                    @foreach ($paiements->linkCollection() as $link)
                                        @if (!str_contains($link['label'], 'Previous') && !str_contains($link['label'], 'Next'))
                                            @if ($link['active'])
                                                <button class="px-3 py-1 rounded-lg bg-brand-blue text-white text-xs font-bold shadow-sm">{{ $link['label'] }}</button>
                                            @elseif ($link['label'] === '...')
                                                <span class="px-2 text-gray-400 text-xs py-1">...</span>
                                            @else
                                                <a href="{{ $link['url'] }}" class="px-3 py-1 rounded-lg border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-400 text-xs font-bold hover:bg-gray-100 dark:hover:bg-slate-700 transition">{{ $link['label'] }}</a>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Bouton Suivant -->
                                @if ($paiements->hasMorePages())
                                    <a href="{{ $paiements->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <button disabled class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-300 cursor-not-allowed">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- MODALE RETRAIT (WIZARD) -->
    <div id="withdrawal-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeWithdrawalModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark modal-bg-light w-full max-w-md rounded-3xl p-6 relative z-10 shadow-2xl border border-gray-100 dark:border-slate-700">
            <!-- ÉTAPE 1 : SAISIE (Refaite avec style moderne) -->
            <div id="withdrawal-step-1" class="space-y-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">Détails du compte</h3>
                    <button onclick="closeWithdrawalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white"><i class="fas fa-times w-6 h-6"></i></button>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Où souhaitez-vous recevoir les <span id="modal-amount-display" class="font-bold text-brand-blue">--- F</span> ?</p>

                <!-- 1. Réseau -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase mb-2">Opérateur</label>
                    <div class="relative">
                        <select id="withdrawal-network" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 pr-8 text-sm font-bold appearance-none focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer transition-all">
                            <option value="mtn">MTN</option>
                            <option value="moov">Moov</option>
                            <option value="celtiis">Celtiis</option>
                        </select>
                        <!-- Chevron custom -->
                        <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down w-4 h-4"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 2. Téléphone (Floating) -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Numéro de téléphone</label>
                    <div id="withdrawal-phone-container" class="flex relative group">
                        <!-- Zone Prefix -->
                        <button type="button" onclick="toggleCountryMenu('withdrawal')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                            <img id="withdrawal-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="BJ">
                            <span id="withdrawal-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                        </button>

                        <!-- Menu Déroulant -->
                        <div id="withdrawal-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-brand-cardDark border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                            <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                <li onclick="selectCountry('bj', '+229', 'withdrawal')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                </li>
                                <li onclick="selectCountry('tg', '+228', 'withdrawal')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                </li>
                                <li onclick="selectCountry('ci', '+225', 'withdrawal')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/ci.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Côte d'Ivoire (+225)</span>
                                </li>
                                <li onclick="selectCountry('sn', '+221', 'withdrawal')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition-colors group">
                                    <img src="https://flagcdn.com/w40/sn.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Sénégal (+221)</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Input -->
                        <div class="relative flex-1">
                            <input 
                                type="tel" 
                                placeholder="XX XX XX XX" 
                                id="withdrawal-phone"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition placeholder-gray-400" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            >
                            <i class="fas fa-mobile-alt absolute right-4 top-0 bottom-0 flex items-center text-gray-400"></i>
                        </div>
                        <input type="hidden" id="withdrawal-phone-code" value="+229">
                    </div>
                </div>
                
                <!-- 3. Nom (Floating) -->
                <div class="input-floating-group">
                    <input type="text" id="withdrawal-name" placeholder=" " class="input-floating">
                    <label class="floating-label">Nom du Bénéficiaire</label>
                 <!-- Note informative -->
                    <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium mt-2 mb-4">
                    Veuillez saisir le nom exact qui apparaîtra lors du dépôt.
                    </p>
                </div>
                
               
                
                <button onclick="proceedToWithdrawalStep2()" class="w-full py-4 bg-brand-sidebarLight dark:bg-brand-blue text-white rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg mt-4">
                    Vérifier et Confirmer
                </button>
            </div>
            
            <!-- ÉTAPE 2 : RÉCAPITULATIF -->
            <div id="withdrawal-step-2" class="hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirmation</h3>
                    <button onclick="closeWithdrawalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times w-5 h-5"></i>
                    </button>
                </div>
                
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-4 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Montant</span>
                            <span id="withdrawal-amount-display" class="font-bold text-gray-800 dark:text-white">---</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Bénéficiaire</span>
                            <span id="withdrawal-name-display" class="font-bold text-gray-800 dark:text-white">---</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Numéro</span>
                            <span id="withdrawal-phone-display" class="font-bold text-gray-800 dark:text-white">---</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Réseau</span>
                            <span id="withdrawal-network-display" class="font-bold text-gray-800 dark:text-white">---</span>
                        </div>
                    </div>
                </div>
                
                <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Voulez-vous confirmer le retrait de <span id="withdrawal-amount-highlight" class="font-bold text-brand-blue">---</span> vers <span id="withdrawal-name-highlight" class="font-bold text-brand-blue">---</span> ?
                </p>
                
                <div class="flex gap-3">
                    <button onclick="backToWithdrawalStep1()" class="flex-1 py-3 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        Retour
                    </button>
                    <button onclick="confirmWithdrawal()" class="flex-1 py-3 bg-brand-green text-white rounded-xl font-bold hover:bg-green-600 transition">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODALE HISTORIQUE RETRAITS -->
    <div id="withdrawal-history-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeWithdrawalHistoryModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark modal-bg-light w-full max-w-4xl rounded-3xl p-6 relative z-10 shadow-2xl border border-gray-100 dark:border-slate-700 max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Journal des Retraits</h3>
                <button onclick="closeWithdrawalHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times w-6 h-6"></i>
                </button>
            </div>
            
            <div class="relative mb-6">
                <input type="text" id="withdrawal-search" placeholder="Rechercher (Réf, Montant, Bénéficiaire...)" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl py-3 pl-10 pr-4 text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition dark:text-white">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-4 text-left">Date</th>
                            <th class="p-4 text-left">Montant</th>
                            <th class="p-4 text-left">ID Transaction</th>
                            <th class="p-4 text-left">Référence de paiement</th>
                            <th class="p-4 text-left">Bénéficiaire</th>
                            <th class="p-4 text-left">Destination</th>
                            <th class="p-4 text-left">Statut</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="withdrawal-history-tbody" class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-800 rounded-2xl">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total des retraits cette période</span>
                    <span id="total-withdrawals-display" class="text-lg font-bold text-red-600">-0 F</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALE CONFIRMATION ANNULATION -->
    <div id="cancel-withdrawal-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeCancelWithdrawalModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-sm rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700 animate-scale-in">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Annuler le retrait ?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Cette action annulera la demande de versement. Les fonds seront recrédités sur votre solde disponible.</p>
            
            <div class="flex gap-3">
                <button onclick="closeCancelWithdrawalModal()" class="flex-1 py-3 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Non, garder</button>
                <button onclick="executeCancelWithdrawal()" class="flex-1 py-3 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition shadow-lg">Oui, annuler</button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Configuration AJAX avec Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    /**
     * Fonctions de Gestion de la Modale de Retrait (Wizard)
     */
    function openWithdrawalModal() {
        const withdrawSelect = document.getElementById('withdraw-select');
        const amount = parseInt(withdrawSelect.value);
        
        // Mise à jour des affichages du montant partout
        document.getElementById('modal-amount-display').innerText = amount.toLocaleString('fr-FR') + ' F';
        document.getElementById('withdrawal-amount-display').innerText = amount.toLocaleString('fr-FR') + ' F';
        document.getElementById('withdrawal-amount-highlight').innerText = amount.toLocaleString('fr-FR') + ' F';
        
        // Affichage
        document.getElementById('withdrawal-modal').classList.remove('hidden');
    }

    function closeWithdrawalModal() {
        document.getElementById('withdrawal-modal').classList.add('hidden');
        backToWithdrawalStep1();
    }

    function proceedToWithdrawalStep2() {
        const nameInput = document.getElementById('withdrawal-name');
        const phoneInput = document.getElementById('withdrawal-phone');
        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        
        if(!name || !phone) {
            showToast('Veuillez remplir toutes les informations bénéficiaire', 'error');
            return;
        }
        
        // Mise à jour du récapitulatif
        document.getElementById('withdrawal-name-display').innerText = name;
        document.getElementById('withdrawal-name-highlight').innerText = name;
        document.getElementById('withdrawal-phone-display').innerText = document.getElementById('withdrawal-code').innerText + ' ' + phone;
        document.getElementById('withdrawal-network-display').innerText = document.getElementById('withdrawal-network').value.toUpperCase();
        
        // Animation passage étape
        document.getElementById('withdrawal-step-1').classList.add('hidden');
        document.getElementById('withdrawal-step-2').classList.remove('hidden');
    }

    function backToWithdrawalStep1() {
        document.getElementById('withdrawal-step-2').classList.add('hidden');
        document.getElementById('withdrawal-step-1').classList.remove('hidden');
    }

    // Gestion des pays
    function toggleCountryMenu(prefix) {
        const menu = document.getElementById(`${prefix}-country-menu`);
        if(menu) menu.classList.toggle('hidden');
    }

    function selectCountry(iso, code, prefix) {
        document.getElementById(`${prefix}-flag`).src = `https://flagcdn.com/w40/${iso}.png`;
        document.getElementById(`${prefix}-code`).innerText = code;
        document.getElementById(`${prefix}-phone-code`).value = code;
        toggleCountryMenu(prefix);
    }

    // Fermeture du menu si clic ailleurs
    window.addEventListener('click', function(e) {
        const menu = document.getElementById('withdrawal-country-menu');
        const btn = document.querySelector('button[onclick*="toggleCountryMenu"]');
        if (menu && !menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    /**
     * Calcule les frais de transaction selon la grille
     * 0 – 10 000 XOF: 150 XOF
     * 10 001 – 50 000 XOF: 300 XOF
     * 50 001 – 150 000 XOF: 800 XOF
     * 150 001 – 500 000 XOF: 2 000 XOF
     * 500 001 XOF+: 2 500 XOF
     */
    function calculateTransactionFees(amount) {
        if (amount <= 10000) return 150;
        if (amount <= 50000) return 300;
        if (amount <= 150000) return 800;
        if (amount <= 500000) return 2000;
        return 2500;
    }

    /**
     * Calcule la commission (10% du montant)
     */
    function calculateCommission(amount) {
        return Math.round(amount * 0.10);
    }

    /**
     * Met à jour l'affichage des frais et commissions
     */
    function updateWithdrawalFees() {
        const withdrawSelect = document.getElementById('withdraw-select');
        const amount = parseInt(withdrawSelect.value);
        
        const fedapayFee = calculateTransactionFees(amount);
        const commission = calculateCommission(amount);
        const netAmount = amount - fedapayFee - commission;
        
        document.getElementById('transaction-fees').textContent = fedapayFee.toLocaleString('fr-FR') + ' F';
        document.getElementById('commission-amount').textContent = commission.toLocaleString('fr-FR') + ' F';
        document.getElementById('net-amount').textContent = netAmount.toLocaleString('fr-FR') + ' F';
        
        // Afficher/masquer le warning pour gros montants
        const highAmountNote = document.getElementById('high-amount-note');
        if (amount >= 100000) {
            highAmountNote.classList.remove('hidden');
        } else {
            highAmountNote.classList.add('hidden');
        }
    }

    /**
     * Initialise les frais au chargement de la page
     */
    document.addEventListener('DOMContentLoaded', function() {
        updateWithdrawalFees();
    });
    
    /**
     * Surcharge de la fonction de confirmation de retrait (Phase 3)
     */
    async function confirmWithdrawal() {
        const name = document.getElementById('withdrawal-name').value.trim();
        const phoneCode = document.getElementById('withdrawal-phone-code').value;
        const phoneNumber = document.getElementById('withdrawal-phone').value.trim();
        const phone = phoneCode + ' ' + phoneNumber;
        const withdrawSelect = document.getElementById('withdraw-select');
        const networkSelect = document.getElementById('withdrawal-network');
        
        const amount = parseInt(withdrawSelect.value);
        const operator = networkSelect.value;
        const fedapayFee = calculateTransactionFees(amount);
        const ccorpFee = calculateCommission(amount);

        // Overlay de chargement ou désactivation bouton
        const confirmBtn = event.target;
        const originalText = confirmBtn.innerText;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Traitement...';

        try {
            const response = await fetch("{{ route('proprio.api.withdrawals.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    operator: operator,
                    phone_number: phone,
                    beneficiary_name: name,
                    fedapay_fee: fedapayFee,
                    ccorp_fee: ccorpFee
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                closeWithdrawalModal();
                
                // Rafraîchir la page pour mettre à jour les soldes et l'historique
                // Ou mettre à jour dynamiquement via AJAX
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Erreur lors du retrait', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Erreur réseau ou serveur', 'error');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerText = originalText;
        }
    }

    /**
     * Surcharge de l'ouverture de l'historique pour charger les données réelles
     */
    async function openWithdrawalHistoryModal() {
        document.getElementById('withdrawal-history-modal').classList.remove('hidden');
        loadWithdrawalHistory();
        
        // Reset search
        const searchInput = document.getElementById('withdrawal-search');
        if(searchInput) {
            searchInput.value = '';
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#withdrawal-history-tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        }
    }

    /**
     * Chargement AJAX de l'historique des retraits
     */
    async function loadWithdrawalHistory() {
        const tbody = document.getElementById('withdrawal-history-tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="p-10 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i> Chargement...</td></tr>';

        try {
            const response = await fetch("{{ route('proprio.api.withdrawals.index') }}");
            const data = await response.json();

            if (data.success) {
                renderWithdrawalHistory(data.withdrawals.data);
                
                // Mettre à jour le total affiché (somme des retraits complétés)
                const total = data.withdrawals.data
                    .filter(w => w.status === 'completed' || w.status === 'processing')
                    .reduce((sum, w) => sum + parseFloat(w.amount), 0);
                
                document.getElementById('total-withdrawals-display').innerText = `-${total.toLocaleString()} F`;
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="8" class="p-10 text-center text-red-400">Erreur de chargement.</td></tr>';
        }
    }

    /**
     * Rendu HTML du journal des retraits
     */
    function renderWithdrawalHistory(withdrawals) {
        const tbody = document.getElementById('withdrawal-history-tbody');
        tbody.innerHTML = '';

        if (withdrawals.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="p-10 text-center text-gray-400">Aucun retrait trouvé.</td></tr>';
            return;
        }

        withdrawals.forEach(w => {
            const date = new Date(w.created_at || w.requested_at).toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

            const statusStyles = {
                'pending': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30',
                'processing': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30',
                'completed': 'bg-green-100 text-green-700',
                'cancelled': 'bg-gray-100 text-gray-600',
                'failed': 'bg-red-100 text-red-600'
            };
            const statusLabels = {
                'pending': '⏳ En attente',
                'processing': '⚡ Traitement',
                'completed': '✅ Payé',
                'cancelled': '❌ Annulé',
                'failed': '⚠️ Échec'
            };

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 dark:hover:bg-slate-800 transition';
            tr.innerHTML = `
                <td class="p-4 text-gray-600 dark:text-gray-300 text-xs">${date}</td>
                <td class="p-4 font-bold text-red-600">-${parseFloat(w.amount).toLocaleString()} F</td>
                <td class="p-4 font-mono text-gray-600 dark:text-gray-300 text-xs">${w.reference || '-'}</td>
                <td class="p-4 font-mono text-gray-600 dark:text-gray-300 text-xs">${w.fedapay_payout_id || '-'}</td>
                <td class="p-4 font-medium text-gray-800 dark:text-white">${w.momo_name || w.beneficiary_name || '-'}</td>
                <td class="p-4 font-mono text-gray-600 dark:text-gray-300 text-xs">${w.momo_number || w.phone_number || '-'}</td>
                <td class="p-4"><span class="${statusStyles[w.status] || ''} px-2 py-1 rounded-lg text-[10px] font-bold">${statusLabels[w.status] || w.status}</span></td>
                <td class="p-4">
                    ${(w.status === 'pending' || w.status === 'processing') ? `
                        <button onclick="promptCancelWithdrawal(${w.id}, this)" class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition flex items-center gap-1">
                            <i class="fas fa-times w-3 h-3"></i> Annuler
                        </button>
                    ` : '-'}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    /**
     * Surcharge de l'annulation (appel API avec modale personnalisée)
     */
    let currentWithdrawalToCancel = null;
    let currentCancelBtn = null;

    function promptCancelWithdrawal(withdrawalId, btn) {
        currentWithdrawalToCancel = withdrawalId;
        currentCancelBtn = btn;
        document.getElementById('cancel-withdrawal-modal').classList.remove('hidden');
    }

    function closeCancelWithdrawalModal() {
        document.getElementById('cancel-withdrawal-modal').classList.add('hidden');
        currentWithdrawalToCancel = null;
        currentCancelBtn = null;
    }

    async function executeCancelWithdrawal() {
        if (!currentWithdrawalToCancel) return;
        
        let withdrawalId = currentWithdrawalToCancel;
        let btn = currentCancelBtn;
        
        closeCancelWithdrawalModal();

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const url = "{{ url('/api/withdrawals') }}/" + withdrawalId + "/cancel";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                loadWithdrawalHistory(); // Recharger le journal
                
                // Optionnel : Recharger pour rafraîchir le solde
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-times w-3 h-3"></i> Annuler';
            }
        } catch (error) {
            showToast('Erreur lors de l\'annulation', 'error');
            btn.disabled = false;
        }
    }
</script>
@endsection