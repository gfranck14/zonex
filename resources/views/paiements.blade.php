@extends('layout')

@section('content')
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            <div id="header-container"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-fade-in">
                <div class="bg-brand-sidebarLight dark:bg-slate-800 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue opacity-10 rounded-full blur-3xl -translate-y-10 translate-x-20"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-8">
                            <div></div>
                        </div>
                        <div class="flex justify-between items-start mb-8">
                            <div><p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Solde Disponible</p><h3 id="solde-display" class="text-5xl font-bold tracking-tight text-white">{{ number_format($balances['total'] ?? 0, 0, ',', ' ') }} <span class="text-2xl text-brand-blue font-normal">F</span></h3></div>
                            <div class="bg-white/10 p-2 rounded-xl"><i class="fas fa-credit-card w-6 h-6 text-white"></i></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-white/5 p-3 rounded-2xl border border-white/5 flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-black font-bold text-[10px] shadow-lg">MTN</div><div><p class="text-[10px] text-gray-400 uppercase">MTN</p><p class="font-bold">{{ number_format($balances['mtn'] ?? 0, 0, ',', ' ') }} F</p></div></div>
                            <div class="bg-white/5 p-3 rounded-2xl border border-white/5 flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-blue-400 flex items-center justify-center text-white font-bold text-[10px] shadow-lg">MO</div><div><p class="text-[10px] text-gray-400 uppercase">Moov</p><p class="font-bold">{{ number_format($balances['moov'] ?? 0, 0, ',', ' ') }} F</p></div></div>
                            <div class="bg-white/5 p-3 rounded-2xl border border-white/5 flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-[10px] shadow-lg">CE</div><div><p class="text-[10px] text-gray-400 uppercase">Celtiis</p><p class="font-bold">{{ number_format($balances['celtiis'] ?? 0, 0, ',', ' ') }} F</p></div></div>
                        </div>
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
                            <i class="fas fa-arrow-down w-5 h-5"></i>
                        </div>
                    </div>

                    <!-- Zone de sélection -->
                    <div class="space-y-4 mb-8">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Montant à retirer</label>
                            <div class="relative">
                                <select id="withdraw-select" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 pr-8 text-sm font-bold appearance-none focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer transition-all">
                                    <option value="10000">10 000 F</option>
                                    <option value="30000">30 000 F</option>
                                    <option value="50000">50 000 F</option>
                                    <option value="100000">100 000 F</option>
                                    <option value="150000">150 000 F</option>
                                    <option value="300000">300 000 F</option>
                                    <option value="500000">500 000 F</option>
                                </select>
                                <!-- Chevron custom -->
                                <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500">
                                    <i class="fas fa-chevron-down w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Note Warning masquée par défaut -->
                        <div id="high-amount-note" class="hidden bg-orange-50 dark:bg-orange-900/20 p-2 rounded-lg flex items-center gap-2 border border-orange-100 dark:border-orange-800">
                            <i class="fas fa-exclamation-triangle w-4 h-4 text-orange-500 flex-shrink-0"></i>
                            <p class="text-[10px] text-orange-600 dark:text-orange-400 font-medium leading-tight">Délai de traitement : 24h pour ce montant.</p>
                        </div>
                    </div>

                    <!-- Bouton Action -->
                    <button onclick="openWithdrawalModal()" class="w-full bg-brand-sidebarLight dark:bg-brand-blue text-white py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg flex justify-center items-center gap-2">
                        Configurer le virement
                        <i class="fas fa-arrow-right w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden animate-fade-in">
                <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex flex-wrap justify-between items-center gap-4">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Historique des Transactions</h3>
                    <form method="GET" action="{{ route('paiements') }}" class="flex gap-3">
                        <select name="filter_zone" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                            <option value="">Toutes les Zones</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('filter_zone') == $zone->id ? 'selected' : '' }}>{{ $zone->nom }}</option>
                            @endforeach
                        </select>
                        <select name="filter_status" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                            <option value="">Tous statuts</option>
                            <option value="success" {{ request('filter_status') == 'success' ? 'selected' : '' }}>Succès (Vert)</option>
                            <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>En attente (Jaune)</option>
                            <option value="failed" {{ request('filter_status') == 'failed' ? 'selected' : '' }}>Échec (Rouge)</option>
                        </select>
                        <input type="date" name="filter_date" value="{{ request('filter_date') }}" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                        @if(request('filter_zone') || request('filter_status') || request('filter_date'))
                            <a href="{{ route('paiements') }}" class="p-2 text-red-500 flex items-center hover:bg-red-50 rounded-xl transition">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider"><tr><th class="p-5">Réf. Transaction</th><th class="p-5">Date & Heure</th><th class="p-5">Client</th><th class="p-5">Zone</th><th class="p-5">Opérateur</th><th class="p-5">Montant</th><th class="p-5">Statut</th></tr></thead>
                        <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                            @forelse($transactions as $t)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition cursor-pointer">
                                    <td class="p-5 font-mono text-gray-500 dark:text-gray-300 text-xs">{{ $t->reference }}</td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ $t->created_at->format('d M, H:i') }}</td>
                                    <td class="p-5 font-medium text-gray-800 dark:text-white">
                                        {{ $t->client->nom_complet ?? 'Inconnu' }}
                                    </td>
                                    <td class="p-5">
                                        <span class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 px-2 py-1 rounded-lg text-[10px] font-bold">
                                            {{ $t->wifizone->nom ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        @php
                                            $opClass = match(strtolower($t->operator)) {
                                                'mtn' => 'text-yellow-600 bg-yellow-50',
                                                'moov' => 'text-orange-600 bg-orange-50',
                                                'celtiis' => 'text-blue-600 bg-blue-50',
                                                default => 'text-gray-600 bg-gray-50'
                                            };
                                        @endphp
                                        <span class="text-xs font-bold {{ $opClass }} px-2 py-1 rounded">{{ strtoupper($t->operator) }}</span>
                                    </td>
                                    <td class="p-5 font-bold text-gray-800 dark:text-white">{{ number_format($t->amount, 0, ',', ' ') }} F</td>
                                    <td class="p-5">
                                        @if($t->status === 'success')
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold">SUCCÈS</span>
                                        @elseif($t->status === 'pending')
                                            <span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">⏳ EN ATTENTE</span>
                                        @else
                                            <span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-[10px] font-bold">ÉCHEC</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-10 text-center text-gray-400">Aucune transaction trouvée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <!-- Info Affichage -->
                            <div class="flex items-center gap-2 order-2 md:order-1">
                                <span class="text-xs text-gray-400">Affichage de</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $transactions->firstItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">à</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $transactions->lastItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">sur</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $transactions->total() }}</span>
                                <span class="text-xs text-gray-400">transactions</span>
                            </div>
                            
                            <!-- Sélecteur lignes -->
                            <div class="flex items-center gap-2 order-3 md:order-2">
                                <span class="text-xs text-gray-400">Afficher</span>
                                <form method="GET" action="{{ route('paiements') }}" class="inline-block">
                                    @if(request('filter_zone')) <input type="hidden" name="filter_zone" value="{{ request('filter_zone') }}"> @endif
                                    @if(request('filter_status')) <input type="hidden" name="filter_status" value="{{ request('filter_status') }}"> @endif
                                    @if(request('filter_date')) <input type="hidden" name="filter_date" value="{{ request('filter_date') }}"> @endif
                                    <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold p-1 px-2 outline-none cursor-pointer">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </form>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="flex items-center gap-2 order-1 md:order-3">
                                {{ $transactions->appends(request()->except('page'))->onEachSide(1)->links() }}
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
                            <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
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
                            <th class="p-4 text-left">Opérateur</th>
                            <th class="p-4 text-left">Bénéficiaire</th>
                            <th class="p-4 text-left">Destination</th>
                            <th class="p-4 text-left">Réf. Mobile Money</th>
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

@endsection

@section('scripts')
<script>
    // Configuration AJAX avec Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
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

        // Overlay de chargement ou désactivation bouton
        const confirmBtn = event.target;
        const originalText = confirmBtn.innerText;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'Traitement...';

        try {
            const response = await fetch("{{ route('api.withdrawals.store') }}", {
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
                    beneficiary_name: name
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
            const response = await fetch("{{ route('api.withdrawals.index') }}");
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
            const date = new Date(w.requested_at).toLocaleString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            
            const operatorStyles = {
                'mtn': 'text-yellow-600 bg-yellow-50',
                'moov': 'text-orange-600 bg-orange-50',
                'celtiis': 'text-blue-600 bg-blue-50'
            };
            const opClass = operatorStyles[w.operator] || 'text-gray-600 bg-gray-50';

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
                <td class="p-4"><span class="text-xs font-bold ${opClass} px-2 py-1 rounded">${w.operator.toUpperCase()}</span></td>
                <td class="p-4 font-medium text-gray-800 dark:text-white">${w.beneficiary_name}</td>
                <td class="p-4 font-mono text-gray-600 dark:text-gray-300 text-xs">${w.phone_number}</td>
                <td class="p-4 font-mono text-gray-500 dark:text-gray-400 text-xs">${w.mobile_money_ref || w.reference}</td>
                <td class="p-4"><span class="${statusStyles[w.status] || ''} px-2 py-1 rounded-lg text-[10px] font-bold">${statusLabels[w.status] || w.status}</span></td>
                <td class="p-4">
                    ${(w.status === 'pending' || w.status === 'processing') ? `
                        <button onclick="cancelWithdrawal(${w.id}, this)" class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition flex items-center gap-1">
                            <i class="fas fa-times w-3 h-3"></i> Annuler
                        </button>
                    ` : '-'}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    /**
     * Surcharge de l'annulation (appel API)
     */
    async function cancelWithdrawal(withdrawalId, btn) {
        if (!confirm('Voulez-vous vraiment annuler cette demande de retrait ?')) return;

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