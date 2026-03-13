@extends('superadmin.super_admin_layout')

@section('title', 'Retraits')

@section('page-title', 'Gestion des Retraits')

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
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Gestion des Retraits</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Traitement des demandes de retrait des propriétaires.</p>
            </div>
            
            <div class="flex gap-3">
                <!-- Bouton Export -->
                <button class="bg-white dark:bg-brand-cardDark border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </header>
        
        <!-- KPIs Retraits -->
        @php
            $stats = [
                'pending_count' => $retraits->where('status', 'pending')->count(),
                'pending_amount' => $retraits->where('status', 'pending')->sum('amount'),
                'completed_count' => $retraits->where('status', 'completed')->count(),
                'completed_amount' => $retraits->where('status', 'completed')->sum('amount'),
                'total_sent' => $retraits->where('status', 'completed')->sum('amount'),
                'total_commission' => $retraits->where('status', 'completed')->sum('ccorp_fee'),
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-xl">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">En attente</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['pending_count'] }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($stats['pending_amount'], 0, ',', ' ') }} F</p>
                </div>
            </div>
            <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-xl">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Total envoyés</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_sent'], 0, ',', ' ') }}</p>
                    <p class="text-xs text-gray-400">F CFA</p>
                </div>
            </div>
            <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Total commission</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_commission'], 0, ',', ' ') }}</p>
                    <p class="text-xs text-gray-400">F CFA</p>
                </div>
            </div>
            <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xl">
                    <i class="fas fa-list"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Payés</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $stats['completed_count'] }}</p>
                    <p class="text-xs text-gray-400">transactions</p>
                </div>
            </div>
        </div>

        <!-- VUE 1 : LISTE RETRAITS -->
        <div id="retrait-list" class="animate-fade-in">
                <!-- Filtres -->
                <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm p-5 mb-6">
                    <form method="GET" action="{{ route('superadmin.retraits') }}" class="flex flex-wrap items-center gap-4">
                        <!-- Barre de recherche -->
                        <div class="relative flex-1 lg:flex-none min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ID retrait, Nom propriétaire..." 
                                   class="pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl text-xs font-medium w-full focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all">
                            <i class="fas fa-search w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                        </div>

                        <!-- Filtre Statut -->
                        <select name="filter_status" onchange="this.form.submit()" class="px-3 py-2 text-xs bg-gray-50 dark:bg-slate-800 border-none rounded-xl text-gray-600 dark:text-gray-300 font-bold cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>✅ Payés</option>
                            <option value="cancelled" {{ request('filter_status') == 'cancelled' ? 'selected' : '' }}>❌ Annulés</option>
                        </select>

                        <!-- Bouton Reset -->
                        @if(request('search') || request('filter_status'))
                            <a href="{{ route('superadmin.retraits') }}" class="p-2 text-red-400 hover:text-red-600 transition" title="Réinitialiser">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Tableau Retraits -->
                <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-5">ID</th>
                                    <th class="p-5">Propriétaire</th>
                                    <th class="p-5">Numéro Momo</th>
                                    <th class="p-5">Nom du compte Momo</th>
                                    <th class="p-5">Montant</th>
                                    <th class="p-5">Frais FedaPay</th>
                                    <th class="p-5">Commission</th>
                                    <th class="p-5">À Payer</th>
                                    <th class="p-5">Date demande</th>
                                    <th class="p-5">Reference de paiement</th>
                                    <th class="p-5">Statut</th>
                                    <th class="p-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                                @forelse($retraits as $retrait)
                                @php
                                    $ownerInitial = $retrait->proprio ? (isset($retrait->proprio->prenom) ? substr($retrait->proprio->prenom, 0, 1) : 'X') . (isset($retrait->proprio->nom) ? substr($retrait->proprio->nom, 0, 1) : 'X') : 'XX';
                                    $ownerName = $retrait->proprio ? (isset($retrait->proprio->prenom) ? $retrait->proprio->prenom : '') . ' ' . (isset($retrait->proprio->nom) ? $retrait->proprio->nom : '') : 'Inconnu';
                                    $statusClass = match($retrait->status ?? 'unknown') {
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                                        'completed', 'paid' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                        'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                                    };
                                    $statusLabel = match($retrait->status ?? 'unknown') {
                                        'pending' => 'En attente',
                                        'completed', 'paid' => 'Payé',
                                        'cancelled' => 'Annulé',
                                        default => ucfirst($retrait->status),
                                    };
                                    $canTakeAction = in_array($retrait->status ?? null, ['pending']);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition cursor-pointer" onclick="showRetraitDetail('{{ $retrait->id }}')">
                                    <td class="p-5 font-mono text-gray-500 dark:text-gray-300">{{ $retrait->reference ?? $retrait->id }}</td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">{{ $ownerInitial }}</div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-white">{{ $ownerName }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 font-mono text-gray-600 dark:text-gray-300">{{ $retrait->momo_number ?? $retrait->phone_number ?? '-' }}</td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ $retrait->momo_name ?? $retrait->beneficiary_name ?? '-' }}</td>
                                    <td class="p-5 font-bold text-brand-blue">{{ number_format($retrait->amount, 0, ',', ' ') }} F</td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ number_format($retrait->fedapay_fee ?? 0, 0, ',', ' ') }} F</td>
                                    <td class="p-5 text-gray-600 dark:text-gray-300">{{ number_format($retrait->ccorp_fee ?? 0, 0, ',', ' ') }} F</td>
                                    <td class="p-5 font-bold text-green-600">{{ number_format(($retrait->amount ?? 0) - ($retrait->fedapay_fee ?? 0) - ($retrait->ccorp_fee ?? 0), 0, ',', ' ') }} F</td>
                                    <td class="p-5 text-gray-500 dark:text-gray-400">{{ isset($retrait->created_at) ? $retrait->created_at->format('d M Y à H:i') : (isset($retrait->requested_at) ? $retrait->requested_at->format('d M Y à H:i') : '-') }}</td>
                                    <td class="p-5 font-mono text-gray-600 dark:text-gray-300">{{ $retrait->fedapay_payout_id ?? $retrait->mobile_money_ref ?? '-' }}</td>
                                    <td class="p-5">
                                        <span class="{{ $statusClass }} px-2 py-1 rounded-lg text-[10px] font-bold">{{ $statusLabel }}</span>
                                    </td>
                                    <td class="p-5 text-right">
                                        @if($canTakeAction)
                                            <button onclick="event.stopPropagation(); openPaymentModal('{{ $retrait->id }}', '{{ addslashes($ownerName) }}', {{ $retrait->amount }})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Payer
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">---</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="p-8 text-center text-gray-400">Aucun retrait trouvé.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="p-6 border-t border-gray-100 dark:border-slate-700">
                        <div class="flex flex-wrap justify-between items-center gap-4">
                            <!-- GAUCHE : Indicateur de position -->
                            <div class="flex items-center gap-2 order-1">
                                <span class="text-xs text-gray-400">Affichage de</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $retraits->firstItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">à</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $retraits->lastItem() ?? 0 }}</span>
                                <span class="text-xs text-gray-400">sur</span>
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $retraits->total() }}</span>
                                <span class="text-xs text-gray-400">retraits</span>
                            </div>
                            
                            <!-- CENTRE : Sélecteur lignes -->
                            <div class="flex items-center gap-2 order-2">
                                <span class="text-xs text-gray-400">Afficher</span>
                                <form method="GET" action="{{ route('superadmin.retraits') }}" class="inline-block">
                                    @if(request('filter_status')) <input type="hidden" name="filter_status" value="{{ request('filter_status') }}"> @endif
                                    <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold text-gray-600 dark:text-gray-300 p-1 px-2 focus:ring-2 focus:ring-brand-blue outline-none cursor-pointer">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 lignes</option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 lignes</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 lignes</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 lignes</option>
                                    </select>
                                </form>
                            </div>
                            
                            <!-- DROITE : Pagination links -->
                            <div class="flex items-center gap-2 order-3">
                                {{ $retraits->appends(request()->except('page'))->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODALE ENREGISTRER PAIEMENT -->
    <div id="payment-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closePaymentModal()"></div>
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-3xl p-6 relative z-10 text-center shadow-2xl border border-gray-100 dark:border-slate-700 animate-scale-in">
            <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Enregistrer le paiement</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Montant: <span id="payment-amount" class="font-bold text-brand-blue"></span>
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Destinataire: <span id="payment-owner" class="font-bold"></span>
            </p>
            <div class="p-4 bg-gray-50 dark:bg-slate-800 rounded-xl mb-4">
                <label for="payment-reference" class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold mb-1 block text-left">Reference de la transaction *</label>
                <input type="text" id="payment-reference" 
                       class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg text-sm font-bold text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all" 
                       placeholder="Entrez la reference de la transaction"
                       required>
                <p class="text-xs text-red-500 mt-1 hidden" id="reference-error">Ce champ est obligatoire</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closePaymentModal()" class="flex-1 py-3 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                <button onclick="enregistrerPaiement()" class="flex-1 py-3 bg-green-500 text-white rounded-xl font-bold hover:bg-green-600 transition shadow-lg">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentRetraitId = null;

        function showRetraitDetail(id) {
            console.log('Afficher retrait:', id);
        }

        function openPaymentModal(id, owner, amount) {
            currentRetraitId = id;
            document.getElementById('payment-amount').textContent = parseInt(amount).toLocaleString('fr-FR') + ' F';
            document.getElementById('payment-owner').textContent = owner;
            document.getElementById('payment-reference').value = '';
            document.getElementById('reference-error').classList.add('hidden');
            document.getElementById('payment-modal').classList.remove('hidden');
            document.getElementById('payment-reference').focus();
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            currentRetraitId = null;
        }

        function enregistrerPaiement() {
            const reference = document.getElementById('payment-reference').value.trim();
            const errorEl = document.getElementById('reference-error');
            
            if (!reference) {
                errorEl.textContent = 'Ce champ est obligatoire';
                errorEl.classList.remove('hidden');
                document.getElementById('payment-reference').focus();
                return;
            }
            
            errorEl.classList.add('hidden');
            
            if (currentRetraitId) {
                console.log('[Payment] Envoi paiement pour retrait:', currentRetraitId);
                
                fetch('/god-admin/withdrawals/' + currentRetraitId + '/pay', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_reference: reference
                    })
                })
                .then(response => {
                    console.log('[Payment] Réponse reçue:', response.status, response.headers.get('content-type'));
                    
                    // Vérifier si la réponse est JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        // Si ce n'est pas du JSON, c'est probablement une page de login ou d'erreur
                        if (response.status === 401 || response.redirected) {
                            console.log('[Payment] Session expirée, redirection vers login');
                            window.location.href = '/god-admin/login';
                            return Promise.reject(new Error('session_expired'));
                        }
                        // Retourner une erreur pour déclencher le catch
                        return response.text().then(text => {
                            throw new Error('Réponse non-JSON: ' + text.substring(0, 100));
                        });
                    }
                    
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Erreur HTTP: ' + response.status);
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('[Payment] Données reçues:', data);
                    if (data.success) {
                        if(typeof showToast === 'function') {
                            showToast('Paiement enregistré avec succès. Reference: ' + reference, 'success');
                        }
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if(typeof showToast === 'function') {
                            showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('[Payment] Erreur:', error);
                    if (error.message === 'session_expired') {
                        return; // Already handling redirect
                    }
                    if(typeof showToast === 'function') {
                        showToast('Erreur: ' + error.message, 'error');
                    }
                });
            }
            closePaymentModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePaymentModal();
            }
        });
    </script>
@endsection
