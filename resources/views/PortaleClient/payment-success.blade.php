<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        /* Animation de succès */
        .success-animation {
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Animation d'erreur */
        .error-animation {
            animation: error-shake 0.5s ease-in-out;
        }

        @keyframes error-shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Card de statut -->
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center fade-in">
            <!-- Icône dynamique selon le statut -->
            <div id="status-icon" class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i id="status-icon-element" class="text-5xl"></i>
            </div>
            
            <!-- Titre dynamique -->
            <h1 id="status-title" class="text-2xl font-bold text-gray-900 mb-2"></h1>
            
            <!-- Message -->
            <p id="status-message" class="text-gray-600 mb-6"></p>
            
            <!-- Indicateur de redirection -->
            <div id="redirect-indicator" class="flex items-center justify-center gap-2 text-sm text-gray-500">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Redirection en cours...</span>
            </div>
            
            <!-- Bouton retour -->
            <div id="back-button" class="hidden mt-4">
                <a href="{{ route('client.shop') }}" class="inline-block px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Retour à la boutique
                </a>
            </div>
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

        // Récupérer les paramètres de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const username = urlParams.get('username');
        const password = urlParams.get('password');
        const message = urlParams.get('message');

        // Masquer les paramètres de l'URL
        history.replaceState({}, document.title, window.location.pathname);

        // Éléments DOM
        const statusIcon = document.getElementById('status-icon');
        const statusIconElement = document.getElementById('status-icon-element');
        const statusTitle = document.getElementById('status-title');
        const statusMessage = document.getElementById('status-message');
        const redirectIndicator = document.getElementById('redirect-indicator');
        const backButton = document.getElementById('back-button');

        // Configurer selon le statut
        if (status === 'success') {
            // Style succès
            statusIcon.className = 'w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 success-animation';
            statusIconElement.className = 'fas fa-check text-green-500 text-5xl';
            statusTitle.textContent = 'Paiement Réussi !';
            statusTitle.className = 'text-2xl font-bold text-gray-900 mb-2';
            statusMessage.textContent = message || 'Votre paiement a été traité avec succès.';
            redirectIndicator.querySelector('span').textContent = 'Redirection vers WiFi789...';
            
            // Afficher toast
            showToast(message || 'Paiement réussi !', 'success', 3000);
            
            // Redirection vers wifi789.net après toast (3000ms = après le toast)
            setTimeout(function() {
                if (username && password) {
                    window.location.href = `http://wifi789.net/login?username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;
                } else {
                    window.location.href = '{{ route('client.shop') }}';
                }
            }, 4000);  // 4000ms = toast duration (3000) + buffer (1000)
            
        } else if (status === 'failed' || status === 'declined') {
            // Style échec
            statusIcon.className = 'w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 error-animation';
            statusIconElement.className = 'fas fa-times text-red-500 text-5xl';
            statusTitle.textContent = 'Paiement Décliné';
            statusTitle.className = 'text-2xl font-bold text-red-600 mb-2';
            statusMessage.textContent = message || 'Le paiement a été décliné. Veuillez réessayer.';
            
            // Masquer indicateur de redirection, montrer bouton retour
            redirectIndicator.classList.add('hidden');
            
            // Configurer le bouton retour avec forfait_id si disponible
            const forfaitId = urlParams.get('forfait_id');
            const shopUrl = '{{ route('client.shop') }}';
            const checkoutBaseUrl = '{{ url('/portal/checkout') }}';
            
            if (forfaitId) {
                const checkoutUrl = checkoutBaseUrl + '/' + forfaitId;
                backButton.innerHTML = `<a href="${checkoutUrl}" class="inline-block px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Réessayer le paiement
                </a>`;
            } else {
                backButton.innerHTML = `<a href="${shopUrl}" class="inline-block px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Retour à la boutique
                </a>`;
            }
            backButton.classList.remove('hidden');
            
            // Afficher toast d'erreur
            showToast(message || 'Le paiement a été décliné.', 'error', 5000);
            
        } else if (status === 'canceled') {
            // Style annulé
            statusIcon.className = 'w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6';
            statusIconElement.className = 'fas fa-ban text-blue-500 text-5xl';
            statusTitle.textContent = 'Paiement Annulé';
            statusTitle.className = 'text-2xl font-bold text-blue-600 mb-2';
            statusMessage.textContent = message || 'Le paiement a été annulé. Vous pouvez réessayer.';
            
            // Masquer indicateur de redirection, montrer bouton retour
            redirectIndicator.classList.add('hidden');
            
            // Configurer le bouton retour avec forfait_id si disponible
            const forfaitId = urlParams.get('forfait_id');
            const shopUrl = '{{ route('client.shop') }}';
            const checkoutBaseUrl = '{{ url('/portal/checkout') }}';
            
            if (forfaitId) {
                const checkoutUrl = checkoutBaseUrl + '/' + forfaitId;
                backButton.innerHTML = `<a href="${checkoutUrl}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Réessayer le paiement
                </a>`;
            } else {
                backButton.innerHTML = `<a href="${shopUrl}" class="inline-block px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Retour à la boutique
                </a>`;
            }
            backButton.classList.remove('hidden');
            
            // Afficher toast d'info
            showToast(message || 'Le paiement a été annulé.', 'info', 5000);
            
        } else {
            // Statut inconnu - rediriger vers la boutique
            statusIcon.className = 'w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6';
            statusIconElement.className = 'fas fa-question text-gray-500 text-5xl';
            statusTitle.textContent = 'Statut Inconnu';
            statusTitle.className = 'text-2xl font-bold text-gray-600 mb-2';
            statusMessage.textContent = 'Le statut de votre paiement est inconnu.';
            
            redirectIndicator.classList.add('hidden');
            backButton.classList.remove('hidden');
            
            // Redirection après un délai
            setTimeout(function() {
                window.location.href = '{{ route('client.shop') }}';
            }, 3000);
        }
    </script>
</body>
</html>
