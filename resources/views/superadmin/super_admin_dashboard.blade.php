@extends('superadmin.super_admin_layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard SuperAdmin')

@section('content')
<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 dark:text-green-400 font-bold text-xl">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Revenus Totaux</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">1 250 000 F</p>
                <p class="text-green-500 text-xs mt-1">
                    <i class="fas fa-arrow-up mr-1"></i>+12.5% ce mois
                </p>
            </div>
        </div>
        
        <!-- Total Proprietaires -->
        <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Propriétaires</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">156</p>
                <p class="text-blue-500 text-xs mt-1">
                    <i class="fas fa-check mr-1"></i>148 actifs
                </p>
            </div>
        </div>
        
        <!-- Total WiFi Zones -->
        <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 dark:text-yellow-400 font-bold text-xl">
                <i class="fas fa-wifi"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Zones WiFi</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">423</p>
                <p class="text-green-500 text-xs mt-1">
                    <i class="fas fa-circle mr-1"></i>398 en ligne
                </p>
            </div>
        </div>
        
        <!-- Total Tickets Support -->
        <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xl">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase font-bold">Tickets Support</p>
                <p class="text-xl font-bold text-gray-800 dark:text-white">89</p>
                <p class="text-red-500 text-xs mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>12 ouverts
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
        
        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm p-6">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-4">Transactions récentes</h3>
            <div class="space-y-3">
                @php
                    $transactions = [
                        ['client' => 'Dupont Jean', 'amount' => 5000, 'status' => 'completed', 'time' => 'Il y a 2 min'],
                        ['client' => 'Marie Louise', 'amount' => 2500, 'status' => 'pending', 'time' => 'Il y a 15 min'],
                        ['client' => 'Koffi Aime', 'amount' => 10000, 'status' => 'completed', 'time' => 'Il y a 1h'],
                        ['client' => 'Sow Fatou', 'amount' => 3500, 'status' => 'failed', 'time' => 'Il y a 2h'],
                        ['client' => 'Ben Ali', 'amount' => 7500, 'status' => 'completed', 'time' => 'Il y a 3h'],
                    ];
                @endphp
                @foreach($transactions as $trans)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-800 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold mr-3">
                                {{ substr($trans['client'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $trans['client'] }}</p>
                                <p class="text-[10px] text-gray-400">{{ $trans['time'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($trans['amount'], 0, ',', ' ') }} F</p>
                            <p class="text-[10px] @if($trans['status'] === 'completed') text-green-500 @elseif($trans['status'] === 'pending') text-yellow-500 @else text-red-500 @endif font-bold">
                                @if($trans['status'] === 'completed') Terminé @elseif($trans['status'] === 'pending') En attente @else Échoué @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Pending Withdrawals -->
    <div class="bg-white dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Retraits en attente</h3>
            <a href="#" class="text-brand-blue hover:text-brand-dark text-xs font-bold transition">
                Voir tout <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 dark:text-gray-300 text-[10px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="p-5">Propriétaire</th>
                        <th class="p-5">Montant</th>
                        <th class="p-5">Date</th>
                        <th class="p-5">Statut</th>
                        <th class="p-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                    @php
                        $withdrawals = [
                            ['name' => 'Café Internet Abidjan', 'amount' => 45000, 'date' => '12 Fév 2026', 'status' => 'pending'],
                            ['name' => 'Hotel Le Relax', 'amount' => 125000, 'date' => '11 Fév 2026', 'status' => 'pending'],
                            ['name' => 'Coworking Space', 'amount' => 78000, 'date' => '10 Fév 2026', 'status' => 'pending'],
                        ];
                    @endphp
                    @foreach($withdrawals as $wd)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold text-xs">
                                        {{ substr($wd['name'], 0, 2) }}
                                    </div>
                                    <span class="font-bold text-gray-800 dark:text-white">{{ $wd['name'] }}</span>
                                </div>
                            </td>
                            <td class="p-5 font-bold text-brand-blue">{{ number_format($wd['amount'], 0, ',', ' ') }} F</td>
                            <td class="p-5 text-gray-500 dark:text-gray-400">{{ $wd['date'] }}</td>
                            <td class="p-5">
                                <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">
                                    En attente
                                </span>
                            </td>
                            <td class="p-5 text-right">
                                <button class="text-green-500 hover:text-green-600 mr-3 transition" title="Approuver">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-600 transition" title="Rejeter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
