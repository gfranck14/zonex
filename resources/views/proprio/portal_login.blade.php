<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion WiFi - {{ $zone->nom_zone ?? 'Zone WiFi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .text-custom-blue { color: #083e5f; }
        .bg-custom-blue { background-color: #083e5f; }
        .bg-custom-blue:hover { background-color: #062b42; }
        
        .bg-logo-gradient {
            background: linear-gradient(135deg, #083e5f 0%, #0EA5E9 50%, #84CC16 100%);
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-[#0f172a] h-screen w-screen overflow-hidden flex items-center justify-center p-4 sm:p-6 transition-colors duration-300">

    <div class="w-full h-full overflow-hidden flex relative">
        
        <!-- CÔTÉ GAUCHE : Le Visuel "Poster" -->
        <div class="hidden md:flex w-[45%] h-full p-4">
            <div class="w-full h-full bg-logo-gradient rounded-[2rem] relative flex flex-col p-12 text-white overflow-hidden">
                
                <!-- Décoration fond -->
                <div class="absolute top-0 left-0 w-80 h-80 bg-white opacity-5 rounded-full blur-3xl -translate-x-10 -translate-y-10"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-[#84CC16] opacity-20 rounded-full blur-3xl translate-x10 translate-y-10"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E')] opacity-20 mix-blend-soft-light"></div>

                <!-- Logo (Fixe en haut) -->
                <div class="relative z-10 flex items-center gap-3 flex-none">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/10">
                        <i class="fas fa-wifi w-6 h-6"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">{{ $zone->nom_zone ?? 'WiFiProfit' }}</span>
                </div>

                <!-- Contenu Central -->
                <div class="relative z-10 flex flex-col justify-center h-full">
                    <h1 class="text-4xl font-bold mb-4">Connectez-vous<br>au WiFi</h1>
                    <p class="text-white/80 text-lg">Accès Internet haute vitesse</p>
                    
                    <!-- Points forts -->
                    <div class="mt-8 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-white/90">Internet illimité</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-white/90">Connexion sécurisée</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-white/90">Support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CÔTÉ DROIT : FORMULAIRE DE CONNEXION -->
        <div class="w-full md:w-[55%] h-full p-4 flex items-center justify-center">
            <div class="w-full max-w-md">
                
                <!-- Logo Mobile -->
                <div class="md:hidden flex items-center gap-3 justify-center mb-8">
                    <div class="w-10 h-10 bg-logo-gradient rounded-xl flex items-center justify-center">
                        <i class="fas fa-wifi w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-custom-blue">{{ $zone->nom_zone ?? 'WiFiProfit' }}</span>
                </div>

                <!-- Carte du formulaire -->
                <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl p-8 border border-gray-100 dark:border-slate-700">
                    
                    <!-- En-tête -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            Connexion WiFi
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Entrez vos identifiants pour accéder à Internet
                        </p>
                    </div>

                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('portal.auth') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="zone_token" value="{{ request('z') }}">
                        
                        <!-- Username -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                                Nom d'utilisateur
                            </label>
                            <input 
                                type="text"
                                name="username"
                                placeholder="Entrez votre nom d'utilisateur"
                                class="w-full bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none focus:ring-2 focus:ring-brand-blue/20 transition-all"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                                Mot de passe
                            </label>
                            <input 
                                type="password"
                                name="password"
                                placeholder="Entrez votre mot de passe"
                                class="w-full bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none focus:ring-2 focus:ring-brand-blue/20 transition-all"
                                required
                            >
                        </div>

                        <!-- Bouton de connexion -->
                        <button type="submit"
                            class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10">
                            Se connecter
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Besoin d'aide ? Contactez le support
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
