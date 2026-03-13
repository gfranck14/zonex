<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wifi Zone Components - AeuxStyle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { 
                            dark: '#0A2A2A', 
                            green: '#2ECC71', 
                            light: '#F4F6F8',
                            warning: '#F59E0B' 
                        }
                    },
                    borderRadius: { '3xl': '1.5rem' }
                }
            }
        }
    </script>
    <style>
        html { font-size: 14px; }
        body { background-color: #e2e8f0; }
        /* Pour masquer la checkbox native */
        .custom-checkbox:checked {
            background-color: #0A2A2A;
            border-color: #0A2A2A;
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body class="p-10 flex flex-col items-center gap-10">

    <h1 class="text-2xl font-bold text-slate-700">📡 Composants Gestion Wifi Zone</h1>

    <!-- GRILLE PRINCIPALE -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl">

        <!-- ==========================================
             1. STOCK WIDGET (Jauge d'alerte)
             But : Dire au vendeur "Attention, importe des tickets !"
             ========================================== -->
        <div class="space-y-2 col-span-1">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">01. Stock Tickets (Alert)</h2>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm flex flex-col items-center relative overflow-hidden">
                <!-- Header -->
                <div class="w-full flex justify-between items-center mb-2">
                    <span class="font-bold text-gray-800">Forfait 1 Heure</span>
                    <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full">LOW STOCK</span>
                </div>

                <!-- Jauge -->
                <div class="relative w-48 h-28 flex items-end justify-center">
                    <svg viewBox="0 0 100 55" class="w-full h-full overflow-visible">
                        <!-- Fond Gris -->
                        <path d="M10,50 A40,40 0 0,1 90,50" fill="none" stroke="#F3F4F6" stroke-width="8" stroke-linecap="round" />
                        
                        <!-- Barre Rouge (Car stock bas) - 25% -->
                        <path d="M10,50 A40,40 0 0,1 90,50" 
                              fill="none" stroke="#EF4444" stroke-width="8" stroke-linecap="round"
                              stroke-dasharray="31.5 126" stroke-dashoffset="0" /> <!-- 25% de 126 = ~31.5 -->
                    </svg>
                    <!-- Gros Chiffre -->
                    <div class="absolute bottom-0 left-0 w-full flex flex-col items-center mb-2">
                        <span class="text-4xl font-extrabold text-gray-800">12</span>
                        <span class="text-xs text-gray-400 font-medium">Tickets restants</span>
                    </div>
                </div>

                <!-- Footer Action -->
                <p class="text-center text-xs text-gray-500 mb-4 mt-2">Capacité totale: 500 tickets</p>
                <button class="w-full bg-brand-dark text-white py-3 rounded-xl text-sm font-medium hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer CSV
                </button>
            </div>
        </div>


        <!-- ==========================================
             2. IMPORT FILE (Drag & Drop)
             But : Importer le fichier Mikrotik
             ========================================== -->
        <div class="space-y-2 col-span-1">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">02. Import Mikrotik CSV</h2>
            
            <div class="bg-white p-1 rounded-3xl shadow-sm h-full">
                <!-- Zone pointillée interactive -->
                <div class="h-full border-2 border-dashed border-gray-200 rounded-[1.3rem] flex flex-col items-center justify-center p-8 text-center hover:border-brand-green hover:bg-green-50/30 transition-all cursor-pointer group">
                    
                    <!-- Icône CSV stylée -->
                    <div class="w-16 h-16 bg-brand-light rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mb-1">Import Tickets</h3>
                    <p class="text-xs text-gray-400 mb-6 px-4">Drag & drop your <span class="font-bold text-gray-600">Mikrotik CSV</span> file here or browse.</p>
                    
                    <button class="bg-white border border-gray-200 text-gray-700 px-6 py-2 rounded-xl text-xs font-bold shadow-sm group-hover:border-brand-green group-hover:text-brand-green transition-colors">
                        Browse Files
                    </button>
                </div>
            </div>
        </div>


        <!-- ==========================================
             3. SMART TABLE (Tickets)
             But : Lister les tickets importés et vendus
             ========================================== -->
        <div class="space-y-2 col-span-1 md:col-span-2 lg:col-span-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">03. Smart Ticket Table</h2>
            
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <!-- Toolbar du tableau -->
                <div class="p-5 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-lg text-gray-800">Tickets Database</h3>
                        <span class="bg-brand-light text-gray-500 text-[10px] font-bold px-2 py-1 rounded-lg">1,240 Total</span>
                    </div>
                    
                    <div class="flex gap-2">
                        <!-- Search Input -->
                        <div class="relative">
                            <input type="text" placeholder="Search user or code..." class="pl-9 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-brand-green/20 outline-none w-48 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <!-- Filter Button -->
                        <button class="p-2 bg-gray-50 rounded-xl text-gray-500 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg></button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-5 w-10"><input type="checkbox" class="custom-checkbox w-4 h-4 rounded border-gray-300 appearance-none border cursor-pointer"></th>
                                <th class="p-5">Code Ticket</th>
                                <th class="p-5">Utilisateur (Client)</th>
                                <th class="p-5">Forfait</th>
                                <th class="p-5">Prix</th>
                                <th class="p-5">Statut</th>
                                <th class="p-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">
                            
                            <!-- ROW 1 : VENDU -->
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="p-5"><input type="checkbox" class="custom-checkbox w-4 h-4 rounded border-gray-300 appearance-none border cursor-pointer"></td>
                                <td class="p-5 font-mono font-bold text-gray-700">TK-8849-XXXX</td>
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs">JD</div>
                                        <div>
                                            <p class="font-bold text-gray-800">John Doe</p>
                                            <p class="text-[10px] text-gray-400">@john_d</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5 font-medium text-gray-600">24 Heures</td>
                                <td class="p-5 font-bold text-gray-800">500 F</td>
                                <td class="p-5">
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> VENDU
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <button class="text-gray-300 hover:text-brand-dark transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg></button>
                                </td>
                            </tr>

                            <!-- ROW 2 : EN ATTENTE -->
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="p-5"><input type="checkbox" class="custom-checkbox w-4 h-4 rounded border-gray-300 appearance-none border cursor-pointer"></td>
                                <td class="p-5 font-mono font-bold text-gray-700">TK-9921-XXXX</td>
                                <td class="p-5">
                                    <span class="text-gray-400 italic text-xs">- Non attribué -</span>
                                </td>
                                <td class="p-5 font-medium text-gray-600">1 Heure</td>
                                <td class="p-5 font-bold text-gray-800">100 F</td>
                                <td class="p-5">
                                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> LIBRE
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <button class="text-gray-300 hover:text-brand-dark transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg></button>
                                </td>
                            </tr>

                            <!-- ROW 3 : EXPIRÉ -->
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="p-5"><input type="checkbox" class="custom-checkbox w-4 h-4 rounded border-gray-300 appearance-none border cursor-pointer"></td>
                                <td class="p-5 font-mono font-bold text-gray-700 line-through opacity-50">TK-1002-XXXX</td>
                                <td class="p-5">
                                    <div class="flex items-center gap-3 opacity-50">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">AS</div>
                                        <div>
                                            <p class="font-bold text-gray-800">Alice Smith</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5 font-medium text-gray-400">7 Jours</td>
                                <td class="p-5 font-bold text-gray-400">2000 F</td>
                                <td class="p-5">
                                    <span class="bg-orange-50 text-orange-400 px-2 py-1 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> EXPIRÉ
                                    </span>
                                </td>
                                <td class="p-5 text-right">
                                    <button class="text-gray-300 hover:text-brand-dark transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg></button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 flex justify-center">
                    <button class="text-xs font-bold text-gray-400 hover:text-brand-dark transition">Load more</button>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. WALLET & REVENUS (Mobile Money)
             But : Voir l'argent encaissé par opérateur
             ========================================== -->
        <div class="space-y-2 col-span-1">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">04. Wallet Mobile Money</h2>
            
            <div class="bg-brand-dark p-6 rounded-3xl shadow-lg text-white relative overflow-hidden h-full flex flex-col justify-between">
                <!-- Déco fond -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-brand-green opacity-10 rounded-full blur-3xl -translate-y-10 translate-x-10"></div>
                
                <!-- Header Solde -->
                <div>
                    <p class="text-gray-400 text-xs font-medium mb-1">Solde disponible</p>
                    <h3 class="text-4xl font-bold tracking-tight">125.000 <span class="text-lg text-brand-green">F</span></h3>
                    <div class="flex items-center gap-2 mt-4">
                        <span class="bg-white/10 px-3 py-1 rounded-full text-[10px] flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Agrégateur Connecté
                        </span>
                    </div>
                </div>

                <!-- Répartition Opérateurs -->
                <div class="space-y-3 mt-6">
                    <!-- MTN -->
                    <div class="flex items-center justify-between bg-white/5 p-3 rounded-xl border border-white/5 hover:bg-white/10 transition cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-black font-bold text-[10px]">MTN</div>
                            <span class="text-sm font-medium">MTN Mobile Money</span>
                        </div>
                        <span class="text-sm font-bold">75.000 F</span>
                    </div>
                    <!-- MOOV/ORANGE -->
                    <div class="flex items-center justify-between bg-white/5 p-3 rounded-xl border border-white/5 hover:bg-white/10 transition cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-[10px]">OM</div>
                            <span class="text-sm font-medium">Orange Money</span>
                        </div>
                        <span class="text-sm font-bold">50.000 F</span>
                    </div>
                </div>

                <!-- Action -->
                <button class="w-full mt-6 bg-brand-green text-brand-dark py-3 rounded-xl text-sm font-bold hover:bg-green-400 transition">
                    Demander un retrait
                </button>
            </div>
        </div>


        <!-- ==========================================
             5. PLAN CARD (Création de Forfait)
             But : Configurer ce qu'on vend (Liaison Ticket <-> Prix)
             ========================================== -->
        <div class="space-y-2 col-span-1">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">05. Plan / Forfait Card</h2>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-transparent hover:border-brand-green/30 transition-all h-full relative group">
                
                <!-- Badge "Populaire" -->
                <div class="absolute top-0 right-0 bg-brand-green text-brand-dark text-[10px] font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl">
                    POPULAIRE
                </div>

                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-light flex items-center justify-center text-2xl">⚡</div>
                </div>

                <h3 class="text-xl font-bold text-gray-800">Pass 24 Heures</h3>
                <p class="text-xs text-gray-400 mt-1">Connexion illimitée pour une journée entière.</p>

                <div class="my-6">
                    <span class="text-3xl font-bold text-brand-dark">500 F</span>
                    <span class="text-xs text-gray-400">/ utilisateur</span>
                </div>

                <!-- Détails techniques (Liaison Mikrotik) -->
                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Durée Mikrotik: <span class="font-bold">24h 00m</span></span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Vitesse Max: <span class="font-bold">5 Mbps</span></span>
                    </div>
                </div>

                <!-- Boutons Admin -->
                <div class="flex gap-2">
                    <button class="flex-1 bg-brand-light text-brand-dark py-2 rounded-xl text-xs font-bold hover:bg-gray-200 transition">Modifier</button>
                    <button class="w-10 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>


        <!-- ==========================================
             6. LIVE SESSION MONITOR (Temps Réel)
             But : Voir les clients connectés et le temps restant
             ========================================== -->
        <div class="space-y-2 col-span-1">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wide">06. Live Sessions (Active)</h2>
            
            <div class="bg-white p-5 rounded-3xl shadow-sm h-full flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800">Utilisateurs Actifs</h3>
                    <span class="flex items-center gap-1.5 bg-green-50 text-green-600 px-2 py-1 rounded-lg text-[10px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> 42 Online
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto space-y-4 pr-1 max-h-64 no-scrollbar">
                    
                    <!-- User 1 (Beaucoup de temps) -->
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">JD</div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-bold text-gray-800">John Doe</span>
                                <span class="text-[10px] font-mono text-gray-400">10.0.0.45</span>
                            </div>
                            <!-- Barre de progression Temps -->
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-brand-green h-full rounded-full" style="width: 80%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[10px] text-gray-400">Pass 24h</span>
                                <span class="text-[10px] font-bold text-brand-green">18h restants</span>
                            </div>
                        </div>
                    </div>

                    <!-- User 2 (Bientôt fini) -->
                    <div class="flex items-center gap-3 opacity-80">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">AL</div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-bold text-gray-800">Alice L.</span>
                                <span class="text-[10px] font-mono text-gray-400">10.0.0.12</span>
                            </div>
                            <!-- Barre Rouge -->
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-red-400 h-full rounded-full" style="width: 10%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[10px] text-gray-400">Pass 1h</span>
                                <span class="text-[10px] font-bold text-red-400">5 min restants</span>
                            </div>
                        </div>
                    </div>

                     <!-- User 3 -->
                     <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">MK</div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-bold text-gray-800">Mike K.</span>
                                <span class="text-[10px] font-mono text-gray-400">10.0.0.89</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-brand-green h-full rounded-full" style="width: 45%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[10px] text-gray-400">Pass 24h</span>
                                <span class="text-[10px] font-bold text-brand-green">10h restants</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</body>
</html>