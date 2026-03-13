@extends('layout')

@section('title', 'Clients')

@section('page-title', 'Gestion des Clients')

@section('content')
    <!-- Messages de succès et d'erreurs (Toast) -->
    @if(session('success'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('success') }}", 'success');
                }
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('error') }}", 'error');
                }
            });
        </script>
    @endif

    <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
        <!-- HEADER : Titre + Boutons Actions -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">

            
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Gestion Clients</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Base de données utilisateurs et fidélité.</p>
            </div>
            
            <div class="flex gap-3">
                <!-- Bouton Export (Existant) -->
                <button class="bg-white dark:bg-brand-cardDark border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
                
                <!-- NOUVEAU : Bouton Ajouter Client -->
                <button onclick="openAddClientModal()" class="bg-brand-sidebarLight hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Ajouter un Client
                </button>
            </div>
        </header>
        
        <!-- VUE 1 : LISTE CLIENTS -->
        <div id="client-list" class="animate-fade-in">
                <!-- KPIs Clients -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xl">{{ $totalClients }}</div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Total Clients</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">Base active</p></div></div>
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">+{{ $nouveauxClients }}</div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Nouveaux (30j)</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">Croissance</p></div></div>
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-xl"><i class="fas fa-gem"></i></div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Clients VIP</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">Dépensent > 10k</p></div></div>
                </div>
                <!-- Tableau Clients -->
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                        <form method="GET" action="{{ route('clients') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <!-- Titre à gauche -->
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Liste des Clients</h3>
                            
                            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                                <!-- Barre de recherche -->
                                <div class="relative flex-1 lg:flex-none">
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           placeholder="Nom, Téléphone..." 
                                           class="pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all">
                                    <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                                </div>

                                <!-- Filtre Statut -->
                                <select name="filter_type" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                    <option value="">Tous les clients</option>
                                    <option value="vip" {{ request('filter_type') == 'vip' ? 'selected' : '' }}>💎 VIP (>10k)</option>
                                    <option value="new" {{ request('filter_type') == 'new' ? 'selected' : '' }}>✨ Nouveaux (30j)</option>
                                    <option value="blocked" {{ request('filter_type') == 'blocked' ? 'selected' : '' }}>🚫 Bloqués</option>
                                </select>

                                <!-- Bouton Reset (si filtres actifs) -->
                                @if(request('search') || request('filter_type'))
                                    <a href="{{ route('clients') }}" class="p-2 text-red-400 hover:text-red-600 transition" title="Réinitialiser">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="client-table" class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-5">Nom</th>
                                    <th class="p-5">Téléphone</th>
                                    <th class="p-5">Dernière Zone</th>
                                    <th class="p-5">Total Dépensé</th>
                                    <th class="p-5">Date Création</th>
                                    <th class="p-5">Statut</th>
                                    <th class="p-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                                @forelse($clients as $client)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition cursor-pointer" 
                                    onclick="showClientDetail({{ $client->id }}, '{{ addslashes($client->nom_complet) }}', '{{ $client->telephone }}', '{{ number_format($client->total_depense_calculated ?? $client->total_depense, 0, ',', ' ') }} F', {{ $client->is_blocked ? 'true' : 'false' }})">
                                    
                                    <!-- Colonne Nom + Avatar -->
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                                {{ substr($client->nom_complet, 0, 2) ?? 'CL' }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-white">{{ $client->nom_complet ?? 'Client Inconnu' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Téléphone -->
                                    <td class="p-5 font-mono text-gray-600 dark:text-gray-300">{{ $client->telephone }}</td>

                                    <!-- Dernière Zone -->
                                    <td class="p-5">
                                        <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-white px-2 py-1 rounded-lg text-[10px] font-bold">
                                            {{ $client->derniere_zone ?? 'Aucune' }}
                                        </span>
                                    </td>

                                    <!-- Total Dépensé -->
                                    <td class="p-5 font-bold text-brand-blue">
                                        {{ number_format($client->total_depense_calculated ?? $client->total_depense, 0, ',', ' ') }} F
                                    </td>

                                    <!-- Date Création -->
                                    <td class="p-5 text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $client->created_at->format('d M Y H:i') }}
                                    </td>

                                    <!-- Statut -->
                                    <td class="p-5">
                                        @if($client->is_blocked)
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-[10px] font-bold">BLOQUÉ</span>
                                        @else
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold">ACTIF</span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="p-5 text-right">
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 hover:text-brand-dark">Voir ›</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400">Aucun client trouvé pour le moment.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer (Style Tickets) -->
                    <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            
                            <!-- GAUCHE : Info Affichage -->
                            <div class="flex items-center gap-2 order-2 md:order-1">
                                <span class="text-xs text-gray-400">Affichage de</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $clients->firstItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">à</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $clients->lastItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">sur</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $clients->total() }}</span>
                                <span class="text-xs text-gray-400">clients</span>
                            </div>
                            
                            <!-- MILIEU : Sélecteur lignes -->
                            <div class="flex items-center gap-2 order-3 md:order-2">
                                <span class="text-xs text-gray-400">Afficher</span>
                                <form method="GET" action="{{ route('clients') }}" class="inline-block">
                                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                                    @if(request('filter_type')) <input type="hidden" name="filter_type" value="{{ request('filter_type') }}"> @endif

                                    <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold p-1 px-2 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 lignes</option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 lignes</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 lignes</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 lignes</option>
                                    </select>
                                </form>
                            </div>
                            
                            <!-- DROITE : Pagination -->
                            <div class="flex items-center gap-2 order-1 md:order-3">
                                {{ $clients->appends(request()->except('page'))->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- VUE 2 : DÉTAIL CLIENT -->
            <div id="client-detail" class="hidden animate-fade-in">
                <div class="mb-6"><button onclick="showClientList()" class="flex items-center gap-2 text-gray-500 hover:text-brand-dark transition text-sm font-bold"><i class="fas fa-arrow-left w-4 h-4"></i>Retour à la liste</button></div>
                <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm mb-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 rounded-full bg-brand-sidebarLight dark:bg-brand-blue text-white flex items-center justify-center text-2xl font-bold" id="detail-avatar">JD</div>
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white" id="detail-name">John Doe</h2>
                                    <button onclick="toggleEditMode()" class="px-3 py-1 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition flex items-center gap-1">
                                        <i class="fas fa-edit w-3 h-3"></i>
                                        Modifier
                                    </button>
                                    <button onclick="showResetPasswordModal()" class="px-3 py-1 bg-blue-100 dark:bg-slate-700 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold hover:bg-blue-200 dark:hover:bg-slate-600 transition flex items-center gap-1">
                                        <i class="fas fa-key w-3 h-3"></i>
                                        Réinitialiser MDP
                                    </button>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <i class="fas fa-phone w-4 h-4"></i>
                                    <span id="detail-phone">229 66 77 88 99</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="mb-4">
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Total Dépensé</p>
                                <p class="text-4xl font-bold text-brand-blue" id="detail-spent">-</p>
                            </div>
                            <button id="block-btn" onclick="toggleBlock()" class="px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold transition">
                                BLOQUER CE CLIENT
                            </button>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">Ce client ne pourra plus acheter de tickets sur vos zones.</p>
                        </div>
                    </div>
                    
                    <!-- FORMULAIRE D'ÉDITION (caché par défaut) -->
                    <div id="edit-form" class="hidden border-t border-gray-100 dark:border-slate-700 pt-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Modifier les informations</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2">Nom / Pseudo</label>
                                <input type="text" id="edit-name" class="w-full px-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-blue/20 outline-none" value="John Doe">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Téléphone</label>
                                <div class="flex relative group">
                                    <!-- ZONE GAUCHE : SÉLECTEUR PAYS -->
                                    <button type="button" onclick="toggleCountryMenu('edit-client')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                                        <img id="edit-client-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Benin">
                                        <span id="edit-client-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                        <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                                    </button>

                                    <!-- MENU DÉROULANT -->
                                    <div id="edit-client-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-brand-cardDark border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                                        <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                            <li onclick="selectCountry('bj', '+229', 'edit-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                                <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                            </li>
                                            <li onclick="selectCountry('tg', '+228', 'edit-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                                <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                            </li>
                                            <li onclick="selectCountry('ci', '+225', 'edit-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                                <img src="https://flagcdn.com/w40/ci.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Côte d'Ivoire (+225)</span>
                                            </li>
                                            <li onclick="selectCountry('sn', '+221', 'edit-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition-colors group">
                                                <img src="https://flagcdn.com/w40/sn.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Sénégal (+221)</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- INPUT -->
                                    <div class="relative flex-1">
                                        <input type="tel" id="edit-client-phone" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition" value="66 77 88 99" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button onclick="saveClientChanges()" class="bg-brand-blue text-white px-6 py-2 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">
                                Sauvegarder
                            </button>
                            <button onclick="cancelEdit()" class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-6 py-2 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                                Annuler
                            </button>
                            <button onclick="confirmDeleteClient()" class="ml-auto bg-red-100 text-red-600 px-6 py-2 rounded-xl text-sm font-bold hover:bg-red-200 transition">
                                <i class="fas fa-trash-alt mr-2"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-800">Historique d'achat <i class="fas fa-receipt text-gray-400"></i></h3></div>
                    <table class="w-full text-left"><thead class="bg-gray-50 text-[10px] uppercase text-gray-400 font-bold"><tr><th class="p-5">Date</th><th class="p-5">Forfait / Ticket</th><th class="p-5">Zone</th><th class="p-5">Adresse MAC</th><th class="h-max p-5">Prix</th><th class="p-5">Ticket</th></tr></thead><tbody id="history-table-body" class="text-sm divide-y divide-gray-50 dark:divide-slate-700"><tr><td colspan="6" class="p-8 text-center text-gray-400">Chargement...</td></tr></tbody></table>
                </div>
            </div>
        </div>
    </main>
    <!-- MODALE SUPPRESSION CLIENT -->
    <div id="delete-client-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteClientModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-sm rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700 animate-scale-in">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Supprimer <span id="delete-match-name" class="text-brand-blue"></span> ?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Cette action est irréversible. Toutes les données associées (historique, etc.) seront perdues.</p>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteClientModal()" class="flex-1 py-3 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                <button onclick="deleteClient()" class="flex-1 py-3 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition shadow-lg">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- MODALE RÉINITIALISER MOT DE PASSE -->
    <div id="reset-password-modal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeResetPasswordModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-sm rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700 animate-scale-in">
            <div class="w-16 h-16 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-key"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Générer un lien de réinitialisation</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Un lien sera généré pour permettre au client de réinitialiser son mot de passe.</p>
            
            <div class="flex gap-3">
                <button onclick="closeResetPasswordModal()" class="flex-1 py-3 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                <button onclick="generateResetLink()" class="flex-1 py-3 bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-600 transition shadow-lg">Générer le lien</button>
            </div>
        </div>
    </div>

    <!-- MODALE LIEN DE RÉINITIALISATION -->
    <div id="reset-link-modal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeResetLinkModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-sm rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700 animate-scale-in">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-link"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Lien généré !</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Copiez ce lien et envoyez-le au client:</p>
            
            <div class="mb-4">
                <input type="text" id="reset-link-input" readonly class="w-full px-3 py-2 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-xl text-xs font-mono text-gray-600 dark:text-gray-300">
            </div>
            
            <div class="flex gap-3">
                <button onclick="copyResetLink()" class="flex-1 py-3 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                    <i class="fas fa-copy mr-1"></i> Copier
                </button>
                <button onclick="closeResetLinkModal()" class="flex-1 py-3 bg-blue-500 text-white rounded-xl font-bold hover:bg-blue-600 transition shadow-lg">Fermer</button>
            </div>
        </div>
    </div>

    <div id="ticket-info-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeTicketModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-xs rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Code du Ticket</h3>
            <div class="space-y-4">
                <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-2xl">
                    <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Utilisateur</p>
                    <p id="t-user" class="text-xl font-mono font-bold text-brand-blue">---</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-slate-800 rounded-2xl">
                    <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Mot de passe</p>
                    <p id="t-pass" class="text-xl font-mono font-bold text-brand-blue">---</p>
                </div>
            </div>
            <button onclick="closeTicketModal()" class="w-full mt-6 py-3 border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Fermer</button>
        </div>
    </div>

    <!-- MODALE AJOUTER CLIENT -->
    <div id="add-client-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeAddClientModal()"></div>
        
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-[2rem] shadow-2xl relative z-10 overflow-hidden border border-gray-100 dark:border-slate-700">
            
            <!-- En-tête -->
            <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Nouveau Client</h3>
                <button onclick="closeAddClientModal()" class="text-gray-400 hover:text-red-500 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('clients.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                
                <!-- Nom Complet -->
                <div class="input-floating-group">
                    <input type="text" name="nom_complet" placeholder=" " class="input-floating" required>
                    <label class="floating-label">Nom Complet</label>
                </div>

                <!-- Téléphone (Clean Design) -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Numéro de téléphone</label>
                    <div class="flex relative group">
                        
                        <!-- ZONE GAUCHE : SÉLECTEUR PAYS -->
                        <button type="button" onclick="toggleCountryMenu('add-client')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                            <img id="add-client-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Benin">
                            <span id="add-client-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                        </button>

                        <!-- MENU DÉROULANT -->
                        <div id="add-client-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-brand-cardDark border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                            <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                <li onclick="selectCountry('bj', '+229', 'add-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                </li>
                                <li onclick="selectCountry('tg', '+228', 'add-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                </li>
                                <li onclick="selectCountry('ci', '+225', 'add-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                    <img src="https://flagcdn.com/w40/ci.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Côte d'Ivoire (+225)</span>
                                </li>
                                <li onclick="selectCountry('sn', '+221', 'add-client')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition-colors group">
                                    <img src="https://flagcdn.com/w40/sn.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Sénégal (+221)</span>
                                </li>
                            </ul>
                        </div>

                        <!-- INPUT -->
                        <div class="relative flex-1">
                            <input 
                                type="tel" 
                                name="telephone" 
                                id="add-client-phone"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 focus:border-brand-blue outline-none transition placeholder-gray-400" 
                                placeholder="XX XX XX XX"
                                required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            >
                            <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
                        </div>
                        <input type="hidden" name="phone_code" id="add-client-phone-code" value="+229">
                    </div>
                </div>

                <!-- Solde Initial (Optionnel) -->
                <div class="input-floating-group">
                    <input type="number" name="total_depense" placeholder=" " class="input-floating" value="0">
                    <label class="floating-label">Dépense Initiale (FCFA)</label>
                </div>

                <!-- Boutons -->
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeAddClientModal()" class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-brand-blue text-white font-bold hover:brightness-110 transition shadow-lg">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script pour ouvrir/fermer la modale -->
    <script>
        function openAddClientModal() {
            document.getElementById('add-client-modal').classList.remove('hidden');
        }
        function closeAddClientModal() {
            document.getElementById('add-client-modal').classList.add('hidden');
        }



        // --- NOUVELLE LOGIQUE GESTION CLIENTS ---
        let currentClientId = null;

        function showClientDetail(id, name, phone, spent, isBlocked) {
            currentClientId = id;
            
            // Remplir les infos
            document.getElementById('detail-avatar').textContent = name.substring(0,2).toUpperCase();
            document.getElementById('detail-name').textContent = name;
            document.getElementById('detail-phone').textContent = phone;
            document.getElementById('detail-spent').textContent = spent;
            
            // Remplir le formulaire d'édition
            document.getElementById('edit-name').value = name;
            
            // Nouveau traitement du téléphone pour l'édition
            // On essaie d'extraire le code pays (ex: "+229") et le numéro
            let cleanPhone = phone.replace(/\s+/g, '');
            let phoneCode = '+229'; // Default
            let phoneNumber = cleanPhone;

            if (cleanPhone.startsWith('+')) {
                // Format: +22966123456
                phoneCode = cleanPhone.substring(0, 4);
                phoneNumber = cleanPhone.substring(4);
            } else if (cleanPhone.length > 8) {
                // Peut-être "22966123456" sans le +
                phoneCode = '+' + cleanPhone.substring(0, 3);
                phoneNumber = cleanPhone.substring(3);
            }

            // Mettre à jour l'UI de l'input pays
            const countryMap = { '+229': 'bj', '+228': 'tg', '+225': 'ci', '+221': 'sn' };
            const cCode = countryMap[phoneCode] || 'bj';
            selectCountry(cCode, phoneCode, 'edit-client');
            document.getElementById('edit-client-phone').value = phoneNumber;

            // UI Bouton Bloquer
            updateBlockButtonUI(isBlocked);

            // Charger l'historique
            loadClientHistory(id);

            // Changer de vue
            document.getElementById('client-list').classList.add('hidden');
            document.getElementById('client-detail').classList.remove('hidden');
            
            // Cacher le formulaire d'édition s'il était ouvert
            document.getElementById('edit-form').classList.add('hidden');
        }

        async function loadClientHistory(clientId) {
            const tbody = document.getElementById('history-table-body');
            tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Chargement...</td></tr>';

            try {
                const response = await fetch(`/clients/${clientId}/history`);
                const data = await response.json();

                if (data.success) {
                    // Mettre à jour le total dépensé
                    if (data.total_spent !== undefined) {
                        document.getElementById('detail-spent').textContent = data.total_spent.toLocaleString('fr-FR') + ' F';
                    }
                    
                    if (data.tickets && data.tickets.length > 0) {
                        tbody.innerHTML = data.tickets.map(t => `
                            <tr>
                                <td class="p-5 text-gray-500">${t.date}</td>
                                <td class="p-5 font-bold text-gray-800">${t.forfait}</td>
                                <td class="p-5 text-gray-500">${t.zone}</td>
                                <td class="p-5 font-mono text-gray-400 text-xs">${t.mac || '-'}</td>
                                <td class="p-5 font-bold text-brand-blue">${t.prix}</td>
                                <td class="p-5">
                                    <button onclick="viewTicket('${t.login}', '${t.password}')" class="px-3 py-1 bg-brand-blue text-white rounded-lg text-[10px] font-bold hover:bg-blue-600 transition">
                                        Voir Ticket
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">Aucun historique d\'achat trouvé.</td></tr>';
                    }
                }
            } catch (e) {
                console.error(e);
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-red-400">Erreur lors du chargement de l\'historique.</td></tr>';
            }
        }

        function showClientList() {
            document.getElementById('client-detail').classList.add('hidden');
            document.getElementById('client-list').classList.remove('hidden');
            currentClientId = null;
        }

        function viewTicket(user, pass) {
            document.getElementById('t-user').textContent = user;
            document.getElementById('t-pass').textContent = pass;
            document.getElementById('ticket-info-modal').classList.remove('hidden');
        }

        function closeTicketModal() {
            document.getElementById('ticket-info-modal').classList.add('hidden');
        }

        function toggleEditMode() {
            const form = document.getElementById('edit-form');
            form.classList.toggle('hidden');
        }

        function cancelEdit() {
            document.getElementById('edit-form').classList.add('hidden');
        }

        function updateBlockButtonUI(isBlocked) {
            const btn = document.getElementById('block-btn');
            if(isBlocked) {
                btn.innerHTML = '<i class="fas fa-unlock"></i> DÉBLOQUER CE CLIENT';
                btn.className = 'px-4 py-2 bg-green-500 text-white rounded-xl text-xs font-bold transition hover:bg-green-600 shadow-md';
            } else {
                btn.innerHTML = '<i class="fas fa-ban"></i> BLOQUER CE CLIENT';
                btn.className = 'px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold transition hover:bg-red-600 shadow-md';
            }
        }

        async function saveClientChanges() {
            if (!currentClientId) return;
            
            const name = document.getElementById('edit-name').value;
            const phone = document.getElementById('edit-client-phone').value;
            const phoneCode = document.getElementById('edit-client-code').textContent;
            const fullPhone = phoneCode + ' ' + phone;
            
            // Feedback visuel
            const btn = event.target; // Le bouton cliqué est l'event target s'il est appelé via onclick inline, sinon attention
            // Pour être sûr, on ne touche pas au bouton ici ou on le récupère via ID si besoin
            
            try {
                const response = await fetch(`/clients/${currentClientId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ nom_complet: name, telephone: fullPhone })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.reload(); // Rafraîchir pour voir les changements
                } else {
                    showToast('Erreur: ' + (data.message || 'Impossible de mettre à jour'), 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur système lors de la mise à jour', 'error');
            }
        }

        async function toggleBlock() {
            if (!currentClientId) return;
            
            try {
                const response = await fetch(`/clients/${currentClientId}/block`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    updateBlockButtonUI(data.is_blocked);
                    showToast(data.message, 'success');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur lors du changement de statut', 'error');
            }
        }
        
        // --- GESTION SUPPRESSION ---
        function confirmDeleteClient() {
            const name = document.getElementById('detail-name').innerText;
            document.getElementById('delete-match-name').innerText = name;
            document.getElementById('delete-client-modal').classList.remove('hidden');
        }

        function closeDeleteClientModal() {
            document.getElementById('delete-client-modal').classList.add('hidden');
        }

        // Fonctions Réinitialiser Mot de Passe
        function showResetPasswordModal() {
            document.getElementById('reset-password-modal').classList.remove('hidden');
        }

        function closeResetPasswordModal() {
            document.getElementById('reset-password-modal').classList.add('hidden');
        }

        function closeResetLinkModal() {
            document.getElementById('reset-link-modal').classList.add('hidden');
        }

        async function generateResetLink() {
            if (!currentClientId) return;
            
            try {
                const response = await fetch(`/clients/${currentClientId}/reset-password-link`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    document.getElementById('reset-link-input').value = data.reset_url;
                    closeResetPasswordModal();
                    document.getElementById('reset-link-modal').classList.remove('hidden');
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Erreur lors de la génération du lien', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur système', 'error');
            }
        }

        function copyResetLink() {
            const copyText = document.getElementById('reset-link-input');
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            showToast('Lien copié !', 'success');
        }

        async function deleteClient() {
            if (!currentClientId) return;

            try {
                const response = await fetch(`/clients/${currentClientId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Client supprimé avec succès', 'success');
                    closeDeleteClientModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Erreur: ' + data.message, 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur système lors de la suppression', 'error');
            }
        }
    </script>
@endsection