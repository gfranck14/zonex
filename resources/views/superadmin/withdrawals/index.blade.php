@extends('superadmin.layout')

@section('title', 'Retraits')
@section('page-title', 'Validation des Retraits')

@section('content')
<div class="space-y-6">
    <!-- Filtres -->
    <div class="card p-6">
        <div class="flex gap-4">
            <a href="{{ route('superadmin.withdrawals.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 {{ request('status', 'pending') === 'pending' ? 'bg-red-600' : 'bg-gray-700 hover:bg-gray-600' }} text-white rounded">
                <i class="fas fa-clock mr-2"></i>En Attente
            </a>
            <a href="{{ route('superadmin.withdrawals.index', ['status' => 'approved']) }}" 
               class="px-4 py-2 {{ request('status') === 'approved' ? 'bg-red-600' : 'bg-gray-700 hover:bg-gray-600' }} text-white rounded">
                <i class="fas fa-check mr-2"></i>Approuvés
            </a>
            <a href="{{ route('superadmin.withdrawals.index', ['status' => 'reject']) }}" 
               class="px-4 py-2 {{ request('status') === 'rejected' ? 'bg-red-600' : 'bg-gray-700 hover:bg-gray-600' }} text-white rounded">
                <i class="fas fa-times mr-2"></i>Rejetés
            </a>
        </div>
    </div>
    
    <!-- Liste des retraits -->
    <div class="card p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 py-3 px-4">ID</th>
                        <th class="text-left text-gray-400 py-3 px-4">Propriétaire</th>
                        <th class="text-left text-gray-400 py-3 px-4">Montant</th>
                        <th class="text-left text-gray-400 py-3 px-4">Méthode</th>
                        <th class="text-left text-gray-400 py-3 px-4">Date Demande</th>
                        <th class="text-left text-gray-400 py-3 px-4">Statut</th>
                        <th class="text-left text-gray-400 py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                    <tr class="border-b border-gray-800 hover:bg-gray-800">
                        <td class="py-3 px-4 text-gray-400">#{{ $withdrawal->id }}</td>
                        <td class="py-3 px-4">
                            <div class="text-white font-medium">
                                {{ $withdrawal->proprio->prenom ?? '' }} {{ $withdrawal->proprio->nom ?? '' }}
                            </div>
                            <div class="text-sm text-gray-400">{{ $withdrawal->proprio->email ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-green-400 font-bold text-lg">
                                {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $withdrawal->method ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-gray-300">
                            {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($withdrawal->status === 'pending')
                                <span class="px-2 py-1 bg-yellow-900 text-yellow-200 rounded text-sm">
                                    <i class="fas fa-clock"></i> En attente
                                </span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="px-2 py-1 bg-green-900 text-green-200 rounded text-sm">
                                    <i class="fas fa-check"></i> Approuvé
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-900 text-red-200 rounded text-sm">
                                    <i class="fas fa-times"></i> Rejeté
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($withdrawal->status === 'pending' && (Auth::guard('superadmin')->user()->isGod() || Auth::guard('superadmin')->user()->hasRole('admin')))
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('superadmin.withdrawals.approve', $withdrawal->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm"
                                            onclick="return confirm('Approuver ce retrait de {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA?')">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                </form>
                                
                                <button type="button" 
                                        onclick="openRejectModal({{ $withdrawal->id }})"
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                    <i class="fas fa-times"></i> Rejeter
                                </button>
                            </div>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Aucun retrait {{ $status }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($withdrawals->hasPages())
        <div class="mt-6">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Rejet (simplifié) -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-white mb-4">Rejeter le retrait</h3>
        
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Raison du rejet</label>
                <textarea 
                    name="rejection_reason" 
                    required
                    rows="4"
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded focus:border-red-500 focus:outline-none"
                    placeholder="Expliquez la raison du rejet..."></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    Rejeter
                </button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = '/god-admin/withdrawals/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
@endsection
