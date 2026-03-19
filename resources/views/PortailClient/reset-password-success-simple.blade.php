<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mot de passe réinitialisé</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-custom-blue { background-color: #083e5f; }
        .bg-custom-blue:hover { background-color: #062b42; }
        .text-custom-blue { color: #083e5f; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-6 py-10 bg-gradient-to-br from-blue-50 to-indigo-100">

    <!-- Carte principale -->
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <!-- Icône de succès -->
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-green-500 text-3xl"></i>
            </div>

            <!-- Titre -->
            <h1 class="text-2xl font-bold text-gray-900 mb-3">
                Mot de passe réinitialisé !
            </h1>

            <!-- Message -->
            <p class="text-gray-600 mb-8">
                Votre mot de passe a été modifié avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
            </p>

            <!-- Bouton de connexion -->
            <div class="space-y-3">
                <button onclick="goToLanding()" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg">
                    Se connecter
                </button>
                
                <button onclick="showInstructions()" class="w-full border border-gray-300 text-gray-700 py-3.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition">
                    Besoin d'aide ?
                </button>
            </div>
        </div>

        <!-- Instructions (cachées par défaut) -->
        <div id="instructions" class="hidden mt-6 bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Instructions de connexion
            </h3>
            
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start gap-3">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                    <p>Retournez à la page d'accueil du portail</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                    <p>Entrez votre token d'accès (fourni par votre administrateur)</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                    <p>Connectez-vous avec votre numéro de téléphone et votre nouveau mot de passe</p>
                </div>
            </div>

            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-xs text-amber-700">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Conseil :</strong> Choisissez un mot de passe facile à mémoriser mais difficile à deviner.
                </p>
            </div>
        </div>
    </div>

    <script>
        function goToLanding() {
            // Rediriger vers la page d'accueil du portail
            window.location.href = '/portal';
        }

        function showInstructions() {
            const instructions = document.getElementById('instructions');
            instructions.classList.toggle('hidden');
        }

        // Afficher les messages de session s'ils existent
        @if(session('success'))
            setTimeout(() => {
                showToast('{{ session('success') }}', 'success');
            }, 500);
        @endif

        // Fonction toast simple
        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }
            
            const colors = {
                'success': 'bg-green-500 text-white',
                'error': 'bg-red-500 text-white',
                'info': 'bg-blue-500 text-white'
            };

            const toast = document.createElement('div');
            toast.className = `${colors[type]} px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px]`;
            toast.innerHTML = `
                <div class="bg-white/20 rounded-full p-1">
                    <i class="fas fa-${type === 'success' ? 'check' : 'info-circle'} text-sm"></i>
                </div>
                <p class="text-sm font-medium">${message}</p>
            `;

            container.appendChild(toast);

            // Animation d'entrée
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            });

            // Auto-suppression
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }
    </script>
</body>
</html>
