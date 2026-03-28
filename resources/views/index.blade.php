<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WifiProfit - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<!-- BODY : Fond sombre auto en dark mode -->
<body class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-slate-800 dark:text-slate-100 h-screen w-screen overflow-hidden flex transition-colors duration-300">


    <!-- MAIN CONTENT -->
    <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
        <!-- Fond Blanc en Light, Noir en Dark -->
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            
            <div id="header-container"></div>

            <!-- SECTION 1: KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                
                <!-- KPI 1 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-brand-blue/30 transition-all cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-brand-green">
                            <i class="fas fa-ticket-alt text-lg"></i>
                        </div>
                        <span class="flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 px-2 py-1 rounded-lg text-[10px] font-bold">ACTIFS</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">42</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets utilisés maintenant</p>
                </div>

                <!-- KPI 2 (Dark Accent) -->
                <div class="bg-brand-sidebarLight dark:bg-brand-blue p-5 rounded-3xl shadow-lg text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-green opacity-20 rounded-full blur-2xl group-hover:opacity-30 transition"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white"><i class="fas fa-money-bill-wave text-lg"></i></div>
                        <span class="text-brand-green dark:text-white text-xs font-bold">+12% vs hier</span>
                    </div>
                    <h3 class="text-3xl font-bold text-white relative z-10">24.500 <span class="text-sm text-blue-200 font-normal">F</span></h3>
                    <p class="text-xs text-blue-200 font-medium mt-1 relative z-10">Revenu aujourd'hui</p>
                </div>

                <!-- KPI 3 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <i class="fas fa-ticket-alt text-lg"></i>
                        </div>
                        <button class="text-[10px] font-bold text-gray-400 hover:text-brand-blue">IMPORT CSV</button>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">128</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">Tickets en stock</p>
                </div>

                <!-- KPI 4 -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-white dark:border-slate-700 hover:border-gray-200 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                            <i class="fas fa-star text-lg"></i>
                        </div>
                        <span class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">TOP VENTE</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white tracking-tight">Forfait 1h</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium mt-1">60% des ventes totales</p>
                </div>
            </div>

            <!-- SECTION 2: Chart & Zones List -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-80 mb-6">
                <!-- Chart Area -->
                <div class="lg:col-span-2 bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">Ventes de la semaine 📈</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Volume total de tickets vendus</p>
                        </div>
                        <select class="bg-gray-50 dark:bg-slate-700 border-none text-xs font-bold text-gray-500 dark:text-gray-300 rounded-lg py-2 px-3 outline-none cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                            <option>Cette semaine</option>
                            <option>Semaine dernière</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 w-full relative flex items-end px-2 pb-2">
                        <!-- Grille -->
                        <div class="absolute inset-0 flex flex-col justify-between text-[10px] text-gray-300 dark:text-slate-600 pointer-events-none pb-6">
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full">100</div>
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full">50</div>
                            <div class="border-b border-gray-100 dark:border-slate-700 w-full">0</div>
                        </div>
                        
                        <!-- Courbe -->
                        <svg class="w-full h-full overflow-visible z-10" viewBox="0 0 100 50" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="salesGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#0EA5E9" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="#0EA5E9" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <path d="M0,50 L0,35 L16,40 L33,20 L50,30 L66,15 L83,5 L100,10 V50 Z" fill="url(#salesGradient)" />
                            <path d="M0,35 L16,40 L33,20 L50,30 L66,15 L83,5 L100,10" fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                            <circle cx="83" cy="5" r="1.5" fill="#0EA5E9" stroke="white" stroke-width="0.5" />
                        </svg>
                        
                        <div class="absolute bottom-0 left-0 w-full flex justify-between text-[10px] text-gray-400 dark:text-gray-500 px-1">
                            <span>Lun</span><span>Mar</span><span>Mer</span><span>Jeu</span><span>Ven</span><span>Sam</span><span>Dim</span>
                        </div>
                    </div>
                </div>

                <!-- Stock Ticket par Zone -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Stock par Zone</h3>
                        <a href="wifi-zones.html" class="text-xs font-bold text-brand-blue hover:underline">Gérer</a>
                    </div>

                    <div class="flex-1 overflow-y-auto space-y-3 no-scrollbar pr-2">
                        <!-- Zone 1 -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm text-gray-400">
                                    <i class="fas fa-wifi text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">Bar Central</p>
                                    <p class="text-[10px] text-green-600 dark:text-green-400 font-medium flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> En ligne
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-bold text-gray-800 dark:text-white">84</span>
                                <span class="text-[10px] text-gray-400">tickets</span>
                            </div>
                        </div>

                        <!-- Zone 2 -->
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shadow-sm text-gray-400">
                                    <i class="fas fa-wifi text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">Campus Nord</p>
                                    <p class="text-[10px] text-orange-500 font-medium flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span> Instable
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-bold text-gray-800 dark:text-white">12</span>
                                <span class="text-[10px] text-gray-400">tickets</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>