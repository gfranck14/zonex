@extends('layout')
@section('title', 'Forfaits & Tickets')

@section('content')
    <!-- Messages de succès et d'erreurs -->
    @if(session('success'))
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('success') }}", 'success');
                } else {
                    console.log("{{ session('success') }}");
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('error') }}", 'error');
                } else {
                    console.log("{{ session('error') }}");
                }
            });
        </script>
    @endif

    <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
        
        <!-- HEADER PERSONNALISÉ -->
        <header class="relative z-50 flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <!-- Partie Gauche : Titres -->
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Forfaits & Tickets</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gérez vos offres par zone</p>
            </div>

            <!-- Partie Droite : Sélecteur de Zone + Bouton Sync -->
            <div class="relative z-[60] flex items-center gap-3 w-full md:w-auto">
                <!-- Bouton Sync Tickets (Visible seulement sur Liste des Tickets) -->
                <button id="btn-sync-tickets-header" onclick="syncMikrotikTickets()" class="hidden items-center justify-center gap-2 bg-brand-blue hover:bg-blue-600 text-white px-5 py-3.5 rounded-xl shadow-lg transition whitespace-nowrap">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="font-bold text-sm">Synchroniser Tickets</span>
                </button>

                <div class="relative w-full md:w-72">
                    <div class="relative">
                        
                        <!-- BOUTON SÉLECTEUR (Affiche la zone active) -->
                        <button id="zone-selector-btn" onclick="toggleDropdown()" class="w-full flex items-center justify-between bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3.5 rounded-xl shadow-lg border border-transparent hover:brightness-110 transition">
                        <div class="flex items-center gap-3">
                            <!-- Icône WiFi fixe (ne change pas de couleur) -->
                            <i class="fas fa-wifi text-white/80"></i>
                            <span id="selected-zone-name" class="font-bold text-sm">{{ $activeZoneId != 'all' ? ($wifizones->where('id', $activeZoneId)->first()->nom_zone ?? 'Zone') : 'Toutes les zones' }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-white/80 text-xs"></i>
                    </button>

                    <!-- MENU DÉROULANT (La liste) -->
                    <div id="zone-dropdown-menu" class="absolute right-0 top-full mt-2 w-full bg-brand-cardLight dark:bg-brand-cardDark rounded-xl shadow-xl p-2 hidden border border-gray-100 dark:border-gray-700 z-[100]">
                        
                        <!-- OPTION : TOUTES LES ZONES -->
                        <div onclick="selectZoneFilter('all', 'Toutes les zones')" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium text-gray-800 dark:text-white flex items-center gap-2 transition">
                            <i class="fas fa-wifi text-gray-400"></i> Toutes les zones
                        </div>

                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>

                        <!-- OPTIONS : LES ZONES DU PROPRIO -->
                        @foreach($wifizones as $zone)
                            @php
                                // Récupération des infos envoyées par le controller
                                $stockInfo = $zoneStockStatus[$zone->id] ?? [
                                    'status' => 'empty',
                                    'icon' => 'fa-times-circle',
                                    'color' => 'text-red-500'
                                ];
                            @endphp
                            <div onclick="selectZoneFilter('{{ $zone->id }}', '{{ addslashes($zone->nom_zone) }}')" class="p-3 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium text-gray-800 dark:text-white flex items-center gap-2 transition">
                                <!-- ICÔNE FA DYNAMIQUE (Verte, Orange ou Rouge) -->
                                <i class="fas {{ $stockInfo['icon'] }} {{ $stockInfo['color'] }}"></i>
                                <span>{{ $zone->nom_zone }}</span>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </header>

        <!-- Navigation des Onglets -->
        <div class="border-b border-gray-200 dark:border-slate-700 mb-8 flex gap-8">
            <button onclick="switchTab('catalogue')" id="tab-catalogue" class="tab-btn active text-sm pb-3 border-b-2 font-bold transition-all border-[#072b47] dark:border-[#0EA5E9] text-gray-800 dark:text-white">Catalogue des Offres</button>
            <button onclick="switchTab('stock')" id="tab-stock" class="tab-btn text-sm pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 transition-all">Stock de Tickets</button>
            <button onclick="switchTab('list')" id="tab-list" class="tab-btn text-sm pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 transition-all">Liste des Tickets</button>
        </div>

        <!-- ==================== ONGLET 1 : CATALOGUE ==================== -->
        <div id="content-catalogue" class="tab-content animate-fade-in">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Bouton Créer Nouveau -->
                <div onclick="openCreatePackageModal()" class="h-full min-h-[250px] border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-3xl flex flex-col items-center justify-center p-6 text-gray-400 dark:text-gray-500 hover:border-brand-blue hover:text-brand-blue hover:bg-blue-50/10 transition cursor-pointer group">
                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center mb-4 group-hover:bg-brand-blue group-hover:text-white transition shadow-sm">
                        <i class="fas fa-plus text-xl"></i>
                    </div>
                    <span class="font-bold text-lg">Créer un Forfait</span>
                    <span class="text-xs mt-1 opacity-70 text-center px-4">Créer un profil Mikrotik</span>
                </div>

                <!-- Bouton Importer Profils MikroTik -->
                <div id="btn-sync-profiles" onclick="syncMikrotikProfiles()" class="h-full min-h-[250px] border-2 border-dashed border-purple-300 dark:border-purple-900/50 rounded-3xl flex flex-col items-center justify-center p-6 text-purple-400 dark:text-purple-500 hover:border-purple-500 hover:text-purple-500 hover:bg-purple-50/10 transition cursor-pointer group relative">
                    <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center mb-4 group-hover:bg-purple-500 group-hover:text-white transition shadow-sm">
                        <i class="fas fa-sync text-xl"></i>
                    </div>
                    <span class="font-bold text-lg text-center leading-tight">Importer Profils<br>MikroTik</span>
                    <span class="text-xs mt-1 opacity-70 text-center px-4">Importer les profils déjà créés sur votre routeur</span>
                    
                    <!-- Overlay si aucune zone sélectionnée -->
                    <div id="sync-disabled-overlay" class="absolute inset-0 bg-white/60 dark:bg-brand-bgDark/60 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-3xl cursor-not-allowed" onclick="highlightZoneSelector()">
                        <div class="bg-gray-800 text-white text-[10px] px-3 py-1 rounded-full font-bold pointer-events-none">CHOISIR UNE ZONE</div>
                    </div>
                </div>



                <!-- BOUCLE DYNAMIQUE LARAVEL -->
                @forelse($forfaits as $forfait)
                <div class="filterable-item bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 hover:border-brand-blue/30 dark:hover:border-brand-blue/30 transition-all relative flex flex-col justify-between h-full min-h-[250px] {{ !$forfait->is_active ? 'opacity-60' : '' }}" 
                     data-zone-id="{{ $forfait->wifizones_id }}">
                    
                    <!-- Badge Nom de la Zone -->
                    <div class="absolute top-0 right-0 flex gap-1">
                        @if(!$forfait->is_active)
                            <div class="bg-gray-500 text-white text-[9px] font-bold px-2 py-1 rounded-bl-lg shadow-sm">
                                INACTIF
                            </div>
                        @endif
                        @if($forfait->prix == 0)
                            <div class="bg-orange-500 text-white text-[9px] font-bold px-2 py-1 rounded-bl-lg shadow-sm animate-pulse">
                                BROUILLON
                            </div>
                        @endif
                        <div class="bg-brand-blue text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl @if($forfait->prix > 0) rounded-tr-2xl @endif shadow-sm">
                            {{ $forfait->wifizone->nom_zone ?? 'Zone Inconnue' }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <!-- Icône dynamique selon le nom -->
                            <div class="w-12 h-12 rounded-2xl {{ str_contains($forfait->color_class, 'bg-') ? str_replace('bg-', 'bg-opacity-10 text-', $forfait->color_class) : 'bg-blue-50 text-brand-blue' }} dark:bg-slate-800 flex items-center justify-center text-2xl shadow-inner">
                                @if(str_contains(strtolower($forfait->nom), '24')) 🌙 
                                @elseif(str_contains(strtolower($forfait->nom), 'semaine')) 📅
                                @elseif(str_contains(strtolower($forfait->nom), 'mois')) 💎
                                @else ⚡ @endif
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $forfait->nom }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 line-clamp-2">{{ $forfait->description ?? 'Aucune description' }}</p>
                        
                        <div class="my-4">
                            <span class="text-3xl font-bold text-[#1e293b] dark:text-[#0ea5e9]">{{ number_format($forfait->prix, 0, ',', ' ') }} <span class="text-sm font-medium text-gray-400">F</span></span>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-3 text-[11px] text-gray-500 dark:text-gray-400 font-mono mb-4 flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            <span class="truncate">Limite: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $forfait->temps_limit }}</span></span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mt-auto">
                        <!-- Bouton Modifier -->
                        <button onclick="openPackageModal('edit', {
                            id: '{{ $forfait->id }}',
                            nom: '{{ addslashes($forfait->nom) }}',
                            prix: '{{ $forfait->prix }}',
                            validite: '{{ $forfait->validite }}',
                            temps_limit: '{{ $forfait->temps_limit }}',
                            description: '{{ addslashes($forfait->description) }}',
                            color_class: '{{ $forfait->color_class }}',
                            zone_id: '{{ $forfait->wifizones_id }}',
                            zone_name: '{{ addslashes($forfait->wifizone->nom_zone) }}',
                            stock_max: '{{ $forfait->stock_max }}',
                            seuil_alerte: '{{ $forfait->seuil_alerte }}',
                            auto_replenish: {{ $forfait->auto_replenish ? 'true' : 'false' }}
                        })" class="flex-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 py-3 rounded-xl text-xs font-bold hover:bg-brand-blue hover:text-white dark:hover:bg-brand-blue transition">
                            Modifier
                        </button>
                        
                        <!-- Bouton Toggle Actif/Inactif -->
                        <button onclick="toggleForfaitActive({{ $forfait->id }}, this)" 
                                title="{{ $forfait->is_active ? 'Masquer du portail' : 'Afficher sur le portail' }}"
                                data-active="{{ $forfait->is_active ? '1' : '0' }}"
                                class="w-11 h-11 flex items-center justify-center rounded-xl text-xs transition-all shadow-sm {{ $forfait->is_active ? 'bg-green-100 dark:bg-green-900/20 text-green-600 hover:bg-red-50 hover:text-red-500' : 'bg-gray-100 dark:bg-slate-700 text-gray-400 hover:bg-green-50 hover:text-green-600' }}">
                            <i class="fas {{ $forfait->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        </button>

                        <!-- Bouton Supprimer -->
                        <button onclick="showDeleteOptionsModal({{ $forfait->id }}, '{{ addslashes($forfait->nom) }}', {{ $forfait->tickets_count ?? 0 }})" 
                                class="w-11 h-11 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-400 rounded-xl hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all shadow-sm">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-10 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-3"></i>
                    <p>Aucun forfait créé pour le moment.</p>
                </div>
                @endforelse

            </div>
            <div id="no-results-msg" class="hidden text-center py-12 text-gray-400">
                <p>Aucune offre trouvée pour cette zone.</p>
            </div>
        </div>

        <!-- ==================== ONGLET 2 : STOCK & IMPORT ==================== -->
        <div id="content-stock" class="tab-content hidden animate-fade-in space-y-8">
            
            <!-- KPIs DYNAMIQUES (Top 4 Stocks Critiques) -->
            <div id="stock-kpi-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                
                <!-- EXEMPLE 1 : Stock Critique (Rouge) -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-red-50 dark:border-red-900/30 flex flex-col justify-between h-full group hover:border-red-200 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500 font-bold text-lg">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Forfait 1H</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Bar Central</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-[10px] font-bold animate-pulse">CRITIQUE</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold text-red-500">5 tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-red-500 h-full rounded-full" style="width: 5%"></div>
                        </div>
                    </div>
                </div>

                <!-- EXEMPLE 2 : Stock Faible (Orange) -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-orange-50 dark:border-orange-900/30 flex flex-col justify-between h-full group hover:border-orange-200 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-500 font-bold text-lg">
                                <i class="fas fa-battery-quarter"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Pass 24H</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Campus Nord</p>
                            </div>
                        </div>
                        <span class="bg-orange-100 text-orange-600 px-2 py-1 rounded-lg text-[10px] font-bold">BAS</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold text-orange-500">24 tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-orange-500 h-full rounded-full" style="width: 15%"></div>
                        </div>
                    </div>
                </div>

                <!-- EXEMPLE 3 : Stock Moyen (Bleu) -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 flex flex-col justify-between h-full group hover:border-blue-200 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-brand-blue font-bold text-lg">
                                <i class="fas fa-battery-half"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Pass Semaine</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Bar Central</p>
                            </div>
                        </div>
                        <span class="bg-blue-50 text-brand-blue px-2 py-1 rounded-lg text-[10px] font-bold">MOYEN</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold text-brand-blue">142 tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-brand-blue h-full rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>

                <!-- EXEMPLE 4 : Stock Plein (Vert) -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 flex flex-col justify-between h-full group hover:border-green-200 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-brand-green font-bold text-lg">
                                <i class="fas fa-battery-full"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">Pass 1H</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Campus Nord</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-brand-green px-2 py-1 rounded-lg text-[10px] font-bold">OK</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold text-brand-green">850 tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-brand-green h-full rounded-full" style="width: 90%"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. Zone Importation (WIZARD D'IMPORT) -->
            <div class="bg-white dark:bg-brand-cardDark p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700 relative overflow-hidden">
                <!-- Déco background -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue opacity-5 rounded-full blur-3xl -translate-y-10 translate-x-10 pointer-events-none"></div>

                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Importer des Tickets</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ajoutez du stock depuis vos fichiers CSV Mikhmon.</p>
                    </div>
                    <span class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                        CSV Requis
                    </span>
                </div>

                <form id="import-form" onsubmit="event.preventDefault(); startImportAnalysis();" class="relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        
                        <!-- ÉTAPE 1 : Configuration -->
                        <div class="space-y-5">
                            <p class="text-xs font-bold text-brand-blue uppercase flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-brand-blue text-white flex items-center justify-center text-[10px]">1</span>
                                Destination
                            </p>
                            
                            <!-- Select Zone -->
                            <div class="input-floating-group">
                                <select id="import-zone" class="input-floating cursor-pointer" required>
                                    <option value="" disabled selected></option>
                                    @foreach($wifizones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->nom_zone }}</option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Zone Cible</label>
                            </div>

                            <!-- Select Forfait -->
                            <div class="input-floating-group">
                                <select id="import-package" class="input-floating cursor-pointer" required>
                                    <option value="" disabled selected></option>
                                    @foreach($forfaits as $f)
                                        <option value="{{ $f->id }}" data-zone-id="{{ $f->wifizones_id }}">{{ $f->nom }} ({{ $f->prix }}F)</option>
                                    @endforeach
                                </select>
                                <label class="floating-label">Forfait à alimenter</label>
                            </div>

                            <div class="p-3 bg-blue-50 dark:bg-slate-800 rounded-xl border border-blue-100 dark:border-slate-700">
                                <p class="text-[11px] text-blue-600 dark:text-blue-400 leading-snug">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Assurez-vous que le CSV correspond au profil Mikrotik du forfait sélectionné.
                                </p>
                            </div>
                        </div>

                        <!-- ÉTAPE 2 : Fichier -->
                        <div class="lg:col-span-2 space-y-5">
                            <p class="text-xs font-bold text-brand-blue uppercase flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-brand-blue text-white flex items-center justify-center text-[10px]">2</span>
                                Fichier Source
                            </p>
                            
                            <!-- Zone Drag & Drop Stylisée -->
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-3xl cursor-pointer bg-gray-50/50 dark:bg-slate-800/30 hover:bg-brand-blue/5 dark:hover:bg-brand-blue/10 hover:border-brand-blue dark:hover:border-brand-blue transition-all group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="w-16 h-16 bg-white dark:bg-slate-700 rounded-full shadow-sm flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-brand-blue"></i>
                                    </div>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-bold text-gray-700 dark:text-gray-200">Cliquez pour upload</span> ou glissez le fichier</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Format accepté : .CSV (Mikhmon export)</p>
                                </div>
                                <input id="import-file" type="file" class="hidden" accept=".csv" />
                            </label>
                        </div>
                    </div>

                    <!-- Bouton Action -->
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-slate-700 flex justify-end items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="ignore-duplicates" class="custom-checkbox w-4 h-4 rounded border-gray-300">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ignorer les doublons</span>
                        </label>
                        <button type="submit" class="bg-brand-sidebarLight dark:bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg flex items-center gap-2">
                            <i class="fas fa-microchip"></i> Analyser & Importer
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABLEAU HISTORIQUE DES IMPORTS (Design HTML Strict) -->
            <div class="bg-brand-cardLight dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden mt-8 border border-gray-100 dark:border-slate-700">
                <!-- Header avec filtres -->
                <div class="p-6 border-b border-gray-100 dark:border-slate-700">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <!-- Titre à gauche -->
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Historiques des Lots de Tickets créés</h3>
                        
                        <!-- Barre de recherche au milieu -->
                        <div class="relative flex-1 max-w-md mx-0 lg:mx-8">
                            <input type="text" placeholder="Rechercher un fichier, zone, forfait..." class="pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-blue/20 outline-none w-full text-gray-600 dark:text-gray-300">
                            <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                        </div>
                        
                        <!-- Filtres à droite -->
                        <div class="flex flex-wrap gap-3 items-center">
                            <!-- Filtre Zone -->
                            <select id="filter-zone" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="">Toutes les zones</option>
                                @foreach($wifizones as $zone)
                                    <option value="{{ $zone->nom_zone }}">{{ $zone->nom_zone }}</option>
                                @endforeach
                            </select>
                            
                            <!-- Filtre Forfait -->
                            <select id="filter-forfait" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="">Tous les forfaits</option>
                                @foreach($forfaits as $f)
                                    <option value="{{ $f->nom }}" data-zone="{{ $f->wifizone->nom_zone ?? '' }}">{{ $f->nom }}</option>
                                @endforeach
                            </select>
                            
                            <!-- Filtre Statut -->
                            <select id="filter-statut" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="">Tous statuts</option>
                                <option value="succes">Succès</option>
                                <option value="partiel">Partiel</option>
                                <option value="echec">Échec</option>
                            </select>
                            
                            <!-- Filtre Date (Optionnel selon HTML, mais présent dans design) -->
                            <input type="date" id="filter-date" class="px-3 py-2 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="imports-table">
                        <thead class="bg-gray-50/50 dark:bg-slate-800 text-gray-400 dark:text-gray-500 text-[10px] uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(0, 'imports-table')">
                                    Date <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(1, 'imports-table')">
                                    NOM DU LOT <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(2, 'imports-table')">
                                    Zone Cible <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(3, 'imports-table')">
                                    Forfait Lié <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(4, 'imports-table')">
                                    Quantité <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(5, 'imports-table')">
                                    Statut <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(6, 'imports-table')">
                                    BATCH ID <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(7, 'imports-table')">
                                    OBSERVATION <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700" id="imports-tbody">
                            
                            @forelse($imports as $import)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition" data-forfait="{{ $import->forfait_nom }}" data-statut="{{ $import->statut }}" data-zone="{{ $import->zone_nom }}">
                                <td class="p-5 text-gray-500 dark:text-gray-400">
                                    {{ $import->created_at->format('d M, H:i') }}
                                </td>
                                <td class="p-5 font-mono text-xs text-gray-600 dark:text-gray-300">
                                    {{ $import->nom_fichier }}
                                </td>
                                <td class="p-5 font-bold text-gray-800 dark:text-white">
                                    {{ $import->zone_nom }}
                                </td>
                                <td class="p-5">
                                    <span class="bg-blue-50 dark:bg-blue-900/30 text-brand-blue px-2 py-1 rounded text-[10px] font-bold border border-blue-100 dark:border-blue-800">
                                        {{ $import->forfait_nom }}
                                    </span>
                                </td>
                                <td class="p-5 font-bold text-brand-green">
                                    + {{ $import->quantite }}
                                </td>
                                <td class="p-5">
                                    @if($import->statut == 'succes')
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-brand-green bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded-full w-fit">
                                            <i class="fas fa-check"></i> SUCCÈS
                                        </span>
                                    @elseif($import->statut == 'partiel')
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-orange-500 bg-orange-50 dark:bg-orange-900/30 px-2 py-1 rounded-full w-fit">
                                            <i class="fas fa-exclamation-triangle"></i> PARTIEL
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-red-500 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded-full w-fit">
                                            <i class="fas fa-times"></i> ÉCHEC
                                        </span>
                                    @endif
                                </td>
                                <td class="p-5 font-mono text-xs text-gray-600 dark:text-gray-300">
                                    {{ $import->import_batch_id ?? '-' }}
                                </td>
                                <td class="p-5 text-xs text-gray-500 dark:text-gray-400 max-w-xs break-words">
                                    @if($import->statut == 'succes' && ($import->observation == 'Aucune' || empty($import->observation)))
                                        <span class="opacity-50">-</span>
                                    @else
                                        {{ $import->observation }}
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400">
                                    Aucun historique d'importation disponible.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>



                <!-- Pagination (Réelle et Fonctionnelle) -->
                <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        
                        <!-- GAUCHE : Info Affichage (Générations) -->
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            <span class="text-xs text-gray-400">Affichage de</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $imports->firstItem() ?? 0 }}</span>
                            <span class="text-xs text-gray-400">à</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $imports->lastItem() ?? 0 }}</span>
                            <span class="text-xs text-gray-400">sur</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $imports->total() }}</span>
                            <span class="text-xs text-gray-400">imports</span>
                        </div>
                        
                        <!-- MILIEU : Sélecteur lignes (History) -->
                        <div class="flex items-center gap-2 order-3 md:order-2">
                            <span class="text-xs text-gray-400">Afficher</span>
                            <form method="GET" action="{{ route('forfait_ticket') }}" class="inline-block">
                                <input type="hidden" name="tab" value="stock">
                                @if(request('filter_zone')) <input type="hidden" name="filter_zone" value="{{ request('filter_zone') }}"> @endif
                                
                                <select name="per_page_history" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold p-1 px-2 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                                    <option value="5" {{ request('per_page_history') == 5 ? 'selected' : '' }}>5 lignes</option>
                                    <option value="10" {{ request('per_page_history', 10) == 10 ? 'selected' : '' }}>10 lignes</option>
                                    <option value="25" {{ request('per_page_history') == 25 ? 'selected' : '' }}>25 lignes</option>
                                </select>
                            </form>
                        </div>
                        
                        <!-- DROITE : Pagination Séquentielle -->
                        <div class="flex items-center gap-2 order-1 md:order-3">
                            <!-- Bouton Précédent -->
                            @if ($imports->onFirstPage())
                                <button disabled class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-300 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $imports->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            <div class="flex gap-1">
                                @foreach ($imports->linkCollection() as $link)
                                    @if (!str_contains($link['label'], 'Previous') && !str_contains($link['label'], 'Next'))
                                        @if ($link['active'])
                                            <button class="px-3 py-1 rounded-lg bg-brand-blue text-white text-xs font-bold shadow-sm">{{ $link['label'] }}</button>
                                        @elseif ($link['label'] === '...')
                                            <span class="px-2 text-gray-400 text-xs py-1">...</span>
                                        @else
                                            <a href="{{ $link['url'] }}" class="px-3 py-1 rounded-lg border border-gray-200 dark:border-slate-600 text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                                {{ $link['label'] }}
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            </div>

                            <!-- Bouton Suivant -->
                            @if ($imports->hasMorePages())
                                <a href="{{ $imports->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
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

        <!-- ==================== ONGLET 3 : LISTE DES TICKETS ==================== -->
        <div id="content-list" class="tab-content hidden animate-fade-in space-y-8">
            
            <!-- KPI Cards pour Liste des Tickets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Tickets -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Total Tickets</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalTicketsCount, 0, ',', ' ') }}</p>
                    </div>
                </div>
                
                <!-- Disponibles -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Disponibles</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($availableTicketsCount, 0, ',', ' ') }}</p>
                    </div>
                </div>
                
                <!-- Valeur Vendus -->
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-xl">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Vendus</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($soldTicketsValue, 0, ',', ' ') }} <span class="text-sm font-medium text-gray-400">F</span></p>
                    </div>
                </div>
            </div>
            
            <!-- Tableau Tickets -->
            <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden border border-gray-100 dark:border-slate-700">
                <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                    <form method="GET" action="{{ route('forfait_ticket') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <input type="hidden" name="tab" value="list">
                        
                        <!-- Titre -->
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Liste des Tickets</h3>
                        
                        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            
                            <!-- Barre de recherche -->
                            <div class="relative flex-1 lg:flex-none">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Code..." 
                                       class="pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all">
                                <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                            </div>

                            <!-- Filtre Zone (Nouveau) -->
                            <select name="filter_zone" id="table-filter-zone" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                <option value="">Toutes les Zones</option>
                                @foreach($wifizones as $z)
                                    <option value="{{ $z->id }}" {{ request('filter_zone') == $z->id ? 'selected' : '' }}>{{ $z->nom_zone }}</option>
                                @endforeach
                            </select>

                            <!-- Filtre Forfait -->
                            <select name="filter_forfait" id="table-filter-forfait" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                <option value="">Tous les Forfaits</option>
                                @foreach($forfaits as $f)
                                    <option value="{{ $f->id }}" data-zone-id="{{ $f->wifizones_id }}" {{ request('filter_forfait') == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
                                @endforeach
                            </select>

                            <!-- Filtre Statut -->
                            <select name="filter_statut" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                <option value="">Tous statuts</option>
                                <option value="libre" {{ request('filter_statut') == 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="pending" {{ request('filter_statut') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="vendu" {{ request('filter_statut') == 'vendu' ? 'selected' : '' }}>Vendu</option>
                            </select>

                            <!-- Filtre Date -->
                            <input type="date" name="filter_date" value="{{ request('filter_date') }}" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition" title="Filtrer par date">

                            <!-- Bouton Reset (si filtres actifs) -->
                            @if(request('search') || request('filter_forfait') || request('filter_statut') || request('filter_zone') || request('filter_date'))
                                <a href="{{ route('forfait_ticket', ['tab' => 'list']) }}" class="p-2 text-red-400 hover:text-red-600 transition" title="Réinitialiser">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                
                @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="tickets-table">
                        <thead class="bg-gray-50/50 dark:bg-slate-800 text-gray-400 dark:text-gray-500 text-[10px] uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(0, 'tickets-table')">
                                    Code (Login) <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(1, 'tickets-table')">
                                    Mot de passe <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(2, 'tickets-table')">
                                    Forfait <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(3, 'tickets-table')">
                                    Zone <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(4, 'tickets-table')">
                                    Vendu à <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(5, 'tickets-table')">
                                    Date Vente <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300" onclick="sortTable(6, 'tickets-table')">
                                    Statut <i class="fas fa-sort text-xs ml-1"></i>
                                </th>
                                <th class="p-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                            @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition group">
                                <!-- 1. Code (Cliquable pour copier) -->
                                <td class="p-5 font-mono text-xs text-brand-sidebarLight dark:text-brand-blue font-bold cursor-pointer underline decoration-brand-blue/20" 
                                    onclick="copyCode('{{ $ticket->username }}', 'Login')" 
                                    title="Cliquer pour copier le compte login">
                                    {{ $ticket->username }}
                                </td>

                                <!-- 2. Mot de passe (Cliquable pour copier) -->
                                <td class="p-5">
                                    <div class="relative group cursor-pointer inline-block" 
                                         onclick="copyCode('{{ $ticket->password }}', 'Mot de passe')" 
                                         title="Cliquer pour copier le mot de passe">
                                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400 blur-[3px] group-hover:blur-none transition-all duration-300 select-none">
                                            {{ $ticket->password }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 3. Forfait -->
                                <td class="p-5">
                                    <span class="bg-blue-50 dark:bg-blue-900/30 text-brand-blue px-2 py-1 rounded text-[10px] font-bold border border-blue-100 dark:border-blue-800">
                                        {{ $ticket->forfait->nom ?? 'Inconnu' }}
                                    </span>
                                </td>

                                <!-- 4. Zone -->
                                <td class="p-5 font-bold text-gray-600 dark:text-gray-300 text-xs">
                                    {{ $ticket->forfait->wifizone->nom_zone ?? '-' }}
                                </td>

                                <!-- 5. Vendu à -->
                                <td class="p-5 font-bold text-gray-800 dark:text-white text-sm">
                                    {{ $ticket->client ? $ticket->client->nom_complet : '-' }}
                                </td>

                                <!-- 6. Date Vente -->
                                <td class="p-5 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $ticket->date_vente ? \Carbon\Carbon::parse($ticket->date_vente)->format('d M, H:i') : '-' }}
                                </td>

                                <!-- 7. Statut -->
                                <td class="p-5">
                                    @if($ticket->statut == 'libre')
                                        <span class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 px-2 py-1 rounded-lg text-[10px] font-bold border border-gray-200 dark:border-slate-600">
                                            LIBRE
                                        </span>
                                    @elseif($ticket->statut == 'pending')
                                        <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 px-2 py-1 rounded-lg text-[10px] font-bold border border-orange-200 dark:border-orange-800">
                                            EN ATTENTE
                                        </span>
                                    @else
                                        <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded-lg text-[10px] font-bold border border-green-200 dark:border-green-800">
                                            VENDU
                                        </span>
                                    @endif
                                </td>

                                <!-- 8. Action -->
                                <td class="p-5 text-right flex justify-end gap-2">
                                    <!-- Bouton Copier Tout -->
                                    <button onclick="copyCode('Code: {{ $ticket->username }} / PIN: {{ $ticket->password }}', 'Ticket complet')" 
                                            class="p-2 rounded-lg text-gray-400 hover:text-brand-blue hover:bg-blue-50 dark:hover:bg-slate-700 transition" 
                                            title="Copier Login + Pass">
                                        <i class="fas fa-copy w-4 h-4"></i>
                                    </button>
                                    
                                    <!-- Bouton Supprimer (uniquement pour les tickets libres) -->
                                    @if($ticket->statut == 'libre')
                                    <form id="delete-ticket-form-{{ $ticket->id }}" action="{{ route('tickets.destroy', $ticket->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDeleteTicket({{ $ticket->id }})" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-slate-700 transition" title="Supprimer">
                                            <i class="fas fa-trash w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Alignée (Conforme au style Historique) -->
                <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        
                        <!-- GAUCHE : Info Affichage (Tickets) -->
                        <div class="flex items-center gap-2 order-2 md:order-1">
                            <span class="text-xs text-gray-400">Affichage de</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $tickets->firstItem() ?? 0 }}</span>
                            <span class="text-xs text-gray-400">à</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $tickets->lastItem() ?? 0 }}</span>
                            <span class="text-xs text-gray-400">sur</span>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $tickets->total() }}</span>
                            <span class="text-xs text-gray-400">tickets</span>
                        </div>
                        
                        <!-- MILIEU : Sélecteur lignes -->
                        <div class="flex items-center gap-2 order-3 md:order-2">
                            <span class="text-xs text-gray-400">Afficher</span>
                            <form method="GET" action="{{ route('forfait_ticket') }}" class="inline-block">
                                <input type="hidden" name="tab" value="list">
                                <!-- Conservation des filtres -->
                                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                                @if(request('filter_forfait')) <input type="hidden" name="filter_forfait" value="{{ request('filter_forfait') }}"> @endif
                                @if(request('filter_statut')) <input type="hidden" name="filter_statut" value="{{ request('filter_statut') }}"> @endif
                                @if(request('filter_zone')) <input type="hidden" name="filter_zone" value="{{ request('filter_zone') }}"> @endif
                                @if(request('filter_date')) <input type="hidden" name="filter_date" value="{{ request('filter_date') }}"> @endif

                                <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold p-1 px-2 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 lignes</option>
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 lignes</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 lignes</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 lignes</option>
                                </select>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // S'assurer que le formulaire de pagination conserve bien l'onglet actif
                                        const perPageSelect = document.querySelector('select[name="per_page"]');
                                        if (perPageSelect) {
                                            const perPageForm = perPageSelect.closest('form');
                                            if (perPageForm) {
                                                perPageForm.addEventListener('submit', function(e) {
                                                // Vérifier que le champ tab est bien présent
                                                let tabInput = this.querySelector('input[name="tab"]');
                                                if (!tabInput) {
                                                    // Si le champ tab n'existe pas, l'ajouter
                                                    tabInput = document.createElement('input');
                                                    tabInput.type = 'hidden';
                                                    tabInput.name = 'tab';
                                                    tabInput.value = 'list';
                                                    this.appendChild(tabInput);
                                                }
                                                tabInput.value = 'list'; // Forcer la valeur
                                            });
                                            }
                                        }
                                        
                                        // Intercepter tous les clics sur les liens de pagination pour conserver l'onglet
                                        const paginationLinks = document.querySelectorAll('a[href*="page="]');
                                        paginationLinks.forEach(link => {
                                            link.addEventListener('click', function(e) {
                                                const url = new URL(this.href);
                                                // S'assurer que le paramètre tab est présent
                                                url.searchParams.set('tab', 'list');
                                                this.href = url.toString();
                                            });
                                        });
                                    });
                                </script>
                            </form>
                        </div>
                        
                        <!-- DROITE : Pagination Séquentielle -->
                        <div class="flex items-center gap-2 order-1 md:order-3">
                            <!-- Bouton Précédent -->
                            @if ($tickets->onFirstPage())
                                <button disabled class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-300 cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <a href="{{ $tickets->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif
                            
                            <!-- NUMÉROS DE PAGES -->
                            <div class="flex gap-1">
                                @foreach ($tickets->linkCollection() as $link)
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
                            @if ($tickets->hasMorePages())
                                <a href="{{ $tickets->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-sm bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
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

                @else
                <div class="p-10 text-center text-gray-400">
                    <i class="fas fa-database text-4xl mb-3 opacity-50"></i>
                    <p>Aucun ticket trouvé dans la base de données.</p>
                    <p class="text-xs mt-2">Générer des tickets via l'onglet "Stock de Tickets".</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MODALE CRÉATION FORFAIT -->
    <div id="create-package-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand-sidebarLight/60 dark:bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeCreatePackageModal()"></div>
        
        <div class="bg-white dark:bg-brand-cardDark modal-bg-light w-full max-w-4xl rounded-[2rem] shadow-2xl relative z-10 overflow-hidden flex flex-col md:flex-row border border-gray-100 dark:border-slate-700 max-h-[90vh]">
            
            <!-- COLONNE GAUCHE : FORMULAIRE -->
            <div class="w-full md:w-3/5 p-8 overflow-y-auto no-scrollbar">
                <h2 id="modal-title" class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Nouveau Forfait WiFi 📦</h2>
                
                <form id="package-form" action="{{ route('forfaits.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Champs cachés pour le mode édition -->
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    <input type="hidden" name="id" id="pkg-id">
                    
                    <!-- INPUT CACHÉ POUR L'ID DE ZONE -->
                    <input type="hidden" name="wifizones_id" id="modal_zone_id">
                    
                    <!-- INFO ZONE SÉLECTIONNÉE (VISUEL UNIQUEMENT) -->
                    <div class="bg-blue-50 dark:bg-slate-800/50 p-3 rounded-xl border border-blue-100 dark:border-slate-700 mb-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-sidebarLight dark:bg-brand-blue flex items-center justify-center text-white">
                            <i class="fas fa-wifi text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold">Zone sélectionnée</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-white" id="modal_zone_name">...</p>
                        </div>
                    </div>
                    
                    <!-- BLOC 1 : APPARENCE -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">1. Détails de l'offre</h3>
                        
                        <div class="input-floating-group mb-4">
                            <input type="text" name="nom" id="pkg-name" placeholder=" " class="input-floating" oninput="updatePreview()" required>
                            <label class="floating-label">Nom du Forfait (ex: Forfait 1H)</label>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2">Couleur d'étiquette</label>
                            <div class="flex gap-3">
                                <label class="cursor-pointer"><input type="radio" name="color_class" value="bg-brand-blue" class="peer hidden" checked onchange="updatePreview()"><div class="w-8 h-8 rounded-full bg-[#0EA5E9] ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-[#0EA5E9] transition"></div></label>
                                <label class="cursor-pointer"><input type="radio" name="color_class" value="bg-red-500" class="peer hidden" onchange="updatePreview()"><div class="w-8 h-8 rounded-full bg-[#EF4444] ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-[#EF4444] transition"></div></label>
                                <label class="cursor-pointer"><input type="radio" name="color_class" value="bg-green-500" class="peer hidden" onchange="updatePreview()"><div class="w-8 h-8 rounded-full bg-[#84CC16] ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-[#84CC16] transition"></div></label>
                                <label class="cursor-pointer"><input type="radio" name="color_class" value="bg-yellow-500" class="peer hidden" onchange="updatePreview()"><div class="w-8 h-8 rounded-full bg-[#F59E0B] ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-[#F59E0B] transition"></div></label>
                                <label class="cursor-pointer"><input type="radio" name="color_class" value="bg-purple-500" class="peer hidden" onchange="updatePreview()"><div class="w-8 h-8 rounded-full bg-[#8B5CF6] ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-[#8B5CF6] transition"></div></label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Limite de temps</label>
                                <div class="flex relative group">
                                    <div class="relative flex-1">
                                        <input type="number" name="limite_temps" id="pkg-time-limit" placeholder=" " class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-l-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition" oninput="updatePreview()" required>
                                    </div>
                                    <div class="relative">
                                        <select name="limite_temps_unite" id="pkg-time-limit-unit" class="h-full flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-l-0 rounded-r-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20 text-sm font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer appearance-none" onchange="updatePreview()">
                                            <option value="minute">Minute</option>
                                            <option value="heure">Heure</option>
                                            <option value="jour">Jour</option>
                                            <option value="mois">Mois</option>
                                        </select>
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none z-30 text-gray-400">
                                            <i class="fas fa-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Validité</label>
                                <div class="flex relative group">
                                    <div class="relative flex-1">
                                        <input type="number" name="validite" id="pkg-duration" placeholder=" " class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-l-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition" oninput="updatePreview()" required>
                                    </div>
                                    <div class="relative">
                                        <select name="validite_unite" id="pkg-duration-unit" class="h-full flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-l-0 rounded-r-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20 text-sm font-bold text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer appearance-none" onchange="updatePreview()">
                                            <option value="minute">Minute</option>
                                            <option value="heure">Heure</option>
                                            <option value="jour">Jour</option>
                                            <option value="mois">Mois</option>
                                        </select>
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none z-30 text-gray-400">
                                            <i class="fas fa-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-floating-group mt-4">
                            <textarea name="description" id="pkg-desc" placeholder=" " class="input-floating h-20 pt-6 resize-none" oninput="updatePreview()" maxlength="100"></textarea>
                            <label class="floating-label">Description courte (Optionnel)</label>
                        </div>

                        <!-- BLOC 3 : INTELLIGENCE DE STOCK (NOUVEAU) -->
                        <div class="pt-4 border-t border-gray-100 dark:border-slate-800">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">3. Intelligence de Stock 🤖</h3>
                            
                            <div class="flex items-center justify-between p-4 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-800/20 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-brand-blue">
                                        <i class="fas fa-magic"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">Génération automatique</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Maintenir le stock sans intervention</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_replenish" id="pkg-auto-replenish" value="1" class="sr-only peer" onchange="updatePreview()">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <p class="text-[11px] text-brand-blue/80 dark:text-blue-400/80 mb-6 px-2 leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i> Si activé, le système génèrera des tickets jusqu'au <b>Quota maximum</b> quand le stock tombe sous le <b>Seuil d'alerte</b>.
                            </p>

                            <div class="flex flex-col gap-4">
                                <div class="w-full">
                                    <div class="input-floating-group">
                                        <input type="number" name="stock_max" id="pkg-stock-max" placeholder=" " class="input-floating" min="1" max="200" oninput="if(value>200)value=200; updatePreview()">
                                        <label class="floating-label">Quota maximum (ex: 150)</label>
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-1 px-2">Limite maximale de tickets en stock (Max 200).</p>
                                </div>
                                <div class="w-full">
                                    <div class="input-floating-group">
                                        <input type="number" name="seuil_alerte" id="pkg-seuil-alerte" placeholder=" " class="input-floating" min="1" max="100" value="15" oninput="updatePreview()">
                                        <label class="floating-label">Seuil d'alerte (%)</label>
                                    </div>
                                    <div id="pkg-threshold-help" class="hidden text-[9px] text-brand-blue/80 dark:text-blue-400/80 mt-1 px-2 font-medium">
                                        Wifipay génère automatiquement de nouveaux tickets quand il reste <span id="pkg-threshold-count" class="font-bold text-brand-blue">2</span> tickets en stock.
                                    </div>
                                    <p id="pkg-threshold-default-help" class="text-[9px] text-gray-400 mt-1 px-2 italic text-left">Déclenche la génération quand il reste X% du quota.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeCreatePackageModal()" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition">Annuler</button>
                        <button type="submit" id="modal-submit-btn" class="bg-brand-sidebarLight dark:bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">Créer le Forfait</button>
                    </div>
                </form>
            </div>

            <!-- COLONNE DROITE : PREVIEW -->
            <div class="hidden md:flex w-2/5 bg-gray-50 dark:bg-[#0f172a] p-8 flex-col items-center justify-center border-l border-gray-200 dark:border-slate-700 relative">
                <h3 class="absolute top-6 left-6 text-xs font-bold text-gray-400 uppercase">Aperçu Portail Client</h3>
                
                <!-- Téléphone Factice -->
                <div class="w-64 h-[480px] bg-white dark:bg-brand-cardDark rounded-[2.5rem] border-8 border-gray-800 dark:border-slate-600 shadow-2xl overflow-hidden relative flex flex-col">
                    <div class="absolute top-0 w-full h-6 bg-gray-800 dark:bg-slate-600 flex justify-center z-20"><div class="w-24 h-4 bg-black rounded-b-xl"></div></div>
                    
                    <div class="flex-1 p-5 pt-12 bg-gray-50 dark:bg-slate-900 overflow-y-auto">
                        <p class="text-center text-sm font-bold text-gray-800 dark:text-white mb-6">Choisissez votre offre</p>
                        
                        <!-- LA CARTE FORFAIT PREVIEW -->
                        <div class="bg-white dark:bg-brand-cardDark rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transform scale-105 transition-all duration-300">
                            <div id="prev-header" class="h-16 bg-[#0EA5E9] flex items-center justify-center transition-colors duration-300">
                                <span id="prev-price" class="text-2xl font-bold text-white tracking-tight">100 FCFA</span>
                            </div>
                            <div class="p-4 text-center">
                                <h4 id="prev-name" class="text-lg font-bold text-gray-800 dark:text-white mb-1">Forfait 1 Heure</h4>
                                <p id="prev-duration" class="text-xs font-bold text-brand-blue uppercase tracking-wide mb-3">Valide 1H</p>
                                <p id="prev-desc" class="text-[10px] text-gray-400 leading-snug">Description du forfait...</p>
                                <button class="w-full mt-4 bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-white py-2 rounded-lg text-xs font-bold">Choisir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- MODALE SUPPRESSION FORFAIT - DEUX OPTIONS -->
    <div id="delete-package-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeletePackageModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-[2.5rem] p-8 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700">
            <!-- Icône -->
            <div class="w-20 h-20 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-trash text-2xl"></i>
            </div>

            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Supprimer le forfait</h3>
            <p id="delete-message" class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">Choisissez une option:</p>
            
            <!-- Option: Nettoyage MikroTik (Synchronisée) -->
            <div class="mb-6 p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 text-left">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" id="clean-mikrotik-check" class="sr-only peer" checked>
                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-brand-blue"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-800 dark:text-white">Supprimer aussi du MikroTik</p>
                    </div>
                </label>
            </div>
            
            <!-- Option 1: Supprimer le forfait + tickets non vendus -->
            <button id="btn-delete-forfait" onclick="confirmDeleteForfait()" class="w-full mb-3 p-4 rounded-xl border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-500">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Supprimer le forfait</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Le forfait et ses tickets seront supprimés de la plateforme et du MikroTik</p>
                    </div>
                </div>
            </button>
            
            <!-- Option 2: Supprimer uniquement les tickets non vendus -->
            <button id="btn-delete-tickets" onclick="confirmDeleteTickets()" class="w-full mb-4 p-4 rounded-xl border border-orange-200 dark:border-orange-800 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-500">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Supprimer les tickets non vendus</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Les tickets non vendus seront supprimés de la plateforme et du MikroTik</p>
                    </div>
                </div>
            </button>

            <button type="button" onclick="closeDeletePackageModal()" class="w-full py-3 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Annuler</button>
            
            <form id="delete-package-form" method="POST" class="hidden">
                @csrf @method('DELETE')
                <input type="hidden" name="clean_mikrotik" id="clean-mikrotik-input" value="1">
            </form>
        </div>
    </div>

    <!-- MODALE SUPPRESSION EN MASSE -->
    <div id="bulk-delete-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBulkDeleteModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-[2.5rem] p-8 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700">
            <!-- Icône dynamique (Danger ou Avertissement) -->
            <div id="bulk-delete-icon-box" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-orange-100 dark:bg-orange-900/30 text-orange-500">
                <i id="bulk-delete-icon" class="fas fa-exclamation-triangle text-2xl"></i>
            </div>

            <h3 id="bulk-delete-title" class="text-xl font-bold text-gray-800 dark:text-white mb-2">Suppression en Masse !</h3>
            <p id="bulk-delete-message" class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed"></p>
            
            <!-- Warning for sold tickets -->
            <div id="bulk-delete-warning" class="hidden bg-red-50 dark:bg-red-900/20 p-4 rounded-xl mb-6 text-left">
                <p id="bulk-delete-warning-text" class="text-xs text-red-600 dark:text-red-400"></p>
            </div>

            <form id="bulk-delete-form" method="POST" onsubmit="event.preventDefault(); submitBulkDelete(this);">
                @csrf @method('DELETE')
                <input type="hidden" id="bulk-delete-date" name="date">
                <input type="hidden" id="bulk-clean-mikrotik-input" name="clean_mikrotik" value="1">

                <!-- Option: Nettoyage MikroTik (Bulk) -->
                <div class="mb-6 p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 text-left">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" id="bulk-clean-mikrotik-check" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-brand-blue"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-gray-800 dark:text-white">Supprimer aussi du MikroTik</p>
                        </div>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeBulkDeleteModal()" class="flex-1 py-3 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Annuler</button>
                    <button type="submit" id="bulk-delete-confirm-btn" class="flex-1 py-3 text-white rounded-xl text-sm font-bold shadow-lg transition bg-orange-600 hover:bg-orange-700">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODALE FORMAT INVALIDE -->
    <div id="invalid-format-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeInvalidFormatModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-lg rounded-[2.5rem] p-8 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700">
            <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500 text-2xl">
                <i class="fas fa-file-excel"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Format de fichier invalide</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-6 text-left">
                <p class="mb-3">Ce système n'accepte que les fichiers CSV exportés depuis Mikrotik.</p>
                <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-lg text-xs">
                    <p class="font-bold mb-2">Format attendu :</p>
                    <code class="text-red-500">Username,Password,Profile,Time Limit,Data Limit,Comment</code>
                </div>
            </div>
            <button onclick="closeInvalidFormatModal()" class="bg-red-500 text-white px-6 py-2 rounded-xl font-bold hover:bg-red-600 transition">
                Compris
            </button>
        </div>
    </div>

    <!-- MODALE PROCESSUS IMPORTATION -->
    <div id="import-process-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
        <!-- Fond -->
        <div class="absolute inset-0 bg-brand-sidebarLight/90 dark:bg-black/90 backdrop-blur-md transition-opacity"></div>
        
        <!-- Carte -->
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden border border-gray-100 dark:border-slate-700 p-8 text-center">
            
            <!-- ÉTAPE 1 : PROGRESSION -->
            <div id="import-step-loading">
                <div class="w-20 h-20 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <i class="fas fa-cog fa-spin text-3xl text-gray-400 absolute"></i>
                    <i class="fas fa-file-csv text-xl text-brand-blue relative z-10"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Traitement en cours...</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Lecture du fichier et création des tickets.</p>
                
                <!-- Barre de progression colorée -->
                <div class="w-full bg-gray-100 dark:bg-slate-700 h-4 rounded-full overflow-hidden mb-2 relative">
                    <div id="import-progress-bar" class="h-full bg-brand-blue rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
                <p id="import-percent" class="text-xs font-bold text-brand-blue text-right">0%</p>
            </div>

            <!-- ÉTAPE 2 : RÉSULTAT INTELLIGENT -->
            <div id="import-step-confirm" class="hidden animate-fade-in text-left">
                <!-- En-tête dynamique -->
                <div class="text-center mb-6">
                    <div id="result-icon-container" class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i id="result-icon" class="fas fa-check text-3xl"></i>
                    </div>
                    <h3 id="result-title" class="text-xl font-bold text-gray-800 dark:text-white mb-1">Analyse Terminée</h3>
                    <p id="result-subtitle" class="text-sm text-gray-500">Voici le rapport d'importation</p>
                </div>

                <!-- Carte Résumé Chiffré -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-xl">
                        <span class="block text-xs text-gray-400 uppercase">Total Lu</span>
                        <span id="res-total" class="font-bold text-gray-800 dark:text-white text-lg">--</span>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-xl">
                        <span class="block text-xs text-orange-500 uppercase">Doublons</span>
                        <span id="res-doublons" class="font-bold text-orange-600 text-lg">--</span>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-xl border border-blue-100 dark:border-blue-800">
                        <span class="block text-xs text-brand-blue uppercase font-bold">Importés</span>
                        <span id="res-net" class="font-bold text-brand-blue text-lg">--</span>
                    </div>
                </div>

                <!-- BOÎTE DE DIAGNOSTIC (S'affiche si erreur) -->
                <div id="diagnostic-box" class="hidden p-4 rounded-xl mb-6 text-sm border-l-4">
                    <h4 class="font-bold mb-1 flex items-center gap-2">
                        <i class="fas fa-search"></i> Diagnostic :
                    </h4>
                    <p id="diagnostic-text" class="opacity-90 leading-relaxed"></p>
                    
                    <!-- Solution proposée -->
                    <div class="mt-3 pt-3 border-t border-black/10">
                        <p class="font-bold text-xs uppercase opacity-70 mb-1">💡 Action recommandée :</p>
                        <p id="diagnostic-fix" class="font-medium"></p>
                    </div>
                </div>

                <div class="space-y-3">
                    <button onclick="finalizeImport()" class="w-full bg-[#114c6c] text-white py-4 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-xl">
                        Terminer et Actualiser
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- MODALE DE CONFIRMATION GÉNÉRIQUE -->
    <div id="confirm-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] hidden">
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 dark:border-slate-700 text-center animate-fade-in">
            <div id="confirm-icon-container" class="w-20 h-20 bg-orange-50 dark:bg-orange-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-orange-500">
                <i id="confirm-icon" class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
            <h3 id="confirm-title" class="text-xl font-bold text-gray-800 dark:text-white mb-2">Confirmation</h3>
            <p id="confirm-message" class="text-sm text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            
            <div class="flex gap-3">
                <button id="confirm-cancel-btn" onclick="closeConfirmModal()" class="flex-1 py-3 text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-xl transition">
                    Annuler
                </button>
                <button id="confirm-proceed-btn" class="flex-1 py-3 text-white rounded-xl text-sm font-bold shadow-lg transition">
                    Confirmer
                </button>
            </div>
        </div>
    </div>

    <!-- MODALE D'ERREUR PERSONNALISÉE (Aussi utilisée pour les Alerts) -->
    <div id="error-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] hidden">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 dark:border-slate-700">
            <div class="text-center">
                <div id="error-icon-container" class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500">
                    <i id="error-icon" class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 id="error-title" class="text-xl font-bold text-gray-800 dark:text-white mb-3">Erreur</h3>
                <p id="error-message" class="text-gray-600 dark:text-gray-300 mb-6">Message d'erreur</p>
                <button onclick="closeErrorModal()" class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-lg">
                    Fermer
                </button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Initialisation avec la valeur du serveur (Filtre actif persisté)
    let currentSelectedZoneId = '{!! $activeZoneId !!}';
    let currentSelectedZoneName = {!! json_encode($activeZoneId != 'all' ? ($wifizones->where("id", $activeZoneId)->first()->nom_zone ?? "Zone") : "Toutes les zones") !!};

    // Initialisation de l'overlay de synchro
    window.addEventListener('DOMContentLoaded', () => {
        updateSyncOverlay(currentSelectedZoneId);
        
        // Initialiser les KPIs de stock
        updateStockKPIs(currentSelectedZoneId);
    });

    function updateSyncOverlay(zoneId) {
        const overlayProfiles = document.getElementById('sync-disabled-overlay');
        const overlayTickets = document.getElementById('sync-tickets-disabled-overlay');
        
        if (zoneId === 'all') {
            if (overlayProfiles) overlayProfiles.style.display = 'flex';
            if (overlayTickets) overlayTickets.style.display = 'flex';
        } else {
            if (overlayProfiles) overlayProfiles.style.display = 'none';
            if (overlayTickets) overlayTickets.style.display = 'none';
        }
    }

    async function syncMikrotikTickets() {
        if (currentSelectedZoneId === 'all') {
            showToast('Veuillez d\'abord sélectionner une zone WiFi', 'info');
            highlightZoneSelector();
            return;
        }

        const btn = document.getElementById('btn-sync-tickets-header');
        const icon = btn.querySelector('.fa-ticket-alt');
        
        // Afficher l'état de chargement
        icon.classList.replace('fa-ticket-alt', 'fa-sync');
        icon.classList.add('fa-spin');
        btn.classList.add('opacity-70', 'pointer-events-none');
        showToast('Synchronisation des tickets en cours...', 'info');

        try {
            const response = await fetch(`/forfaits/sync-tickets/${currentSelectedZoneId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const msg = `Sync terminée ! ${data.details.imported} nouveaux tickets, ${data.details.updated} mis à jour.`;
                showToast(msg, 'success');
                // Recharger la page après un court délai
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showToast(data.message || 'Erreur lors de la synchronisation', 'error');
            }
        } catch (error) {
            console.error('Sync Error:', error);
            showToast('Erreur réseau lors de la synchronisation', 'error');
        } finally {
            icon.classList.remove('fa-spin');
            icon.classList.replace('fa-sync', 'fa-ticket-alt');
            btn.classList.remove('opacity-70', 'pointer-events-none');
        }
    }

    async function syncMikrotikProfiles() {
        if (currentSelectedZoneId === 'all') {
            showToast('Veuillez d\'abord sélectionner une zone WiFi', 'info');
            highlightZoneSelector();
            return;
        }

        const btn = document.getElementById('btn-sync-profiles');
        const icon = btn.querySelector('.fa-sync');
        
        // Afficher l'état de chargement
        icon.classList.add('fa-spin');
        btn.classList.add('opacity-70', 'pointer-events-none');
        showToast('Synchronisation avec MikroTik en cours...', 'info');

        try {
            const response = await fetch(`/forfaits/sync-profiles/${currentSelectedZoneId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                const msg = `Sync terminée ! ${data.data.imported} nouveaux profils importés.`;
                showToast(msg, 'success');
                // Recharger la page après un court délai pour voir les nouveaux forfaits
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Erreur lors de la synchronisation', 'error');
            }
        } catch (error) {
            console.error('Sync Error:', error);
            showToast('Erreur réseau lors de la synchronisation', 'error');
        } finally {
            icon.classList.remove('fa-spin');
            btn.classList.remove('opacity-70', 'pointer-events-none');
        }
    }

    /* --- GESTION DES MODALES PERSONNALISÉES (REMPLACEMENT ALERT/CONFIRM) --- */
    function showErrorModal(title, message) {
        document.getElementById('error-title').innerText = title;
        document.getElementById('error-message').innerText = message;
        document.getElementById('error-modal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    function showConfirmModal(options) {
        const modal = document.getElementById('confirm-modal');
        const title = document.getElementById('confirm-title');
        const message = document.getElementById('confirm-message');
        const proceedBtn = document.getElementById('confirm-proceed-btn');
        const cancelBtn = document.getElementById('confirm-cancel-btn');
        const icon = document.getElementById('confirm-icon');
        const iconContainer = document.getElementById('confirm-icon-container');

        title.innerText = options.title || 'Confirmation';
        message.innerText = options.message || 'Êtes-vous sûr ?';
        proceedBtn.innerText = options.confirmText || 'Confirmer';
        cancelBtn.innerText = options.cancelText || 'Annuler';
        
        // Styles par défaut
        proceedBtn.className = "flex-1 py-3 text-white rounded-xl text-sm font-bold shadow-lg transition " + (options.confirmClass || "bg-brand-blue hover:bg-blue-600");
        icon.className = "fas " + (options.icon || "fa-exclamation-triangle") + " text-3xl";
        iconContainer.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 " + (options.iconBg || "bg-orange-50 dark:bg-orange-900/20 text-orange-500");

        // Action au clic
        proceedBtn.onclick = function() {
            if (options.onConfirm) options.onConfirm();
            closeConfirmModal();
        };

        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
    }

    function confirmDeleteTicket(ticketId) {
        showConfirmModal({
            title: "Supprimer ce ticket ?",
            message: "Cette action est irréversible. Le ticket sera définitivement retiré de la base de données <b>et supprimé du routeur MikroTik</b>.",
            confirmText: "Supprimer",
            confirmClass: "bg-red-500 hover:bg-red-600",
            icon: "fa-trash",
            iconBg: "bg-red-50 dark:bg-red-900/20 text-red-500",
            onConfirm: function() {
                document.getElementById(`delete-ticket-form-${ticketId}`).submit();
            }
        });
    }

    // Initialisation
    document.addEventListener("DOMContentLoaded", function() {
        // Gérer l'onglet actif depuis l'URL (pour la recherche)
        // Chercher l'onglet via paramètre URL ou Hash (#list, #stock)
        const urlParams = new URLSearchParams(window.location.search);
        let tabFromUrl = urlParams.get('tab');
        
        // Si pas en paramètre, essayer le hash
        if (!tabFromUrl && window.location.hash) {
            tabFromUrl = window.location.hash.replace('#', '');
        }
        
        // Initialiser le nom de la zone si un filtre est présent
        if(currentSelectedZoneId !== 'all') {
            const activeOption = document.querySelector(`div[onclick*="selectZoneFilter('${currentSelectedZoneId}'"]`);
            if(activeOption) {
                 const nameSpan = activeOption.querySelector('span:nth-child(2)');
                 if(nameSpan) {
                     currentSelectedZoneName = nameSpan.innerText;
                     document.getElementById('selected-zone-name').innerText = currentSelectedZoneName;
                 }
            }
        }

        const initialTab = tabFromUrl || 'catalogue';
        showTabContent(initialTab);
        updateSyncButtonVisibility(initialTab);
        
        // Appliquer le filtre visuel pour le catalogue
        filterByZone(currentSelectedZoneId);
        
        // Initialiser les KPIs de stock
        updateStockKPIs(currentSelectedZoneId);
    });

    function updateSyncButtonVisibility(tabName) {
        const syncBtnHeader = document.getElementById('btn-sync-tickets-header');
        if (syncBtnHeader) {
            if (tabName === 'list') {
                syncBtnHeader.classList.remove('hidden');
                syncBtnHeader.classList.add('flex');
            } else {
                syncBtnHeader.classList.add('hidden');
                syncBtnHeader.classList.remove('flex');
            }
        }
    }

    function highlightZoneSelector() {
        const btn = document.getElementById('zone-selector-btn');
        if (btn) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            btn.classList.remove('border-transparent');
            btn.classList.add('ring-4', 'ring-red-500/50', 'border-red-500', 'animate-pulse');
            setTimeout(() => {
                btn.classList.remove('ring-4', 'ring-red-500/50', 'border-red-500', 'animate-pulse');
                btn.classList.add('border-transparent');
            }, 2000);
        }
    }

    // --- GESTION DU CUSTOM DROPDOWN (ZONES) ---
    function toggleDropdown() {
        const menu = document.getElementById('zone-dropdown-menu');
        
        // Force la fermeture des autres dropdowns potentiels
        document.querySelectorAll('[id$="-dropdown-menu"]').forEach(d => {
            if(d !== menu) d.classList.add('hidden');
        });
        
        menu.classList.toggle('hidden');
    }

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('zone-dropdown-menu');
        const button = (event.target && typeof event.target.closest === 'function') ? event.target.closest('button[onclick="toggleDropdown()"]') : null;
        
        if (!dropdown.contains(event.target) && !button) {
            dropdown.classList.add('hidden');
        }
    });

    // --- FONCTION DE FILTRAGE PAR ZONE ---
    function filterByZone(zoneId) {
        const items = document.querySelectorAll('.filterable-item');
        let hasResults = false;

        items.forEach(item => {
            const itemZoneId = item.getAttribute('data-zone-id');
            if (zoneId === 'all' || itemZoneId === zoneId) {
                item.style.display = ''; 
                if(item.classList.contains('hidden')) item.classList.remove('hidden');
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });

        const noResultsMsg = document.getElementById('no-results-msg');
        if(noResultsMsg) {
            if(!hasResults && items.length > 0) {
                noResultsMsg.classList.remove('hidden');
            } else {
                noResultsMsg.classList.add('hidden');
            }
        }
    }

    // --- GESTION DES ONGLETS ---
    
    // Fonction interne pour changer le visuel uniquement (sans logique de reload)
    function showTabContent(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'font-bold', 'text-gray-800', 'dark:text-white');
            btn.classList.remove('border-[#072b47]', 'dark:border-[#0EA5E9]');
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        const activeBtn = document.getElementById('tab-' + tabName);
        if(activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('active', 'font-bold', 'text-gray-800', 'dark:text-white');
            activeBtn.classList.add('border-[#072b47]', 'dark:border-[#0EA5E9]');
        }
        const activeContent = document.getElementById('content-' + tabName);
        if(activeContent) activeContent.classList.remove('hidden');
    }

    // Fonction appelée par le clic utilisateur
    function switchTab(tabName) {
        // LOGIQUE SPECIALE POUR L'ONGLET LISTE
        if (tabName === 'list') {
            // On vérifie si on doit recharger la page pour appliquer le filtre de zone
            const urlParams = new URLSearchParams(window.location.search);
            const urlZone = urlParams.get('filter_zone') || 'all';

            // Si la zone sélectionnée (JS) est différente de celle dans l'URL
            // On doit recharger pour avoir les bonnes données
            if (currentSelectedZoneId !== urlZone) {
                let newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', 'list');
                
                if (currentSelectedZoneId === 'all') {
                    newUrl.searchParams.delete('filter_zone');
                } else {
                    newUrl.searchParams.set('filter_zone', currentSelectedZoneId);
                }
                
                window.location.href = newUrl.toString();
                return;
            }
        }

        // Affichage standard
        showTabContent(tabName);

        // --- NOUVEAU : Auto-sélection de la zone dans l'onglet Stock ---
        if (tabName === 'stock' && typeof currentSelectedZoneId !== 'undefined' && currentSelectedZoneId !== 'all') {
            const impZone = document.getElementById('import-zone');
            const histZone = document.getElementById('filter-zone'); // Filtre historique
            
            if (impZone && !impZone.value) {
                impZone.value = currentSelectedZoneId;
                impZone.dispatchEvent(new Event('change'));
            }
            // Pour l'historique, la valeur est le NOM de la zone
            if (histZone && !histZone.value && typeof currentSelectedZoneName !== 'undefined') {
                histZone.value = currentSelectedZoneName;
                histZone.dispatchEvent(new Event('change'));
            }
        }

        // Gérer la visibilité du bouton Sync dans le header
        updateSyncButtonVisibility(tabName);
        
        // Mise à jour de l'URL sans rechargement pour confort (si on n'est pas dans le cas du reload ci-dessus)
        // Cela permet de garder l'état si on rafraichit
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', tabName);
        window.history.pushState({}, '', newUrl);
    }

    // --- GESTION UNIFIÉE DE LA MODALE (CRÉATION / MODIFICATION) ---
    function openPackageModal(mode, data = {}) {
        const modal = document.getElementById('create-package-modal');
        const title = document.getElementById('modal-title');
        const btn = document.getElementById('modal-submit-btn');
        const form = document.getElementById('package-form');
        const methodInput = document.getElementById('form-method');

        if (mode === 'edit') {
            // --- MODE ÉDITION ---
            title.innerText = "Modifier le Forfait";
            btn.innerText = "Sauvegarder les modifications";
            methodInput.value = "PUT"; // Force le mode PUT pour Laravel
            form.action = `/forfaits/${data.id}`; // Route d'update
            
            // Remplissage des champs
            document.getElementById('pkg-id').value = data.id;
            document.getElementById('pkg-name').value = data.nom;
            document.getElementById('pkg-price').value = data.prix;
            
            // Fonction helper pour splitter (ex: "1 heure" ou "1H")
            const parseTimeValue = (str) => {
                if (!str) return { val: '', unit: 'heure' };
                const match = str.match(/^(\d+)\s*(.*)$/);
                if (!match) return { val: str, unit: 'heure' };
                let unit = match[2].toLowerCase().trim();
                if (unit.startsWith('h')) unit = 'heure';
                else if (unit.startsWith('j')) unit = 'jour';
                else if (unit.startsWith('mi')) unit = 'minute';
                else if (unit.startsWith('mo') || unit === 'm') unit = 'mois';
                return { val: match[1], unit: unit || 'heure' };
            };

            const tLimit = parseTimeValue(data.temps_limit);
            document.getElementById('pkg-time-limit').value = tLimit.val;
            document.getElementById('pkg-time-limit-unit').value = tLimit.unit;

            const tValid = parseTimeValue(data.validite);
            document.getElementById('pkg-duration').value = tValid.val;
            document.getElementById('pkg-duration-unit').value = tValid.unit;

            document.getElementById('pkg-desc').value = data.description || '';
            
            // Remplir la zone liée au forfait
            document.getElementById('modal_zone_id').value = data.zone_id;
            document.getElementById('modal_zone_name').innerText = data.zone_name;
            
            // Sélection couleur
            const radio = document.querySelector(`input[name="color_class"][value="${data.color_class}"]`);
            if(radio) radio.checked = true;

        } else {
            // --- MODE CRÉATION ---
            title.innerText = "Nouveau Forfait WiFi";
            btn.innerText = "Créer le Forfait";
            methodInput.value = "POST";
            form.action = "{{ route('forfaits.store') }}";
            form.reset(); // Vide tout
            
            // Réinitialiser les champs spécifiques non reset par reset()
            document.getElementById('pkg-id').value = '';

            // Intelligence de Stock (Default)
            const autoReplenishCheckbox = document.getElementById('pkg-auto-replenish');
            if (autoReplenishCheckbox) autoReplenishCheckbox.checked = false;
            document.getElementById('pkg-stock-max').value = 200;
            document.getElementById('pkg-seuil-alerte').value = 15;
        }

        // --- NOUVEAU : Remplissage des champs de stock (si édition) ---
        if (mode === 'edit') {
            const autoReplenishCheckbox = document.getElementById('pkg-auto-replenish');
            if (autoReplenishCheckbox) {
                autoReplenishCheckbox.checked = !!data.auto_replenish;
            }
            document.getElementById('pkg-stock-max').value = data.stock_max || '';
            document.getElementById('pkg-seuil-alerte').value = data.seuil_alerte || 15;
        }

        updatePreview(); // Rafraîchit le téléphone à droite
        modal.classList.remove('hidden');
    }

    function closeCreatePackageModal() {
        document.getElementById('create-package-modal').classList.add('hidden');
    }

    // --- MODALE CRÉATION (COMPATIBILITÉ) ---
    function openCreatePackageModal() {
        // Si l'utilisateur a filtré sur "Toutes les zones", on lui demande de choisir d'abord
        if (currentSelectedZoneId === 'all') {
            showErrorModal("Zone requise", "Veuillez sélectionner une zone dans le menu en haut avant de créer un forfait");
            return;
        }

        // --- NOUVEAU : Appel centralisé pour réinitialiser la modale ---
        openPackageModal('create');

        // On remplit les champs de la zone (écrasé par openPackageModal s'il y avait un reset)
        document.getElementById('modal_zone_id').value = currentSelectedZoneId;
        document.getElementById('modal_zone_name').textContent = currentSelectedZoneName;

        // Note: openPackageModal('create') a déjà fait le classList.remove('hidden')
    }



    // --- GESTION SUPPRESSION FORFAIT ---
    function toggleDeleteMenu(menuId) {
        const menu = document.getElementById(menuId);
        if (menu.classList.contains('hidden')) {
            // Fermer tous les autres menus
            document.querySelectorAll('[id^="delete-menu-"]').forEach(m => {
                if (m.id !== menuId) m.classList.add('hidden');
            });
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }
    
    // Fermer le menu quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (e.target && typeof e.target.closest === 'function' && !e.target.closest('.relative')) {
            document.querySelectorAll('[id^="delete-menu-"]').forEach(m => m.classList.add('hidden'));
        }
    });
    
    // Variables pour stocker les infos de suppression
    let deleteForfaitId = null;
    let deleteForfaitName = '';
    let deleteTicketsCount = 0;
    
    // Fonction pour afficher le modal avec deux options
    function showDeleteOptionsModal(id, name, count) {
        const modal = document.getElementById('delete-package-modal');
        const btnDeleteForfait = document.getElementById('btn-delete-forfait');
        const btnDeleteTickets = document.getElementById('btn-delete-tickets');
        
        // Stocker les infos
        deleteForfaitId = id;
        deleteForfaitName = name;
        deleteTicketsCount = count;
        
        // Afficher/masquer le bouton de suppression des tickets selon s'il y a des tickets
        if (count > 0) {
            btnDeleteTickets.classList.remove('hidden');
        } else {
            btnDeleteTickets.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
    }
    
    // Confirmer la suppression du forfait
    function confirmDeleteForfait() {
        const form = document.getElementById('delete-package-form');
        const cleanMikroCheck = document.getElementById('clean-mikrotik-check');
        const cleanMikroInput = document.getElementById('clean-mikrotik-input');
        
        if (cleanMikroCheck && cleanMikroInput) {
            cleanMikroInput.value = cleanMikroCheck.checked ? "1" : "0";
        }
        
        form.action = `/forfaits/${deleteForfaitId}`;
        form.submit();
    }
    
    // Confirmer la suppression des tickets non vendus
    function confirmDeleteTickets() {
        // Utiliser le modal de suppression en masse
        closeDeletePackageModal();
        previewBulkDelete('forfait', deleteForfaitId, deleteForfaitName);
    }
    
    function prepDeletePackage(id, name, count, deleteType = 'forfait') {
        // Ancien code - maintenant on utilise showDeleteOptionsModal à la place
        showDeleteOptionsModal(id, name, count);
    }

    function closeDeletePackageModal() {
        document.getElementById('delete-package-modal').classList.add('hidden');
    }

    // --- GESTION SUPPRESSION EN MASSE ---
    async function previewBulkDelete(type, id, name) {
        const modal = document.getElementById('bulk-delete-modal');
        const title = document.getElementById('bulk-delete-title');
        const msg = document.getElementById('bulk-delete-message');
        const warningDiv = document.getElementById('bulk-delete-warning');
        const warningText = document.getElementById('bulk-delete-warning-text');
        const form = document.getElementById('bulk-delete-form');
        const iconBox = document.getElementById('bulk-delete-icon-box');
        const icon = document.getElementById('bulk-delete-icon');

        // Hide warning by default
        warningDiv.classList.add('hidden');

        // Determine endpoint
        let endpoint = '';
        let modalTitle = '';
        let modalMessage = '';
        let hasSoldTickets = false;

        if (type === 'forfait') {
            endpoint = `/tickets/by-forfait/${id}`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> du forfait "<strong>${name}</strong>" ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
            hasSoldTickets = await checkForSoldTickets(id, 'forfait');
        } else if (type === 'zone') {
            endpoint = `/tickets/by-zone/${id}`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> de la zone "<strong>${name}</strong>" ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
            hasSoldTickets = await checkForSoldTickets(id, 'zone');
        } else if (type === 'date') {
            endpoint = `/tickets/by-date`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> créés le <strong>${formatDate(id)}</strong> ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
            hasSoldTickets = await checkForSoldTickets(id, 'date');
            // Set the hidden date input for date deletion
            document.getElementById('bulk-delete-date').value = id;
            // Also add zone filter if selected
            const currentZoneId = document.getElementById('filter-zone')?.value;
            if (currentZoneId && currentZoneId !== 'all') {
                let hiddenZoneInput = document.getElementById('bulk-delete-zone');
                if (!hiddenZoneInput) {
                    hiddenZoneInput = document.createElement('input');
                    hiddenZoneInput.type = 'hidden';
                    hiddenZoneInput.id = 'bulk-delete-zone';
                    hiddenZoneInput.name = 'zone_id';
                    document.getElementById('bulk-delete-form').appendChild(hiddenZoneInput);
                }
                hiddenZoneInput.value = currentZoneId;
            }
        }

        if (hasSoldTickets) {
            warningDiv.classList.remove('hidden');
            warningText.innerText = "Attention : Suppression des tickets non vendus !";
            iconBox.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-red-50 dark:bg-red-900/20 text-red-500";
            icon.className = "fas fa-exclamation-triangle text-2xl";
        } else {
            iconBox.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-orange-100 dark:bg-orange-900/30 text-orange-500";
            icon.className = "fas fa-exclamation-triangle text-2xl";
        }

        form.action = endpoint;
        title.innerText = modalTitle;
        msg.innerHTML = modalMessage;
        modal.classList.remove('hidden');
    }

    // --- SOUMISSION SUPPRESSION EN MASSE AVEC TOAST ---
    async function submitBulkDelete(form) {
        // Update the clean_mikrotik input based on the checkbox
        const cleanMikroCheck = document.getElementById('bulk-clean-mikrotik-check');
        const cleanMikroInput = document.getElementById('bulk-clean-mikrotik-input');
        if (cleanMikroCheck && cleanMikroInput) {
            cleanMikroInput.value = cleanMikroCheck.checked ? "1" : "0";
        }

        const formData = new FormData(form);
        const url = form.action;
        
        console.log('Submitting bulk delete to:', url);
        
        // Add CSRF token and method to FormData for Laravel
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('Response status:', response.status, 'OK:', response.ok);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                // Not JSON response - might be redirect or error page
                closeBulkDeleteModal();
                if (response.ok) {
                    // Success but not JSON - reload page
                    if (typeof showToast === 'function') {
                        showToast('Opération réussie !', 'success');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof showToast === 'function') {
                        showToast('Erreur lors de la suppression', 'error');
                    }
                }
                return;
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            // Fermer le modal
            closeBulkDeleteModal();
            
            if (data.success) {
                // Afficher le toast de succès
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Suppression réussie !', 'success');
                } else {
                    alert(data.message || 'Suppression réussie !');
                }
                // Recharger la page pour mettre à jour l'affichage
                setTimeout(() => window.location.reload(), 1500);
            } else {
                // Afficher le toast d'erreur
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
            closeBulkDeleteModal();
            if (typeof showToast === 'function') {
                showToast('Erreur lors de la connexion au serveur', 'error');
            } else {
                alert('Erreur lors de la connexion au serveur');
            }
        }
    }

    async function checkForSoldTickets(id, type) {
        try {
            let url = `/tickets/preview?type=${type}&id=${id}`;
            if (type === 'date') {
                // For date type, get the current zone ID
                const currentZoneId = document.getElementById('filter-zone')?.value;
                if (currentZoneId && currentZoneId !== 'all') {
                    url += `&zone_id=${currentZoneId}`;
                }
            }
            
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    // Display the actual preview data
                    const warningDiv = document.getElementById('bulk-delete-warning');
                    const warningText = document.getElementById('bulk-delete-warning-text');
                    const msg = document.getElementById('bulk-delete-message');
                    
                    // Update message with actual counts
                    if (type === 'forfait') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> du forfait "<strong>${data.data.forfait_name}</strong>" ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
                    } else if (type === 'zone') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> de la zone "<strong>${data.data.zone_name}</strong>" ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
                    } else if (type === 'date') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> créés le <strong>${formatDate(id)}</strong> ? <br><small class="text-gray-400 font-normal">Ils seront aussi supprimés du MikroTik.</small>`;
                    }
                    
                    // Show warning if there are sold tickets
                    if (data.data.vendu_count > 0) {
                        warningDiv.classList.remove('hidden');
                        warningText.innerHTML = `Attention : <strong>${data.data.vendu_count} ticket(s) ont déjà été vendus</strong> et seront supprimés.`;
                    } else {
                        warningDiv.classList.add('hidden');
                    }
                    
                    return data.data.vendu_count > 0;
                }
            }
        } catch (error) {
            console.error('Error checking tickets:', error);
        }
        return false;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function closeBulkDeleteModal() {
        document.getElementById('bulk-delete-modal').classList.add('hidden');
    }

    function updatePreview() {
        const name = document.getElementById('pkg-name').value || 'Nom du Forfait';
        const price = document.getElementById('pkg-price').value || '0';
        const limiteTemps = document.getElementById('pkg-time-limit').value || '1';
        const limiteTempsUnite = document.getElementById('pkg-time-limit-unit')?.value || 'heure';
        const validite = document.getElementById('pkg-duration').value || '1';
        const validiteUnite = document.getElementById('pkg-duration-unit')?.value || 'heure';
        const desc = document.getElementById('pkg-desc').value || 'Description...';

        // --- LOGIQUE DYNAMIQUE SEUIL ---
        const stockMax = parseInt(document.getElementById('pkg-stock-max').value) || 0;
        const seuilAlerte = parseInt(document.getElementById('pkg-seuil-alerte').value) || 0;
        const isAuto = document.getElementById('pkg-auto-replenish')?.checked;
        
        const thresholdHelp = document.getElementById('pkg-threshold-help');
        const defaultHelp = document.getElementById('pkg-threshold-default-help');
        const thresholdCountSpan = document.getElementById('pkg-threshold-count');

        if (stockMax > 0 && seuilAlerte > 0 && isAuto) {
            const thresholdCount = Math.floor((stockMax * seuilAlerte) / 100);
            if (thresholdCountSpan) thresholdCountSpan.textContent = thresholdCount;
            if (thresholdHelp) thresholdHelp.classList.remove('hidden');
            if (defaultHelp) defaultHelp.classList.add('hidden');
        } else {
            if (thresholdHelp) thresholdHelp.classList.add('hidden');
            if (defaultHelp) defaultHelp.classList.remove('hidden');
        }
        // ------------------------------
        
        // Formater les textes avec unités
        const limiteTempsText = formatUniteTemps(limiteTemps, limiteTempsUnite);
        const validiteText = formatUniteTemps(validite, validiteUnite);
        
        const colorRadio = document.querySelector('input[name="color_class"]:checked');
        const colorClass = colorRadio ? colorRadio.value : 'bg-brand-blue';
        
        const colorMap = {
            'bg-brand-blue': '#0EA5E9',
            'bg-red-500': '#EF4444',
            'bg-green-500': '#84CC16',
            'bg-yellow-500': '#F59E0B',
            'bg-purple-500': '#8B5CF6'
        };
        const activeColor = colorMap[colorClass] || '#0EA5E9';

        document.getElementById('prev-name').textContent = name;
        document.getElementById('prev-price').textContent = price + ' FCFA';
        document.getElementById('prev-duration').textContent = 'Limite: ' + limiteTempsText + ' | Validité: ' + validiteText;
        document.getElementById('prev-desc').textContent = desc;
        document.getElementById('prev-header').style.backgroundColor = activeColor;
    }

    // Fonction pour formater les unités de temps
    function formatUniteTemps(valeur, unite) {
        const valeurNum = parseInt(valeur) || 1;
        const uniteText = valeurNum > 1 ? (unite === 'mois' ? 'mois' : unite + 's') : unite;
        
        // Gérer les abréviations
        const abreviations = {
            'minutes': 'min',
            'minute': 'min',
            'heures': 'H',
            'heure': 'H',
            'jours': 'J',
            'jour': 'J',
            'mois': 'M'
        };
        
        return valeurNum + (abreviations[uniteText] || uniteText);
    }

    /* --- GESTION DE L'INPUT FICHIER (DESIGN) --- */
    const fileInput = document.getElementById('import-file');
    if (fileInput) {
        const dropZone = fileInput.closest('label');
        if (dropZone) {
            const dropText = dropZone.querySelector('p.mb-2');
            const dropIcon = dropZone.querySelector('i');

            // Quand un fichier est choisi via clic
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    updateDropZone(this.files[0].name, dropText, dropIcon, dropZone);
                }
            });

            // Gestion du Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            // Effet visuel au survol
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('border-brand-blue', 'bg-blue-50', 'dark:bg-slate-700');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('border-brand-blue', 'bg-blue-50', 'dark:bg-slate-700');
                }, false);
            });

            // Quand on relâche le fichier
            dropZone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files; // Assigner le fichier à l'input
                    updateDropZone(files[0].name, dropText, dropIcon, dropZone);
                }
            }, false);
        }
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function updateDropZone(fileName, dropText, dropIcon, dropZone) {
        if (dropText) dropText.innerHTML = `<span class="font-bold text-brand-blue">${fileName}</span>`;
        if (dropIcon) dropIcon.className = "fas fa-check-circle text-3xl text-green-500";
        if (dropZone) dropZone.classList.add('border-green-500');
    }

    // --- FILTRAGE DYNAMIQUE DES FORFAITS PAR ZONE ---
    document.addEventListener('DOMContentLoaded', function() {
        const zoneSelect = document.getElementById('import-zone');
        const forfaitSelect = document.getElementById('import-package');
        
        if (zoneSelect && forfaitSelect) {
            zoneSelect.addEventListener('change', function() {
                const selectedZoneId = this.value;
                const options = forfaitSelect.querySelectorAll('option');
                
                // Réinitialiser le select de forfait
                forfaitSelect.value = '';
                
                // Filtrer les options
                options.forEach(function(option) {
                    if (option.value === '') {
                        option.hidden = false;
                        option.disabled = false;
                    } else {
                        const zoneId = option.getAttribute('data-zone-id');
                        const isMatch = (zoneId === selectedZoneId);
                        option.hidden = !isMatch;
                        option.disabled = !isMatch;
                        // Support fallback pour certains navigateurs
                        option.style.display = isMatch ? 'block' : 'none';
                    }
                });
            });
        }
        
        // --- ÉCOUTEURS POUR LES FILTRES DU TABLEAU HISTORIQUE ---
        const filterZone = document.getElementById('filter-zone');
        const filterForfait = document.getElementById('filter-forfait');
        const filterStatut = document.getElementById('filter-statut');
        const filterDate = document.getElementById('filter-date');
        
        // Fonction pour appliquer les filtres
        function applyImportFilters() {
            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        }
        
        // Attacher les écouteurs
        if (filterZone) filterZone.addEventListener('change', applyImportFilters);
        if (filterForfait) filterForfait.addEventListener('change', applyImportFilters);
        if (filterStatut) filterStatut.addEventListener('change', applyImportFilters);
        if (filterDate) filterDate.addEventListener('change', applyImportFilters);
        
        // Initialiser le tableau des imports
        if (typeof initImportsTable === 'function') {
            initImportsTable();
        }
    });

    function closeImportModal() {
        document.getElementById('import-process-modal').classList.add('hidden');
    }

    /* --- IMPORTATION CSV CORRIGÉE --- */
    let importInterval;

    function startImportAnalysis() {
        const zoneId = document.getElementById('import-zone').value;
        const packageId = document.getElementById('import-package').value;
        const fileInput = document.getElementById('import-file');
        
        // NOUVEAU : Récupérer la checkbox (Audit Point D)
        const ignoreDuplicatesCheckbox = document.getElementById('ignore-duplicates'); 
        const ignoreDuplicates = ignoreDuplicatesCheckbox ? ignoreDuplicatesCheckbox.checked : false;

        if (!zoneId || !packageId || fileInput.files.length === 0) {
            showErrorModal("Champs manquants", "Veuillez sélectionner une zone, un forfait et choisir un fichier CSV.");
            return;
        }

        const file = fileInput.files[0];
        const modal = document.getElementById('import-process-modal');
        const stepLoading = document.getElementById('import-step-loading');
        const stepConfirm = document.getElementById('import-step-confirm');
        const invalidModal = document.getElementById('invalid-format-modal'); // Assurez-vous d'avoir cette modale dans le HTML
        const bar = document.getElementById('import-progress-bar');
        const txt = document.getElementById('import-percent');

        // Reset UI
        modal.classList.remove('hidden');
        stepLoading.classList.remove('hidden');
        stepConfirm.classList.add('hidden');
        if(invalidModal) invalidModal.classList.add('hidden');

        // Barre de chargement réelle (Indéterminée)
        bar.style.width = '100%';
        bar.classList.add('animate-pulse');
        txt.innerText = 'Traitement...';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('import-zone', zoneId);
        formData.append('import-package', packageId);
        formData.append('ignore_duplicates', ignoreDuplicates); // Envoi de la checkbox
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("tickets.import") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) {
                // Gérer les erreurs HTTP (422, 403, 500, etc.)
                return response.json().then(data => {
                    throw new Error(data.message || `Erreur ${response.status}: ${response.statusText}`);
                }).catch(() => {
                    // Si la réponse n'est pas du JSON
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            bar.classList.remove('animate-pulse');
            
            // GESTION ERREUR FORMAT (Audit Point C)
            if (data.error_type === 'invalid_format') {
                modal.classList.add('hidden'); // Fermer modale chargement
                // Ouvrir modale format invalide
                const formatModal = document.getElementById('invalid-format-modal');
                if(formatModal) formatModal.classList.remove('hidden');
                else showErrorModal("Format invalide", data.message); // Fallback avec modale personnalisée
                return;
            }

            if (data.success === false) {
                closeImportModal();
                showErrorModal("Erreur d'import", data.message);
                return;
            }

            // SUCCÈS : Affichage des résultats
            bar.style.width = '100%';
            txt.innerText = '100%';
            setTimeout(() => {
                stepLoading.classList.add('hidden');
                stepConfirm.classList.remove('hidden');
                
                // MISE À JOUR DES CHIFFRES (DEBUG LOG)
                console.log("Résultat Import:", data);

                // Données brutes
                const imported = data.imported || 0;
                const duplicates = data.duplicates || 0;
                const profileErrors = data.profile_errors || 0;
                const total = imported + duplicates + profileErrors;

                // Mise à jour des chiffres
                document.getElementById('res-total').innerText = total;
                document.getElementById('res-doublons').innerText = duplicates;
                document.getElementById('res-net').innerText = '+' + imported;

                // --- INTELLIGENCE DU DIAGNOSTIC ---
                const iconContainer = document.getElementById('result-icon-container');
                const icon = document.getElementById('result-icon');
                const title = document.getElementById('result-title');
                const diagBox = document.getElementById('diagnostic-box');
                const diagText = document.getElementById('diagnostic-text');
                const diagFix = document.getElementById('diagnostic-fix');

                // Reset classes
                diagBox.className = "hidden p-4 rounded-xl mb-6 text-sm border-l-4";

                // CAS 1 : SUCCÈS TOTAL
                if (profileErrors === 0 && imported > 0) {
                    iconContainer.className = "w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-green";
                    icon.className = "fas fa-check text-3xl";
                    title.innerText = "Succès !";
                    diagBox.classList.add('hidden');
                }

                // CAS 2 : ERREUR DE PROFIL (Le plus important)
                else if (profileErrors > 0) {
                    iconContainer.className = "w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500";
                    icon.className = "fas fa-times text-3xl";
                    title.innerText = "Importation Bloquée";
                    
                    diagBox.classList.remove('hidden');
                    diagBox.classList.add('bg-red-50', 'text-red-800', 'border-red-500', 'dark:bg-red-900/20', 'dark:text-red-200');
                    
                    diagText.innerHTML = `Le fichier CSV contient le profil <strong>"${data.found_profile}"</strong>, mais le forfait sélectionné attend le profil <strong>"${data.expected_profile}"</strong>.`;
                    diagFix.innerText = "Modifiez le 'Nom du Profile' dans la configuration du forfait pour qu'il corresponde exactement au fichier CSV.";
                }

                // CAS 3 : QUE DES DOUBLONS
                else if (imported === 0 && duplicates > 0) {
                    iconContainer.className = "w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-orange-500";
                    icon.className = "fas fa-exclamation-triangle text-3xl";
                    title.innerText = "Déjà importés";
                    
                    diagBox.classList.remove('hidden');
                    diagBox.classList.add('bg-orange-50', 'text-orange-800', 'border-orange-500', 'dark:bg-orange-900/20', 'dark:text-orange-200');
                    
                    diagText.innerText = `Tous les ${duplicates} tickets de ce fichier existent déjà dans la base de données.`;
                    diagFix.innerText = "Vérifiez que vous n'avez pas déjà importé ce fichier.";
                }

                // CAS 4 : FICHIER VIDE OU AUTRE
                else if (total === 0) {
                    iconContainer.className = "w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-500";
                    icon.className = "fas fa-question text-3xl";
                    title.innerText = "Rien à importer";
                    diagBox.classList.add('hidden');
                }

            }, 500);
        })
        .catch(error => {
            bar.classList.remove('animate-pulse');
            console.error('Import error:', error); // Afficher l'erreur complète pour le diagnostic
            
            // Fermer la modale d'importation
            closeImportModal();
            
            // Extraire le code d'erreur du message (ex: "Erreur 409: Conflict")
            const errorMessage = error.message || '';
            const is409Error = errorMessage.includes('409') || errorMessage.toLowerCase().includes('conflict');
            
            // GESTION SPÉCIFIQUE DOUBLON (409)
            if (is409Error) {
                showErrorModal("Fichier déjà importé", "Ce fichier CSV a déjà été importé avec succès. Si vous souhaitez réimporter, cochez la case 'Ignorer les doublons'.");
            } else {
                showErrorModal("Erreur lors de l'import", errorMessage || "Une erreur système est survenue lors de l'importation du fichier CSV. Veuillez réessayer.");
            }
        });
    }

    function finalizeImport() {
        // Fermer la modale
        closeImportModal();
        
        // Afficher un message de succès
        if(typeof showToast === 'function') {
            const imported = document.getElementById('res-net').innerText;
            showToast(`Importation terminée ! ${imported} tickets ajoutés au stock.`, 'success');
        }
        
        // Optionnel : Recharger la page après un court délai pour voir les données mises à jour
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    function showErrorModal(title, message) {
        document.getElementById('error-title').innerText = title;
        document.getElementById('error-message').innerText = message;
        document.getElementById('error-modal').classList.remove('hidden');
    }

    function closeImportModal() {
        clearInterval(importInterval);
        document.getElementById('import-process-modal').classList.add('hidden');
    }

    function closeInvalidFormatModal() {
        document.getElementById('invalid-format-modal').classList.add('hidden');
    }

    function showInvalidFormatModal() {
        document.getElementById('invalid-format-modal').classList.remove('hidden');
    }

    /* --- KPIs DYNAMIQUES (MISE À JOUR PAR ZONE) --- */
    function updateStockKPIs(zoneId = 'all') {
        const container = document.getElementById('stock-kpi-container');
        
        // Utiliser les vraies données du contrôleur
        const zoneStats = @json($zoneStats);
        const data = zoneStats[zoneId] || zoneStats['all'] || [];
        
        // Trier par urgence (critique -> bas -> moyen -> bon)
        const statusOrder = { 'critical': 0, 'low': 1, 'medium': 2, 'good': 3 };
        data.sort((a, b) => statusOrder[a.status] - statusOrder[b.status]);
        
        // Générer le HTML des KPIs
        let kpisHtml = '';
        data.forEach(item => {
            const statusConfig = {
                'critical': { 
                    border: 'border-red-50 dark:border-red-900/30', 
                    icon: 'fa-exclamation-triangle', 
                    iconBg: 'bg-red-50 dark:bg-red-900/20', 
                    iconColor: 'text-red-500',
                    badge: 'bg-red-100 text-red-600',
                    badgeText: 'CRITIQUE',
                    barColor: 'bg-red-500',
                    textColor: 'text-red-500',
                    hoverBorder: 'hover:border-red-200'
                },
                'low': { 
                    border: 'border-orange-50 dark:border-orange-900/30', 
                    icon: 'fa-battery-quarter', 
                    iconBg: 'bg-orange-50 dark:bg-orange-900/20', 
                    iconColor: 'text-orange-500',
                    badge: 'bg-orange-100 text-orange-600',
                    badgeText: 'BAS',
                    barColor: 'bg-orange-500',
                    textColor: 'text-orange-500',
                    hoverBorder: 'hover:border-orange-200'
                },
                'medium': { 
                    border: 'border-gray-100 dark:border-slate-700', 
                    icon: 'fa-battery-half', 
                    iconBg: 'bg-blue-50 dark:bg-blue-900/20', 
                    iconColor: 'text-brand-blue',
                    badge: 'bg-blue-50 text-brand-blue',
                    badgeText: 'MOYEN',
                    barColor: 'bg-brand-blue',
                    textColor: 'text-brand-blue',
                    hoverBorder: 'hover:border-blue-200'
                },
                'good': { 
                    border: 'border-gray-100 dark:border-slate-700', 
                    icon: 'fa-battery-full', 
                    iconBg: 'bg-green-50 dark:bg-green-900/20', 
                    iconColor: 'text-brand-green',
                    badge: 'bg-green-100 text-brand-green',
                    badgeText: 'OK',
                    barColor: 'bg-brand-green',
                    textColor: 'text-brand-green',
                    hoverBorder: 'hover:border-green-200'
                }
            };

            const config = statusConfig[item.status];
            const animateClass = item.status === 'critical' ? 'animate-pulse' : '';
            
            kpisHtml += `
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border ${config.border} flex flex-col justify-between h-full group ${config.hoverBorder} transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl ${config.iconBg} flex items-center justify-center ${config.iconColor} font-bold text-lg">
                                <i class="fas ${config.icon}"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">${item.name}</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">${item.zone}</p>
                            </div>
                        </div>
                        <span class="${config.badge} px-2 py-1 rounded-lg text-[10px] font-bold ${animateClass}">${config.badgeText}</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold ${config.textColor}">${item.stock} tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="${config.barColor} h-full rounded-full" style="width: ${item.percentage}%"></div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = kpisHtml;
    }

    // Mettre à jour les KPIs quand la zone change
    function selectZoneFilter(id, name) {
        currentSelectedZoneId = id;
        currentSelectedZoneName = name;

        // Mise à jour visuelle du bouton
        document.getElementById('selected-zone-name').textContent = name;

        // Persister le choix en session via AJAX
        fetch('{{ route("forfaits.set-active-zone") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ zone_id: id })
        });
        
        // Mise à jour de l'overlay de synchro
        updateSyncOverlay(id);
        
        // Si on est sur l'onglet Liste des Tickets, appliquer le filtre
        const listTab = document.getElementById('tab-list');
        if (listTab && listTab.classList.contains('active')) {
            // Rediriger vers la page avec le filtre de zone
            const currentUrl = new URL(window.location.href);
            if (id === 'all') {
                currentUrl.searchParams.delete('filter_zone');
            } else {
                currentUrl.searchParams.set('filter_zone', id);
            }
            currentUrl.searchParams.set('tab', 'list');
            window.location.href = currentUrl.toString();
            return;
        }
        
        // Sinon, sur l'onglet Catalogue
        // Appel du filtrage des cartes
        filterByZone(id);

        // Mettre à jour les KPIs de stock
        updateStockKPIs(id);

        // Fermer le menu
        document.getElementById('zone-dropdown-menu').classList.add('hidden');

        // Notification visuelle
        if(typeof showToast === 'function') {
            showToast('Affichage du catalogue : ' + name, 'success');
        } else {
            console.log('Affichage du catalogue : ' + name);
        }
    }

    /* --- FONCTIONS POUR LE TABLEAU D'HISTORIQUE --- */

    /* --- TOGGLE ACTIF/INACTIF FORFAIT --- */
    async function toggleForfaitActive(id, btn) {
        const icon = btn.querySelector('i');
        const card = btn.closest('.filterable-item');
        btn.disabled = true;

        try {
            const res = await fetch(`/forfaits/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (!data.success) throw new Error(data.message);

            const isActive = data.is_active;
            btn.dataset.active = isActive ? '1' : '0';
            icon.className = `fas ${isActive ? 'fa-eye' : 'fa-eye-slash'}`;
            btn.className = `w-11 h-11 flex items-center justify-center rounded-xl text-xs transition-all shadow-sm ${isActive ? 'bg-green-100 dark:bg-green-900/20 text-green-600 hover:bg-red-50 hover:text-red-500' : 'bg-gray-100 dark:bg-slate-700 text-gray-400 hover:bg-green-50 hover:text-green-600'}`;
            btn.title = isActive ? 'Masquer du portail' : 'Afficher sur le portail';

            if (card) card.classList.toggle('opacity-60', !isActive);

            const badgeContainer = card ? card.querySelector('.absolute.top-0.right-0') : null;
            if (badgeContainer) {
                let inactiveBadge = badgeContainer.querySelector('[data-inactive-badge]');
                if (!isActive && !inactiveBadge) {
                    inactiveBadge = document.createElement('div');
                    inactiveBadge.setAttribute('data-inactive-badge', '');
                    inactiveBadge.className = 'bg-gray-500 text-white text-[9px] font-bold px-2 py-1 rounded-bl-lg shadow-sm';
                    inactiveBadge.textContent = 'INACTIF';
                    badgeContainer.prepend(inactiveBadge);
                } else if (isActive && inactiveBadge) {
                    inactiveBadge.remove();
                }
            }

            showToast(data.message, 'success');
        } catch (err) {
            showToast('Erreur lors du changement d\'état', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    /* --- FONCTION TOAST AMÉLIORÉE --- */
    function showToast(message, type = 'success') {
        // 1. Créer le conteneur s'il n'existe pas
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(container);
        }

        // 2. Définir les couleurs selon le type
        let colors, icon;
        switch(type) {
            case 'success':
                colors = 'bg-[#083e5f] text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                break;
            case 'error':
                colors = 'bg-red-500 text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                break;
            case 'warning':
                colors = 'bg-orange-500 text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                break;
            default:
                colors = 'bg-[#083e5f] text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }

        // 3. Créer l'élément Toast
        const toast = document.createElement('div');
        toast.className = `${colors} px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px] border border-white/10`;
        toast.innerHTML = `
            <div class="bg-white/20 rounded-full p-1">${icon}</div>
            <p class="text-sm font-bold whitespace-pre-line">${message}</p>
        `;

        // 4. Ajouter au DOM et Animer
        container.appendChild(toast);
        
        // Animation Entrée
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        });

        // 5. Suppression auto après 10s (plus long pour les warnings)
        const duration = type === 'warning' ? 12000 : 10000;
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, duration);
    }
    
    function sortTable(columnIndex, tableId) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle sort direction
        const currentSort = table.dataset.sortColumn === columnIndex.toString() ? table.dataset.sortDirection : 'asc';
        const newDirection = currentSort === 'asc' ? 'desc' : 'asc';
        
        table.dataset.sortColumn = columnIndex;
        table.dataset.sortDirection = newDirection;
        
        // Sort rows
        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent.trim();
            let bValue = b.cells[columnIndex].textContent.trim();
            
            // Handle numeric values
            if (columnIndex === 4) { // Quantité column
                aValue = parseInt(aValue.replace('+', '')) || 0;
                bValue = parseInt(bValue.replace('+', '')) || 0;
            }
            
            if (aValue < bValue) return newDirection === 'asc' ? -1 : 1;
            if (aValue > bValue) return newDirection === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
        
        // Update sort icons
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            const icon = header.querySelector('i');
            if (icon) {
                if (index === columnIndex) {
                    icon.className = `fas fa-sort-${newDirection === 'asc' ? 'up' : 'down'} text-xs ml-1`;
                } else {
                    icon.className = 'fas fa-sort text-xs ml-1';
                }
            }
        });
    }

    // Initialisation des filtres
    document.addEventListener('DOMContentLoaded', function() {
        // Code pour gérer la couleur "placeholder" des selects (Gris si vide, Noir si rempli)
        document.querySelectorAll('select').forEach(select => {
            // Fonction pour mettre à jour la couleur
            const updateColor = () => {
                if (select.value === "" || select.value === "all") {
                    select.classList.add('text-gray-500', 'dark:text-gray-400');
                    select.classList.remove('text-gray-800', 'dark:text-white');
                } else {
                    select.classList.remove('text-gray-500', 'dark:text-gray-400');
                    select.classList.add('text-gray-800', 'dark:text-white');
                }
            };

            // Appliquer au chargement
            updateColor();

            // Appliquer au changement
            select.addEventListener('change', updateColor);
        });
        
        // Filtrage du tableau d'historique - Utiliser les IDs corrects
        const searchInput = document.querySelector('input[placeholder*="Rechercher un fichier"]');
        const forfaitFilter = document.getElementById('filter-forfait');
        const statutFilter = document.getElementById('filter-statut');
        const zoneFilter = document.getElementById('filter-zone');
        const dateFilter = document.querySelector('input[type="date"]');
        
        function filterTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const forfaitValue = forfaitFilter ? forfaitFilter.value.toLowerCase() : '';
            const statutValue = statutFilter ? statutFilter.value.toLowerCase() : '';
            const zoneValue = zoneFilter ? zoneFilter.value.toLowerCase() : '';
            const dateValue = dateFilter ? dateFilter.value : '';
            
            const rows = document.querySelectorAll('#imports-tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty row
                
                const fileName = row.cells[1].textContent.toLowerCase();
                const zone = row.cells[2].textContent.toLowerCase();
                const forfait = row.cells[3].textContent.toLowerCase();
                const statut = row.cells[5].textContent.toLowerCase();
                const date = row.cells[0].textContent;
                
                let matches = true;
                
                // Search filter
                if (searchTerm && !fileName.includes(searchTerm) && !zone.includes(searchTerm) && !forfait.includes(searchTerm)) {
                    matches = false;
                }
                
                // Zone filter
                if (zoneValue && zone !== zoneValue) {
                    matches = false;
                }
                
                // Forfait filter
                if (forfaitValue && forfait !== forfaitValue) {
                    matches = false;
                }
                
                // Status filter
                if (statutValue && !statut.includes(statutValue)) {
                    matches = false;
                }
                
                // Date filter
                if (dateValue) {
                    const rowDate = new Date(date);
                    const filterDate = new Date(dateValue);
                    if (rowDate.toDateString() !== filterDate.toDateString()) {
                        matches = false;
                    }
                }
                
                row.style.display = matches ? '' : 'none';
            });
        }
        
        // --- NOUVEAU : Dynamisation du select forfait selon la zone ---
        if (zoneFilter && forfaitFilter) {
            zoneFilter.addEventListener('change', function() {
                const selectedZone = this.value; // Nom de la zone
                const forfaitOptions = forfaitFilter.querySelectorAll('option');
                
                // Réinitialiser le forfait sélectionné
                forfaitFilter.value = '';
                
                forfaitOptions.forEach(opt => {
                    if (!opt.value) return; // Garder "Tous les forfaits"
                    
                    const optZone = opt.getAttribute('data-zone');
                    const isVisible = !selectedZone || optZone === selectedZone;
                    
                    opt.hidden = !isVisible;
                    opt.disabled = !isVisible;
                    opt.style.display = isVisible ? 'block' : 'none';
                });
                
                filterTable();
            });
        }
        
        // Add event listeners
        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (forfaitFilter) forfaitFilter.addEventListener('change', filterTable);
        if (statutFilter) statutFilter.addEventListener('change', filterTable);
        if (zoneFilter) zoneFilter.addEventListener('change', filterTable);
        if (dateFilter) dateFilter.addEventListener('change', filterTable);

        // --- NOUVEAU : Dynamisation du filtre forfait dans le grand tableau Liste (Mobile) ---
        const tableZoneFilter = document.getElementById('table-filter-zone');
        const tableForfaitFilter = document.getElementById('table-filter-forfait');
        
        if (tableZoneFilter && tableForfaitFilter) {
            const syncTableForfaits = () => {
                const selectedZoneId = tableZoneFilter.value;
                const options = tableForfaitFilter.querySelectorAll('option');
                
                options.forEach(opt => {
                    if (!opt.value) return; // "Tous les forfaits"
                    
                    const optZoneId = opt.getAttribute('data-zone-id');
                    const isVisible = !selectedZoneId || optZoneId === selectedZoneId;
                    
                    opt.hidden = !isVisible;
                    opt.disabled = !isVisible;
                    opt.style.display = isVisible ? 'block' : 'none';
                });
            };
            
            tableZoneFilter.addEventListener('change', syncTableForfaits);
            // Appliquer au chargement si une zone est déjà sélectionnée
            syncTableForfaits();
        }
    });
</script>

<!-- MODALE OPTIONS DE SUPPRESSION -->
<div id="delete-options-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteOptionsModal()"></div>
    <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-3xl p-6 relative z-10 shadow-2xl border border-gray-100 dark:border-slate-700">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Options de Suppression</h3>
            <button onclick="closeDeleteOptionsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times w-6 h-6"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <!-- Option 1: Supprimer le forfait -->
            <button onclick="prepDeletePackage(deleteOptions.forfaitId, deleteOptions.forfaitName, deleteOptions.ticketsCount, 'forfait')" 
                    class="w-full text-left p-4 rounded-xl border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-red-600 dark:text-red-400">
                        <i class="fas fa-trash"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Supprimer le forfait</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cela supprimera le forfait et tous les tickets non vendus</p>
                    </div>
                </div>
            </button>
            
            <!-- Option 2: Supprimer les tickets non vendus -->
            <button id="delete-unsold-tickets-btn" onclick="previewBulkDelete('forfait', deleteOptions.forfaitId, deleteOptions.forfaitName)" 
                    class="hidden w-full text-left p-4 rounded-xl border border-orange-200 dark:border-orange-800 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Supprimer les tickets non vendus</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Seuls les tickets non vendus seront supprimés</p>
                    </div>
                </div>
            </button>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button onclick="closeDeleteOptionsModal()" class="flex-1 py-3 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                Annuler
            </button>
        </div>
    </div>
</div>

<script>
// Variables globales pour le modal de suppression
let deleteOptions = {
    forfaitId: null,
    forfaitName: '',
    ticketsCount: 0
};

/**
 * Ouvre le modal des options de suppression
 */
function openDeleteOptionsModal(forfaitId, forfaitName, ticketsCount) {
    deleteOptions = {
        forfaitId: forfaitId,
        forfaitName: forfaitName,
        ticketsCount: ticketsCount
    };
    
    // Afficher/masquer le bouton de suppression des tickets non vendus
    const deleteUnsoldBtn = document.getElementById('delete-unsold-tickets-btn');
    if (ticketsCount > 0) {
        deleteUnsoldBtn.classList.remove('hidden');
    } else {
        deleteUnsoldBtn.classList.add('hidden');
    }
    
    document.getElementById('delete-options-modal').classList.remove('hidden');
}

/**
 * Ferme le modal des options de suppression
 */
function closeDeleteOptionsModal() {
    document.getElementById('delete-options-modal').classList.add('hidden');
    deleteOptions = {
        forfaitId: null,
        forfaitName: '',
        ticketsCount: 0
    };
}

</script>
@endsection