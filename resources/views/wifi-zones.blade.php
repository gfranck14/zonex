<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wifi Zone Manager - Mes Zones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Heroicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Surcharge CSS spécifique pour harmoniser les onglets avec le thème -->
    <style>
        .tab-btn.active {
            border-bottom-color: #072b47; /* brand-blue */
            color: #072b47;
        }
        /* En dark mode, les onglets inactifs doivent être clairs */
        .dark .tab-btn { color: #94a3b8; }
        .dark .tab-btn:hover { color: #e2e8f0; }
        .dark .tab-btn.active { color: #072b47; }
    </style>
</head>
<body class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-slate-800 dark:text-slate-100 h-screen w-screen overflow-hidden flex transition-colors duration-300">

    <!-- MAIN CONTENT -->
    <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            
            <!-- VUE 1 : LISTE DES ZONES -->
            <div id="zone-list" class="animate-fade-in">
                <div id="header-container"></div>
                
                <!-- GRILLE DES ZONES -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- ZONE 1: Bar Central (OK) -->
                    <div onclick="showDetail('Bar Central', 'WZ-8821-XJ')" class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border border-transparent hover:border-brand-blue/30 transition-all group relative cursor-pointer flex flex-col justify-between">
                        
                        <!-- En-tête : Titre + Badge État -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Bar Central</h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Cotonou, Quartier Haie Vive</p>
                            </div>
                            <!-- Badge Vert : OK -->
                            <span class="bg-brand-green text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-md shadow-green-500/20">
                                STOCK OK
                            </span>
                        </div>

                        <!-- Corps : Ventes & Stock -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ventes jour</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-white">12</span>
                            </div>
                            
                            <!-- NOUVELLE BARRE DE PROGRESSION -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500 dark:text-gray-400">Niveau de Stock</span>
                                    <!-- Calcul visuel : 128 tickets -->
                                    <span class="font-bold text-brand-green">128 tickets</span>
                                </div>
                                <!-- Barre unifiée (Pleine à 500, ici ~25%) -->
                                <div class="w-full bg-gray-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                    <div class="bg-brand-green h-full rounded-full transition-all duration-500" style="width: 25%"></div>
                                </div>
                            </div>

                            <!-- Bouton Copier (Stop Propagation pour ne pas ouvrir le détail) -->
                            <button onclick="event.stopPropagation(); showToast('Lien copié !')" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-brand-blue hover:underline py-2">
                                <i class="fas fa-copy text-sm"></i>
                                Copier lien du Portail captif
                            </button>

                        <!-- Pied : Actions -->
                        <div class="flex gap-2 mt-auto">
                            <!-- Bouton Gérer Stock (Redirection vers Tickets) -->
                            <button onclick="event.stopPropagation(); window.location.href='tickets.html'" class="flex-1 bg-brand-sidebarLight dark:bg-slate-700 text-white py-3 rounded-xl text-xs font-bold hover:brightness-110 transition shadow-lg">
                                GÉRER LE STOCK
                            </button>
                            <!-- Bouton Paramètres (Ouvre le détail) -->
                            <button onclick="event.stopPropagation(); showDetail('Bar Central', 'WZ-8821-XJ')" class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-xl hover:text-brand-blue transition">
                                <i class="fas fa-cog text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ZONE 2: Campus Nord (ALERTE) -->
                    <div onclick="showDetail('Campus Nord', 'WZ-4402-AB')" class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border border-orange-100 dark:border-orange-900/30 hover:border-orange-200 transition-all group relative cursor-pointer flex flex-col justify-between">
                        
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Campus Nord</h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Abomey-Calavi</p>
                            </div>
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-md shadow-orange-500/20">
                                ALERTE STOCK
                            </span>
                        </div>

                        <div class="space-y-4 mb-6">
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ventes jour</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-white">4</span>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-red-500 font-bold">⚠️ Stock Critique</span>
                                    <span class="font-bold text-orange-500">12 tickets</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                    <div class="bg-red-500 h-full rounded-full animate-pulse" style="width: 5%"></div>
                                </div>
                            </div>

                            <!-- Bouton Copier (Stop Propagation pour ne pas ouvrir le détail) -->
                            <button onclick="event.stopPropagation(); showToast('Lien copié !')" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-brand-blue hover:underline py-2">
                                <i class="fas fa-copy text-sm"></i>
                                Copier lien du Portail captif
                            </button>
                        </div>

                        <div class="flex gap-2 mt-auto">
                            <!-- Bouton Gérer Stock (Redirection vers Tickets) -->
                            <button onclick="event.stopPropagation(); window.location.href='tickets.html'" class="flex-1 bg-brand-sidebarLight dark:bg-slate-700 text-white py-3 rounded-xl text-xs font-bold hover:brightness-110 transition shadow-lg">
                                GÉRER LE STOCK
                            </button>
                            <!-- Bouton Paramètres (Ouvre le détail) -->
                            <button onclick="event.stopPropagation(); showDetail('Campus Nord', 'WZ-4402-AB')" class="w-10 h-10 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-xl hover:text-brand-blue transition">
                                <i class="fas fa-cog text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VUE 2 : DÉTAIL D'UNE ZONE -->
            <div id="zone-detail" class="hidden animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <button onclick="showList()" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-brand-dark transition text-sm font-bold"><i class="fas fa-arrow-left text-sm"></i>Retour aux zones</button>
                    <div class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 font-mono text-xs px-3 py-1.5 rounded-lg">ID: <span id="display-zone-id" class="text-gray-800 dark:text-white font-bold">WZ-XXXX</span></div>
                </div>
                <div class="flex items-start gap-5 mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-brand-blue flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fas fa-map-marker-alt text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white" id="zone-title">Titre de la zone</h2>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="w-2 h-2 rounded-full bg-brand-blue animate-pulse"></span>
                            <span class="text-sm text-brand-blue font-bold">Zone Active</span>
                        </div>
                    </div>
                </div>
                <!-- TABS -->
                <div class="border-b border-gray-200 dark:border-slate-700 mb-8 flex gap-8">
                    <button onclick="switchTab('general')" id="tab-general" class="tab-btn active text-sm">Général</button>
                    <button onclick="switchTab('integration')" id="tab-integration" class="tab-btn text-sm">Intégration (Mikrotik)</button>
                    <button onclick="switchTab('custom')" id="tab-custom" class="tab-btn text-sm">Personnalisation</button>
                </div>
                <!-- CONTENU ONGLETS -->
                <div id="content-general" class="tab-content">
                    <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm max-w-3xl">
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="input-floating-group">
                                    <input type="text" value="Bar Central" placeholder=" " class="input-floating">
                                    <label class="floating-label">Nom de la zone</label>
                                </div>
                                <div><label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Type de lieu</label><select class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none"><option>Restaurant / Bar</option><option>Hôtel</option></select></div>
                            </div>
                            <div class="input-floating-group">
                                <input type="text" value="Quartier Haie Vive, Rue 12, Cotonou" placeholder=" " class="input-floating">
                                <label class="floating-label">Adresse physique</label>
                            </div>
                            <div class="pt-4"><button class="bg-brand-dark text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-800 transition">Enregistrer</button></div>
                        </div>
                    </div>
                </div>
                <div id="content-integration" class="tab-content hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lien du Portail captif</h3>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">À configurer dans le Hotspot Mikrotik (Login URL).</p>
                                </div>
                            </div>
                            <div class="bg-slate-900 dark:bg-black rounded-2xl p-5 relative group border border-slate-700 shadow-inner">
                                <code class="text-brand-blue font-mono text-xs break-all block pr-10 leading-relaxed">https://auth.wifizone-manager.com/connect?zone_id=<span class="text-white font-bold" id="code-zone-id">WZ-8821-XJ</span></code>
                                <button onclick="showToast('Lien d\'authentification copié', 'success')" class="absolute top-4 right-4 text-gray-400 hover:text-white" title="Copier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                </button>
                            </div>
                            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-100 dark:border-yellow-800 rounded-xl p-4 flex gap-3">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <p class="text-xs text-yellow-800 dark:text-yellow-200">Assurez-vous que le <strong>Walled Garden</strong> est configuré.</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Walled Garden 🛡️</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Domaines à autoriser (Allow) IP List.</p>
                            <ul class="space-y-3 text-xs font-mono text-gray-600 dark:text-gray-300">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    *.wifizone-manager.com
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    *.cinetpay.com
                                </li>
                            </ul>
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs font-bold rounded-xl border border-red-100 dark:border-red-800 leading-relaxed">
                                ⚠️ Si vous ne mettez pas ces domaines dans le Walled Garden, vos clients verront une page blanche et ne pourront pas vous payer.
                            </div>
                            <button onclick="showToast('Liste Walled Garden copiée', 'success')" class="mt-6 w-full py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700">Copier la liste</button>
                        </div>
                    </div>
                </div>
                <div id="content-custom" class="tab-content hidden">
                    <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm max-w-4xl flex gap-10">
                        <div class="flex-1 space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Apparence Portail Captif</h3>
                            <div class="input-floating-group">
                                <input type="text" value="Wifi Bar Central" placeholder=" " class="input-floating">
                                <label class="floating-label">Nom affiché</label>
                                <p class="text-xs text-gray-400 mt-1">Ex: Wifi Bar Central</p>
                            </div>
                            <div class="input-floating-group">
                                <textarea placeholder=" " class="input-floating h-24 resize-none">Bienvenue !</textarea>
                                <label class="floating-label">Message de bienvenue</label>
                                <p class="text-xs text-gray-400 mt-1">Ex: Bienvenue !</p>
                            </div>
                            <button class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-blue-600 transition">Sauvegarder</button>
                        </div>
                        <div class="w-64 h-[500px] border-4 border-gray-800 dark:border-slate-600 rounded-[2rem] overflow-hidden bg-gray-100 dark:bg-slate-800 relative shadow-2xl flex-shrink-0">
                            <div class="absolute top-0 w-full h-6 bg-gray-800 dark:bg-slate-700 flex justify-center">
                                <div class="w-20 h-4 bg-black rounded-b-xl"></div>
                            </div>
                            <div class="mt-10 px-4 text-center">
                                <div class="w-12 h-12 bg-green-500 rounded-xl mx-auto mb-4"></div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm">Wifi Bar Central</h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-2">Bienvenue !</p>
                                <div class="mt-8 space-y-2">
                                    <div class="h-8 bg-white dark:bg-slate-700 rounded-lg shadow-sm"></div>
                                    <div class="h-8 bg-white dark:bg-slate-700 rounded-lg shadow-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODALE : AJOUTER UNE ZONE (Wizard) -->
    <div id="add-zone-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Fond flouté -->
        <div class="absolute inset-0 bg-brand-sidebarLight/60 dark:bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeWizard()"></div>
        
        <!-- Contenu Modale -->
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-lg rounded-[2rem] shadow-2xl relative z-10 overflow-hidden transform transition-all border border-gray-100 dark:border-slate-700">
            
            <!-- HEADER -->
            <div class="px-8 pt-8 pb-4">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Nouvelle Boutique WiFi 🚀</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Étape <span id="wizard-step-num">1</span> sur 2</p>
                <!-- Barre de progression Wizard -->
                <div class="h-1 w-full bg-gray-100 dark:bg-slate-700 rounded-full mt-4 overflow-hidden">
                    <div id="wizard-progress" class="h-full bg-brand-blue w-1/2 transition-all duration-300"></div>
                </div>
            </div>

            <!-- CORPS (Les Étapes) -->
            <div class="p-8 pt-2">
                
                <!-- ÉTAPE 1 : IDENTITÉ -->
                <div id="step-1" class="space-y-5 animate-fade-in">
                    <div class="input-floating-group">
                        <input type="text" id="new-zone-name" placeholder=" " class="input-floating">
                        <label class="floating-label">Nom de gestion (Interne)</label>
                        <p class="text-xs text-gray-400 mt-1">Ex: Routeur Maquis</p>
                    </div>

                    <div class="input-floating-group">
                        <input type="text" placeholder=" " class="input-floating">
                        <label class="floating-label">Lieu / Adresse (Optionnel)</label>
                        <p class="text-xs text-gray-400 mt-1">Ex: Cocody, Rue des Jardins</p>
                    </div>
                    <div class="pt-4 flex justify-between items-center">
                        <button onclick="closeWizard()" class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                        <button onclick="goToStep2()" class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                            Suivant <i class="fas fa-arrow-right text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- ÉTAPE 2 : TECHNIQUE -->
                <div id="step-2" class="hidden space-y-6 animate-fade-in">
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl flex items-center gap-3 border border-green-100 dark:border-green-800">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center text-brand-green">✓</div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-white">Zone créée avec succès !</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: <span class="font-mono font-bold">zone_8f4a2b</span></p>
                        </div>
                    </div>

                    <!-- URL -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">1. URL de Redirection (Login)</label>
                        <div onclick="showToast('Lien de redirection copié !')" class="bg-brand-sidebarLight dark:bg-slate-900 p-4 rounded-xl flex justify-between items-center group cursor-pointer hover:ring-2 ring-brand-blue/50 transition">
                            <code class="text-xs text-blue-200 font-mono truncate mr-4">https://taplateforme.com/portal/login?z=zone_8f4a2b...</code>
                            <span class="text-xs font-bold text-white bg-white/20 px-2 py-1 rounded">COPIER</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">À mettre dans le bouton "Se connecter" de votre Mikrotik.</p>
                    </div>

                    <!-- Walled Garden -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">2. Walled Garden (Autorisations)</label>
                        <div class="bg-gray-50 dark:bg-slate-800 p-3 rounded-xl border border-gray-200 dark:border-slate-700">
                            <ul class="text-xs font-mono text-gray-600 dark:text-gray-300 space-y-1">
                                <li>taplateforme.com</li>
                                <li>cinetpay.com</li>
                                <li>*.kkiapay.me</li>
                            </ul>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-between items-center">
                        <button onclick="closeWizard()" class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                            Fermer
                        </button>
                        <button onclick="showToast('Nouvelle zone créée avec succès !'); setTimeout(finishWizard, 1000);" class="bg-brand-blue text-white px-6 py-3.5 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">
                            Terminer & Aller au Stock
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MODALE D'ERREUR PERSONNALISÉE -->
    <div id="error-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] hidden">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 dark:border-slate-700">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 id="error-title" class="text-xl font-bold text-gray-800 dark:text-white mb-3">Erreur</h3>
                <p id="error-message" class="text-gray-600 dark:text-gray-300 mb-6">Message d'erreur</p>
                <button onclick="closeErrorModal()" class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-lg">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        function showErrorModal(title, message) {
            document.getElementById('error-title').innerText = title;
            document.getElementById('error-message').innerText = message;
            document.getElementById('error-modal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('error-modal').classList.add('hidden');
        }
        /* --- WIZARD LOGIC --- */

        function openWizard() {
            document.getElementById('add-zone-modal').classList.remove('hidden');
            // Reset à l'étape 1
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('wizard-progress').style.width = '50%';
            document.getElementById('wizard-step-num').innerText = '1';
            document.getElementById('new-zone-name').value = ''; // Clear input
        }

        function closeWizard() {
            document.getElementById('add-zone-modal').classList.add('hidden');
        }

        function goToStep2() {
            const name = document.getElementById('new-zone-name').value;
            if(!name) { 
                showErrorModal("Oups !", "Veuillez donner un nom à la zone !"); 
                return; 
            }

            // Transition visuelle
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            
            // Update progress
            document.getElementById('wizard-progress').style.width = '100%';
            document.getElementById('wizard-step-num').innerText = '2';
        }

        function finishWizard() {
            // Simulation : On ferme et on redirige vers le stock
            closeWizard();
            // Idéalement, on recharge la page ou on ajoute la carte au DOM ici.
            // Pour la démo, on redirige vers les tickets comme demandé
            window.location.href = 'tickets.html?new_zone=true';
        }
    </script>
</body>
</html>