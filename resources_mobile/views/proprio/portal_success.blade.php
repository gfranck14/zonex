<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion réussie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .bg-logo-gradient {
            background: linear-gradient(135deg, #083e5f 0%, #0EA5E9 50%, #84CC16 100%);
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-[#0f172a] h-screen w-screen overflow-hidden flex items-center justify-center p-4">

    <div class="w-full max-w-md text-center">
        
        <!-- Carte de succès -->
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl p-8 border border-gray-100 dark:border-slate-700">
            
            <!-- Icône de succès -->
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Message -->
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Connexion réussie !
            </h1>
            
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                Vous êtes maintenant connecté à Internet. Profitez de votre navigation !
            </p>

            <!-- Informations de connexion -->
            <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4 mb-8">
                <div class="flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Connexion active et sécurisée</span>
                </div>
            </div>

            <!-- Bouton pour continuer -->
            <button onclick="window.close()" class="w-full bg-logo-gradient text-white py-3 rounded-xl font-bold hover:opacity-90 transition">
                Continuer vers Internet
            </button>

            <!-- Footer -->
            <div class="mt-6 text-xs text-gray-400 dark:text-gray-500">
                Cette fenêtre se fermera automatiquement
            </div>
        </div>
    </div>

    <script>
        // Redirection automatique après 5 secondes
        setTimeout(() => {
            window.close();
            // Si la fenêtre ne peut pas se fermer, rediriger vers Google
            window.location.href = 'https://google.com';
        }, 5000);

        // Essayer de fermer la fenêtre si c'est une popup
        window.onload = function() {
            if (window.opener) {
                window.opener.location.reload();
            }
        };
    </script>

</body>
</html>
