@extends('superadmin.super_admin_layout')

@section('title', 'Propriétaires')

@section('page-title', 'Gestion des Propriétaires')

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

    <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
        <!-- HEADER : Titre + Boutons Actions -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Gestion Propriétaires</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Administration de tous les propriétaires de zones WiFi.</p>
            </div>
            
            <div class="flex gap-3">
                <!-- Bouton Export -->
                <button class="bg-white dark:bg-brand-cardDark border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
                
                <!-- Bouton Ajouter Propriétaire -->
                <button onclick="openAddModal()" class="bg-brand-sidebarLight hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Ajouter un Propriétaire
                </button>
            </div>
        </header>
        
        <!-- VUE 1 : LISTE PROPRIÉTAIRES -->
        <div id="list-view" class="animate-fade-in">
                <!-- KPIs Propriétaires -->
                @php
                    $stats = [
                        'total' => $proprios->count(),
                        'active' => $proprios->where('is_active', true)->count(),
                        'inactive' => $proprios->where('is_active', false)->count(),
                        'total_zones' => $proprios->sum(function($p) { return $p->wifizones->count(); }),
                    ];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xl">{{ $stats['total'] }}</div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Total Propriétaires</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">Base active</p></div></div>
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-xl">{{ $stats['active'] }}</div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Actifs</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">Opérationnels</p></div></div>
                    <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4"><div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-xl">{{ $stats['inactive'] }}</div><div><p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Inactifs</p><p class="text-sm font-medium text-gray-600 dark:text-gray-300">En attente</p></div></div>
                </div>
                
                <!-- Tableau Propriétaires -->
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-slate-700">
                        <form method="GET" action="{{ route('superadmin.proprietaires') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Liste des Propriétaires</h3>
                            
                            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                                <!-- Barre de recherche -->
                                <div class="relative flex-1 lg:flex-none">
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           placeholder="Nom, Email, Téléphone..." 
                                           class="pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all">
                                    <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                                </div>

                                <!-- Filtre Statut -->
                                <select name="filter_status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('filter_status') == 'active' ? 'selected' : '' }}>✅ Actifs</option>
                                    <option value="inactive" {{ request('filter_status') == 'inactive' ? 'selected' : '' }}>❌ Inactifs</option>
                                </select>

                                @if(request('search') || request('filter_status'))
                                    <a href="{{ route('superadmin.proprietaires') }}" class="p-2 text-red-400 hover:text-red-600 transition" title="Réinitialiser">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-5">Nom</th>
                                    <th class="p-5">Email</th>
                                    <th class="p-5">Téléphone</th>
                                    <th class="p-5">Zones WiFi</th>
                                    <th class="p-5">Solde</th>
                                    <th class="p-5">Date Création</th>
                                    <th class="p-5">Statut</th>
                                    <th class="p-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                                @forelse($proprios as $proprio)
                                @php
                                    $zonesCount = $proprio->wifizones->count();
                                    $zonesOnline = $proprio->wifizones->where('is_online', true)->count();
                                    $zoneData = json_encode($proprio->wifizones);
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition cursor-pointer" onclick="showDetail({{ $proprio->id }}, '{{ addslashes($proprio->prenom . ' ' . $proprio->nom) }}', '{{ $proprio->email }}', '{{ $proprio->numero }}', '{{ $zonesCount }}', '{{ $zoneData }}', {{ $proprio->is_active ? 'true' : 'false' }})">
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                                {{ substr($proprio->prenom, 0, 1) }}{{ substr($proprio->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-white">{{ $proprio->prenom }} {{ $proprio->nom }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ $proprio->email }}</td>
                                    <td class="p-5 font-mono text-gray-600 dark:text-gray-300">{{ $proprio->numero }}</td>
                                    <td class="p-5">
                                        <span class="bg-brand-blue/10 text-brand-blue px-2 py-1 rounded-lg text-[10px] font-bold">
                                            {{ $zonesCount }} {{ $zonesCount <= 1 ? 'zone' : 'zones' }}
                                            @if($zonesOnline > 0)
                                                <span class="text-green-500">({{ $zonesOnline }} en ligne)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="p-5 font-bold text-brand-blue">{{ number_format(rand(45000, 320000), 0, ',', ' ') }} F</td>
                                    <td class="p-5 text-gray-500 dark:text-gray-400 text-xs">{{ $proprio->created_at->format('d M Y') }}</td>
                                    <td class="p-5">
                                        @if($proprio->is_active)
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold">ACTIF</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-[10px] font-bold">INACTIF</span>
                                        @endif
                                    </td>
                                    <td class="p-5 text-right">
                                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 hover:text-brand-dark">Voir ›</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-gray-400">Aucun propriétaire trouvé.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-2 order-2 md:order-1">
                                <span class="text-xs text-gray-400">Affichage de</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">1</span>
                                <span class="text-xs text-gray-400">à</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $proprios->count() }}</span>
                                <span class="text-xs text-gray-400">sur</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $proprios->count() }}</span>
                                <span class="text-xs text-gray-400">propriétaires</span>
                            </div>
                            <div class="flex items-center gap-2 order-1 md:order-3">
                                <button class="px-3 py-1 bg-gray-50 dark:bg-slate-800 rounded-lg text-xs font-bold text-gray-400">Précédent</button>
                                <button class="px-3 py-1 bg-brand-blue text-white rounded-lg text-xs font-bold">1</button>
                                <button class="px-3 py-1 bg-gray-50 dark:bg-slate-800 rounded-lg text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Suivant</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- VUE 2 : DÉTAIL PROPRIÉTAIRE + SES ZONES -->
            <div id="detail-view" class="hidden animate-fade-in">
                <!-- Bouton retour -->
                <div class="mb-6">
                    <button onclick="showList()" class="flex items-center gap-2 text-gray-500 hover:text-brand-dark transition text-sm font-bold">
                        <i class="fas fa-arrow-left w-4 h-4"></i>Retour à la liste
                    </button>
                </div>
                
                <!-- Card infos propriétaire -->
                <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 rounded-full bg-brand-sidebarLight dark:bg-brand-blue text-white flex items-center justify-center text-2xl font-bold" id="detail-avatar">KA</div>
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white" id="detail-name">Koffi Amani</h2>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <i class="fas fa-envelope w-4 h-4"></i>
                                    <span id="detail-email">koffi@email.com</span>
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <i class="fas fa-phone w-4 h-4"></i>
                                    <span id="detail-phone">225 01 02 03 04 05</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="mb-4">
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">Solde Total</p>
                                <p class="text-4xl font-bold text-brand-blue" id="detail-balance">125 000 F</p>
                            </div>
                            <button id="status-btn" class="px-4 py-2 bg-green-500 text-white rounded-xl text-xs font-bold transition">
                                ACTIF
                            </button>
                        </div>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-4 border-t border-gray-100 dark:border-slate-700 pt-6">
                        <div class="text-center p-4 bg-gray-50 dark:bg-slate-800 rounded-xl">
                            <p class="text-2xl font-bold text-brand-blue" id="detail-zones-count">5</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Zones WiFi</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-slate-800 rounded-xl">
                            <p class="text-2xl font-bold text-green-500">1 250 000 F</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Revenus Totaux</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-slate-800 rounded-xl">
                            <p class="text-2xl font-bold text-yellow-500">125</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Transactions</p>
                        </div>
                    </div>
                </div>
                
                <!-- Section Zones WiFi du propriétaire -->
                <div class="mb-6">
                    <h3 class="font-bold text-xl text-gray-800 dark:text-white mb-4">Zones WiFi de ce propriétaire</h3>
                    
                    <!-- Grille des zones -->
                    <div id="zones-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Les zones seront injectées ici via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Script pour la navigation et l'affichage des zones -->
    <script>
        function showDetail(id, name, email, phone, zonesCount, zonesData, isActive) {
            const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            document.getElementById('detail-avatar').textContent = initials;
            document.getElementById('detail-name').textContent = name;
            document.getElementById('detail-email').textContent = email;
            document.getElementById('detail-phone').textContent = phone;
            document.getElementById('detail-balance').textContent = parseInt('{{ rand(45000, 320000) }}').toLocaleString('fr-FR') + ' F';
            document.getElementById('detail-zones-count').textContent = zonesCount;
            
            const statusBtn = document.getElementById('status-btn');
            if (isActive) {
                statusBtn.textContent = 'ACTIF';
                statusBtn.className = 'px-4 py-2 bg-green-500 text-white rounded-xl text-xs font-bold transition';
            } else {
                statusBtn.textContent = 'INACTIF';
                statusBtn.className = 'px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold transition';
            }
            
            // Afficher les zones
            renderZones(zonesData);
            
            document.getElementById('list-view').classList.add('hidden');
            document.getElementById('detail-view').classList.remove('hidden');
        }

        function showList() {
            document.getElementById('detail-view').classList.add('hidden');
            document.getElementById('list-view').classList.remove('hidden');
        }

        function openAddModal() {
            alert('Fonctionnalité d\'ajout à implémenter');
        }

        function renderZones(zonesData) {
            const grid = document.getElementById('zones-grid');
            grid.innerHTML = '';
            
            try {
                const zones = JSON.parse(zonesData);
                
                if (zones.length === 0) {
                    grid.innerHTML = '<div class="col-span-3 text-center text-gray-400 py-8">Aucune zone WiFi pour ce propriétaire.</div>';
                    return;
                }
                
                zones.forEach(zone => {
                    const isOnline = zone.is_online;
                    
                    // Calculer le stock total à partir des forfaits
                    let totalStock = 0;
                    let totalSalesToday = 0;
                    if (zone.forfaits && zone.forfaits.length > 0) {
                        zone.forfaits.forEach(forfait => {
                            totalStock += forfait.tickets_count || 0;
                            totalSalesToday += forfait.sales_today_count || 0;
                        });
                    }
                    
                    // Valeurs par défaut si pas de forfaits
                    const stockLevel = totalStock || 0;
                    const salesToday = totalSalesToday || 0;
                    
                    // Calculer le pourcentage de stock (supposé max 500 tickets)
                    const maxStock = 500;
                    const stockPercentage = Math.min((stockLevel / maxStock) * 100, 100);
                    
                    // Badge selon le stock
                    let badgeClass = 'bg-green-500';
                    let badgeText = 'STOCK OK';
                    if (stockPercentage < 20) {
                        badgeClass = 'bg-red-500';
                        badgeText = 'STOCK CRITIQUE';
                    } else if (stockPercentage < 50) {
                        badgeClass = 'bg-orange-500';
                        badgeText = 'STOCK FAIBLE';
                    }
                    
                    // Couleur de la barre de progression
                    let progressClass = 'bg-green-500';
                    let stockText = stockLevel + ' tickets';
                    if (stockPercentage < 20) {
                        progressClass = 'bg-red-500';
                    } else if (stockPercentage < 50) {
                        progressClass = 'bg-orange-500';
                    }
                    
                    const zoneCard = `
                        <div class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border border-transparent hover:border-brand-blue/30 transition-all group relative flex flex-col justify-between">
                            <!-- En-tête : Titre + Badge État -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                        <i class="fas fa-wifi text-brand-blue"></i>
                                        ${zone.nom_zone || zone.nom}
                                    </h4>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt w-3 h-3"></i>
                                        ${zone.adresse || 'Adresse non définie'}
                                    </p>
                                </div>
                                <span class="${badgeClass} text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-md">
                                    ${badgeText}
                                </span>
                            </div>

                            <!-- Corps : Ventes & Stock -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ventes jour</span>
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">${salesToday}</span>
                                </div>
                                
                                <!-- Barre de progression -->
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-500 dark:text-gray-400">Niveau de Stock</span>
                                        <span class="font-bold ${stockPercentage < 20 ? 'text-red-500' : 'text-brand-green'}">${stockText}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                        <div class="${progressClass} h-full rounded-full transition-all duration-500" style="width: ${stockPercentage}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    grid.innerHTML += zoneCard;
                });
            } catch (e) {
                console.error('Erreur lors du parsing des zones:', e);
                grid.innerHTML = '<div class="col-span-3 text-center text-gray-400 py-8">Erreur lors du chargement des zones.</div>';
            }
        }
    </script>
@endsection
