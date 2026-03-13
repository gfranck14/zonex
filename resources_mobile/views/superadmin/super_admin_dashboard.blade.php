@extends('superadmin.super_admin_layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard SuperAdmin')

@section('content')
<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
    <!-- HEADER -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Vue d'ensemble du système</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-white dark:bg-brand-cardDark border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                <i class="fas fa-download"></i> Exporter
            </button>
            <button class="bg-brand-sidebarLight hover:bg-opacity-90 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </header>

    <!-- KPIs -->
    <div class="grid grid-cols-5 gap-4 mb-8">
        <!-- Total Commission du Jour (20%) -->
        <div class="col-span-1 bg-white dark:bg-brand-cardDark p-4 rounded-3xl shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold">Commission Jour</p>
                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ number_format($commissionJour ?? 0, 0, ',', ' ') }} F</p>
            </div>
        </div>

        <!-- Paiements du Jour (40%) -->
        <div class="col-span-2 bg-white dark:bg-brand-cardDark p-4 rounded-3xl shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-white text-sm">Paiements du Jour</h3>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <!-- Réussis -->
                <div class="bg-green-50 dark:bg-green-900/20 p-2 rounded-xl">
                    <div class="flex items-center gap-1 mb-1">
                        <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 text-xs">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="text-[10px] font-bold text-green-600 dark:text-green-400">Réussis</span>
                    </div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $paiementsReussisCount ?? 0 }}</p>
                    <p class="text-xs font-bold text-green-600 dark:text-green-400">{{ number_format($paiementsReussisMontant ?? 0, 0, ',', ' ') }} F</p>
                </div>
                <!-- Échoués -->
                <div class="bg-red-50 dark:bg-red-900/20 p-2 rounded-xl">
                    <div class="flex items-center gap-1 mb-1">
                        <div class="w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-red-600 dark:text-red-400 text-xs">
                            <i class="fas fa-times"></i>
                        </div>
                        <span class="text-[10px] font-bold text-red-600 dark:text-red-400">Échoués</span>
                    </div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $paiementsEchouesCount ?? 0 }}</p>
                    <p class="text-xs font-bold text-red-600 dark:text-red-400">{{ number_format($paiementsEchouesMontant ?? 0, 0, ',', ' ') }} F</p>
                </div>
            </div>
        </div>

        <!-- Retraits (40%) -->
        <div class="col-span-2 bg-white dark:bg-brand-cardDark p-4 rounded-3xl shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-white text-sm">Retraits</h3>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <!-- En attente -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded-xl">
                    <div class="flex items-center gap-1 mb-1">
                        <div class="w-6 h-6 rounded-full bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 text-xs">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="text-[10px] font-bold text-yellow-600 dark:text-yellow-400">En attente</span>
                    </div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $retraitsEnAttenteCount ?? 0 }}</p>
                    <p class="text-xs font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($retraitsEnAttenteMontant ?? 0, 0, ',', ' ') }} F</p>
                </div>
                <!-- Payés -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-2 rounded-xl">
                    <div class="flex items-center gap-1 mb-1">
                        <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400">Payés</span>
                    </div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $retraitsPayesCount ?? 0 }}</p>
                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ number_format($retraitsPayesMontant ?? 0, 0, ',', ' ') }} F</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm p-6">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-4">Revenus des 6 derniers mois</h3>
            <div class="flex items-end justify-between space-x-2 h-48">
                @php
                    $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
                    $revenues = [180000, 220000, 190000, 250000, 280000, 320000];
                    $maxRevenue = max($revenues);
                @endphp
                @foreach($months as $index => $month)
                    @php
                        $height = ($revenues[$index] / $maxRevenue) * 160;
                        $color = $revenues[$index] >= 250000 ? 'bg-green-500' : 'bg-brand-blue';
                    @endphp
                    <div class="flex-1 flex flex-col items-center group">
                        <div class="text-[10px] text-gray-400 mb-1">{{ number_format($revenues[$index], 0, ',', ' ') }}</div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-t h-full relative">
                            <div class="{{ $color }} w-full h-full rounded-t opacity-80 hover:opacity-100 transition-all absolute bottom-0 cursor-pointer hover:brightness-110" style="height: {{ $height }}px"></div>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $month }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
