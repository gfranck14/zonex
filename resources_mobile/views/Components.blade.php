<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFiProfit - UI Kit & Components</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/config.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        html { font-size: 14px; }
        body { background-color: #f8fafc; }
        .component-section { background: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .component-title { font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; }
        .button-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .button-demo { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        .button-label { font-size: 0.75rem; color: #64748b; text-align: center; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">🎨 WiFiProfit - UI Kit: Boutons</h1>
        
        <!-- ==========================================
             1. BOUTONS PRINCIPAUX (Actions Principales)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">1. BOUTONS PRINCIPAUX (Actions Principales)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3 rounded-xl text-sm font-bold shadow-lg hover:brightness-110 transition">
                        <i class="fas fa-plus w-4 h-4 mr-2"></i>
                        Ajouter une Zone
                    </button>
                    <span class="button-label">Action principale avec icône</span>
                </div>
                
                <div class="button-demo">
                    <button class="bg-brand-blue text-white px-6 py-3.5 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">
                        Se connecter
                    </button>
                    <span class="button-label">Action principale simple</span>
                </div>
                
                <div class="button-demo">
                    <button class="bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10">
                        Continuer
                    </button>
                    <span class="button-label">Style login personnalisé</span>
                </div>
                
                <div class="button-demo">
                    <button class="bg-brand-dark text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-800 transition">
                        Enregistrer
                    </button>
                    <span class="button-label">Action sombre</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             2. BOUTONS SECONDAIRES (Actions Secondaires)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">2. BOUTONS SECONDAIRES (Actions Secondaires)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                        <i class="fas fa-download w-4 h-4"></i>
                        Exporter CSV
                    </button>
                    <span class="button-label">Action secondaire avec icône</span>
                </div>
                
                <div class="button-demo">
                    <button class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        Annuler
                    </button>
                    <span class="button-label">Action secondaire simple</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="openWithdrawalHistoryModal()" class="border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-gray-400 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
                        <i class="fas fa-clock w-4 h-4"></i>
                        Historique Retraits
                    </button>
                    <span class="button-label">Action avec hover subtil</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             3. BOUTONS DE NAVIGATION (Sidebar)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">3. BOUTONS DE NAVIGATION (Sidebar)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="w-full flex items-center justify-between bg-black/20 hover:bg-black/30 px-4 py-2 rounded-xl text-xs font-bold transition">
                        <span>Mode Apparence</span>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-sun w-4 h-4 text-yellow-300"></i>
                            <i class="fas fa-moon w-4 h-4 text-white"></i>
                        </div>
                    </button>
                    <span class="button-label">Toggle avec icônes</span>
                </div>
                
                <div class="button-demo">
                    <a href="#" class="nav-item-active flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all">
                        <i class="fas fa-users w-5 h-5"></i>
                        <span class="font-medium">Clients</span>
                    </a>
                    <span class="button-label">Navigation active</span>
                </div>
                
                <div class="button-demo">
                    <a href="#" class="nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white">
                        <i class="fas fa-th-large w-5 h-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <span class="button-label">Navigation inactive</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             4. BOUTONS D'ACTION RAPIDE (Quick Actions)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">4. BOUTONS D'ACTION RAPIDE (Quick Actions)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="w-10 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-xl hover:text-brand-blue transition">
                        <i class="fas fa-cog w-5 h-5"></i>
                    </button>
                    <span class="button-label">Icône carré</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="copyCode('azerty')" class="p-2 rounded-lg text-gray-400 hover:text-brand-blue hover:bg-blue-50 dark:hover:bg-slate-700 transition">
                        <i class="fas fa-copy w-5 h-5"></i>
                    </button>
                    <span class="button-label">Icône copier</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="deleteTicket(this)" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-slate-700 transition">
                        <i class="fas fa-trash w-5 h-5"></i>
                    </button>
                    <span class="button-label">Icône supprimer</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="closeWithdrawalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <i class="fas fa-times w-6 h-6"></i>
                    </button>
                    <span class="button-label">Icône fermer</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             5. BOUTONS DE FILTRE (Filter Buttons)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">5. BOUTONS DE FILTRE (Filter Buttons)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="px-4 py-2 rounded-full text-xs font-bold bg-brand-blue text-white shadow-md">
                        Tous
                    </button>
                    <span class="button-label">Filtre actif</span>
                </div>
                
                <div class="button-demo">
                    <button class="px-4 py-2 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-800 text-gray-500 hover:bg-gray-200 transition">
                        💎 VIP
                    </button>
                    <span class="button-label">Filtre inactif</span>
                </div>
                
                <div class="button-demo">
                    <button class="px-4 py-2 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-800 text-gray-500 hover:bg-gray-200 transition">
                        ✨ Nouveaux
                    </button>
                    <span class="button-label">Filtre avec emoji</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             6. BOUTONS D'ONGLETS (Tab Buttons)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">6. BOUTONS D'ONGLETS (Tab Buttons)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="tab-btn active text-sm">Catalogue des Offres</button>
                    <span class="button-label">Onglet actif</span>
                </div>
                
                <div class="button-demo">
                    <button class="tab-btn text-sm">Stock & Import</button>
                    <span class="button-label">Onglet inactif</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             7. BOUTONS DANGEREUX (Danger Buttons)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">7. BOUTONS DANGEREUX (Danger Buttons)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold transition">
                        BLOQUER CE CLIENT
                    </button>
                    <span class="button-label">Action de blocage</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="cancelWithdrawal('WDR-2024-001', this)" class="px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-[10px] font-bold hover:bg-red-100 dark:hover:bg-red-900/30 transition flex items-center gap-1">
                        <i class="fas fa-times w-3 h-3"></i>
                        Annuler
                    </button>
                    <span class="button-label">Action annulation</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             8. BOUTONS SPÉCIAUX (Special Buttons)
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">8. BOUTONS SPÉCIAUX (Special Buttons)</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button class="flex items-center gap-3 bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3 rounded-xl shadow-lg border border-transparent hover:brightness-110 transition">
                        <div class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></div>
                        <span class="text-sm font-bold">Zone: Bar Central</span>
                        <i class="fas fa-chevron-down w-4 h-4 text-white/70"></i>
                    </button>
                    <span class="button-label">Sélecteur de zone</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="toggleTheme()" class="p-3 rounded-full bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 transition text-gray-500 dark:text-white">
                        <i class="fas fa-moon w-5 h-5 block dark:hidden"></i>
                        <i class="fas fa-sun w-5 h-5 hidden dark:block text-yellow-400"></i>
                    </button>
                    <span class="button-label">Toggle theme circulaire</span>
                </div>
                
                <div class="button-demo">
                    <div class="relative inline-block w-10 h-6 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="toggle" id="toggle-switch" checked class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-brand-green right-0 transition-all duration-300"/>
                        <label for="toggle-switch" class="toggle-label block overflow-hidden h-6 rounded-full bg-brand-green cursor-pointer transition-colors duration-300"></label>
                    </div>
                    <span class="button-label">Toggle switch</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             9. BOUTONS DE WIZARD/MODALE
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">9. BOUTONS DE WIZARD/MODALE</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button onclick="goToStep2()" class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
                        Suivant <i class="fas fa-arrow-right w-4 h-4"></i>
                    </button>
                    <span class="button-label">Étape suivante</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="showToast('Nouvelle zone créée avec succès !'); setTimeout(finishWizard, 1000);" class="bg-[#114c6c] text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">
                        Terminer & Aller au Stock
                    </button>
                    <span class="button-label">Finalisation wizard</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="closeWizard()" class="border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-300 px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        Annuler
                    </button>
                    <span class="button-label">Annuler wizard</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             10. BOUTONS D'IMPORT/ACTION
             ========================================== -->
        <div class="component-section">
            <h2 class="component-title">10. BOUTONS D'IMPORT/ACTION</h2>
            <div class="button-grid">
                <div class="button-demo">
                    <button id="import-button" class="bg-brand-sidebarLight dark:bg-brand-blue text-white px-10 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-xl">
                        Confirmer l'importation
                    </button>
                    <span class="button-label">Action d'import</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="showToast('Liste Walled Garden copiée', 'success')" class="mt-6 w-full py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700">
                        Copier la liste
                    </button>
                    <span class="button-label">Copier liste</span>
                </div>
                
                <div class="button-demo">
                    <button onclick="showToast('Lien d\'authentification copié', 'success')" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                        <i class="fas fa-copy w-5 h-5"></i>
                    </button>
                    <span class="button-label">Copier dans champ</span>
                </div>
            </div>
        </div>

    </div>
</body>
</html>