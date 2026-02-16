@extends('superadmin.super_admin_layout')

@section('page-title', 'Analytics')
@section('page-subtitle', 'Statistiques et analyses globales')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Revenue -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Revenus Totaux</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format(1250000, 0, ',', ' ') }} CFA</p>
                <p class="text-green-500 text-sm mt-2">
                    <i class="fas fa-arrow-up mr-1"></i>+12.5% ce mois
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <i class="fas fa-wallet text-red-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Transactions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">1,234</p>
                <p class="text-blue-500 text-sm mt-2">
                    <i class="fas fa-exchange-alt mr-1"></i>Ce mois
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <i class="fas fa-exchange-alt text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Average Transaction -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Panier Moyen</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format(1013, 0, ',', ' ') }} CFA</p>
                <p class="text-yellow-500 text-sm mt-2">
                    <i class="fas fa-chart-line mr-1"></i>Par transaction
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Conversion Rate -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Taux de Conversion</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">78%</p>
                <p class="text-green-500 text-sm mt-2">
                    <i class="fas fa-percentage mr-1"></i>Achats réussis
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <i class="fas fa-percentage text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Over Time -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <h3 class="text-gray-900 dark:text-white font-semibold mb-4">Revenus sur les 30 derniers jours</h3>
        <div class="h-64 flex items-end justify-between space-x-2">
            @php
                $days = ['1', '5', '10', '15', '20', '25', '30'];
                $revenues = [45000, 52000, 48000, 61000, 55000, 67000, 72000];
                $maxRevenue = max($revenues);
            @endphp
            @foreach($days as $index => $day)
                @php
                    $height = ($revenues[$index] / $maxRevenue) * 200;
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <div class="text-xs text-gray-400 mb-1">{{ number_format($revenues[$index], 0, ',', ' ') }}</div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-t" style="height: {{ $height }}px">
                        <div class="bg-red-500 w-full h-full rounded-t opacity-80 hover:opacity-100 transition-opacity cursor-pointer"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-2">Jour {{ $day }}</div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Top Operators -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <h3 class="text-gray-900 dark:text-white font-semibold mb-4">Répartition par Opérateur</h3>
        <div class="space-y-4">
            @php
                $operators = [
                    ['name' => 'Moov Africa', 'count' => 456, 'percent' => 45],
                    ['name' => 'MTN', 'count' => 312, 'percent' => 31],
                    ['name' => 'Orange', 'count' => 234, 'percent' => 23],
                ];
            @endphp
            @foreach($operators as $op)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">{{ $op['name'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400">{{ $op['count'] }} transactions</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $op['percent'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Top Zones -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
    <h3 class="text-gray-900 dark:text-white font-semibold mb-4">Top 10 Zones par Revenus</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-gray-700">
                    <th class="pb-3">Rang</th>
                    <th class="pb-3">Zone WiFi</th>
                    <th class="pb-3">Propriétaire</th>
                    <th class="pb-3">Transactions</th>
                    <th class="pb-3">Revenus</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 dark:text-white text-sm">
                @php
                    $zones = [
                        ['name' => 'Café Internet Centre', 'proprio' => 'Koffi A.', 'transactions' => 156, 'revenue' => 245000],
                        ['name' => 'Hotel Le Relax', 'proprio' => 'Diallo M.', 'transactions' => 134, 'revenue' => 198000],
                        ['name' => 'Coworking Space', 'proprio' => 'Sow F.', 'transactions' => 98, 'revenue' => 156000],
                        ['name' => 'Restaurant La Pause', 'proprio' => 'N\'guessan K.', 'transactions' => 87, 'revenue' => 134000],
                        ['name' => 'Cyber Café Bac', 'proprio' => 'Acket J.', 'transactions' => 76, 'revenue' => 112000],
                    ];
                @endphp
                @foreach($zones as $index => $zone)
                    <tr class="border-b border-gray-100 dark:border-gray-700/50">
                        <td class="py-3 font-medium">{{ $index + 1 }}</td>
                        <td class="py-3">{{ $zone['name'] }}</td>
                        <td class="py-3 text-gray-500 dark:text-gray-400">{{ $zone['proprio'] }}</td>
                        <td class="py-3">{{ $zone['transactions'] }}</td>
                        <td class="py-3 font-semibold text-green-600 dark:text-green-400">{{ number_format($zone['revenue'], 0, ',', ' ') }} CFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
