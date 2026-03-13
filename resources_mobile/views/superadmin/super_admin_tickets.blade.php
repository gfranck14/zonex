@extends('superadmin.super_admin_layout')

@section('title', 'Tickets')
@section('page-title', 'Gestion des Tickets')

@section('content')
    <!-- Messages de succès et d'erreurs -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-900 border border-green-700 text-green-100 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
    <div class="card p-6">
        <!-- HEADER : Titre + Boutons Actions -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white">Gestion des Tickets</h2>
                <p class="text-gray-400 text-sm mt-1">Suivi de tous les tickets du système.</p>
            </div>
            
            <div class="flex gap-3">
                <button class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </header>
        
        <!-- KPIs Tickets -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-900/50 flex items-center justify-center text-blue-400 font-bold text-xl">{{ \App\Models\Ticket::count() }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Total</p>
                    <p class="text-sm font-medium text-gray-300">Tous tickets</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-900/50 flex items-center justify-center text-green-400 font-bold text-xl">{{ \App\Models\Ticket::where('statut', 'utilise')->count() }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Utilisés</p>
                    <p class="text-sm font-medium text-gray-300">Validés</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-900/50 flex items-center justify-center text-yellow-400 font-bold text-xl">{{ \App\Models\Ticket::where('statut', 'libre')->count() }}</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Libres</p>
                    <p class="text-sm font-medium text-gray-300">Disponibles</p>
                </div>
            </div>
            <div class="bg-gray-800 p-5 rounded-xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-900/50 flex items-center justify-center text-purple-400 font-bold text-xl">{{ number_format(\App\Models\Ticket::count() > 0 ? (\App\Models\Ticket::where('statut', 'utilise')->count() / \App\Models\Ticket::count() * 100) : 0, 0) }}%</div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Taux</p>
                    <p class="text-sm font-medium text-gray-300">Utilisation</p>
                </div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-gray-800 p-4 rounded-xl mb-6">
            <form method="GET" action="{{ route('super_admin.tickets') }}" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <h3 class="font-bold text-lg text-white">Liste des Tickets</h3>
                
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
                        <option value="libre" {{ request('statut') == 'libre' ? 'selected' : '' }}>🔓 Libres</option>
                        <option value="utilise" {{ request('statut') == 'utilise' ? 'selected' : '' }}>🔒 Utilisés</option>
                        <option value="expire" {{ request('statut') == 'expire' ? 'selected' : '' }}>⏰ Expirés</option>
                    </select>

                    @if(request('search') || request('statut'))
                        <a href="{{ route('super_admin.tickets') }}" class="p-2 text-gray-400 hover:text-red-400 transition" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tableau Tickets -->
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-700 text-gray-400 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-5">ID</th>
                            <th class="p-5">Code</th>
                            <th class="p-5">Client</th>
                            <th class="p-5">Forfait</th>
                            <th class="p-5">Zone</th>
                            <th class="p-5">Date achat</th>
                            <th class="p-5">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-700">
                        @forelse($tickets ?? \App\Models\Ticket::with(['client', 'forfait', 'wifizone'])->latest()->paginate(10) as $ticket)
                        <tr class="hover:bg-gray-700 transition">
                            <td class="p-5 text-gray-400">#{{ $ticket->id }}</td>
                            <td class="p-5 font-mono text-white">{{ $ticket->token ?? $ticket->code ?? 'N/A' }}</td>
                            <td class="p-5 text-gray-300">{{ $ticket->client->nom_complet ?? 'N/A' }}</td>
                            <td class="p-5 text-gray-300">{{ $ticket->forfait->nom ?? 'N/A' }}</td>
                            <td class="p-5 text-gray-300">{{ $ticket->wifizone->nom ?? 'N/A' }}</td>
                            <td class="p-5 text-gray-400 text-xs">
                                {{ isset($ticket->created_at) ? $ticket->created_at->format('d M Y H:i') : 'N/A' }}
                            </td>
                            <td class="p-5">
                                @switch($ticket->statut)
                                    @case('libre')
                                        <span class="bg-green-900 text-green-300 px-2 py-1 rounded-lg text-[10px] font-bold">LIBRE</span>
                                        @break
                                    @case('utilise')
                                        <span class="bg-blue-900 text-blue-300 px-2 py-1 rounded-lg text-[10px] font-bold">UTILISÉ</span>
                                        @break
                                    @case('expire')
                                        <span class="bg-red-900 text-red-300 px-2 py-1 rounded-lg text-[10px] font-bold">EXPIRÉ</span>
                                        @break
                                    @default
                                        <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-lg text-[10px] font-bold">{{ $ticket->statut }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">Aucun ticket trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($tickets) && $tickets->count() > 0)
            <div class="p-4 border-t border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">Affichage de</span>
                        <span class="text-xs font-bold text-white">{{ $tickets->firstItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">à</span>
                        <span class="text-xs font-bold text-white">{{ $tickets->lastItem() ?? 0 }}</span>
                        <span class="text-xs text-gray-400">sur</span>
                        <span class="text-xs font-bold text-white">{{ $tickets->total() }}</span>
                        <span class="text-xs text-gray-400">tickets</span>
                    </div>
                    {{ $tickets->appends(request()->except('page'))->links() }}
                </div>
            </div>
            @elseif(!isset($tickets))
            <div class="p-4 border-t border-gray-700">
                <div class="flex justify-center">
                    {{ \App\Models\Ticket::with(['client', 'forfait', 'wifizone'])->latest()->paginate(10)->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
