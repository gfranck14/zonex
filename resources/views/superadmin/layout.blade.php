<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ZoneX SuperAdmin</title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #dc2626;
            --primary-dark: #991b1b;
            --sidebar-bg: #1a1a1a;
            --content-bg: #0f0f0f;
        }
        
        body {
            background: var(--content-bg);
            color: #e5e5e5;
        }
        
        .sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid #2a2a2a;
        }
        
        .sidebar-link {
            transition: all 0.2s;
        }
        
        .sidebar-link:hover {
            background: rgba(220, 38, 38, 0.1);
            border-left: 3px solid var(--primary);
        }
        
        .sidebar-link.active {
            background: rgba(220, 38, 38, 0.2);
            border-left: 3px solid var(--primary);
        }
        
        .card {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            border-radius: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="sidebar w-64 flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-red-500">
                    <i class="fas fa-crown"></i> ZoneX GOD
                </h1>
                <p class="text-xs text-gray-400 mt-1">SuperAdmin Dashboard</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('superadmin.dashboard') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                
                <a href="{{ route('superadmin.proprios.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.proprios.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Propriétaires</span>
                </a>
                
                <a href="{{ route('superadmin.withdrawals.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.withdrawals.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave w-5"></i>
                    <span class="ml-3">Retraits</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                
                <a href="{{ route('superadmin.analytics.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.analytics.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span class="ml-3">Analytics</span>
                </a>
                
                <a href="{{ route('superadmin.tickets.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.tickets.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt w-5"></i>
                    <span class="ml-3">Support</span>
                </a>
                
                <a href="{{ route('superadmin.audit-logs.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.audit-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history w-5"></i>
                    <span class="ml-3">Audit Logs</span>
                </a>
                
                @if(Auth::guard('superadmin')->user()->isGod())
                <a href="{{ route('superadmin.settings.index') }}" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-300 {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Configuration</span>
                </a>
                @endif
            </nav>
            
            <div class="absolute bottom-0 w-64 p-6 border-t border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white">{{ Auth::guard('superadmin')->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::guard('superadmin')->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('superadmin.logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-400">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-gray-900 border-b border-gray-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                    
                    <div class="flex items-center space-x-4">
                        @if(session('impersonating_from_superadmin'))
                        <form method="POST" action="{{ route('superadmin.stop-impersonation') }}">
                            @csrf
                            <button class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                <i class="fas fa-user-slash mr-2"></i>Arrêter Impersonation
                            </button>
                        </form>
                        @endif
                        
                        <span class="text-sm text-gray-400">
                            <i class="far fa-clock mr-1"></i>{{ now()->format('H:i') }}
                        </span>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-900 border border-green-700 text-green-100 rounded">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-900 border border-red-700text-red-100 rounded">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-900 border border-red-700 text-red-100 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
