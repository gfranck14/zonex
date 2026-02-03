@extends('superadmin.layout')

@section('title', 'Propriétaires')
@section('page-title', 'Gestion des Propriétaires')

@section('content')
<div class="space-y-6">
    <!-- Filtres et recherche -->
    <div class="card p-6">
        <form method="GET" action="{{ route('superadmin.proprios.index') }}" class="flex gap-4">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Rechercher (nom, email, numéro...)" 
                class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded focus:border-red-500 focus:outline-none"
            >
            
            <select name="status" class="px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded focus:border-red-500 focus:outline-none">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                <i class="fas fa-search mr-2"></i>Rechercher
            </button>
            
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('superadmin.proprios.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>
    
    <!-- Liste des propriétaires -->
    <div class="card p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 py-3 px-4">ID</th>
                        <th class="text-left text-gray-400 py-3 px-4">Nom Complet</th>
                        <th class="text-left text-gray-400 py-3 px-4">Email</th>
                        <th class="text-left text-gray-400 py-3 px-4">Numéro</th>
                        <th class="text-left text-gray-400 py-3 px-4">Zones</th>
                        <th class="text-left text-gray-400 py-3 px-4">Statut</th>
                        <th class="text-left text-gray-400 py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proprios as $proprio)
                    <tr class="border-b border-gray-800 hover:bg-gray-800">
                        <td class="py-3 px-4 text-gray-400">#{{ $proprio->id }}</td>
                        <td class="py-3 px-4">
                            <div class="text-white font-medium">{{ $proprio->prenom }} {{ $proprio->nom }}</div>
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $proprio->email }}</td>
                        <td class="py-3 px-4 text-gray-300">{{ $proprio->numero }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-blue-900 text-blue-200 rounded text-sm">
                                {{ $proprio->wifizones_count }} {{ $proprio->wifizones_count > 1 ? 'zones' : 'zone' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($proprio->is_active)
                                <span class="px-2 py-1 bg-green-900 text-green-200 rounded text-sm">
                                    <i class="fas fa-check-circle"></i> Actif
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-900 text-red-200 rounded text-sm">
                                    <i class="fas fa-times-circle"></i> Inactif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('superadmin.proprios.show', $proprio->id) }}" 
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <form method="POST" action="{{ route('superadmin.proprios.toggle-active', $proprio->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1 {{ $proprio->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded text-sm">
                                        <i class="fas fa-{{ $proprio->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                
                                @if(Auth::guard('superadmin')->user()->isGod())
                                <form method="POST" action="{{ route('superadmin.proprios.impersonate', $proprio->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm"
                                            onclick="return confirm('Se connecter en tant que {{ $proprio->prenom }} {{ $proprio->nom }}?')">
                                        <i class="fas fa-user-secret"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Aucun propriétaire trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($proprios->hasPages())
        <div class="mt-6">
            {{ $proprios->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
