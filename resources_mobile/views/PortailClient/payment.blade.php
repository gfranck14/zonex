<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paiement - {{ $forfait->nom }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; }
        .tap-highlight-transparent { -webkit-tap-highlight-color: transparent; }
        
        .payment-method-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .payment-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .payment-method-card.selected {
            border-color: #083e5f;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        .loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="history.back()" class="text-gray-500 hover:text-gray-700 transition">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-900">Finaliser l'achat</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-shield-alt mr-1"></i>Paiement Sécurisé
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-6 py-8">
        <!-- Récapitulatif de la commande -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Récapitulatif de la commande</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Détails du forfait -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Forfait sélectionné</h3>
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-2xl font-bold">{{ $forfait->nom }}</h4>
                                <p class="text-blue-100">{{ $forfait->validite }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold">{{ number_format($forfait->prix, 0, ',', ' ') }}</p>
                                <p class="text-blue-100">FCFA</p>
                            </div>
                        </div>
                        
                        @if($forfait->description)
                            <p class="text-blue-100 text-sm mt-3">{{ $forfait->description }}</p>
                        @endif
                    </div>
                    
                    <!-- Caractéristiques -->
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-3 w-4"></i>
                            <span>Accès Internet haute vitesse</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-3 w-4"></i>
                            <span>Connexion sécurisée et privée</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-3 w-4"></i>
                            <span>Support technique 24/7</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-3 w-4"></i>
                            <span>Activation immédiate</span>
                        </div>
                    </div>
                </div>
                
                <!-- Informations du client -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vos informations</h3>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                                <p class="text-lg font-bold text-gray-900">{{ $client->pseudo }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                <p class="text-lg font-bold text-gray-900">{{ $client->telephone }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <p class="text-lg font-bold text-gray-900">{{ $client->email ?? 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de paiement -->
        <form id="payment-form" class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Méthode de paiement</h2>
            
            <!-- Méthode Fedapay -->
            <div class="payment-method-card selected border-2 border-blue-500 rounded-xl p-6 cursor-pointer" onclick="selectPaymentMethod('fedapay')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Fedapay</h3>
                            <p class="text-sm text-gray-600">Paiement mobile rapide et sécurisé</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Bénéfices -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                        <span>100% Sécurisé</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        <span>Instantané</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-lock text-blue-500 mr-2"></i>
                        <span>Données protégées</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>
                        <span>Disponible 24/7</span>
                    </div>
                </div>
            </div>
            
            <!-- Champs supplémentaires -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone de confirmation <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone" 
                           value=""
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                           placeholder="01000000"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Pour recevoir la confirmation de paiement</p>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email de réception <span class="text-gray-400">(Optionnel)</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ $client->email ?? '' }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                           placeholder="votre@email.com">
                    <p class="text-xs text-gray-500 mt-1">Pour recevoir votre ticket par email</p>
                </div>
            </div>
            
            <!-- Bouton de paiement classique avec téléphone -->
            <div class="mt-8">
                <button type="submit" id="pay-btn" class="w-full bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-4 rounded-xl text-lg shadow-lg transform transition active:scale-95">
                    <span class="flex items-center justify-center">
                        <i class="fas fa-lock mr-3"></i>
                        Payer {{ number_format($forfait->prix, 0, ',', ' ') }} FCFA
                        <span id="loading-spinner" class="hidden ml-3">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </span>
                </button>
            </div>
            
            <!-- Bouton de paiement SANS téléphone (Fedapay checkout) -->
            <div class="mt-4">
                <a href="{{ route('client.fedapay.checkout.no-phone', $forfait->id) }}" 
                   class="block w-full bg-white border-2 border-orange-500 text-orange-600 hover:bg-orange-50 font-bold py-4 rounded-xl text-lg text-center transform transition active:scale-95">
                    <span class="flex items-center justify-center">
                        <i class="fas fa-mobile-alt mr-3"></i>
                        Payer {{ number_format($forfait->prix, 0, ',', ' ') }} FCFA sans saisir le numéro
                    </span>
                </a>
                <p class="text-xs text-gray-500 mt-2 text-center">Vous saisirez votre numéro directement sur Fedapay</p>
            </div>
            
            <!-- Informations de sécurité -->
            <div class="mt-6 bg-blue-50 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Paiement 100% sécurisé</p>
                        <p>Vos informations bancaires ne sont jamais stockées. La transaction est cryptée et traitée par Fedapay, notre partenaire de paiement agréé.</p>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-16">
        <div class="max-w-4xl mx-auto px-6 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600">
                    <p>&copy; {{ date('Y') }} ZoneX. Tous droits réservés.</p>
                </div>
                <div class="flex items-center space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">Conditions générales</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">Politique de confidentialité</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Fonction Toast
        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            let colors, icon;
            switch(type) {
                case 'success':
                    colors = 'bg-green-500 text-white';
                    icon = '<i class="fas fa-check-circle"></i>';
                    break;
                case 'error':
                    colors = 'bg-red-500 text-white';
                    icon = '<i class="fas fa-exclamation-circle"></i>';
                    break;
                default:
                    colors = 'bg-blue-500 text-white';
                    icon = '<i class="fas fa-info-circle"></i>';
            }

            const toast = document.createElement('div');
            toast.className = `${colors} px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px]`;
            toast.innerHTML = `
                <div class="text-xl">${icon}</div>
                <p class="text-sm font-bold">${message}</p>
            `;

            container.appendChild(toast);
            
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Sélection de la méthode de paiement
        function selectPaymentMethod(method) {
            // Fedapay est la seule méthode pour le moment
            console.log('Méthode sélectionnée:', method);
        }

        // Gestion de la soumission du formulaire
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const payBtn = document.getElementById('pay-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            
            // Désactiver le bouton et afficher le chargement
            payBtn.disabled = true;
            loadingSpinner.classList.remove('hidden');
            payBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Préparer les données
            const formData = new FormData(this);
            
            // Appel à l'API pour initialiser le paiement
            fetch(`/fedapay/pay/{{ $forfait->id }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Préparation du paiement...', 'success');
                    // Rediriger immédiatement vers la page de paiement Fedapay
                    window.location.href = data.payment_url;
                } else {
                    showToast(data.message || 'Erreur lors du paiement', 'error');
                    // Réactiver le bouton
                    payBtn.disabled = false;
                    loadingSpinner.classList.add('hidden');
                    payBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur technique. Veuillez réessayer.', 'error');
                // Réactiver le bouton
                payBtn.disabled = false;
                loadingSpinner.classList.add('hidden');
                payBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        });
    </script>
</body>
</html>
