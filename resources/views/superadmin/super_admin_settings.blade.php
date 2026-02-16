@extends('superadmin.super_admin_layout')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres Système')

@section('content')
    <!-- Messages de succès et d'erreurs -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-900 border border-green-700 text-green-100 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-900 border border-red-700 text-red-100 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="card p-6">
        <h2 class="text-3xl font-bold text-white mb-8">Paramètres Système</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Configuration Générale -->
            <div class="bg-gray-800 p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-cog text-red-400 mr-2"></i>Configuration Générale
                </h3>
                
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Nom de l'application</label>
                        <input type="text" value="ZoneX" class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">URL du site</label>
                        <input type="url" value="{{ url('/') }}" class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Devise par défaut</label>
                        <select class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                            <option value="XOF">FCFA (XOF)</option>
                            <option value="EUR">Euro (EUR)</option>
                            <option value="USD">Dollar US (USD)</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl font-bold transition">
                        Sauvegarder
                    </button>
                </form>
            </div>
            
            <!-- FedaPay Configuration -->
            <div class="bg-gray-800 p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-credit-card text-blue-400 mr-2"></i>Configuration FedaPay
                </h3>
                
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Mode</label>
                        <select class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                            <option value="sandbox">Sandbox (Test)</option>
                            <option value="live">Production</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Clé API publique</label>
                        <input type="text" placeholder="pk_..." class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-2">Clé secrète</label>
                        <input type="password" placeholder="sk_..." class="w-full px-4 py-2 bg-gray-700 text-white rounded-xl border border-gray-600 focus:ring-2 focus:ring-red-500/20 outline-none">
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl font-bold transition">
                        Sauvegarder
                    </button>
                </form>
            </div>
            
            <!-- Notifications -->
            <div class="bg-gray-800 p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-bell text-yellow-400 mr-2"></i>Notifications
                </h3>
                
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="text-white font-bold">Email lors d'un nouveau retrait</p>
                            <p class="text-gray-400 text-sm">Recevoir un email quand un propriétaire demande un retrait</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="text-white font-bold">Email lors d'un grand volume de ventes</p>
                            <p class="text-gray-400 text-sm">Alerte si plus de 100 tickets vendus en 1h</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl font-bold transition">
                        Sauvegarder
                    </button>
                </form>
            </div>
            
            <!-- Maintenance -->
            <div class="bg-gray-800 p-6 rounded-xl">
                <h3 class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-tools text-purple-400 mr-2"></i>Maintenance
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="text-white font-bold">Mode Maintenance</p>
                            <p class="text-gray-400 text-sm">Empêcher l'accès au site temporairement</p>
                        </div>
                        <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition">
                            Activer
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="text-white font-bold">Vider le cache</p>
                            <p class="text-gray-400 text-sm">Effacer tous les caches de l'application</p>
                        </div>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition">
                            Vider
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                        <div>
                            <p class="text-white font-bold">Optimiser l'application</p>
                            <p class="text-gray-400 text-sm">Compiler les assets et le routing</p>
                        </div>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-bold text-sm transition">
                            Optimiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
