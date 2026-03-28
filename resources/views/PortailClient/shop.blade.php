<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Boutique - {{ $wifizone->display_name ?? $wifizone->nom_zone ?? 'Zone WiFi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#0EA5E9',
                            sidebarLight: '#072b47',
                            sidebarDark: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; }
        .tap-highlight-transparent { -webkit-tap-highlight-color: transparent; }
        
        .forfait-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .forfait-card:hover:not(.out-of-stock) {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .out-of-stock {
            opacity: 0.7;
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
<body class="min-h-screen pb-10">

    <!-- Header -->
    <div class="px-6 pt-10 pb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Choisissez votre offre</h2>
        <p class="text-sm text-gray-500 mt-1">Sélectionnez un forfait adapté</p>
    </div>

    <!-- Grid des Offres -->
    <div class="px-6 space-y-6 max-w-md mx-auto">
        
        @forelse($forfaits as $forfait)
            @php
                $hasTickets = $forfait->tickets_count > 0;
            @endphp
            
            <div class="forfait-card bg-white rounded-3xl shadow-sm overflow-hidden transform transition active:scale-95 duration-200 {{ !$hasTickets ? 'out-of-stock' : '' }}">
                
                <!-- Header Couleur (Prix) -->
                <div class="{{ $hasTickets ? ($forfait->color_class ?? 'bg-red-500') : 'bg-gray-400' }} py-6 text-center relative">
                    <h3 class="text-3xl font-black text-white tracking-tighter">
                        {{ number_format($forfait->prix, 0, ',', ' ') }} <span class="text-lg font-bold opacity-80">FCFA</span>
                    </h3>
                    
                    <!-- Badge de stock -->
                    @if(!$hasTickets)
                        <div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                            <i class="fas fa-times-circle mr-1"></i>Rupture
                        </div>
                    @endif
                </div>

                <!-- Corps -->
                <div class="p-6 text-center">
                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $forfait->nom }}</h4>
                    
                    <p class="text-[10px] font-bold text-blue-400 tracking-widest uppercase mb-4">
                        VALIDE {{ $forfait->validite }}
                    </p>

                    @if($forfait->description)
                        <p class="text-gray-400 text-xs mb-6 px-4 leading-relaxed">
                            {{ $forfait->description }}
                        </p>
                    @else
                        <p class="text-gray-400 text-xs mb-6 px-4">Idéal pour surfer sans limites.</p>
                    @endif
                    
                    <!-- Bouton d'action -->
                    @if($hasTickets)
                        <button onclick="acheterForfait({{ $forfait->id }})" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-sm text-sm transform active:scale-95">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Acheter maintenant
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-xl cursor-not-allowed text-sm">
                            <i class="fas fa-times-circle mr-2"></i>
                            Rupture de stock
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="text-center py-10">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-400">Aucun forfait disponible pour cette zone.</p>
            </div>
        @endforelse

    </div>

    <!-- Retour (Optionnel) -->
    <div class="mt-8 text-center">
         <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button class="text-xs font-bold text-gray-400 hover:text-red-500 uppercase tracking-wide">
                Se déconnecter
            </button>
        </form>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

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

        // Fonction pour acheter un forfait (redirection directe vers modal FedaPay)
        function acheterForfait(forfaitId) {
            // Trouver le bouton cliqué
            const button = event.target;
            const originalText = button.innerHTML;
            
            // Afficher le chargement immédiatement dans le bouton
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Chargement...';
            button.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Afficher le toast
            showToast('Préparation du paiement...', 'info');
            
            // Préparer les données pour le paiement direct
            const formData = new FormData();
            formData.append('forfait_id', forfaitId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            
            // Appel à l'API FedaPay directe
            fetch(`/portal/fedapay/pay-direct/${forfaitId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Redirection vers FedaPay...', 'success');
                    // Afficher la modal FedaPay avec iframe
                    showFedapayModal(data.payment_url);
                } else {
                    showToast(data.message || 'Erreur lors du paiement', 'error');
                    // Réactiver le bouton
                    button.disabled = false;
                    button.innerHTML = originalText;
                    button.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur technique. Veuillez réessayer.', 'error');
                // Réactiver le bouton
                button.disabled = false;
                button.innerHTML = originalText;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        }

        // Fonction pour afficher la modal FedaPay sur la même page
        function showFedapayModal(paymentUrl) {
            // Rediriger la page actuelle vers FedaPay
            window.location.href = paymentUrl;
            
            // Ajouter un script pour vider le champ téléphone après le chargement
            const script = document.createElement('script');
            script.textContent = `
                // Script pour vider le champ téléphone dans FedaPay Mobile Money
                function clearPhoneField() {
                    const phoneSelectors = [
                        // Sélecteurs spécifiques pour la page mobile-money
                        'input[id="phone_number"]',
                        'input[name="phone_number"]',
                        'input[placeholder*="téléphone"]',
                        'input[placeholder*="numéro"]',
                        'input[placeholder*="phone"]',
                        'input[placeholder*="Votre numéro"]',
                        'input[placeholder*="Votre numero"]',
                        'input[type="tel"]',
                        'input.form-control[type="text"]',
                        'input.form-control',
                        'input[type="text"]:not([readonly])',
                        // Sélecteurs spécifiques FedaPay
                        'input[data-testid*="phone"]',
                        'input[data-testid*="telephone"]',
                        'input[aria-label*="téléphone"]',
                        'input[aria-label*="numéro"]'
                    ];
                    
                    let attempts = 0;
                    const maxAttempts = 30; // Plus de tentatives pour la page mobile-money
                    
                    const clearInterval = setInterval(() => {
                        attempts++;
                        
                        // Chercher sur la page mobile-money de FedaPay
                        if (window.location.href.includes('process.fedapay.com/mobile-money')) {
                            console.log('Page FedaPay Mobile Money détectée, tentative:', attempts);
                            
                            for (const selector of phoneSelectors) {
                                const phoneInput = document.querySelector(selector);
                                if (phoneInput && phoneInput.value && phoneInput.value.trim() !== '') {
                                    phoneInput.value = '';
                                    phoneInput.focus();
                                    console.log('Champ téléphone vidé sur FedaPay Mobile Money avec sélecteur:', selector);
                                    clearInterval(clearInterval);
                                    return;
                                }
                            }
                        }
                        
                        if (attempts >= maxAttempts) {
                            console.log('Impossible de vider le champ téléphone après', maxAttempts, 'tentatives');
                            clearInterval(clearInterval);
                        }
                    }, 500); // Vérifier toutes les 500ms
                }
                
                // Lancer la fonction après le chargement du DOM
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', clearPhoneField);
                } else {
                    clearPhoneField();
                }
            `;
            
            // Injecter le script dans la page
            document.head.appendChild(script);
        }

        // Fonction pour fermer la modal de redirection
        function closeFedapayRedirectModal() {
            const modal = document.getElementById('fedapay-redirect-modal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = 'auto';
                showToast('Fenêtre FedaPay fermée', 'info');
            }
        }
    </script>

</body>
</html>
