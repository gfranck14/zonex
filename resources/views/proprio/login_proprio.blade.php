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
        
        /* Animation for fade in */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E')] opacity-20 mix-blend-soft-light"></div>

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

                <!-- Success Message after password reset -->
                @if(session('status') == 'password_reset_success')
                    <div id="password-reset-success" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <p class="font-bold text-green-700 dark:text-green-400 text-sm">Mot de passe modifié !</p>
                                <p class="text-xs text-green-600 dark:text-green-500">Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.</p>
                            </div>
                        </div>
                    </div>
                    <script>
                        setTimeout(() => {
                            const successMsg = document.getElementById('password-reset-success');
                            if(successMsg) {
                                successMsg.style.transition = 'opacity 0.5s';
                                successMsg.style.opacity = '0';
                                setTimeout(() => successMsg.remove(), 500);
                            }
                        }, 5000);
                    </script>
                @endif

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
                                <button type="button" onclick="showForgotPasswordModal()" class="text-xs font-bold text-custom-blue hover:underline">Mot de passe oublié ?</button>
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

                <!-- Modal Mot de passe oublié (style client portal) -->
                <div id="forgot-password-modal" class="fixed inset-0 bg-black/50 z-[9999] hidden flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-whatsapp text-3xl text-green-500"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Mot de passe oublié</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Veuillez contacter le gérant pour réinitialiser votre mot de passe.
                            </p>
                            <a href="https://wa.me/22967864795?text=Bonjour, je souhaite réinitialiser mon mot de passe WiFi" 
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold transition shadow-lg shadow-green-500/20">
                                <i class="fab fa-whatsapp text-xl"></i>
                                Contacter sur WhatsApp
                            </a>
                            <button onclick="closeForgotPasswordModal()" class="mt-4 text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- VUE 1b : FORGOT PASSWORD - Enter Phone -->
                <div id="view-forgot-phone" class="{{ isset($forgot_password_step) && $forgot_password_step == 'enter_phone' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.login') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <i class="fas fa-phone w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Mot de passe oublié</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Entrez votre numéro de téléphone pour commencer.</p>
                    </div>

                    <form method="POST" action="{{ route('proprio.forgot_password.verify_phone') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="relative" id="forgot-phone-container">
                                
                                <!-- Bouton Sélecteur -->
                                <button type="button" onclick="toggleCountryMenu('forgot')" class="absolute left-1 top-1 bottom-1 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition border border-transparent focus:border-brand-blue z-20">
                                    <img id="forgot-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                    <span id="forgot-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down w-3 h-3 text-gray-400"></i>
                                </button>
                                
                                <!-- Menu Déroulant -->
                                <div id="forgot-country-menu" class="hidden absolute top-full left-0 mt-2 w-64 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                                    <ul class="max-h-48 overflow-y-auto no-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-6 rounded-sm" alt="BJ">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                            <span class="text-xs text-gray-500">Bénin</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-6 rounded-sm" alt="TG">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+228</span>
                                            <span class="text-xs text-gray-500">Togo</span>
                                        </li>
                                        <li onclick="selectCountry('ci', '+225', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/ci.png" class="w-6 rounded-sm" alt="CI">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+225</span>
                                            <span class="text-xs text-gray-500">Côte d'Ivoire</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <input 
                                    type="tel" 
                                    name="numero" 
                                    id="forgot-phone"
                                    placeholder="Numéro" 
                                    required 
                                    class="input-standard pl-32 font-bold"
                                >
                                <input type="hidden" name="phone_code" id="forgot-phone-code" value="+229">
                            </div>
                            @error('numero')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" id="btn-verify-phone" onclick="showLoading(this)" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg uppercase tracking-wider flex items-center justify-center gap-2">
                            <span>Vérifier le numéro</span>
                            <svg class="animate-spin hidden w-4 h-4" id="spinner-verify-phone" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4">
                            Vous avez besoin d'aide ? 
                            <a href="https://wa.me/22967864795?text=Bonjour, j'ai besoin d'aide pour réinitialiser mon mot de passe WiFiProfit" target="_blank" class="font-bold text-green-500 hover:underline flex items-center gap-1 justify-center mt-2">
                                <i class="fab fa-whatsapp"></i> Contacter le support
                            </a>
                        </p>
                    </form>
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
                                id="signup-password"
                                placeholder=" " 
                                required 
                                class="input-floating pr-10"
                            >
                            <label class="floating-label">Mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('signup-password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                <i class="far fa-eye" id="icon-signup-password"></i>
                            </button>
                            @error('password')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Champ de confirmation du mot de passe -->
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                name="password_confirmation"
                                id="signup-password-confirmation"
                                placeholder=" " 
                                required 
                                class="input-floating pr-10"
                            >
                            <label class="floating-label">Confirmer le mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('signup-password-confirmation')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                <i class="far fa-eye" id="icon-signup-password-confirmation"></i>
                            </button>
                            @error('password_confirmation')
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

                <!-- VUE 4b : FORGOT PASSWORD - Enter Email -->
                <div id="view-forgot-email" class="{{ isset($forgot_password_step) && $forgot_password_step == 'enter_email' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.login') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <i class="fas fa-key w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Mot de passe oublié</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Entrez votre numéro de téléphone pour commencer.</p>
                    </div>

                    <form method="POST" action="{{ route('proprio.forgot_password.verify_email') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Téléphone</label>
                            <div class="relative" id="forgot-phone-container">
                                
                                <!-- Bouton Sélecteur -->
                                <button type="button" onclick="toggleCountryMenu('forgot')" class="absolute left-1 top-1 bottom-1 flex items-center gap-2 px-3 rounded-lg bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 transition border border-transparent focus:border-brand-blue z-20">
                                    <img id="forgot-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                    <span id="forgot-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                    <i class="fas fa-chevron-down w-3 h-3 text-gray-400"></i>
                                </button>
                                
                                <!-- Menu Déroulant -->
                                <div id="forgot-country-menu" class="hidden absolute top-full left-0 mt-2 w-64 bg-white dark:bg-[#1E293B] border border-gray-200 dark:border-slate-600 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in">
                                    <ul class="max-h-48 overflow-y-auto no-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-6 rounded-sm" alt="BJ">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+229</span>
                                            <span class="text-xs text-gray-500">Bénin</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-6 rounded-sm" alt="TG">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+228</span>
                                            <span class="text-xs text-gray-500">Togo</span>
                                        </li>
                                        <li onclick="selectCountry('ci', '+225', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/ci.png" class="w-6 rounded-sm" alt="CI">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+225</span>
                                            <span class="text-xs text-gray-500">Côte d'Ivoire</span>
                                        </li>
                                        <li onclick="selectCountry('sn', '+221', 'forgot')" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition border-b border-gray-50 dark:border-slate-700/50 last:border-0">
                                            <img src="https://flagcdn.com/w40/sn.png" class="w-6 rounded-sm" alt="SN">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">+221</span>
                                            <span class="text-xs text-gray-500">Sénégal</span>
                                        </li>
                                    </ul>
                                </div>

                                <input 
                                    type="tel" 
                                    name="numero"
                                    id="forgot-phone"
                                    placeholder="01000000"
                                    required
                                    class="input-standard pl-32 font-bold"
                                >
                                <input type="hidden" name="phone_code" id="forgot-phone-code" value="+229">
                            </div>
                            @error('numero')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg uppercase tracking-wider">
                            Vérifier le numéro
                        </button>
                    </form>
                </div>

                <!-- VUE 4b : FORGOT PASSWORD - Enter Email -->
                <div id="view-forgot-email" class="{{ isset($forgot_password_step) && $forgot_password_step == 'enter_email' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.forgot_password') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <i class="fas fa-envelope w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Entrez votre email</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Veuillez entrer l'adresse email associée à votre compte.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('proprio.forgot_password.verify_email') }}" class="space-y-5">
                        @csrf
                        <div class="input-floating-group">
                            <input 
                                type="email" 
                                name="email"
                                id="forgot-email"
                                placeholder=" " 
                                required 
                                class="input-floating"
                                value="{{ old('email') }}"
                            >
                            <label class="floating-label">Adresse email</label>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror

                        <button type="submit" id="btn-continue-email" onclick="showLoading(this)" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg uppercase tracking-wider flex items-center justify-center gap-2">
                            <span>Continuer</span>
                            <svg class="animate-spin hidden w-4 h-4" id="spinner-continue-email" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4">
                            Vous avez besoin d'aide ? 
                            <a href="https://wa.me/22967864795?text=Bonjour, j'ai besoin d'aide pour réinitialiser mon mot de passe WiFiProfit" target="_blank" class="font-bold text-green-500 hover:underline flex items-center gap-1 justify-center mt-2">
                                <i class="fab fa-whatsapp"></i> Contacter le support
                            </a>
                        </p>
                    </form>
                </div>

                <!-- VUE 5 : FORGOT PASSWORD - Link Sent via Email -->
                <div id="view-forgot-link-sent" class="{{ isset($forgot_password_step) && $forgot_password_step == 'link_sent' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.forgot_password') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-green-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-green-600 mx-auto mb-4">
                            <i class="fas fa-envelope w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Email envoyé!</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Nous avons envoyé un lien de réinitialisation à votre adresse email:
                            <span class="font-bold text-custom-blue block mt-1">{{ $masked_email ?? '' }}</span>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-green-700 dark:text-green-400 text-sm">Email envoyé avec succès!</p>
                                    <p class="text-xs text-green-600 dark:text-green-500">Vérifiez votre boîte de réception pour le lien de réinitialisation.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 rounded-xl">
                            <p class="text-xs text-yellow-700 dark:text-yellow-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Si vous ne recevez pas l'email dans quelques minutes, vérifiez votre dossier spam.
                            </p>
                        </div>

                        <p class="text-center text-xs text-gray-400 mt-4">
                            Ce n'est pas la bonne adresse ? 
                            <button onclick="window.location.href='{{ route('proprio.forgot_password') }}'" class="font-bold text-custom-blue hover:underline">Changer l'email</button>
                        </p>
                    </div>
                </div>

                <!-- VUE 5b : FORGOT PASSWORD - WhatsApp Support with User Info -->
                <div id="view-forgot-whatsapp-support" class="{{ isset($forgot_password_step) && $forgot_password_step == 'whatsapp_support' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.forgot_password') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-green-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-green-500 mx-auto mb-4">
                            <i class="fab fa-whatsapp w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Contacter le support</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Cliquez sur le bouton ci-dessous pour contacter le support via WhatsApp et demander la réinitialisation de votre mot de passe.
                        </p>
                    </div>

                    <!-- Account Info Display -->
                    <div class="bg-gray-50 dark:bg-slate-800 rounded-xl p-4 mb-6">
                        <p class="text-xs font-bold text-gray-400 uppercase mb-3">Informations du compte</p>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Nom:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $proprio_nom ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Prénom:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $proprio_prenom ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Numéro:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $proprio_numero ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Button -->
                    <a href="{{ $whatsapp_url ?? '' }}" target="_blank" class="w-full bg-green-500 text-white py-3.5 rounded-xl text-sm font-bold hover:bg-green-600 transition shadow-lg flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-lg"></i>
                        <span>Contacter le support WhatsApp</span>
                    </a>

                    <p class="text-center text-xs text-gray-400 mt-4">
                        Le support vous répondra rapidement pour réinitialiser votre mot de passe.
                    </p>
                </div>

                <!-- VUE 6 : FORGOT PASSWORD - Reset Password (via link) -->
                <div id="view-forgot-confirm-email" class="{{ isset($forgot_password_step) && $forgot_password_step == 'confirm_email' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.forgot_password') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-blue-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-custom-blue mx-auto mb-4">
                            <i class="fas fa-envelope w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Entrez votre email</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Pour le numéro <span class="font-bold text-custom-blue">{{ $phone ?? '' }}</span>, entrez l'email complet : <span class="font-bold text-gray-900 dark:text-white">{{ $masked_email ?? '' }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('proprio.forgot_password.send_link') }}" class="space-y-5">
                        @csrf
                        <div class="input-floating-group">
                            <input 
                                type="email" 
                                name="email"
                                id="confirm-email-input"
                                placeholder=" " 
                                required 
                                class="input-floating"
                                oninput="validateEmailMatch(this.value)"
                            >
                            <label class="floating-label">Entrez l'email complet</label>
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror

                        <button type="submit" id="btn-confirm-email" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg flex items-center justify-center gap-2" onclick="return validateBeforeSubmit()">
                            <span>Envoyer le lien</span>
                            <svg class="animate-spin hidden w-4 h-4" id="spinner-confirm-email" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-4">
                            Vous avez besoin d'aide ? 
                            <a href="https://wa.me/22967864795?text=Bonjour, j'ai besoin d'aide pour réinitialiser mon mot de passe WiFiProfit" target="_blank" class="font-bold text-green-500 hover:underline flex items-center gap-1 justify-center mt-2">
                                <i class="fab fa-whatsapp"></i> Contacter le support
                            </a>
                        </p>
                    </form>
                </div>

                <!-- VUE 6 : FORGOT PASSWORD - Reset Password (via link) -->
                <div id="view-forgot-reset" class="{{ isset($forgot_password_step) && $forgot_password_step == 'reset_password' ? '' : 'hidden' }} animate-fade-in">
                    <div class="mb-8">
                        <button onclick="window.location.href='{{ route('proprio.login') }}'" class="mb-4 text-xs font-bold text-gray-400 hover:text-custom-blue flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Retour
                        </button>
                        <div class="w-16 h-16 bg-green-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-green-600 mx-auto mb-4">
                            <i class="fas fa-key w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Nouveau mot de passe</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Créez un nouveau mot de passe pour votre compte.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('proprio.forgot_password.reset') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="token" value="{{ $reset_token ?? '' }}">
                        
                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                name="password"
                                id="reset-password"
                                placeholder=" " 
                                required 
                                class="input-floating pr-10"
                            >
                            <label class="floating-label">Nouveau mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('reset-password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                <i class="far fa-eye" id="icon-reset-password"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 -mt-3">Minimum 6 caractères, avec majuscules, minuscules et chiffres</p>

                        @error('password')
                            <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror

                        <div class="input-floating-group">
                            <input 
                                type="password" 
                                name="password_confirmation"
                                id="reset-password-confirm"
                                placeholder=" " 
                                required 
                                class="input-floating pr-10"
                            >
                            <label class="floating-label">Confirmer le mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('reset-password-confirm')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                                <i class="far fa-eye" id="icon-reset-password-confirm"></i>
                            </button>
                        </div>

                        @error('password_confirmation')
                            <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror

                        @error('token')
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl">
                                <p class="text-red-600 dark:text-red-400 text-xs font-bold text-center italic">{{ $message }}</p>
                            </div>
                        @enderror

                        <button type="submit" class="w-full bg-green-600 text-white py-3.5 rounded-xl text-sm font-bold hover:bg-green-700 transition shadow-lg uppercase tracking-wider">
                            Réinitialiser le mot de passe
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Fonctions Modal Mot de passe oublié
        function showForgotPasswordModal() {
            document.getElementById('forgot-password-modal').classList.remove('hidden');
        }
        
        function closeForgotPasswordModal() {
            document.getElementById('forgot-password-modal').classList.add('hidden');
        }
        
        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('forgot-password-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeForgotPasswordModal();
            }
        });

        // Auto-show forgot password view on page load based on step
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($forgot_password_step))
                document.getElementById('view-login').classList.add('hidden');
                document.getElementById('view-signup').classList.add('hidden');
                document.getElementById('view-otp').classList.add('hidden');
                
                @if($forgot_password_step == 'enter_phone')
                    document.getElementById('view-forgot-phone').classList.remove('hidden');
                @elseif($forgot_password_step == 'enter_email')
                    document.getElementById('view-forgot-email').classList.remove('hidden');
                @elseif($forgot_password_step == 'confirm_email')
                    document.getElementById('view-forgot-confirm-email').classList.remove('hidden');
                @elseif($forgot_password_step == 'link_sent')
                    document.getElementById('view-forgot-link-sent').classList.remove('hidden');
                @elseif($forgot_password_step == 'whatsapp_support')
                    document.getElementById('view-forgot-whatsapp-support').classList.remove('hidden');
                @elseif($forgot_password_step == 'reset_password')
                    document.getElementById('view-forgot-reset').classList.remove('hidden');
                @endif
            @endif
        });

        // Show loading spinner on button click
        function showLoading(button) {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Find the spinner inside the button
            const spinner = button.querySelector('svg');
            if (spinner) {
                spinner.classList.remove('hidden');
            }
            
            // Hide the text span
            const text = button.querySelector('span');
            if (text) {
                text.classList.add('hidden');
            }
            
            // Submit the form
            button.closest('form').submit();
        }

        // Email validation for forgot password
        const maskedEmail = '{{ $masked_email ?? '' }}';
        const expectedEmail = '{{ session('forgot_password_email', '') }}';

        function validateEmailMatch(inputEmail) {
            const btn = document.getElementById('btn-confirm-email');
            const input = inputEmail.toLowerCase().trim();
            const expected = expectedEmail.toLowerCase();
            
            // Button is now always enabled but shows toast on wrong email
        }

        function validateBeforeSubmit() {
            const input = document.getElementById('confirm-email-input');
            const email = input.value.toLowerCase().trim();
            const expected = expectedEmail.toLowerCase();
            
            if (email !== expected) {
                // Show error toast
                showToast('L\'email ne correspond pas au compte. Veuillez entrer l\'email exact.', 'error');
                return false;
            }
            
            // Show loading
            const btn = document.getElementById('btn-confirm-email');
            btn.disabled = true;
            btn.classList.add('opacity-75');
            const spinner = btn.querySelector('svg');
            if (spinner) spinner.classList.remove('hidden');
            const text = btn.querySelector('span');
            if (text) text.textContent = 'Envoi en cours...';
            
            return true;
        }

        function switchView(viewName) {
            document.getElementById('view-login').classList.add('hidden');
            document.getElementById('view-signup').classList.add('hidden');
            document.getElementById('view-otp').classList.add('hidden');
            document.getElementById('view-forgot-phone').classList.add('hidden');
            document.getElementById('view-forgot-email').classList.add('hidden');
            document.getElementById('view-forgot-confirm-email').classList.add('hidden');
            document.getElementById('view-forgot-link-sent').classList.add('hidden');
            document.getElementById('view-forgot-reset').classList.add('hidden');
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
            document.getElementById('login-phone').value = '67864795';
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

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
            
            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in flex items-center gap-3`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span class="font-bold text-sm">${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s, transform 0.5s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Auto-focus on missing digits input
        @if(isset($forgot_password_step) && $forgot_password_step == 'verify_digits')
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('missing_digits');
                if(input) {
                    input.focus();
                    input.addEventListener('input', function(e) {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                }
            });
        @endif
    </script>
</body>
</html>