@extends('layout-care')

@section('title', 'Dashboard - Podo-Reflex')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Sidebar Navigation -->
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg min-h-screen">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-blue-600 mb-8">Podo-Reflex</h2>
                
                <!-- Navigation Menu -->
                <nav class="space-y-2">
                    <a href="#dashboard" class="nav-item flex items-center space-x-3 px-4 py-3 bg-blue-50 text-blue-600 rounded-lg font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="#practitioners" class="nav-item flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Praticiens</span>
                    </a>
                    
                    <a href="#appointments" class="nav-item flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Rendez-vous</span>
                    </a>
                    
                    <a href="#clients" class="nav-item flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Clients</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Dashboard Section -->
            <div id="dashboard" class="dashboard-section">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600 mt-2">Vue d'ensemble de l'activité du centre</p>
                </div>

                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-green-600 font-medium">+12%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">24</h3>
                        <p class="text-gray-600 text-sm">Rendez-vous aujourd'hui</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-green-600 font-medium">+8%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">1,850€</h3>
                        <p class="text-gray-600 text-sm">Chiffre d'affaires jour</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-orange-600 font-medium">-2%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">4</h3>
                        <p class="text-gray-600 text-sm">Praticiens actifs</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm text-green-600 font-medium">+5%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">92%</h3>
                        <p class="text-gray-600 text-sm">Taux d'occupation</p>
                    </div>
                </div>

                <!-- Derniers Rendez-vous -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Derniers Rendez-vous</h2>
                    
                    <div class="space-y-4">
                            <!-- Rendez-vous 1 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-green-600">MM</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Marie Martin</h4>
                                        <p class="text-sm text-gray-600">Pédicurie médicale</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Liège Centre</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">14:00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Confirmé
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">60€</p>
                                </div>
                            </div>

                            <!-- Rendez-vous 2 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-blue-600">JD</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Jean Dubois</h4>
                                        <p class="text-sm text-gray-600">Réflexologie plantaire</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Seraing</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">15:30</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        En attente
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">75€</p>
                                </div>
                            </div>

                            <!-- Rendez-vous 3 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-green-600">SB</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Sophie Bernard</h4>
                                        <p class="text-sm text-gray-600">Manucure médicale</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Ans</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">16:00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Confirmé
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">40€</p>
                                </div>
                            </div>

                            <!-- Rendez-vous 4 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-orange-600">PL</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Pierre Lefebvre</h4>
                                        <p class="text-sm text-gray-600">Pédicurie médicale</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Verviers</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">17:00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Annulé
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">58€</p>
                                </div>
                            </div>

                            <!-- Rendez-vous 5 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-purple-600">MM</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Marie Martin</h4>
                                        <p class="text-sm text-gray-600">Modelage d'ongles</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Liège Centre</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">18:00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Confirmé
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">45€</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Planning du jour -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Planning du Jour</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <!-- Marie Martin -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-blue-600">MM</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Marie Martin</h4>
                                    <p class="text-xs text-gray-600">Liège Centre</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">09:00 - Pédicurie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">10:30 - Réflexologie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <div class="text-xs font-medium text-yellow-800">14:00 - Pédicurie</div>
                                    <div class="text-xs text-yellow-600">En attente</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <div class="text-xs font-medium text-gray-600">16:00 - Libre</div>
                                    <div class="text-xs text-gray-500">Disponible</div>
                                </div>
                            </div>
                        </div>

                        <!-- Jean Dubois -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-purple-600">JD</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Jean Dubois</h4>
                                    <p class="text-xs text-gray-600">Seraing</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">09:00 - Réflexologie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <div class="text-xs font-medium text-gray-600">11:00 - Libre</div>
                                    <div class="text-xs text-gray-500">Disponible</div>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">14:30 - Réflexologie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <div class="text-xs font-medium text-yellow-800">16:00 - Pédicurie</div>
                                    <div class="text-xs text-yellow-600">En attente</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sophie Bernard -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-green-600">SB</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Sophie Bernard</h4>
                                    <p class="text-xs text-gray-600">Ans</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">10:00 - Ongles</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <div class="text-xs font-medium text-gray-600">13:00 - Libre</div>
                                    <div class="text-xs text-gray-500">Disponible</div>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">15:00 - Manucure</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <div class="text-xs font-medium text-gray-600">17:00 - Libre</div>
                                    <div class="text-xs text-gray-500">Disponible</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pierre Lefebvre -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-orange-600">PL</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Pierre Lefebvre</h4>
                                    <p class="text-xs text-gray-600">Verviers</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="bg-gray-50 border border-gray-200 rounded p-2">
                                    <div class="text-xs font-medium text-gray-600">09:00 - Libre</div>
                                    <div class="text-xs text-gray-500">Disponible</div>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">11:00 - Pédicurie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                                <div class="bg-red-50 border border-red-200 rounded p-2">
                                    <div class="text-xs font-medium text-red-800">14:00 - Pédicurie</div>
                                    <div class="text-xs text-red-600">Annulé</div>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-2">
                                    <div class="text-xs font-medium text-green-800">16:30 - Pédicurie</div>
                                    <div class="text-xs text-green-600">Confirmé</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Sections (hidden by default) -->
            <div id="practitioners" class="dashboard-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestion des Praticiens</h2>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-gray-600">Section Praticiens - À développer</p>
                </div>
            </div>

            <div id="appointments" class="dashboard-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestion des Rendez-vous</h2>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-gray-600">Section Rendez-vous - À développer</p>
                </div>
            </div>

            <div id="clients" class="dashboard-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestion des Clients</h2>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-gray-600">Section Clients - À développer</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Navigation between sections
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.dashboard-section');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href').substring(1);
            
            // Update active nav
            navItems.forEach(nav => {
                nav.classList.remove('bg-blue-50', 'text-blue-600', 'font-medium');
                nav.classList.add('text-gray-700');
            });
            this.classList.remove('text-gray-700');
            this.classList.add('bg-blue-50', 'text-blue-600', 'font-medium');
            
            // Show/hide sections
            sections.forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(targetId).classList.remove('hidden');
        });
    });
});
</script>
@push('scripts')
<script>
// Script additionnel si nécessaire
</script>
@endpush
