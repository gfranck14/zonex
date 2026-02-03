<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Connexion WiFi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-input: #F8FAFC;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
            --color-primary: #0EA5E9;
            --bg-card: #FFFFFF;
        }

        body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }
        
        /* Custom Blue from Platform */
        .bg-custom-blue { background-color: #083e5f; }
        .bg-custom-blue:hover { background-color: #062b42; }
        .text-custom-blue { color: #083e5f; }

        /* Input Standard Style from Platform */
        .input-standard {
            width: 100%;
            border-radius: 0.75rem; /* rounded-xl */
            padding: 1rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            outline: none;
            transition: all 0.2s ease-in-out;
            background-color: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            color: #0F172A;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .input-standard:focus {
            border-color: #083e5f;
            box-shadow: 0 0 0 4px rgba(8, 62, 95, 0.1);
        }

        /* Input Floating Style */
        .input-floating-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-floating {
            width: 100%;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem 1rem 0.5rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
            outline: none;
            transition: all 0.3s ease;
        }

        .input-floating:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .floating-label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .input-floating:focus ~ .floating-label,
        .input-floating:not(:placeholder-shown) ~ .floating-label {
            top: 0.25rem;
            left: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-primary);
            background: var(--bg-card);
            padding: 0 0.25rem;
            border-radius: 4px;
        }
        
        .btn-hover-lift {
             transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), 
                         box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-hover-lift:active { transform: scale(0.98); }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-6 py-10">

    <!-- Logo & Header -->
    <div class="flex flex-col items-center mb-10 w-full max-w-xs">
        <div class="w-20 h-20 bg-[#0EA5E9] rounded-3xl shadow-xl shadow-blue-500/20 flex items-center justify-center mb-6">
            <i class="fas fa-wifi text-white text-3xl"></i>
        </div>

        <h1 class="text-xl font-bold text-gray-900 text-center leading-tight">
            {{ $wifizone->display_name ?? $wifizone->nom_zone ?? 'Zone WiFi' }}
        </h1>
        <p class="text-sm text-gray-500 mt-2 font-medium">
            {{ $wifizone->welcome_message ?? 'Bienvenue !' }}
        </p>
    </div>

    <!-- Formulaire -->
    <div class="w-full max-w-xs space-y-6">
        
        <!-- Toggle Login/Register -->
        <div class="flex justify-center mb-6">
            <div class="bg-gray-200 rounded-full p-1 flex relative">
                <button onclick="toggleMode('login')" id="btn-login" class="px-6 py-1.5 rounded-full text-xs font-bold transition-all bg-white text-gray-800 shadow-sm">Connexion</button>
                <button onclick="toggleMode('register')" id="btn-register" class="px-6 py-1.5 rounded-full text-xs font-bold transition-all text-gray-500 hover:text-gray-700">Créer compte</button>
            </div>
        </div>

        <!-- Inputs Wrapper -->
        <form id="auth-form" method="POST" action="{{ route('client.login') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="mac" value="{{ $mac }}">
            <input type="hidden" name="zone_id" value="{{ $wifizone->id ?? '' }}">
            
            <!-- Hidden real telephone field for backend -->
            <input type="hidden" name="telephone" id="real_telephone">
            
            <!-- Phone Input Complex (Platform Style) -->
            <div class="relative" id="phone-container">
                <!-- Bouton Sélecteur -->
                <button type="button" onclick="toggleCountryMenu()" class="absolute left-1 top-1 bottom-1 flex items-center gap-2 px-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition border border-transparent focus:border-brand-blue z-20">
                    <img id="current-flag" src="https://flagcdn.com/w40/bj.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                    <span id="current-code" class="text-sm font-bold text-gray-700">+229</span>
                    <i class="fas fa-chevron-down w-3 h-3 text-gray-400"></i>
                </button>

                <!-- Menu Déroulant -->
                <div id="country-menu" class="hidden absolute top-full left-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
                    <ul class="max-h-48 overflow-y-auto no-scrollbar">
                         <li onclick="selectCountry('bj', '+229')" class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer transition border-b border-gray-50 last:border-0">
                            <img src="https://flagcdn.com/w40/bj.png" class="w-6 rounded-sm" alt="BJ">
                            <span class="text-sm font-bold text-gray-700">+229</span> <span class="text-xs text-gray-500">Bénin</span>
                        </li>
                        <li onclick="selectCountry('tg', '+228')" class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer transition border-b border-gray-50 last:border-0">
                            <img src="https://flagcdn.com/w40/tg.png" class="w-6 rounded-sm" alt="TG">
                            <span class="text-sm font-bold text-gray-700">+228</span> <span class="text-xs text-gray-500">Togo</span>
                        </li>
                        <li onclick="selectCountry('ci', '+225')" class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer transition border-b border-gray-50 last:border-0">
                            <img src="https://flagcdn.com/w40/ci.png" class="w-6 rounded-sm" alt="CI">
                            <span class="text-sm font-bold text-gray-700">+225</span> <span class="text-xs text-gray-500">Côte d'Ivoire</span>
                        </li>
                        <li onclick="selectCountry('sn', '+221')" class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer transition border-b border-gray-50 last:border-0">
                            <img src="https://flagcdn.com/w40/sn.png" class="w-6 rounded-sm" alt="SN">
                            <span class="text-sm font-bold text-gray-700">+221</span> <span class="text-xs text-gray-500">Sénégal</span>
                        </li>
                    </ul>
                </div>

                <!-- Input Visuel -->
                <input 
                    type="tel" 
                    id="phone_display"
                    placeholder="01000000" 
                    class="input-standard pl-32 font-bold" 
                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); updateRealPhone();"
                    required
                >
                <input type="hidden" id="phone_code" value="+229">
            </div>

            <!-- Pseudo (Register Only) -->
            <div id="pseudo-field" class="hidden input-floating-group">
                <input type="text" name="pseudo" id="pseudo_input"
                    class="input-floating font-bold"
                    placeholder=" ">
                <label class="floating-label">Votre Pseudo</label>
            </div>

            <!-- Password -->
            <div class="input-floating-group">
                <input type="password" name="password" required 
                     id="password_input"
                    class="input-floating font-bold tracking-widest"
                    placeholder=" ">
                <label class="floating-label">Mot de passe</label>
                 <button type="button" onclick="togglePasswordVisibility('password_input')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <i class="far fa-eye" id="icon-password_input"></i>
                </button>
            </div>

            <!-- Action Button -->
            <div class="pt-2">
                <button type="submit" id="submit-btn" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10 uppercase tracking-wider btn-hover-lift">
                    Se connecter
                </button>
                <p class="text-xs text-center text-gray-400 mt-4 cursor-pointer hover:text-custom-blue transition" id="helper-text" onclick="if(currentMode==='login') toggleMode('register'); else toggleMode('login');">
                    Pas encore de compte ? Créer un compte
                </p>
            </div>
        </form>

    </div>

    <script>
        let currentMode = 'login'; // login | register

        // LocalStorage Logic
        document.addEventListener('DOMContentLoaded', () => {
            const savedPhone = localStorage.getItem('user_phone_display'); // New key for display number
            if (savedPhone) {
                document.getElementById('phone_display').value = savedPhone;
                updateRealPhone();
            }
        });

        document.getElementById('auth-form').addEventListener('submit', () => {
             const phone = document.getElementById('phone_display').value;
             if(phone) localStorage.setItem('user_phone_display', phone);
             updateRealPhone(); // Ensure hidden field is up to date
        });

        function updateRealPhone() {
            const code = document.getElementById('phone_code').value; // e.g., +229
            const number = document.getElementById('phone_display').value; // e.g., 01000000
            // Backend expects "01000000" usually, or maybe full international format?
            // "telephone" in Controller might be expecting just the number or full number.
            // In the previous version, the user typed the whole thing.
            // If the user types "01...", standard format in Benin starts with 01.
            // Let's assume the backend handles whatever we send. 
            // BUT: If the user previously typed "97000000", now they type "97000000" with "+229".
            // I should concatenate IF the backend expects international format.
            // Given the previous code didn't force a format, I'll send just the number for now if that's what works, 
            // OR I can send `code . number`. 
            // Let's check `ClientPortalController`. 
            // It has `Client::where('telephone', $request->telephone)`.
            // So exact match. 
            // I'll concatenate just to be safe if I want uniqueness across countries, 
            // BUT wait, existing users might have registered as "97000000".
            // If I change it to "+22997000000", they can't login!
            // SAFE BET: Just send the number part for now, as that mimics previous behavior (user typed number).
            // UNLESS the previous input allowed User to type +229...
            // Previous input: placeholder="Numéro de téléphone".
            // I will send ONLY the number part to preserve backward compatibility for now.
            // IF I want to support multiple countries properly, I should migrate DB to store code + number separately or full E.164.
            // For this UI task, I'll just put the number in 'telephone'.
            document.getElementById('real_telephone').value = number; 
        }

        function toggleMode(mode) {
            currentMode = mode;
            const form = document.getElementById('auth-form');
            const pseudoField = document.getElementById('pseudo-field');
            const btnLogin = document.getElementById('btn-login');
            const btnRegister = document.getElementById('btn-register');
            const helperText = document.getElementById('helper-text');
            const submitBtn = document.getElementById('submit-btn');

            if (mode === 'register') {
                form.action = "{{ route('client.register') }}";
                pseudoField.classList.remove('hidden');
                document.getElementById('pseudo_input').required = true;
                
                btnLogin.className = "px-6 py-1.5 rounded-full text-xs font-bold transition-all text-gray-500 hover:text-gray-700";
                btnRegister.className = "px-6 py-1.5 rounded-full text-xs font-bold transition-all bg-white text-gray-800 shadow-sm";
                
                submitBtn.innerText = "Créer mon compte";
                helperText.innerText = "Déjà un compte ? Se connecter";
            } else {
                form.action = "{{ route('client.login') }}";
                pseudoField.classList.add('hidden');
                document.getElementById('pseudo_input').required = false;

                btnLogin.className = "px-6 py-1.5 rounded-full text-xs font-bold transition-all bg-white text-gray-800 shadow-sm";
                btnRegister.className = "px-6 py-1.5 rounded-full text-xs font-bold transition-all text-gray-500 hover:text-gray-700";

                submitBtn.innerText = "Se connecter";
                helperText.innerText = "Pas encore de compte ? Créer un compte";
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

        // Country Selector Logic
        function toggleCountryMenu() {
            document.getElementById('country-menu').classList.toggle('hidden');
        }

        function selectCountry(code, dial) {
            document.getElementById('current-flag').src = `https://flagcdn.com/w40/${code}.png`;
            document.getElementById('current-code').innerText = dial;
            document.getElementById('phone_code').value = dial;
            document.getElementById('country-menu').classList.add('hidden');
            document.getElementById('phone_display').focus();
            updateRealPhone();
        }

        // Close menu/dropdowns when clicking outside
        document.addEventListener('click', function(event) {
             const container = document.getElementById('phone-container');
             const menu = document.getElementById('country-menu');
             if (container && !container.contains(event.target) && menu && !menu.classList.contains('hidden')) {
                 menu.classList.add('hidden');
             }
        });
    </script>
</body>
</html>
