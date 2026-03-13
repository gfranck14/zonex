@extends('superadmin.super_admin_layout')

@section('title', 'Propriétaires')
@section('page-title', 'Gestion des Propriétaires')

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

<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
    <div class="card p-6">
        <!-- HEADER : Titre + Boutons Actions -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white">Gestion des Propriétaires</h2>
                <p class="text-gray-400 text-sm mt-1">Administration des comptes propriétaires.</p>
            </div>
            
            <div class="flex gap-3">
                <button class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
                
                <button onclick="openAddProprioModal()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
            </div>
        </header>
        
        <!-- KPIs Propriétaires -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-900/50 flex items-center justify-center text-blue-400 font-bold text-xl">{{ $totalProprios ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total</p>
                    <p class="text-sm font-medium text-gray-300">Propriétaires</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-900/50 flex items-center justify-center text-green-400 font-bold text-xl">{{ $actifs ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Actifs</p>
                    <p class="text-sm font-medium text-gray-300">En activité</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-900/50 flex items-center justify-center text-yellow-400 font-bold text-xl">{{ $inactifs ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Inactifs</p>
                    <p class="text-sm font-medium text-gray-300">Suspendus</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-900/50 flex items-center justify-center text-purple-400 font-bold text-xl">{{ number_format($revenuTotal ?? 0, 0, ',', ' ') }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Revenus</p>
                    <p class="text-sm font-medium text-gray-300">Total (FCFA)</p>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-gray-800 p-4 rounded-xl mb-6">
            <form method="GET" action="{{ route('super_admin.proprios') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <h3 class="font-bold text-lg text-white">Liste des Propriétaires</h3>
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Barre de recherche -->
                    <div class="relative flex-1 lg:flex-none">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nom, Email, Zone..." 
                               class="pl-9 pr-4 py-2 bg-gray-700 text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-red-500/20 outline-none">
                        <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    </div>

                    <!-- Filtre Statut -->
                    <select name="statut" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-700 text-gray-300 rounded-xl font-bold cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>✅ Actifs</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>❌ Inactifs</option>
                    </select>

                    @if(request('search') || request('statut'))
                        <a href="{{ route('super_admin.proprios') }}" class="p-2 text-gray-400 hover:text-red-400 transition" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tableau Propriétaires -->
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-700 text-gray-400 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-5">Nom</th>
                            <th class="p-5">Email</th>
                            <th class="p-5">Téléphone</th>
                            <th class="p-5">Zones</th>
                            <th class="p-5">Revenus</th>
                            <th class="p-5">Date inscription</th>
                            <th class="p-5">Statut</th>
                            <th class="p-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-700">
                        @forelse($proprios ?? [] as $proprio)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 text-gray-300 flex items-center justify-center font-bold text-xs">
                                        {{ substr($proprio->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <p class="font-bold text-white">{{ $proprio->name ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="p-5 text-gray-300">{{ $proprio->email ?? 'N/A' }}</td>
                            <td class="p-5 font-mono text-gray-300">{{ $proprio->numero ?? 'N/A' }}</td>
                            <td class="p-5 text-gray-300">
                                {{ $proprio->zones->count() ?? 0 }} zone(s)
                            </td>
                            <td class="p-5 font-bold text-green-400">{{ number_format($proprio->revenus ?? 0, 0, ',', ' ') }} F</td>
                            <td class="p-5 text-gray-400 text-xs">
                                {{ isset($proprio->created_at) ? $proprio->created_at->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="p-5">
                                @if(isset($proprio->is_active) && $proprio->is_active)
                                    <span class="bg-green-900 text-green-300 px-2 py-1 rounded-lg text-[10px] font-bold">ACTIF</span>
                                @else
                                    <span class="bg-red-900 text-red-300 px-2 py-1 rounded-lg text-[10px] font-bold">INACTIF</span>
                                @endif
                            </td>
                            <td class="p-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('super_admin.proprios.show', $proprio->id) }}" class="p-2 text-gray-400 hover:text-white transition" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(isset($proprio->is_active) && $proprio->is_active)
                                        <form action="{{ route('super_admin.proprios.toggle', $proprio->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-yellow-400 hover:text-yellow-300 transition" title="Désactiver">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('super_admin.proprios.toggle', $proprio->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-green-400 hover:text-green-300 transition" title="Activer">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
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
            
            <!-- Pagination -->
            @if(isset($proprios) && $proprios->count() > 0)
            <div class="p-4 border-t border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Affichage de</span>
                        <span class="text-xs font-bold text-white">{{ $proprios->firstItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">à</span>
                        <span class="text-xs font-bold text-white">{{ $proprios->lastItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">sur</span>
                        <span class="text-xs font-bold text-white">{{ $proprios->total() }}</span>
                        <span class="text-xs text-gray-400">propriétaires</span>
                    </div>
                    {{ $proprios->appends(request()->except('page'))->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modal Ajouter Propriétaire (simplifié) -->
    <div id="add-proprio-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAddProprioModal()"></div>
        <div class="bg-gray-800 w-full max-w-md rounded-2xl p-6 relative z-10 shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-4">Nouveau Propriétaire</h3>
            <form action="{{ route('admin.proprios.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2">Nom</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-2">Téléphone</label>
                    <input type="tel" name="numero" required class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddProprioModal()" class="flex-1 py-2 bg-gray-700 text-gray-300 rounded-xl font-bold hover:bg-gray-600 transition">Annuler</button>
                    <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddProprioModal() {
            document.getElementById('add-proprio-modal').classList.remove('hidden');
        }
        function closeAddProprioModal() {
            document.getElementById('add-proprio-modal').classList.add('hidden');
        }
    </script>
</div>
@endsection