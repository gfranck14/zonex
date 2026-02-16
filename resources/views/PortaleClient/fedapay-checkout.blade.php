<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Fedapay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fedapay-iframe {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 8px;
        }
        
        /* Toast styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
            max-width: 400px;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast-success {
            background-color: #10B981;
            color: white;
        }
        
        .toast-error {
            background-color: #EF4444;
            color: white;
        }
        
        .toast-info {
            background-color: #3B82F6;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <!-- Loader -->
        <div id="loader" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-orange-500 mb-4"></i>
            <p class="text-gray-600">Chargement du paiement...</p>
        </div>
        
        <!-- iframe Fedapay -->
        <div id="fedapay-container" class="hidden">
            <iframe 
                id="fedapay-iframe"
                class="fedapay-iframe shadow-lg"
                allowfullscreen>
            </iframe>
        </div>
        
        <!-- Message de succès -->
        <div id="success-message" class="hidden bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Paiement en cours...</h2>
            <p class="text-gray-600">Ne fermez pas cette page.</p>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-times-circle' : 'fa-info-circle';
            
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas ${icon}" style="font-size: 24px;"></i>
                    <div>
                        <p style="font-weight: 600; margin-bottom: 4px;">${type === 'success' ? 'Succès' : type === 'error' ? 'Erreur' : 'Information'}</p>
                        <p style="font-size: 14px;">${message}</p>
                    </div>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Show toast
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Hide toast after duration
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        
        // Configuration Fedapay Widget
        const fedapayConfig = {
            transaction_id: '{{ $transactionId }}',
            selector: '#fedapay-iframe',
            data_phone: {
                required: false
            },
            data_phone_number: {
                hidden: true
            }
        };
        
        // Variables pour les credentials du ticket
        let ticketCredentials = null;
        
        // Charger le script Fedapay et initialiser le widget
        function loadFedapayWidget() {
            const loader = document.getElementById('loader');
            const container = document.getElementById('fedapay-container');
            const iframe = document.getElementById('fedapay-iframe');
            
            // Construire l'URL Fedapay avec paramètres de personnalisation
            let paymentUrl = '{{ $paymentUrl }}';
            
            // Ajouter des paramètres pour cacher le téléphone
            const separator = paymentUrl.includes('?') ? '&' : '?';
            paymentUrl = paymentUrl + separator + 'widget_mode=payment&hide_submit=true';
            
            console.log('URL Fedapay:', paymentUrl);
            
            // Charger l'iframe
            iframe.onload = function() {
                loader.classList.add('hidden');
                container.classList.remove('hidden');
            };
            
            iframe.src = paymentUrl;
            
            // Essayer d'initialiser le widget Fedapay si disponible
            if (typeof FedaPay !== 'undefined') {
                FedaPay.initialize({
                    transaction: '{{ $transactionId }}',
                    container: '#fedapay-iframe',
                    fields: {
                        phone: {
                            required: false,
                            hidden: true
                        }
                    }
                });
            }
        }
        
        // Écouter les messages de l'iframe (pour le succès/échec)
        window.addEventListener('message', function(event) {
            console.log('Message Fedapay:', event.data);
            
            // Vérifier si c'est un message de paiement Fedapay
            if (event.data && event.data.type === 'fedapay_payment') {
                if (event.data.status === 'success') {
                    // Paiement réussi
                    document.getElementById('success-message').classList.remove('hidden');
                    document.getElementById('fedapay-container').classList.add('hidden');
                    
                    // Rediriger vers le callback pour traitement
                    setTimeout(function() {
                        window.location.href = '{{ $callbackUrl }}?transaction_id={{ $transactionId }}&status=success';
                    }, 2000);
                    
                } else if (event.data.status === 'failed') {
                    // Paiement échoué
                    document.getElementById('fedapay-container').classList.add('hidden');
                    
                    // Rediriger vers la page de succès/échec
                    setTimeout(function() {
                        window.location.href = '{{ $callbackUrl }}?transaction_id={{ $transactionId }}&status=failed';
                    }, 1500);
                    
                } else if (event.data.status === 'canceled') {
                    // Paiement annulé par l'utilisateur
                    document.getElementById('fedapay-container').classList.add('hidden');
                    
                    // Rediriger vers la page de succès/échec
                    setTimeout(function() {
                        window.location.href = '{{ $callbackUrl }}?transaction_id={{ $transactionId }}&status=canceled';
                    }, 1500);
                }
            }
        });
        
        // Vérifier les paramètres URL pour afficher le toast approprié
        document.addEventListener('DOMContentLoaded', function() {
            // Charger le widget
            loadFedapayWidget();
            
            // Vérifier si on revient du callback avec un statut
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');
            const username = urlParams.get('username');
            const password = urlParams.get('password');
            
            if (status === 'success') {
                // Masquer l'URL avec les identifiants pour ne pas les exposer
                history.replaceState({}, document.title, window.location.pathname);
                
                // Toast de succès avec credentials
                if (username && password) {
                    showToast('Paiement réussi ! Redirection vers WiFi789...', 'success', 3000);
                    
                    // Redirection vers wifi789.net après toast
                    setTimeout(function() {
                        window.location.href = `http://wifi789.net/login?username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
                    }, 3000);
                } else {
                    showToast('Paiement réussi !', 'success');
                    setTimeout(function() {
                        window.location.href = '{{ route('client.shop') }}';
                    }, 3000);
                }
            } else if (status === 'failed' || status === 'declined') {
                // Masquer l'URL
                history.replaceState({}, document.title, window.location.pathname);
                
                showToast(message || 'Le paiement a été décliné.', 'error');
                setTimeout(function() {
                    window.location.href = '{{ route('client.shop') }}';
                }, 3000);
            } else if (status === 'canceled') {
                // Masquer l'URL
                history.replaceState({}, document.title, window.location.pathname);
                
                showToast(message || 'Le paiement a été annulé.', 'info');
                setTimeout(function() {
                    window.location.href = '{{ route('client.shop') }}';
                }, 3000);
            }
        });
    </script>
    
    <!-- Script Fedapay Widget (optionnel) -->
    <script src="https://cdn.fedapay.com/checkout.js"></script>
</body>
</html>
