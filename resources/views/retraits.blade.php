@extends('layout')

@section('content')
<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
    <div id="header-container"></div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8 animate-fade-in">
        <!-- Card Solde Disponible -->
        <div class="bg-brand-sidebarLight dark:bg-slate-800 p-8 rounded-3xl shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue opacity-10 rounded-full blur-3xl -translate-y-10 translate-x-20"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <div></div>
                </div>
                <div class="flex justify-between items-end mb-2">
                    <div><p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Solde Disponible</p><h3 id="solde-display" class="text-5xl font-bold tracking-tight text-white">{{ number_format($balance ?? 0, 0, ',', ' ') }} <span class="text-2xl text-brand-blue font-normal">F</span></h3></div>
                    <div class="bg-white/10 p-3 rounded-xl"><i class="fas fa-wallet w-6 h-6 text-white"></i></div>
                </div>
                <p class="text-xs text-gray-400">Montant disponible pour retrait</p>
            </div>
        </div>

        <!-- Widget Demande de Retrait -->
        <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Demander un retrait</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Virement vers Mobile Money</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-brand-blue">
                    <i class="fas fa-arrow-down w-5 h-5"></i>
                </div>
            </div>

            <!-- Formulaire de retrait -->
            <form id="withdrawal-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Montant à retirer</label>
                    <div class="relative">
                        <input type="number" id="withdraw-amount" name="amount" min="100" max="10000000" placeholder="Montant en F CFA" 
                            class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 pr-12 text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition">
                        <span class="absolute right-4 top-3.5 text-gray-400 font-bold">F</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Opérateur</label>
                    <div class="relative">
                        <select id="withdraw-operator" name="operator" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 pr-8 text-sm font-bold appearance-none focus:ring-2 focus:ring-brand-blue/20 outline-none cursor-pointer transition-all">
                            <option value="mtn">MTN</option>
                            <option value="moov">Moov</option>
                            <option value="celtiis">Celtiis</option>
                        </select>
                        <div class="absolute right-3 top-3.5 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down w-4 h-4"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Numéro de téléphone</label>
                    <input type="tel" id="withdraw-phone" name="phone_number" placeholder="XX XX XX XX" 
                        class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Nom du bénéficiaire</label>
                    <input type="text" id="withdraw-name" name="beneficiary_name" placeholder="Nom complet" 
                        class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Description (optionnel)</label>
                    <input type="text" id="withdraw-description" name="description" placeholder="Motif du retrait" 
                        class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 pl-4 text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition">
                </div>

                <button type="submit" id="withdraw-submit" class="w-full bg-brand-sidebarLight dark:bg-brand-blue text-white py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg flex justify-center items-center gap-2">
                    <i class="fas fa-paper-plane w-4 h-4"></i>
                    Envoyer la demande
                </button>
            </form>
        </div>
    </div>

    <!-- Historique des Retraits -->
    <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden animate-fade-in">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex flex-wrap justify-between items-center gap-4">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Historique des retraits</h3>
            <form method="GET" action="{{ route('retraits') }}" class="flex gap-3">
                <select name="filter_status" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                    <option value="">Tous statuts</option>
                    <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="processing" {{ request('filter_status') == 'processing' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="failed" {{ request('filter_status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                    <option value="cancelled" {{ request('filter_status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
                <input type="date" name="filter_date" value="{{ request('filter_date') }}" onchange="this.form.submit()" class="bg-gray-50 dark:bg-slate-700 dark:text-white border-none text-xs font-bold text-gray-600 dark:text-gray-300 rounded-xl py-2 px-4 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                @if(request('filter_status') || request('filter_date'))
                    <a href="{{ route('retraits') }}" class="p-2 text-red-500 flex items-center hover:bg-red-50 rounded-xl transition">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-5">Référence</th>
                        <th class="p-5">Date</th>
                        <th class="p-5">Nom Momo</th>
                        <th class="p-5">Numéro Momo</th>
                        <th class="p-5">Montant</th>
                        <th class="p-5">Statut</th>
                        <th class="p-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                    @forelse($retraits as $retrait)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition">
                            <td class="p-5 font-mono text-gray-500 dark:text-gray-300 text-xs">
                                {{ $retrait->reference }}
                            </td>
                            <td class="p-5 text-gray-600 dark:text-gray-300">{{ $retrait->created_at->format('d M, H:i') }}</td>
                            <td class="p-5 font-medium text-gray-800 dark:text-white">
                                {{ $retrait->momo_name }}
                            </td>
                            <td class="p-5 text-gray-600 dark:text-gray-300">{{ $retrait->momo_number }}</td>
                            <td class="p-5 font-bold text-gray-800 dark:text-white">{{ number_format($retrait->amount, 0, ',', ' ') }} F</td>
                            <td class="p-5">
                                @switch($retrait->status)
                                    @case('pending')
                                        <span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">⏳ EN ATTENTE</span>
                                        @break
                                    @case('processing')
                                        <span class="bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded-lg text-[10px] font-bold">🔄 EN COURS</span>
                                        @break
                                    @case('completed')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold">✅ TERMINÉ</span>
                                        @break
                                    @case('failed')
                                        <span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-[10px] font-bold">❌ ÉCHOUÉ</span>
                                        @break
                                    @case('cancelled')
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-[10px] font-bold">🚫 ANNULÉ</span>
                                        @break
                                    @default
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-lg text-[10px] font-bold">{{ $retrait->status }}</span>
                                @endswitch
                            </td>
                            <td class="p-5">
                                @if($retrait->isCancellable())
                                    <button onclick="cancelRetrait({{ $retrait->id }})" class="text-red-500 hover:text-red-700 text-xs font-bold">
                                        <i class="fas fa-times mr-1"></i>Annuler
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-gray-400">Aucun retrait trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                <div class="flex justify-center">
                    {{ $retraits->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Configuration AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    /**
     * Soumission du formulaire de retrait
     */
    document.getElementById('withdrawal-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('withdraw-submit');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Traitement...';

        const formData = {
            amount: document.getElementById('withdraw-amount').value,
            operator: document.getElementById('withdraw-operator').value,
            phone_number: document.getElementById('withdraw-phone').value,
            beneficiary_name: document.getElementById('withdraw-name').value,
            description: document.getElementById('withdraw-description').value,
        };

        try {
            const response = await fetch("{{ route('api.retraits.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                // Rafraîchir pour voir le nouveau retrait
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Erreur lors du retrait', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Erreur réseau ou serveur', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    /**
     * Annule un retrait
     */
    async function cancelRetrait(id) {
        if (!confirm('Êtes-vous sûr de vouloir annuler ce retrait ?')) return;

        try {
            const response = await fetch(`/api/retraits/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            showToast('Erreur lors de l\'annulation', 'error');
        }
    }

    /**
     * Affiche une notification toast
     */
    function showToast(message, type = 'info') {
        // Implémentation générique - à adapter selon votre système de notifications
        alert(`${type.toUpperCase()}: ${message}`);
    }
</script>
@endsection
