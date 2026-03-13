<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mon Ticket - {{ $wifizone->display_name ?? $wifizone->nom_zone ?? 'Zone WiFi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .dashed-border { background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='8' ry='8' stroke='%23CBD5E1FF' stroke-width='2' stroke-dasharray='8%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e"); }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Succès Message -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-500 text-white text-3xl mb-4 shadow-lg shadow-green-500/50">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">Paiement Réussi !</h1>
        <p class="text-gray-400">Voici votre ticket de connexion</p>
    </div>

    <!-- Ticket Card -->
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden mb-8 relative">
        <!-- Ticket Header -->
        <div class="bg-red-600 p-4 text-center">
            <h2 class="text-white font-bold text-lg tracking-wide uppercase">{{ $wifizone->display_name ?? $wifizone->nom_zone ?? 'WIFI ZONE' }}</h2>
            <p class="text-red-100 text-xs mt-1">FORFAIT INTERNET</p>
        </div>
        
        <!-- Ticket Body -->
        <div class="p-6 relative">
            <!-- Cercles décoratifs ticket -->
            <div class="absolute top-0 left-0 w-4 h-4 bg-gray-900 rounded-full -mt-2 -ml-2"></div>
            <div class="absolute top-0 right-0 w-4 h-4 bg-gray-900 rounded-full -mt-2 -mr-2"></div>

            <div class="text-center mb-6">
                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded text-sm font-bold">
                    {{ $ticket->forfait->nom ?? 'Forfait' }}
                </span>
                <div class="text-sm text-gray-400 mt-2">Utilisable 1 seule fois</div>
            </div>

            <!-- Credentials -->
            <div class="space-y-4 mb-6 dashed-border rounded-xl p-4 bg-gray-50">
                
                <!-- Login -->
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-xs text-gray-500 uppercase font-bold">Login / Identifiant</p>
                        <p class="text-xl font-mono font-bold text-gray-800" id="login-val">{{ $ticket->username }}</p>
                    </div>
                    <button onclick="copyToClipboard('login-val')" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-blue-600 shadow-sm border border-gray-100">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

                <div class="border-t border-gray-200 border-dashed my-2"></div>

                <!-- Password -->
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-xs text-gray-500 uppercase font-bold">Mot de passe</p>
                        <p class="text-xl font-mono font-bold text-gray-800" id="pass-val">{{ $ticket->password }}</p>
                    </div>
                    <button onclick="copyToClipboard('pass-val')" class="w-10 h-10 flex items-center justify-center rounded-full bg-white text-gray-500 hover:text-blue-600 shadow-sm border border-gray-100">
                        <i class="far fa-copy"></i>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <!-- BOUTON MAGIQUE MIKROTIK -->
    <div class="w-full max-w-sm px-4 fixed bottom-8">
        <!-- Formulaire caché POST vers Mikrotik -->
        <form action="http://{{ $mikrotik_ip }}/login" method="post" id="mikrotik-form">
            <input type="hidden" name="username" value="{{ $ticket->username }}">
            <input type="hidden" name="password" value="{{ $ticket->password }}">
            <!-- Si IP mikrotik est inaccessible, ce bouton ne marchera pas en test local. -->
            <!-- Mais en prod, c'est ce qu'il faut. -->
            
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-500/30 transform active:scale-95 transition-all text-lg flex items-center justify-center animate-bounce-slow">
                <span class="mr-2">ACTIVER INTERNET MAINTENANT</span>
                <i class="fas fa-rocket"></i>
            </button>
        </form>
        <p class="text-gray-500 text-xs text-center mt-3">Cliquez pour vous connecter automatiquement au réseau.</p>
    </div>

    <script>
        // Confettis au chargement
        window.addEventListener('load', () => {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        });

        // Copier presse-papier
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                // Feedback visuel temporaire
                const icon = document.querySelector(`button[onclick="copyToClipboard('${elementId}')"] i`);
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check', 'text-green-500');
                setTimeout(() => {
                    icon.classList.remove('fa-check', 'text-green-500');
                    icon.classList.add('fa-copy');
                }, 1500);
            });
        }
    </script>
</body>
</html>
