<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Client - Accès requis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#083e5f',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-primary via-blue-900 to-blue-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md mx-auto">
        <!-- Carte principale -->
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-primary/20 to-blue-600/20 p-6 border-b border-white/10">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Accès Restreint</h1>
                    <p class="text-white/80 text-sm">Cette page nécessite une authentification</p>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6 space-y-6">
                <!-- Message d'information -->
                <div class="bg-blue-500/20 border border-blue-400/30 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-white font-medium mb-1">Accès non autorisé</p>
                            <p class="text-white/80 text-sm">
                                Vous devez être connecté pour accéder à cette page. Veuillez vous connecter avec votre compte client.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="space-y-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Comment accéder
                    </h3>
                    <ol class="space-y-2 text-white/80 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="bg-white/20 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                            <span>Obtenez un token d'accès auprès de votre fournisseur WiFi</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="bg-white/20 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                            <span>Connectez-vous avec votre numéro de téléphone et mot de passe</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="bg-white/20 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                            <span>Accédez au shop et aux services disponibles</span>
                        </li>
                    </ol>
                </div>

                <!-- Champ token -->
                <div class="space-y-3">
                    <label class="text-white font-medium text-sm">Token d'accès</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="token-input"
                            placeholder="Entrez votre token d'accès"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:border-white/40 focus:bg-white/20 transition-all"
                        >
                        <button 
                            onclick="redirectToLanding()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                        >
                            Accéder
                        </button>
                    </div>
                </div>

                <!-- Message de session -->
                @if(session('error'))
                    <div class="bg-red-500/20 border border-red-400/30 rounded-xl p-3">
                        <p class="text-red-200 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-500/20 border border-blue-400/30 rounded-xl p-3">
                        <p class="text-blue-200 text-sm">{{ session('info') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white/60 text-xs">
                Besoin d'aide ? Contactez votre administrateur réseau
            </p>
        </div>
    </div>

    <script>
        function redirectToLanding() {
            const token = document.getElementById('token-input').value.trim();
            if (token) {
                window.location.href = '/portal/landing/' + encodeURIComponent(token);
            } else {
                // Afficher un message d'erreur
                const input = document.getElementById('token-input');
                input.classList.add('border-red-400');
                input.placeholder = 'Veuillez entrer un token valide';
                
                setTimeout(() => {
                    input.classList.remove('border-red-400');
                    input.placeholder = 'Entrez votre token d\'accès';
                }, 3000);
            }
        }

        // Permettre l'accès avec la touche Entrée
        document.getElementById('token-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                redirectToLanding();
            }
        });

        // Afficher les messages de session s'ils existent
        @if(session('error'))
            setTimeout(() => {
                showToast('{{ session('error') }}', 'error');
            }, 1000);
        @endif

        @if(session('info'))
            setTimeout(() => {
                showToast('{{ session('info') }}', 'info');
            }, 1000);
        @endif

        // Fonction toast simple
        function showToast(message, type = 'info') {
            const colors = {
                'info': 'bg-blue-500',
                'error': 'bg-red-500',
                'success': 'bg-green-500'
            };

            const toast = document.createElement('div');
            toast.className = `${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg fixed top-4 right-4 z-50 max-w-sm transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-medium">${message}</p>
                </div>
            `;

            document.body.appendChild(toast);

            // Animation d'entrée
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Auto-suppression
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
