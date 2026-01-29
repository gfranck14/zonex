<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFiProfit - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
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
    <div class="w-full h-full overflow-hidden flex relative">
        
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

                <!-- 2. Contenu Central -->
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
                
                    <!-- Carrousel Témoignages -->
                    <div class="absolute bottom-10 left-6 w-64">
                        <div class="h-28 overflow-hidden relative">
                            <div class="scrolling-wrapper">
                                <!-- Témoignage 1 -->
                                <div class="h-28 flex items-center">
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

                <!-- 3. Footer -->
                <div class="relative z-10 text-xs text-white/60 flex-none">
                    2026 WiFiProfit Platform
                </div>
            </div>
        </div>

        <!-- CÔTÉ DROIT : Formulaire -->
        <div class="w-full md:w-[55%] h-full flex flex-col justify-center items-center p-8 md:p-16 relative">
            
            <!-- Bouton Thème -->
            <div class="absolute top-8 right-8 flex items-center gap-4">
                <!-- Bouton Auto-fill (Uniquement en dev/démo) -->
                <button type="button" onclick="fillLogin()" class="flex items-center gap-2 bg-gray-100 dark:bg-slate-800 text-gray-400 text-[10px] font-bold py-2 px-3 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition uppercase tracking-wider">
                    <i class="fas fa-magic"></i> Auto-fill
                </button>
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
                <div id="view-login" class="{{ $errors->has('signup') || old('form_type') == 'signup' ? 'hidden' : '' }} animate-fade-in">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Bon retour 👋</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Gérez vos zones en toute simplicité.</p>
                    </div>

                    <form method="POST" action="{{ route('proprio.login') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="form_type" value="login">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="relative" id="login-phone-container">
                                
                                <!-- Bouton Sélecteur (Login) -->
                                <button type="button" onclick="toggleCountryMenu('login')" class="absolute left-1 top-1 bottom-1 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition border border-transparent focus:border-brand-blue z-20">
                                    <img id="login-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                    <span id="login-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down w-3 h-3 text-gray-400"></i>
                                </button>

                                <!-- Menu Déroulant (Login) -->
                                <div id="login-country-menu" class="hidden absolute top-full left-0 mt-2 w-64 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                                    <ul class="max-h-48 overflow-y-auto no-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'login')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-6 rounded-sm" alt="BJ">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span> <span class="text-xs text-gray-500">Bénin</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'login')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-6 rounded-sm" alt="TG">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+228</span> <span class="text-xs text-gray-500">Togo</span>
                                        </li>
                                        <li onclick="selectCountry('ci', '+225', 'login')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/ci.png" class="w-6 rounded-sm" alt="CI">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+225</span> <span class="text-xs text-gray-500">Côte d'Ivoire</span>
                                        </li>
                                        <li onclick="selectCountry('sn', '+221', 'login')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/sn.png" class="w-6 rounded-sm" alt="SN">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+221</span> <span class="text-xs text-gray-500">Sénégal</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Input -->
                                <input 
                                    type="tel" 
                                    name="numero"
                                    placeholder="01000000" 
                                    id="login-phone"
                                    class="input-standard pl-32 font-bold" 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required
                                    value="{{ old('numero') }}"
                                >
                                <input type="hidden" name="phone_code" id="login-phone-code" value="{{ old('phone_code', '+229') }}">
                            </div>
                            @error('numero')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                name="password"
                                id="login-password"
                                placeholder=" " 
                                required 
                                class="input-floating pr-10"
                            >
                            <label class="floating-label">Mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('login-password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                <i class="far fa-eye" id="icon-login-password"></i>
                            </button>
                            @error('password')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end mt-2">
                                <a href="#" class="text-xs font-bold text-custom-blue hover:underline">Mot de passe oublié ?</a>
                            </div>
                        </div>

                        @error('login')
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl">
                                <p class="text-red-600 dark:text-red-400 text-xs font-bold text-center italic">{{ $message }}</p>
                            </div>
                        @enderror

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10 uppercase tracking-wider">
                            Se connecter
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-8">
                        Nouveau vendeur ? <button onclick="switchView('signup')" class="font-bold text-custom-blue hover:underline">Créer un compte</button>
                    </p>
                </div>

                <!-- VUE 2 : SIGNUP -->
                <div id="view-signup" class="{{ $errors->has('signup') || old('form_type') == 'signup' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Inscription 🚀</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Commencez à vendre en 2 minutes.</p>
                    </div>

                    <form method="POST" action="{{ route('proprio.signup') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="form_type" value="signup">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="input-floating-group">
                                <input 
                                    type="text" 
                                    name="prenom"
                                    placeholder=" " 
                                    required 
                                    class="input-floating"
                                    value="{{ old('prenom') }}"
                                >
                                <label class="floating-label">Prénom</label>
                            </div>
                            <div class="input-floating-group">
                                <input 
                                    type="text" 
                                    name="nom"
                                    placeholder=" " 
                                    required 
                                    class="input-floating"
                                    value="{{ old('nom') }}"
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
                                value="{{ old('email') }}"
                            >
                            <label class="floating-label">Email (Optionnel)</label>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="relative" id="phone-container">
                                
                                <!-- Bouton Sélecteur Pays -->
                                <button type="button" onclick="toggleCountryMenu('signup')" class="absolute left-1 top-1 bottom-1 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition border border-transparent focus:border-brand-blue z-20">
                                    <img id="current-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                    <span id="current-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down w-3 h-3 text-gray-400"></i>
                                </button>

                                <!-- Menu Déroulant -->
                                <div id="country-menu" class="hidden absolute top-full left-0 mt-2 w-64 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                                    <ul class="max-h-48 overflow-y-auto no-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'signup')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-6 rounded-sm" alt="BJ">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                            <span class="text-xs text-gray-500">Bénin</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'signup')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-6 rounded-sm" alt="TG">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+228</span>
                                            <span class="text-xs text-gray-500">Togo</span>
                                        </li>
                                        <li onclick="selectCountry('ci', '+225', 'signup')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/ci.png" class="w-6 rounded-sm" alt="CI">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+225</span>
                                            <span class="text-xs text-gray-500">Côte d'Ivoire</span>
                                        </li>
                                        <li onclick="selectCountry('sn', '+221', 'signup')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/sn.png" class="w-6 rounded-sm" alt="SN">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+221</span>
                                            <span class="text-xs text-gray-500">Sénégal</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Input Téléphone -->
                                <input 
                                    type="tel" 
                                    name="numero"
                                    placeholder="01000000" 
                                    id="signup-phone"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required 
                                    class="input-standard pl-32 font-bold"
                                    value="{{ old('numero') }}"
                                >
                                <input type="hidden" name="phone_code" id="signup-phone-code" value="{{ old('phone_code', '+229') }}">
                            </div>
                            @error('numero')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                name="password"
                                placeholder=" " 
                                required 
                                class="input-floating"
                            >
                            <label class="floating-label">Mot de passe</label>
                            @error('password')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/20 uppercase tracking-wider">
                            Continuer
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                        Déjà inscrit ? <button onclick="switchView('login')" class="font-bold text-custom-blue hover:underline">Se connecter</button>
                    </p>
                </div>

                <!-- VUE 3 : OTP (Visuel uniquement dans cette phase) -->
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

                    <form onsubmit="event.preventDefault();" class="max-w-[280px] mx-auto space-y-6">
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
                            <i class="fas fa-arrow-left text-[10px]"></i>
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
                startResendCountdown();
            }
        }

        let countdownInterval;
        function startResendCountdown() {
            const resendBtn = document.getElementById('resend-code-btn');
            const countdown = document.getElementById('countdown');
            const countdownTimer = document.getElementById('countdown-timer');
            
            resendBtn.disabled = true;
            resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
            resendBtn.classList.remove('hover:underline');
            countdown.classList.remove('hidden');
            
            let seconds = 30;
            countdownTimer.textContent = seconds;
            
            if (countdownInterval) clearInterval(countdownInterval);
            
            countdownInterval = setInterval(() => {
                seconds--;
                countdownTimer.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    resendBtn.classList.add('hover:underline');
                    countdown.classList.add('hidden');
                }
            }, 1000);
        }
        
        function resendCode() {
            startResendCountdown();
        }

        function toggleCountryMenu(context) {
            const menuId = (context === 'login') ? 'login-country-menu' : 'country-menu';
            const menu = document.getElementById(menuId);
            if(menu) menu.classList.toggle('hidden');
        }

        function selectCountry(code, dial, context) {
            const flagId = (context === 'login') ? 'login-flag' : 'current-flag';
            const codeId = (context === 'login') ? 'login-code' : 'current-code';
            const inputId = (context === 'login') ? 'login-phone' : 'signup-phone';
            const hiddenId = (context === 'login') ? 'login-phone-code' : 'signup-phone-code';
            const menuId = (context === 'login') ? 'login-country-menu' : 'country-menu';

            document.getElementById(flagId).src = `https://flagcdn.com/w40/${code}.png`;
            document.getElementById(codeId).innerText = dial;
            document.getElementById(hiddenId).value = dial;
            document.getElementById(menuId).classList.add('hidden');
            document.getElementById(inputId).focus();
        }

        document.addEventListener('click', function(event) {
            const containers = ['login-phone-container', 'phone-container'];
            const menus = ['login-country-menu', 'country-menu'];
            
            containers.forEach((containerId, idx) => {
                const container = document.getElementById(containerId);
                const menu = document.getElementById(menus[idx]);
                if (container && !container.contains(event.target) && menu) {
                    menu.classList.add('hidden');
                }
            });
        });

        // Auto-fill pour la démo
        function fillLogin() {
            document.getElementById('login-phone').value = '41513430';
            document.getElementById('login-password').value = '123456';
        }

        // Dark Mode
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
        }

        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>