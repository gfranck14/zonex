<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeuxGlobal - UI Kit & Components</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: { dark: '#0A2A2A', green: '#2ECC71', light: '#F4F6F8' }
                    },
                    borderRadius: { '3xl': '1.5rem' }
                }
            }
        }
    </script>
    <style>
        /* Le fix "Zoom 90%" */
        html { font-size: 14px; }
        body { background-color: #e2e8f0; }
    </style>
</head>
<body class="p-10 flex flex-col items-center gap-10">

    <h1 class="text-2xl font-bold text-slate-700 mb-4"> Bibliothèque de Composants (UI Kit)</h1>

    <!-- GRILLE DE PRÉSENTATION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl">

        <!-- ==========================================
             COMPOSANT 1 : Waste Processing (VERSION CORRIGÉE)
             ========================================== -->
        <div class="space-y-2">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Widget: Demi-Jauge</h2>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm flex flex-col items-center justify-center relative">
                <p class="text-gray-800 font-bold text-lg mb-2 w-full text-left">Waste Processing Level</p>
                
                <div class="relative w-48 h-28 flex items-end justify-center">
                    
                    <!-- SVG CALCULÉ -->
                    <svg viewBox="0 0 100 55" class="w-full h-full overflow-visible">
                        <!-- 1. Rail Gris -->
                        <path d="M10,50 A40,40 0 0,1 90,50" 
                              fill="none" 
                              stroke="#F3F4F6" 
                              stroke-width="10" 
                              stroke-linecap="round" />
                        
                        <!-- 2. Barre Verte (Long: 91, Total: 126) -->
                        <path d="M10,50 A40,40 0 0,1 90,50" 
                              fill="none" 
                              stroke="#2ECC71" 
                              stroke-width="10" 
                              stroke-linecap="round"
                              stroke-dasharray="91 126" 
                              stroke-dashoffset="0" />
                              
                        <!-- 3. Segment Noir (Aligné parfaitement) -->
                        <!-- Offset -91 pour commencer là où le vert finit -->
                        <path d="M10,50 A40,40 0 0,1 90,50" 
                              fill="none" 
                              stroke="#0A2A2A" 
                              stroke-width="10" 
                              stroke-linecap="round"
                              stroke-dasharray="35 126"
                              stroke-dashoffset="-91" />
                    </svg>

                    <div class="absolute bottom-0 left-0 w-full flex flex-col items-center mb-2">
                        <span class="text-4xl font-extrabold text-gray-800 tracking-tight">72%</span>
                        <span class="text-xs text-gray-400 font-medium mt-1">Deviation Index 2%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             COMPOSANT 2 : Climate Change (VERSION CORRIGÉE)
             ========================================== -->
        <div class="space-y-2">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Widget: Carte Sombre</h2>
            
            <div class="bg-brand-dark p-6 rounded-3xl shadow-lg text-white flex items-center gap-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl pointer-events-none translate-x-10 -translate-y-10"></div>

                <div class="relative w-20 h-20 flex-shrink-0">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <!-- Fond -->
                        <circle cx="50" cy="50" r="40" 
                                stroke="rgba(255,255,255,0.1)" 
                                stroke-width="8" 
                                fill="none" />
                        
                        <!-- Progression Orange (76.2%) -->
                        <circle cx="50" cy="50" r="40" 
                                stroke="#F97316" 
                                stroke-width="8" 
                                fill="none" 
                                stroke-dasharray="251" 
                                stroke-dashoffset="60" 
                                stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold tracking-wider">76.2</span>
                    </div>
                </div>

                <div class="z-10">
                    <h4 class="font-bold text-lg leading-tight mb-1">Climate Change Index</h4>
                    <p class="text-xs text-gray-400 font-light leading-relaxed opacity-80">
                        Impact of anthropogenic activities on climate.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- SUITE DE LA GRILLE (A ajouter à la suite des autres) -->

    <!-- ==========================================
         GROUPE 3 : LES KPI (Rangée du haut)
         ========================================== -->
    <div class="col-span-1 md:col-span-2 space-y-2">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Groupe: KPI & Sparklines</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- KPI 1: Air Pollution (Vert) -->
            <div class="bg-white p-5 rounded-3xl shadow-sm flex flex-col justify-between h-40">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Air Pollution Level</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">35.05 µg/m³</h3>
                </div>
                <!-- Mini Graphique Barres (SVG fait main) -->
                <div class="flex items-end justify-between gap-1 h-12 mt-2">
                    <div class="w-1/5 bg-emerald-100 rounded-t-md h-[40%]"></div>
                    <div class="w-1/5 bg-emerald-200 rounded-t-md h-[60%]"></div>
                    <div class="w-1/5 bg-emerald-300 rounded-t-md h-[30%]"></div>
                    <div class="w-1/5 bg-brand-green rounded-t-md h-[80%]"></div> <!-- Active -->
                    <div class="w-1/5 bg-emerald-200 rounded-t-md h-[50%]"></div>
                </div>
                <p class="text-[10px] text-brand-green font-bold mt-2 flex items-center">
                    <span class="bg-emerald-100 p-0.5 rounded mr-1">↗</span> 2.3% vs last month
                </p>
            </div>

            <!-- KPI 2: Quality Index (Rouge/Alerte) -->
            <div class="bg-white p-5 rounded-3xl shadow-sm flex flex-col justify-between h-40">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Env. Quality Index</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">75.50 <span class="text-sm text-gray-400 font-normal">/100%</span></h3>
                </div>
                <!-- Mini Graphique -->
                <div class="flex items-end justify-between gap-1 h-12 mt-2">
                    <div class="w-1/5 bg-rose-100 rounded-t-md h-[70%]"></div>
                    <div class="w-1/5 bg-rose-200 rounded-t-md h-[50%]"></div>
                    <div class="w-1/5 bg-rose-400 rounded-t-md h-[90%]"></div> <!-- Active -->
                    <div class="w-1/5 bg-rose-200 rounded-t-md h-[40%]"></div>
                    <div class="w-1/5 bg-rose-100 rounded-t-md h-[60%]"></div>
                </div>
                <p class="text-[10px] text-rose-500 font-bold mt-2 flex items-center">
                    <span class="bg-rose-100 p-0.5 rounded mr-1">↘</span> 1.4% vs last month
                </p>
            </div>

            <!-- KPI 3: Investments -->
            <div class="bg-white p-5 rounded-3xl shadow-sm flex flex-col justify-between h-40">
                <div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Investments</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">$967,570</h3>
                </div>
                 <!-- Mini Graphique -->
                 <div class="flex items-end justify-between gap-1 h-12 mt-2">
                    <div class="w-1/5 bg-emerald-100 rounded-t-md h-[20%]"></div>
                    <div class="w-1/5 bg-emerald-100 rounded-t-md h-[35%]"></div>
                    <div class="w-1/5 bg-emerald-200 rounded-t-md h-[50%]"></div>
                    <div class="w-1/5 bg-brand-green rounded-t-md h-[100%]"></div> <!-- Active -->
                    <div class="w-1/5 bg-emerald-300 rounded-t-md h-[75%]"></div>
                </div>
                <p class="text-[10px] text-brand-green font-bold mt-2 flex items-center">
                    <span class="bg-emerald-100 p-0.5 rounded mr-1">↗</span> 5.1 vs last month
                </p>
            </div>

        </div>
    </div>

    <!-- ==========================================
         COMPOSANT 4 : Renewable Energy (Liste)
         ========================================== -->
    <div class="space-y-2">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Widget: Liste Énergie</h2>
        
        <div class="bg-white p-6 rounded-3xl shadow-sm h-full">
            <div class="flex justify-between items-start mb-4">
                <p class="text-gray-800 font-bold text-lg">Renewable Energy</p>
                <button class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Gros Chiffre -->
                <div class="w-1/3 text-center">
                    <span class="text-5xl font-extrabold text-gray-800 tracking-tight">86%</span>
                </div>
                
                <!-- Liste Légende -->
                <div class="w-2/3 space-y-4">
                    <!-- Item 1 -->
                    <div class="flex items-center justify-between text-xs group">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-green shadow-sm shadow-green-200"></span>
                            <span class="text-gray-500 font-medium">Solar Energy</span>
                        </div>
                        <span class="font-bold text-gray-800">52%</span>
                    </div>
                    <!-- Item 2 -->
                    <div class="flex items-center justify-between text-xs group">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-300"></span>
                            <span class="text-gray-500 font-medium">Hydropower</span>
                        </div>
                        <span class="font-bold text-gray-800">22%</span>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex items-center justify-between text-xs group">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-100"></span>
                            <span class="text-gray-500 font-medium">Wind Energy</span>
                        </div>
                        <span class="font-bold text-gray-800">12%</span>
                    </div>
                </div>
            </div>
            
            <button class="w-full mt-6 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                View Details
            </button>
        </div>
    </div>

    <!-- ==========================================
         COMPOSANT 5 : Carte Map (Placeholder Style)
         ========================================== -->
    <div class="space-y-2">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Widget: Carte Interactive</h2>
        
        <div class="bg-white p-6 rounded-3xl shadow-sm relative overflow-hidden h-full">
            <p class="text-gray-800 font-bold text-lg mb-1">Sources Usage</p>
            <p class="text-xs text-gray-400 mb-6 font-medium">Percentage of renewable energy sources</p>
            
            <!-- Zone Carte -->
            <div class="w-full h-40 bg-slate-50 rounded-2xl relative flex items-center justify-center border border-slate-100">
                <!-- SVG Carte Abstraite (Europe Stylisée) -->
                <svg class="w-full h-full p-4 text-gray-200" fill="currentColor" viewBox="0 0 200 120">
                    <!-- Formes génériques de pays -->
                    <path d="M40,80 L50,60 L70,65 L80,90 L60,100 Z" /> <!-- Spainish like -->
                    <path d="M55,55 L65,40 L85,45 L80,60 Z" /> <!-- France like -->
                    <path d="M70,35 L80,20 L100,25 L95,40 Z" /> <!-- Germany like -->
                    <path d="M90,20 L110,10 L120,30 L100,40 Z" /> <!-- Poland like -->
                    
                    <!-- UKRAINE (Zone active) -->
                    <path d="M105,45 L115,30 L140,35 L145,55 L125,60 Z" class="text-orange-400 drop-shadow-md cursor-pointer hover:text-orange-500 transition-colors" />
                </svg>
                
                <!-- Tooltip Flottant (Le détail important) -->
                <div class="absolute top-1/2 right-[20%] bg-brand-dark text-white p-3 rounded-xl shadow-xl transform -translate-y-8 animate-bounce">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">Ukraine</span>
                        <div class="flex justify-between items-end gap-3">
                            <span class="text-xs font-medium text-gray-300">High Level</span>
                            <span class="text-sm font-bold text-white">89%</span>
                        </div>
                    </div>
                    <!-- Petite flèche tooltip -->
                    <div class="absolute -bottom-1 left-4 w-2 h-2 bg-brand-dark rotate-45"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         COMPOSANT 6 : Communauté & Eau (Bas de page)
         ========================================== -->
    <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Widget Water Level -->
        <div class="bg-white p-4 pr-6 rounded-[2rem] shadow-sm flex items-center gap-4 w-full">
            <div class="w-14 h-14 rounded-full border-[3px] border-emerald-100 flex items-center justify-center flex-shrink-0 relative">
                 <!-- Cercle de progression SVG -->
                 <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#D1FAE5" stroke-width="3" />
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#34D399" stroke-width="3" stroke-dasharray="60, 100" />
                 </svg>
                 <span class="text-xs font-bold text-gray-700">57m</span>
            </div>
            <div>
               <h4 class="font-bold text-sm text-gray-800">Water level in Dnipro</h4>
               <p class="text-[10px] text-gray-400 mt-0.5">Water level value with ice melting</p>
            </div>
       </div>

       <!-- Widget Communauté (Leaves) -->
       <div class="relative rounded-[2rem] shadow-lg overflow-hidden bg-brand-dark text-white h-32 group cursor-pointer">
           <!-- Image de fond (Feuilles) -->
           <div class="absolute inset-0">
               <img src="https://images.unsplash.com/photo-1596327039600-b6f709192404?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                    class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700" alt="Community">
               <!-- Dégradé pour lisibilité -->
               <div class="absolute inset-0 bg-gradient-to-r from-[#0A2A2A] via-[#0A2A2A]/80 to-transparent"></div>
           </div>

           <div class="relative z-10 p-6 flex flex-col justify-center h-full">
                <div class="flex justify-between items-start">
                    <h3 class="font-bold text-lg w-2/3 leading-tight">Let's join our community</h3>
                    <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center group-hover:bg-white group-hover:text-brand-dark transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </div>
                </div>
                
                <!-- Avatar Stack -->
                <div class="mt-3 flex items-center gap-3">
                   <div class="flex -space-x-3">
                       <img class="w-8 h-8 rounded-full border-2 border-[#0A2A2A]" src="https://i.pravatar.cc/150?img=32" alt="">
                       <img class="w-8 h-8 rounded-full border-2 border-[#0A2A2A]" src="https://i.pravatar.cc/150?img=44" alt="">
                       <img class="w-8 h-8 rounded-full border-2 border-[#0A2A2A]" src="https://i.pravatar.cc/150?img=12" alt="">
                   </div>
                   <span class="text-[10px] font-semibold bg-white/10 px-2 py-1 rounded-full backdrop-blur-sm">230k+ people</span>
                </div>
           </div>
       </div>

    </div>

</body>
</html>
