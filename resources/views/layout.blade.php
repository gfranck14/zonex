<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WifiProfit - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <script src="{{ asset('assets/js/config.js') }}"></script>
   <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight leading-none">WiFi<span class="text-brand-blue">Profit</span></h1>
                    <p class="text-[10px] text-blue-200 uppercase tracking-widest">Manager</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <!-- Dashboard Actif -->
                <a href="{{ route('dashboard') }}" class="nav-item-active flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg><span class="font-medium">Dashboard</span></a>
                
                <a href="{{ route('wifizones') }}" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="font-medium">Wifi Zones</span></a>
                <a href="{{ route('forfait_ticket') }}" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg><span class="font-medium">Forfaits & Tickets</span></a>
                <a href="clients.html" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="font-medium">Clients</span></a>
                <a href="paiements.html" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span class="font-medium">Paiements</span></a>
                <a href="settings.html" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all mt-8 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg><span class="font-medium">Paramètres</span></a>
            </nav>
        </div>

        <div class="mt-auto">
            <!-- BOUTON TOGGLE DARK MODE -->
            <button onclick="toggleTheme()" class="w-full flex items-center justify-between bg-black/20 hover:bg-black/30 px-4 py-2 rounded-xl mb-4 text-xs font-bold transition">
                <span>Mode Apparence</span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-300 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg class="w-4 h-4 text-white hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
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
    @yield('content')
</body>
</html>