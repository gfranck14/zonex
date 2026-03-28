<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WiFiProfit - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <script src="{{ asset('assets/js/config.js') }}"></script>
   <script src="{{ asset('assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#072b47">
    <link rel="apple-touch-icon" href="/pwa-icon-192.png">
</head>
<!-- BODY : Fond sombre auto en dark mode -->
<body class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-slate-800 dark:text-slate-100 h-[100dvh] w-screen overflow-hidden flex transition-colors duration-300">

    <!-- SIDEBAR : Couleur Light (#215ba0) vs Dark (#0f172a) -->
    <aside class="hidden md:flex w-64 flex-shrink-0 flex-col justify-between py-6 px-4 h-full bg-brand-sidebarLight dark:bg-brand-sidebarDark transition-colors duration-300 text-white">
        <div>
            <!-- Brand -->
            <div class="flex items-center gap-3 px-4 mb-8">
                <!-- Logo : Dégradé Bleu/Vert -->
                <div class="w-10 h-10 bg-gradient-to-br from-brand-blue to-brand-green rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/50">
                    <i class="fas fa-wifi w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight leading-none">WiFi<span class="text-brand-blue">Profit</span></h1>
                    <p class="text-[10px] text-blue-200 uppercase tracking-widest">Manager</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="{{ request()->routeIs('dashboard') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-th-large w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Wifi Zones -->
                <a href="{{ route('wifizones') }}" 
                   class="{{ request()->routeIs('wifizones') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-map-marker-alt w-5 h-5"></i>
                    <span class="font-medium">Wifi Zones</span>
                </a>

                <!-- Forfaits & Tickets -->
                <a href="{{ route('forfait_ticket') }}" 
                   class="{{ request()->routeIs('forfait_ticket') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-ticket-alt w-5 h-5"></i>
                    <span class="font-medium">Forfaits & Tickets</span>
                </a>

                <!-- Clients -->
                <a href="{{ route('clients') }}" 
                   class="{{ request()->routeIs('clients') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span class="font-medium">Clients</span>
                </a>

                <!-- Paiements -->
                <a href="{{ route('paiements') }}" 
                   class="{{ request()->routeIs('paiements') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-credit-card w-5 h-5"></i>
                    <span class="font-medium">Paiements</span>
                </a>

                <!-- Paramètres (Marge top ajoutée) -->
                <a href="{{ route('settings') }}" 
                   class="{{ request()->routeIs('settings') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all mt-8">
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span class="font-medium">Paramètres</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto">
            <!-- BOUTON TOGGLE DARK MODE -->
            <button onclick="toggleTheme()" class="w-full flex items-center justify-between bg-black/20 hover:bg-black/30 px-4 py-2 rounded-xl mb-4 text-xs font-bold transition">
                <span>Mode Apparence</span>
                <div class="flex items-center gap-2">
                    <i class="fas fa-sun w-4 h-4 text-yellow-300 block dark:hidden"></i>
                    <i class="fas fa-moon w-4 h-4 text-white hidden dark:block"></i>
                </div>
            </button>

            <p class="px-4 text-[10px] text-blue-200 font-mono mb-2 opacity-60">v2.4.0-stable</p>
           <div class="pt-4 border-t border-white/10">
    <!-- On ajoute un onclick qui soumet le formulaire caché ci-dessous -->
    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-red-500/10 cursor-pointer transition group" 
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
         title="Cliquez pour vous déconnecter">
        
        <div class="w-10 h-10 rounded-full bg-brand-light border-2 border-brand-blue flex items-center justify-center overflow-hidden">
            <img src="https://i.pravatar.cc/150?img=11">
        </div>
        
        <div class="flex-1">
            <!-- On affiche dynamiquement le nom du proprio connecté -->
            <p class="text-sm font-bold text-white">
                {{ Auth::guard('proprio')->user()->nom }} {{ Auth::guard('proprio')->user()->prenom }}
            </p>
            <!-- Petit indicateur de déconnexion au survol -->
            <p class="text-[10px] text-blue-200 group-hover:text-red-400 transition">
                Propriétaire (Déconnexion)
            </p>
        </div>

        <!-- Icône de sortie (optionnelle mais recommandée pour l'UX) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
    </div>

    <!-- Formulaire de déconnexion caché -->
    <form id="logout-form" action="{{ route('proprio.logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-4 pb-28 md:p-0 md:py-4 md:pr-4 md:pl-0 h-[100dvh] flex flex-col overflow-hidden relative">
    @yield('content')
    </main>

    <!-- AJOUTEZ CETTE LIGNE ICI 👇 -->
    @yield('scripts')

    <!-- BOTTOM NAVIGATION (MOBILE ONLY) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-brand-sidebarLight dark:bg-brand-sidebarDark z-[100] pb-safe pt-2">
        <div class="flex items-center justify-around px-1 pb-3 overflow-x-auto no-scrollbar gap-1">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('dashboard') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-th-large text-xl"></i>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            <a href="{{ route('wifizones') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('wifizones') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-map-marker-alt text-xl"></i>
                <span class="text-[10px] font-medium">Wifi Zones</span>
            </a>
            <a href="{{ route('forfait_ticket') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('forfait_ticket') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-ticket-alt text-xl"></i>
                <span class="text-[10px] font-medium">Forfaits & Tickets</span>
            </a>
            <a href="{{ route('clients') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('clients') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-users text-xl"></i>
                <span class="text-[10px] font-medium">Clients</span>
            </a>
            <a href="{{ route('paiements') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('paiements') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-credit-card text-xl"></i>
                <span class="text-[10px] font-medium">Paiements</span>
            </a>
            <a href="{{ route('settings') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('settings') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-cog text-xl"></i>
                <span class="text-[10px] font-medium">Paramètres</span>
            </a>
        </div>
    </nav>
    <!-- PWA Install Prompt Modal -->
    <div id="pwa-install-modal" class="fixed inset-0 bg-black/60 z-[9999] hidden flex items-end sm:items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white dark:bg-slate-800 w-full max-w-sm rounded-3xl p-6 shadow-2xl transform transition-transform duration-300 translate-y-full sm:translate-y-0" id="pwa-modal-content">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-brand-blue to-brand-green rounded-2xl flex items-center justify-center shadow-lg shadow-blue-900/20 mb-4">
                    <i class="fas fa-wifi text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Installer WiFiProfit</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Installez l'application sur votre écran d'accueil pour un accès rapide, même hors ligne.</p>
            </div>
            <div class="flex gap-3 mt-6">
                <button id="pwa-cancel-btn" class="flex-1 py-3 px-4 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-200 dark:hover:bg-slate-600 transition">Plus tard</button>
                <button id="pwa-install-btn" class="flex-1 py-3 px-4 bg-brand-blue text-white rounded-xl font-bold text-sm hover:bg-blue-600 shadow-lg shadow-blue-500/30 transition flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i> Installer
                </button>
            </div>
        </div>
    </div>

    <script>
        // Enregistrement du Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('SW enregistré:', reg.scope);
                }).catch(err => {
                    console.log('SW erreur:', err);
                });
            });
        }

        // Logique d'affichage de la modale d'installation
        let deferredPrompt;
        const pwaModal = document.getElementById('pwa-install-modal');
        const pwaContent = document.getElementById('pwa-modal-content');
        const installBtn = document.getElementById('pwa-install-btn');
        const cancelBtn = document.getElementById('pwa-cancel-btn');

        // Fonction pour vérifier si on peut installer la PWA (HTTP ou HTTPS)
        function canInstallPWA() {
            // Toujours permettre, peu importe le type de connexion (HTTP/HTTPS, local/distant)
            return true;
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            // Empêcher l'apparition native immédiate
            e.preventDefault();
            deferredPrompt = e;

            // On vérifie si l'utilisateur n'a pas déjà refusé récemment et si on peut installer
            if (!localStorage.getItem('pwa-dismissed') && canInstallPWA()) {
                showPwaModal();
            }
        });

        // Pour le développement HTTP, déclencher manuellement la modale si pas d'événement beforeinstallprompt
        window.addEventListener('load', () => {
            // Attendre un peu pour voir si beforeinstallprompt se déclenche
            setTimeout(() => {
                if (!deferredPrompt && canInstallPWA() && !localStorage.getItem('pwa-dismissed')) {
                    // Simuler un événement d'installation pour HTTP (car bloqué par le navigateur)
                    deferredPrompt = {
                        prompt: () => {
                            alert("Sur une connexion non sécurisée (HTTP), l'installation automatique est bloquée par le navigateur.\n\nPour installer l'application :\n1. Ouvrez le menu de votre navigateur (les 3 petits points ou le bouton Partager).\n2. Appuyez sur 'Ajouter à l\'écran d\'accueil'.");
                            console.log('Instructions d\'installation manuelle affichées pour HTTP non sécurisé');
                        },
                        userChoice: Promise.resolve({ outcome: 'accepted' })
                    };
                    showPwaModal();
                }
            }, 2000); // Attendre 2 secondes
        });

        function showPwaModal() {
            pwaModal.classList.remove('hidden');
            // Petit délai pour permettre l'animation CSS
            setTimeout(() => {
                pwaContent.classList.remove('translate-y-full');
            }, 50);
        }

        function hidePwaModal() {
            pwaContent.classList.add('translate-y-full');
            setTimeout(() => {
                pwaModal.classList.add('hidden');
            }, 300);
        }

        installBtn.addEventListener('click', async () => {
            hidePwaModal();
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`L'utilisateur a répondu : ${outcome}`);
                deferredPrompt = null;
            }
        });

        cancelBtn.addEventListener('click', () => {
            hidePwaModal();
            // On mémorise le choix pour ne plus harceler l'utilisateur (optionnel, 7 jours etc)
            localStorage.setItem('pwa-dismissed', 'true');
        });
    </script>
</body>
</html>