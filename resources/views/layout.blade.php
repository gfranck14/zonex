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
    <!-- Charts library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<!-- BODY : Fond sombre auto en dark mode -->
<body class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-slate-800 dark:text-slate-100 h-[100dvh] w-screen overflow-hidden flex transition-colors duration-300">

    {{-- ═══════════════════════════════════════════════════════════
         COLLAPSIBLE SIDEBAR — Desktop only (hidden on mobile)
         2 states: sidebar-open (w-64) / sidebar-collapsed (w-20)
         State is persisted in localStorage under key: sidebar_collapsed
    ════════════════════════════════════════════════════════════ --}}
    <aside id="main-sidebar"
           class="sidebar-open hidden md:flex flex-shrink-0 flex-col justify-between py-6 px-3 h-full
                  bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white
                  transition-[width] duration-300 ease-in-out relative z-[60]">

        {{-- Toggle button (FLOATING & PROMINENT) --}}
        <button id="sidebar-toggle"
                onclick="toggleSidebar()"
                class="group absolute -right-5 top-1/2 -translate-y-1/2
                       w-10 h-10 bg-white dark:bg-slate-800 border-2 border-brand-blue
                       rounded-full flex items-center justify-center text-brand-blue 
                       hover:bg-blue-50 dark:hover:bg-brand-blue dark:hover:text-white
                       transition-all duration-300 focus:outline-none z-[100] shadow-[0_10px_25px_-5px_rgba(0,0,0,0.3)] hover:scale-110 active:scale-90">
            <i id="sidebar-chevron" class="fas fa-chevron-left text-base transition-transform duration-300"></i>
            
            {{-- Tooltip for the toggle button --}}
            <span id="toggle-tooltip" class="sidebar-tooltip">Réduire le menu</span>
        </button>

        {{-- ── TOP: Brand + Toggle button ── --}}
        <div>
            <div class="flex items-center mb-8 px-1 relative min-h-[2.5rem]">
                {{-- Logo icon — always visible --}}
                <div class="w-10 h-10 bg-gradient-to-br from-brand-blue to-brand-green rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/50 shrink-0">
                    <i class="fas fa-wifi text-xl text-white"></i>
                </div>
                {{-- Brand wordmark — hidden when collapsed --}}
                <div class="ml-3 sidebar-label overflow-hidden">
                    <h1 class="text-lg font-bold text-white tracking-tight leading-none whitespace-nowrap">WiFi<span class="text-brand-blue">Profit</span></h1>
                    <p class="text-[10px] text-blue-200 uppercase tracking-widest whitespace-nowrap">Manager</p>
                </div>
            </div>

            {{-- ── NAV ITEMS ── --}}
            <nav class="space-y-1">
                @php
                    $navItems = [
                        ['route' => 'proprio.dashboard',      'icon' => 'fa-th-large',      'label' => 'Dashboard',          'extra' => ''],
                        ['route' => 'proprio.wifizones',      'icon' => 'fa-map-marker-alt', 'label' => 'Wifi Zones',         'extra' => ''],
                        ['route' => 'proprio.forfait_ticket', 'icon' => 'fa-ticket-alt',     'label' => 'Forfaits & Tickets', 'extra' => ''],
                        ['route' => 'proprio.clients',        'icon' => 'fa-users',          'label' => 'Clients',            'extra' => ''],
                        ['route' => 'proprio.paiements',      'icon' => 'fa-credit-card',    'label' => 'Paiements',          'extra' => ''],
                        ['route' => 'proprio.settings',       'icon' => 'fa-cog',            'label' => 'Paramètres',         'extra' => 'mt-6'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-nav-item group relative flex items-center gap-3 py-3.5 rounded-2xl transition-all duration-200
                              {{ $item['extra'] }}
                              {{ $active ? 'nav-item-active' : 'nav-item-inactive' }}">
                        {{-- Active indicator bar --}}
                        @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-7 bg-white/80 rounded-r-full"></span>
                        @endif
                        {{-- Icon — always visible, centered in collapsed mode via CSS --}}
                        <i class="fas {{ $item['icon'] }} text-lg shrink-0 w-5 text-center"></i>
                        {{-- Label — fades out when collapsed --}}
                        <span class="sidebar-label font-medium whitespace-nowrap overflow-hidden">{{ $item['label'] }}</span>
                        {{-- Tooltip — only shown in collapsed mode (CSS toggles it) --}}
                        <span class="sidebar-tooltip">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="mt-auto space-y-2">

            {{-- Theme toggle --}}
            <button onclick="toggleTheme()"
                    class="sidebar-nav-item group relative w-full flex items-center gap-3 py-2.5 rounded-xl
                           bg-black/20 hover:bg-black/30 transition-all duration-200 text-xs font-bold">
                <i class="fas fa-sun text-yellow-300 block dark:hidden shrink-0 w-5 text-center text-base"></i>
                <i class="fas fa-moon text-white hidden dark:block shrink-0 w-5 text-center text-base"></i>
                <span class="sidebar-label whitespace-nowrap overflow-hidden">Mode Apparence</span>
                <span class="sidebar-tooltip">Mode Apparence</span>
            </button>

            {{-- Version label (hidden when collapsed) --}}
            <p class="sidebar-label px-1 text-[10px] text-blue-200 font-mono opacity-60 overflow-hidden whitespace-nowrap">v2.4.0-stable</p>

            {{-- Profile + Logout --}}
            <div class="pt-2 border-t border-white/10">
                <form action="{{ route('proprio.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="sidebar-nav-item group relative w-full flex items-center gap-3 py-2 rounded-xl
                                   hover:bg-red-500/10 transition-all duration-200">
                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-full bg-white border-2 border-brand-blue flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                            @if(Auth::guard('proprio')->check())
                                @php
                                    $user     = Auth::guard('proprio')->user();
                                    $initials = strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->nom ?? '', 0, 1));
                                @endphp
                                <span class="text-brand-blue font-bold text-xs">{{ $initials }}</span>
                            @else
                                <i class="fas fa-user text-brand-blue text-xs"></i>
                            @endif
                        </div>
                        {{-- Name + role (hidden when collapsed) --}}
                        <div class="sidebar-label flex-1 overflow-hidden text-left">
                            <p class="text-sm font-bold text-white truncate leading-tight">
                                @if(Auth::guard('proprio')->check())
                                    {{ Auth::guard('proprio')->user()->prenom }} {{ Auth::guard('proprio')->user()->nom }}
                                @else
                                    Visiteur
                                @endif
                            </p>
                            <p class="text-[10px] text-blue-200 group-hover:text-red-400 transition truncate">Propriétaire • Déconnexion</p>
                        </div>
                        {{-- Logout arrow (always visible) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/30 group-hover:text-red-400 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{-- Tooltip --}}
                        <span class="sidebar-tooltip">
                            Déconnexion
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ══ SIDEBAR STYLES ══ -->
    <style>
        /* Open state */
        #main-sidebar.sidebar-open     { width: 16rem; }
        /* Collapsed state */
        #main-sidebar.sidebar-collapsed { width: 5rem; }

        /* Labels: visible when open, zero-width when collapsed */
        #main-sidebar.sidebar-open     .sidebar-label { max-width: 200px; opacity: 1; }
        #main-sidebar.sidebar-collapsed .sidebar-label { max-width: 0; opacity: 0; }

        /* TOOLTIP PREMIUM DESIGN */
        .sidebar-tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%) translateX(10px);
            margin-left: 1.25rem;
            padding: 0.6rem 1rem;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: #072b47;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 10px;
            white-space: nowrap;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 200;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .sidebar-tooltip {
            background-color: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
        }

        /* Tooltip Arrow */
        .sidebar-tooltip::after {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: transparent rgba(255, 255, 255, 0.9) transparent transparent;
        }

        .dark .sidebar-tooltip::after {
            border-color: transparent rgba(15, 23, 42, 0.9) transparent transparent;
        }

        /* Hover behavior */
        .group:hover .sidebar-tooltip {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* Tooltips: only render in collapsed mode */
        #main-sidebar.sidebar-open     .sidebar-tooltip { display: none !important; }
        #main-sidebar.sidebar-collapsed .sidebar-tooltip { display: block; }

        /* Collapsed: center icons */
        #main-sidebar.sidebar-collapsed .sidebar-nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        #main-sidebar.sidebar-collapsed { padding-left: 0.5rem; padding-right: 0.5rem; }

        /* Open: restore nav-item padding */
        #main-sidebar.sidebar-open .sidebar-nav-item { padding-left: 0.75rem; padding-right: 0.75rem; }

        /* Chevron flips when collapsed */
        #main-sidebar.sidebar-collapsed #sidebar-chevron { transform: rotate(180deg); }
        #main-sidebar #sidebar-chevron { transition: transform .3s ease; }
    </style>

    <!-- ══ SIDEBAR JS — run before first paint to avoid flash ══ -->
    <script>
        (function() {
            var s = document.getElementById('main-sidebar');
            var t = document.getElementById('toggle-tooltip');
            if (!s) return;
            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                s.classList.remove('sidebar-open');
                s.classList.add('sidebar-collapsed');
                if (t) t.innerText = 'Agrandir le menu';
            }
        })();

        function toggleSidebar() {
            var s = document.getElementById('main-sidebar');
            var t = document.getElementById('toggle-tooltip');
            var collapsed = s.classList.contains('sidebar-collapsed');
            
            s.classList.toggle('sidebar-collapsed', !collapsed);
            s.classList.toggle('sidebar-open', collapsed);
            
            var newState = !collapsed;
            localStorage.setItem('sidebar_collapsed', String(newState));
            
            if (t) {
                t.innerText = newState ? 'Agrandir le menu' : 'Réduire le menu';
            }
        }
    </script>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-4 pb-28 md:p-0 md:py-4 md:pr-4 md:pl-0 h-[100dvh] flex flex-col overflow-hidden relative">
    @yield('content')
    </main>

    <!-- AJOUTEZ CETTE LIGNE ICI 👇 -->
    @yield('scripts')

    <!-- BOTTOM NAVIGATION (MOBILE ONLY) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-brand-sidebarLight dark:bg-brand-sidebarDark z-[100] pb-safe pt-2">
        <div class="flex items-center justify-around px-1 pb-3 overflow-x-auto no-scrollbar gap-1">
            <a href="{{ route('proprio.dashboard') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.dashboard') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-th-large text-xl"></i>
                <span class="text-[10px] font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('proprio.wifizones') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.wifizones') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-map-marker-alt text-xl"></i>
                <span class="text-[10px] font-medium">Wifi Zones</span>
            </a>
            
            <a href="{{ route('proprio.forfait_ticket') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.forfait_ticket') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-ticket-alt text-xl"></i>
                <span class="text-[10px] font-medium">Forfaits & Tickets</span>
            </a>
            
            <a href="{{ route('proprio.clients') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.clients') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-users text-xl"></i>
                <span class="text-[10px] font-medium">Clients</span>
            </a>
            
            <a href="{{ route('proprio.paiements') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.paiements') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
                <i class="fas fa-credit-card text-xl"></i>
                <span class="text-[10px] font-medium">Paiements</span>
            </a>
            
            <a href="{{ route('proprio.settings') }}" class="flex flex-col items-center gap-1.5 min-w-[60px] {{ request()->routeIs('proprio.settings') ? 'text-brand-blue drop-shadow-[0_0_8px_rgba(14,165,233,0.5)]' : 'text-gray-400 hover:text-white' }} transition-all">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Installer WiFiProfit</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Installez notre application pour un accès rapide et une meilleure expérience!</p>
                <div class="flex gap-3 w-full">
                    <button onclick="closePWAInstallModal()" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Plus tard
                    </button>
                    <button onclick="installPWA()" class="flex-1 px-4 py-2 bg-brand-blue text-white rounded-xl font-medium hover:bg-blue-600 transition-colors">
                        Installer
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>