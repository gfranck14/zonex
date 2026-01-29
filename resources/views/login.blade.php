<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFiProfit - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <i class="fas fa-wifi w-6 h-6"></i>
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
                    <i class="fas fa-moon w-5 h-5 block dark:hidden"></i>
                    <i class="fas fa-sun w-5 h-5 hidden dark:block text-yellow-400"></i>
                </button>
            </div>

            <div class="w-full max-w-sm">
                <!-- Logo Mobile -->
                <div class="md:hidden flex items-center gap-2 mb-8 text-custom-blue dark:text-white">
                    <i class="fas fa-wifi w-8 h-8"></i>
                    <span class="text-2xl font-bold">WiFiProfit</span>
                </div>

                <!-- VUE 1 : LOGIN -->
                <div id="view-login" class="animate-fade-in">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Bon retour 👋</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Gérez vos zones en toute simplicité.</p>
                    </div>

                    <form onsubmit="event.preventDefault(); window.location.href='index.html';" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="flex relative group">
                                <!-- ZONE GAUCHE : SÉLECTEUR PAYS -->
                                <button type="button" onclick="toggleCountryMenu('login')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                                    <img id="login-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Benin">
                                    <span id="login-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                                </button>

                                <!-- MENU DÉROULANT -->
                                <div id="login-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                                    <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'login')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'login')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- INPUT -->
                                <div class="relative flex-1">
                                    <input 
                                        type="tel" 
                                        placeholder="XX XX XX XX" 
                                        id="login-phone"
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-custom-blue/20 outline-none transition placeholder-gray-400" 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        required
                                    >
                                    <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
                                </div>
                                <input type="hidden" id="login-phone-code" value="+229">
                            </div>
                        </div>
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                placeholder=" " 
                                required 
                                class="input-floating"
                            >
                            <label class="floating-label">Mot de passe</label>
                            <div class="flex justify-end mt-2">
                                <a href="#" class="text-xs font-bold text-custom-blue hover:underline">Mot de passe oublié ?</a>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10">
                            Se connecter
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-8">
                        Nouveau vendeur ? <button onclick="switchView('signup')" class="font-bold text-custom-blue hover:underline">Créer un compte</button>
                    </p>
                </div>

                <!-- VUE 2 : SIGNUP -->
                <div id="view-signup" class="hidden animate-fade-in">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Inscription 🚀</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Commencez à vendre en 2 minutes.</p>
                    </div>

                    <form onsubmit="event.preventDefault(); switchView('otp');" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="input-floating-group">
                                <input 
                                    type="text" 
                                    placeholder=" " 
                                    required 
                                    class="input-floating"
                                >
                                <label class="floating-label">Prénom</label>
                            </div>
                            <div class="input-floating-group">
                                <input 
                                    type="text" 
                                    placeholder=" " 
                                    required 
                                    class="input-floating"
                                >
                                <label class="floating-label">Nom</label>
                            </div>
                        </div>
                        <div class="input-floating-group">
                            <input 
                                type="email" 
                                name="email"
                                placeholder=" " 
                                class="input-floating"
                            >
                            <label class="floating-label">Email (Optionnel)</label>
                        </div>
                        <div class="input-floating-group">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="flex relative group">
                                <!-- ZONE GAUCHE : SÉLECTEUR PAYS -->
                                <button type="button" onclick="toggleCountryMenu('signup')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                                    <img id="signup-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Benin">
                                    <span id="signup-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                                </button>

                                <!-- MENU DÉROULANT -->
                                <div id="signup-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-[#1e293b] border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                                    <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'signup')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'signup')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- INPUT -->
                                <div class="relative flex-1">
                                    <input 
                                        type="tel" 
                                        placeholder="XX XX XX XX" 
                                        id="signup-phone"
                                        class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-custom-blue/20 outline-none transition placeholder-gray-400" 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        required
                                    >
                                    <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
                                </div>
                                <input type="hidden" id="signup-phone-code" value="+229">
                            </div>
                        </div>
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                placeholder=" " 
                                required 
                                class="input-floating"
                            >
                            <label class="floating-label">Mot de passe</label>
                        </div>

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/20">
                            Continuer
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                        Déjà inscrit ? <button onclick="switchView('login')" class="font-bold text-custom-blue hover:underline">Se connecter</button>
                    </p>
                </div>

                <!-- VUE 3 : OTP -->
                <div id="view-otp" class="hidden animate-fade-in text-center">
                    <div class="mb-8">
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <i class="fas fa-mobile-alt w-8 h-8"></i>
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
                        <button id="resend-code-btn" onclick="resendCode()" class="font-bold text-gray-600 dark:text-gray-300 hover:underline">Renvoyer le code</button>
                        <span id="countdown" class="hidden font-bold text-gray-400 dark:text-gray-500">(<span id="countdown-timer">30</span>s)</span>
                    </p>
                    
                    <p class="text-center text-xs text-gray-400 mt-4">
                        <button onclick="switchView('signup')" class="font-bold text-custom-blue hover:underline flex items-center gap-1 mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Modifier le numéro de téléphone
                        </button>
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
                // Démarrer le countdown quand on arrive sur la vue OTP
                startResendCountdown();
            }
        }

        // Fonction pour gérer le countdown du renvoi de code
        let countdownInterval;
        
        function startResendCountdown() {
            const resendBtn = document.getElementById('resend-code-btn');
            const countdown = document.getElementById('countdown');
            const countdownTimer = document.getElementById('countdown-timer');
            
            // Réinitialiser et désactiver le bouton
            resendBtn.disabled = true;
            resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
            resendBtn.classList.remove('hover:underline');
            countdown.classList.remove('hidden');
            
            let seconds = 30;
            countdownTimer.textContent = seconds;
            
            // Effacer tout interval existant
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            countdownInterval = setInterval(() => {
                seconds--;
                countdownTimer.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    // Réactiver le bouton
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    resendBtn.classList.add('hover:underline');
                    countdown.classList.add('hidden');
                }
            }, 1000);
        }
        
        function resendCode() {
            // Simuler l'envoi du code
            console.log('Code renvoyé');
            // Redémarrer le countdown
            startResendCountdown();
        }

        // GESTION DU SÉLECTEUR DE PAYS GÉNÉRIQUE
        function toggleCountryMenu(prefix) {
            const menu = document.getElementById(prefix + '-country-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        function selectCountry(countryCode, phoneCode, prefix) {
            const flag = document.getElementById(prefix + '-flag');
            if (flag) {
                flag.src = `https://flagcdn.com/w40/${countryCode}.png`;
            }
            const codeText = document.getElementById(prefix + '-code');
            if (codeText) {
                codeText.textContent = phoneCode;
            }
            const menu = document.getElementById(prefix + '-country-menu');
            if (menu) {
                menu.classList.add('hidden');
            }
            const input = document.getElementById(prefix + '-phone');
            if (input) {
                input.focus();
            }
        }

        // Fermeture au clic dehors
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[id$="-country-menu"]') && !event.target.closest('button[onclick^="toggleCountryMenu"]')) {
                document.querySelectorAll('[id$="-country-menu"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

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