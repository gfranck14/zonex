<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFiProfit - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
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

<!-- BODY : Le Cadre "Floating Inset" (Couleur Sidebar) -->
<body class="bg-gray-50 dark:bg-[#0f172a] h-screen w-screen overflow-hidden flex items-center justify-center p-4 sm:p-6 transition-colors duration-300">

    <!-- LA CARTE BLANCHE (Canvas) -->
    <!-- Pas d'ombre, pas de bordure fine, juste le contraste -->
    <div class="w-full h-full  overflow-hidden flex relative">
        
        <!-- CÔTÉ GAUCHE : Le Visuel "Poster" -->
        <div class="hidden md:flex w-[45%] h-full p-4">
            <div class="w-full h-full bg-logo-gradient rounded-[2rem] relative flex flex-col p-12 text-white overflow-hidden">
                
                <!-- Décoration fond -->
                <div class="absolute top-0 left-0 w-80 h-80 bg-white opacity-5 rounded-full blur-3xl -translate-x-10 -translate-y-10"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-[#84CC16] opacity-20 rounded-full blur-3xl translate-x-10 translate-y-10"></div>
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-soft-light"></div>

                <!-- 1. Logo (Fixe en haut) -->
                <div class="relative z-10 flex items-center gap-3 flex-none">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">WiFiProfit</span>
                </div>

                <!-- 2. Contenu Central (Positionné en absolu pour le carrousel) -->
                <div class="relative z-10 flex flex-col justify-center h-full">
                    
                    <!-- Sous-titre -->
                    <div class="flex items-center gap-2 mb-6">
                        <span class="w-8 h-[2px] bg-[#84CC16]"></span>
                        <span class="text-[#84CC16] font-bold text-xs tracking-widest uppercase">Plateforme de Gestion</span>
                    </div>

                    <!-- Titre -->
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight text-white mb-6">
                        Boostez votre<br>business WiFi.
                    </h1>
                
                    <!-- Carrousel Témoignages (rapproché et mieux équilibré) -->
                    <div class="absolute bottom-10 left-6 w-64">
                        <!-- h-28 définit la hauteur visible d'un seul témoignage -->
                        <div class="h-28 overflow-hidden relative">
                            <div class="scrolling-wrapper">
                                
                                <!-- Témoignage 1 -->
                                <div class="h-28 flex items-center"> <!-- Hauteur fixe importante -->
                                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 w-full">
                                        <div class="flex gap-1 mb-2 text-yellow-400 text-xs">★★★★★</div>
                                        <p class="text-sm text-white/90 italic">"Interface incroyable, mes ventes ont décollé."</p>
                                        <p class="text-xs font-bold text-white mt-2">— Marc K., Vendeur Pro</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Footer (Fixe en bas) -->
                <div class="relative z-10 text-xs text-white/60 flex-none">
                    2026 WiFiProfit Platform
                </div>
            </div>
        </div>

        <!-- CÔTÉ DROIT : Formulaire (Sur fond blanc) -->
        <div class="w-full md:w-[55%] h-full flex flex-col justify-center items-center p-8 md:p-16 relative">
            
            <!-- Bouton Retour / Thème -->
            <div class="absolute top-8 right-8">
                <button onclick="toggleTheme()" class="p-3 rounded-full bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 transition text-gray-500 dark:text-white">
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg class="w-5 h-5 hidden dark:block text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
            </div>

            <div class="w-full max-w-sm">
                <!-- Logo Mobile -->
                <div class="md:hidden flex items-center gap-2 mb-8 text-custom-blue dark:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    <span class="text-2xl font-bold">WiFiProfit</span>
                </div>
<!-- VUE 1 : LOGIN -->
<div id="view-login" class="animate-fade-in">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Bon retour 👋
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm">
            Gérez vos zones en toute simplicité.
        </p>
    </div>

    <!-- Bouton pour remplir automatiquement les champs -->
    <div class="flex justify-end mb-4">
        <button type="button"
            onclick="fillLogin()"
            class="flex items-center gap-2 bg-gray-200 dark:bg-slate-700 text-gray-800 dark:text-gray-200 py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
            Auto-fill
        </button>
    </div>

    <form method="POST" action="{{ route('proprio.login') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                Téléphone
            </label>

            <div class="relative">
                <div class="absolute left-0 top-0 h-full w-20 flex items-center justify-center border-r border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-400 font-bold text-sm pr-8">
                    <span class="mr-2">🇧🇯</span> +229
                </div>

                <input 
                    type="tel"
                    name="numero"
                    id="login-numero"
                    placeholder="01000000"
                    class="input-standard pl-24"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    maxlength="10"
                    required
                    value="{{ old('numero') }}"
                >
            </div>

            @error('numero')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                Mot de passe
            </label>

            <input 
                type="password"
                name="password"
                id="login-password"
                placeholder="•••••••"
                class="input-standard"
                required
            >

            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            @error('login')
                <p class="text-red-500 text-xs mt-2 font-semibold">{{ $message }}</p>
            @enderror

            <div class="flex justify-end mt-2">
                <a href="#" class="text-xs font-bold text-custom-blue hover:underline">
                    Mot de passe oublié ?
                </a>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10">
            Se connecter
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-8">
        Nouveau vendeur ?
        <button onclick="switchView('signup')" class="font-bold text-custom-blue hover:underline">
            Créer un compte
        </button>
    </p>
</div>

<!-- Script pour remplir automatiquement les champs -->
<script>
    function fillLogin() {
        document.getElementById('login-numero').value = '0141513430';
        document.getElementById('login-password').value = '123456';
    }
</script>



               <!-- VUE 2 : SIGNUP -->
<div id="view-signup" class="hidden animate-fade-in">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Inscription 🚀
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm">
            Commencez à vendre en 2 minutes.
        </p>
    </div>

    <form method="POST" action="{{ route('proprio.signup') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                    Prénom
                </label>
                <input 
                    type="text"
                    name="prenom"
                    placeholder="John"
                    required
                    class="input-standard"
                    value="{{ old('prenom') }}"
                >
                @error('prenom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                    Nom
                </label>
                <input 
                    type="text"
                    name="nom"
                    placeholder="Doe"
                    required
                    class="input-standard"
                    value="{{ old('nom') }}"
                >
                @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                Téléphone
            </label>

            <div class="relative">
                <div class="absolute left-0 top-0 h-full w-14 flex items-center justify-center border-r border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-400 font-bold text-sm">
                    +229
                </div>

                <input 
                    type="tel"
                    name="numero"
                    placeholder="01000000"
                    required
                    class="input-standard pl-16"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    maxlength="10"
                    value="{{ old('numero') }}"
                >
            </div>

            @error('numero')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                Mot de passe
            </label>

            <input 
                type="password"
                name="password"
                placeholder="•••••••"
                required
                class="input-standard"
            >

            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/20">
            Continuer
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
        Déjà inscrit ?
        <button onclick="switchView('login')" class="font-bold text-custom-blue hover:underline">
            Se connecter
        </button>
    </p>
</div>


                <!-- VUE 3 : OTP -->
                <div id="view-otp" class="hidden animate-fade-in text-center">
                    <div class="mb-8">
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Code de validation</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Envoyé au <span id="display-phone" class="font-bold text-custom-blue">...</span>
                        </p>
                    </div>

                    <form onsubmit="event.preventDefault(); window.location.href='index.html';" class="max-w-[280px] mx-auto space-y-6">
                        <div class="flex gap-3 justify-center">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold bg-gray-50 dark:bg-[#1E293B] dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl focus:border-[#083e5f] focus:ring-2 focus:ring-[#083e5f]/20 outline-none transition">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold bg-gray-50 dark:bg-[#1E293B] dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl focus:border-[#083e5f] focus:ring-2 focus:ring-[#083e5f]/20 outline-none transition">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold bg-gray-50 dark:bg-[#1E293B] dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl focus:border-[#083e5f] focus:ring-2 focus:ring-[#083e5f]/20 outline-none transition">
                            <input type="text" maxlength="1" class="w-12 h-12 text-center text-xl font-bold bg-gray-50 dark:bg-[#1E293B] dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl focus:border-[#083e5f] focus:ring-2 focus:ring-[#083e5f]/20 outline-none transition">
                        </div>

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg">
                            Valider
                        </button>
                    </form>

                    <p class="text-center text-xs text-gray-400 mt-6">
                        <button class="font-bold text-gray-600 dark:text-gray-300 hover:underline">Renvoyer le code</button>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function switchView(viewName) {
            document.getElementById('view-login').classList.add('hidden');
            document.getElementById('view-signup').classList.add('hidden');
            document.getElementById('view-otp').classList.add('hidden');
            document.getElementById('view-' + viewName).classList.remove('hidden');

            if(viewName === 'otp') {
                const phone = document.getElementById('signup-phone').value;
                document.getElementById('display-phone').innerText = phone || "+229 ...";
            }
        }

        // Dark Mode indépendant pour la page login
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
</body>
</html>