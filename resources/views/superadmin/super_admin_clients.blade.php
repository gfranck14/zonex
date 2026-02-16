@extends('superadmin.super_admin_layout')

@section('title', 'Clients')
@section('page-title', 'Gestion des Clients')

@section('content')
    <!-- Messages de succès et d'erreurs -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-900 border border-green-700 text-green-100 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-900 border border-red-700 text-red-100 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="card p-6">
        <!-- HEADER : Titre + Boutons Actions -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white">Gestion Clients</h2>
                <p class="text-gray-400 text-sm mt-1">Base de données utilisateurs et fidélité.</p>
            </div>
            
            <div class="flex gap-3">
                <!-- Bouton Export -->
                <button class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
                
                <!-- Bouton Ajouter Client -->
                <button onclick="openAddClientModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Ajouter un Client
                </button>
            </div>
        </header>
        
        <!-- KPIs Clients -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-900/50 flex items-center justify-center text-purple-400 font-bold text-xl">{{ $totalClients ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Clients</p>
                    <p class="text-sm font-medium text-gray-300">Base active</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-900/50 flex items-center justify-center text-blue-400 font-bold text-xl">+{{ $nouveauxClients ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Nouveaux (30j)</p>
                    <p class="text-sm font-medium text-gray-300">Croissance</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-900/50 flex items-center justify-center text-yellow-400 font-bold text-xl"><i class="fas fa-gem"></i></div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Clients VIP</p>
                    <p class="text-sm font-medium text-gray-300">Dépensent > 10k</p>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-gray-800 p-4 rounded-xl mb-6">
            <form method="GET" action="{{ route('super_admin.clients') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <h3 class="font-bold text-lg text-white">Liste des Clients</h3>
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Barre de recherche -->
                    <div class="relative flex-1 lg:flex-none">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nom, Téléphone..." 
                               class="pl-9 pr-4 py-2 bg-gray-700 text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-red-500/20 outline-none">
                        <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    </div>

                    <!-- Filtre Statut -->
                    <select name="filter_type" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-700 text-gray-300 rounded-xl font-bold cursor-pointer">
                        <option value="">Tous les clients</option>
                        <option value="vip" {{ request('filter_type') == 'vip' ? 'selected' : '' }}>💎 VIP (>10k)</option>
                        <option value="new" {{ request('filter_type') == 'new' ? 'selected' : '' }}>✨ Nouveaux (30j)</option>
                        <option value="blocked" {{ request('filter_type') == 'blocked' ? 'selected' : '' }}>🚫 Bloqués</option>
                    </select>

                    @if(request('search') || request('filter_type'))
                        <a href="{{ route('super_admin.clients') }}" class="p-2 text-gray-400 hover:text-red-400 transition" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tableau Clients -->
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-700 text-gray-400 text-[10px] uppercase font-bold tracking-wider">
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
                    <tbody class="text-sm divide-y divide-gray-700">
                        @forelse($clients ?? [] as $client)
                        <tr class="hover:bg-gray-700 transition cursor-pointer">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 text-gray-300 flex items-center justify-center font-bold text-xs">
                                        {{ substr($client->nom_complet ?? 'Client Inconnu', 0, 2) }}
                                    </div>
                                    <p class="font-bold text-white">{{ $client->nom_complet ?? 'Client Inconnu' }}</p>
                                </div>
                            </td>
                            <td class="p-5 font-mono text-gray-300">{{ $client->telephone ?? 'N/A' }}</td>
                            <td class="p-5">
                                <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-lg text-[10px] font-bold">
                                    {{ $client->derniere_zone ?? 'Aucune' }}
                                </span>
                            </td>
                            <td class="p-5 font-bold text-red-400">
                                {{ number_format($client->total_depense ?? 0, 0, ',', ' ') }} F
                            </td>
                            <td class="p-5 text-gray-400 text-xs">
                                {{ isset($client->created_at) ? $client->created_at->format('d M Y H:i') : 'N/A' }}
                            </td>
                            <td class="p-5">
                                @if(isset($client->is_blocked) && $client->is_blocked)
                                    <span class="bg-red-900 text-red-300 px-2 py-1 rounded-lg text-[10px] font-bold">BLOQUÉ</span>
                                @else
                                    <span class="bg-green-900 text-green-300 px-2 py-1 rounded-lg text-[10px] font-bold">ACTIF</span>
                                @endif
                            </td>
                            <td class="p-5 text-right">
                                <button class="text-gray-400 hover:text-white transition">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">Aucun client trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($clients) && $clients->count() > 0)
            <div class="p-4 border-t border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Affichage de</span>
                        <span class="text-xs font-bold text-white">{{ $clients->firstItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">à</span>
                        <span class="text-xs font-bold text-white">{{ $clients->lastItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">sur</span>
                        <span class="text-xs font-bold text-white">{{ $clients->total() }}</span>
                        <span class="text-xs text-gray-400">clients</span>
                    </div>
                    {{ $clients->appends(request()->except('page'))->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modal Ajouter Client (simplifié) -->
    <div id="add-client-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddClientModal()"></div>
        <div class="bg-gray-800 w-full max-w-md rounded-2xl p-6 relative z-10 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Nouveau Client</h3>
            <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2">Nom Complet</label>
                    <input type="text" name="nom_complet" required class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2">Téléphone</label>
                    <input type="tel" name="telephone" required class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddClientModal()" class="flex-1 py-2 bg-gray-700 text-gray-300 rounded-xl font-bold hover:bg-gray-600 transition">Annuler</button>
                    <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddClientModal() {
            document.getElementById('add-client-modal').classList.remove('hidden');
        }
        function closeAddClientModal() {
            document.getElementById('add-client-modal').classList.add('hidden');
        }
    </script>
@endsection
