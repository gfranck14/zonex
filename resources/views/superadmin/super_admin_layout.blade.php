<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WiFiProfit - SuperAdmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/superadmin-style.css') }}">
</head>
<!-- BODY : Fond sombre auto en dark mode -->
<body class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-slate-800 dark:text-slate-100 h-screen w-screen overflow-hidden flex transition-colors duration-300">

    <!-- SIDEBAR : Couleur Light (#215ba0) vs Dark (#0f172a) -->
    <aside class="w-64 flex-shrink-0 flex flex-col justify-between py-6 px-4 h-full bg-brand-sidebarLight dark:bg-brand-sidebarDark transition-colors duration-300 text-white">
        <div>
            <!-- Brand -->
            <div class="flex items-center gap-3 px-4 mb-8">
                <!-- Logo : Dégradé Bleu/Vert -->
                <div class="w-10 h-10 bg-gradient-to-br from-brand-blue to-brand-green rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/50">
                    <i class="fas fa-shield-alt w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight leading-none">WiFi<span class="text-brand-blue">Profit</span></h1>
                    <p class="text-[10px] text-blue-200 uppercase tracking-widest">SuperAdmin</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="{{ request()->routeIs('superadmin.dashboard') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-th-large w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Propriétaires -->
                <a href="{{ route('superadmin.proprietaires') }}" 
                   class="{{ request()->routeIs('superadmin.proprietaires') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-building w-5 h-5"></i>
                    <span class="font-medium">Propriétaires</span>
                </a>

                <!-- Clients -->
                <a href="{{ route('superadmin.clients') }}" 
                   class="{{ request()->routeIs('superadmin.clients') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span class="font-medium">Clients</span>
                </a>

                <!-- Retraits -->
                <a href="{{ route('superadmin.retraits') }}" 
                   class="{{ request()->routeIs('superadmin.retraits') ? 'nav-item-active' : 'nav-item-inactive' }} flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                    <i class="fas fa-money-bill-wave w-5 h-5"></i>
                    <span class="font-medium">Retraits</span>
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
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-red-500/10 cursor-pointer transition group" 
                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                     title="Cliquez pour vous déconnecter">
                    
                    <div class="w-10 h-10 rounded-full bg-brand-light border-2 border-brand-blue flex items-center justify-center overflow-hidden">
                        <i class="fas fa-user-shield w-5 h-5 text-white"></i>
                    </div>
                    
                    <div class="flex-1">
                        <p class="text-sm font-bold text-white">SuperAdmin</p>
                        <p class="text-[10px] text-blue-200 group-hover:text-red-400 transition">
                            Administrateur (Déconnexion)
                        </p>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 group-hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>

                <!-- Formulaire de déconnexion caché -->
                <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
    @yield('content')
    </main>

    <!-- AJOUTEZ CETTE LIGNE ICI 👇 -->
    @yield('scripts')
</body>
</html>
