@extends('superadmin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard SuperAdmin')

@section('content')
<div class="space-y-6">
    <!-- KPIs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Propriétaires -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Propriétaires</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_proprios'] }}</p>
                    <p class="text-green-400 text-sm mt-1">
                        <i class="fas fa-check-circle"></i> {{ $stats['proprios_actifs'] }} actifs
                    </p>
                </div>
                <div class="text-4xl text-red-500">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Zones -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Zones WiFi</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_zones'] }}</p>
                    <p class="text-blue-400 text-sm mt-1">
                        <i class="fas fa-wifi"></i> Actives
                    </p>
                </div>
                <div class="text-4xl text-blue-500">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        
        <!-- Revenue Aujourd'hui -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Revenue Aujourd'hui</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['revenue_today'], 0, ',', ' ') }}</p>
                    <p class="text-gray-400 text-sm mt-1">FCFA</p>
                </div>
                <div class="text-4xl text-green-500">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        
        <!-- Retraits en attente -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Retraits Pending</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['pending_withdrawals'] }}</p>
                    <p class="text-yellow-400 text-sm mt-1">
                        <i class="fas fa-clock"></i> En attente
                    </p>
                </div>
                <div class="text-4xl text-yellow-500">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Revenue du Mois</h3>
            <p class="text-2xl font-bold text-green-400">{{ number_format($stats['revenue_month'], 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Revenue Total</h3>
            <p class="text-2xl font-bold text-blue-400">{{ number_format($stats['revenue_total'], 0, ',', ' ') }} FCFA</p>
        </div>
        
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-white mb-2">Total Transactions</h3>
            <p class="text-2xl font-bold text-purple-400">{{ number_format($stats['total_transactions'], 0, ',', ' ') }}</p>
        </div>
    </div>
    
    <!-- Top Propriétaires -->
    <div class="card p-6">
        <h3 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 5 Propriétaires
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-gray-400 py-3 px-4">Rang</th>
                        <th class="text-left text-gray-400 py-3 px-4">Propriétaire</th>
                        <th class="text-left text-gray-400 py-3 px-4">Zones</th>
                        <th class="text-left text-gray-400 py-3 px-4">Revenue</th>
                        <th class="text-left text-gray-400 py-3 px-4">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProprios as $index => $proprio)
                    <tr class="border-b border-gray-800 hover:bg-gray-800">
                        <td class="py-3 px-4">
                            @if($index < 3)
                                <i class="fas fa-medal text-{{ ['yellow', 'gray', 'orange'][$index] }}-500"></i>
                            @else
                                <span class="text-gray-500">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-white font-medium">
                            {{ $proprio->prenom }} {{ $proprio->nom }}
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ $proprio->wifizones_count }}</td>
                        <td class="py-3 px-4 text-green-400 font-semibold">
                            {{ number_format($proprio->revenue, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="py-3 px-4 text-gray-400 text-sm">{{ $proprio->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('superadmin.proprios.index') }}" class="card p-6 hover:bg-gray-800 transition">
            <i class="fas fa-users text-red-500 text-2xl mb-2"></i>
            <h4 class="text-white font-semibold">Gérer les Propriétaires</h4>
            <p class="text-gray-400 text-sm mt-1">CRUD, activation, impersonation</p>
        </a>
        
        <a href="{{ route('superadmin.withdrawals.index') }}" class="card p-6 hover:bg-gray-800 transition">
            <i class="fas fa-money-bill-wave text-yellow-500 text-2xl mb-2"></i>
            <h4 class="text-white font-semibold">Retraits en Attente</h4>
            <p class="text-gray-400 text-sm mt-1">{{ $stats['pending_withdrawals'] }} demandes</p>
        </a>
        
        <a href="{{ route('superadmin.analytics.index') }}" class="card p-6 hover:bg-gray-800 transition">
            <i class="fas fa-chart-line text-blue-500 text-2xl mb-2"></i>
            <h4 class="text-white font-semibold">Analytics Avancés</h4>
            <p class="text-gray-400 text-sm mt-1">Rapports et statistiques</p>
        </a>
    </div>
</div>
@endsection
