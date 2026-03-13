@extends('superadmin.super_admin_layout')

@section('title', 'Retraits')
@section('page-title', 'Gestion des Retraits')

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
                <h2 class="text-3xl font-bold text-white">Gestion des Retraits</h2>
                <p class="text-gray-400 text-sm mt-1">Suivi des demandes de retrait des propriétaires.</p>
            </div>
            
            <div class="flex gap-3">
                <button class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </header>
        
        <!-- KPIs Retraits -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-900/50 flex items-center justify-center text-blue-400 font-bold text-xl">{{ $totalRetraits ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total</p>
                    <p class="text-sm font-medium text-gray-300">Toutes demandes</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-900/50 flex items-center justify-center text-yellow-400 font-bold text-xl">{{ $retraitsEnAttente ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">En Attente</p>
                    <p class="text-sm font-medium text-gray-300">En traitement</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-900/50 flex items-center justify-center text-green-400 font-bold text-xl">{{ $retraitsApprouves ?? 0 }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Approuvés</p>
                    <p class="text-sm font-medium text-gray-300">Payés</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-900/50 flex items-center justify-center text-red-400 font-bold text-xl">{{ number_format($montantTotal ?? 0, 0, ',', ' ') }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total Payé</p>
                    <p class="text-sm font-medium text-gray-300">FCFA</p>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-gray-800 p-4 rounded-xl mb-6">
            <form method="GET" action="{{ route('super_admin.retraits') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <h3 class="font-bold text-lg text-white">Liste des Retraits</h3>
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Barre de recherche -->
                    <div class="relative flex-1 lg:flex-none">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Rechercher..." 
                               class="pl-9 pr-4 py-2 bg-gray-700 text-white border-none rounded-xl text-xs font-medium w-full lg:w-48 focus:ring-2 focus:ring-red-500/20 outline-none">
                        <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    </div>

                    <!-- Filtre Statut -->
                    <select name="statut" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-700 text-gray-300 rounded-xl font-bold cursor-pointer">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="approuve" {{ request('statut') == 'approuve' ? 'selected' : '' }}>✅ Approuvé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>❌ Rejeté</option>
                        <option value="paye" {{ request('statut') == 'paye' ? 'selected' : '' }}>💰 Payé</option>
                    </select>

                    @if(request('search') || request('statut'))
                        <a href="{{ route('super_admin.retraits') }}" class="p-2 text-gray-400 hover:text-red-400 transition" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tableau Retraits -->
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-700 text-gray-400 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-5">ID</th>
                            <th class="p-5">Propriétaire</th>
                            <th class="p-5">Téléphone</th>
                            <th class="p-5">Montant</th>
                            <th class="p-5">Date demande</th>
                            <th class="p-5">Statut</th>
                            <th class="p-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-700">
                        @forelse($retraits ?? [] as $retrait)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="p-5 text-gray-400">#{{ $retrait->id }}</td>
                            <td class="p-5">
                                <p class="font-bold text-white">{{ $retrait->user->name ?? 'N/A' }}</p>
                            </td>
                            <td class="p-5 font-mono text-gray-300">{{ $retrait->user->numero ?? 'N/A' }}</td>
                            <td class="p-5 font-bold text-red-400">{{ number_format($retrait->montant, 0, ',', ' ') }} F</td>
                            <td class="p-5 text-gray-400 text-xs">
                                {{ $retrait->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="p-5">
                                @switch($retrait->statut)
                                    @case('en_attente')
                                        <span class="bg-yellow-900 text-yellow-300 px-2 py-1 rounded-lg text-[10px] font-bold">EN ATTENTE</span>
                                        @break
                                    @case('approuve')
                                        <span class="bg-blue-900 text-blue-300 px-2 py-1 rounded-lg text-[10px] font-bold">APPROUVÉ</span>
                                        @break
                                    @case('rejete')
                                        <span class="bg-red-900 text-red-300 px-2 py-1 rounded-lg text-[10px] font-bold">REJETÉ</span>
                                        @break
                                    @case('paye')
                                        <span class="bg-green-900 text-green-300 px-2 py-1 rounded-lg text-[10px] font-bold">PAYÉ</span>
                                        @break
                                    @default
                                        <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-lg text-[10px] font-bold">{{ $retrait->statut }}</span>
                                @endswitch
                            </td>
                            <td class="p-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('super_admin.retraits.show', $retrait->id) }}" class="p-2 text-gray-400 hover:text-white transition" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($retrait->statut == 'en_attente')
                                        <form action="{{ route('super_admin.retraits.approve', $retrait->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-green-400 hover:text-green-300 transition" title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('super_admin.retraits.reject', $retrait->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-2 text-red-400 hover:text-red-300 transition" title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">Aucun retrait trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($retraits) && $retraits->count() > 0)
            <div class="p-4 border-t border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Affichage de</span>
                        <span class="text-xs font-bold text-white">{{ $retraits->firstItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">à</span>
                        <span class="text-xs font-bold text-white">{{ $retraits->lastItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">sur</span>
                        <span class="text-xs font-bold text-white">{{ $retraits->total() }}</span>
                        <span class="text-xs text-gray-400">retraits</span>
                    </div>
                    {{ $retraits->appends(request()->except('page'))->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
