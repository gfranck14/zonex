document.addEventListener("DOMContentLoaded", function() {
    generateSidebar();
    
    // Détecter la page actuelle et générer le header approprié
    const path = window.location.pathname;
    const page = path.split("/").pop() || "index.html";
    
    switch(page) {
        case "index.html":
            generateHeader("Bonjour, Caleb 👋", "Voici ce qui se passe sur vos zones aujourd'hui.");
            break;
        case "wifi-zones.html":
            generateHeader("Mes Zones", "Gérez vos emplacements physiques.");
            break;
        case "tickets.html":
            generateHeader("Forfaits & Tickets", "Gérez vos offres par zone.");
            break;
        case "clients.html":
            generateHeader("Gestion Clients", "Base de données utilisateurs et fidélité.");
            break;
        case "paiements.html":
            generateHeader("Finance & Wallet", "Transactions et retraits.");
            break;
        case "settings.html":
            generateHeader("Paramètres", "Configuration du système.");
            break;
        default:
            generateHeader("Dashboard", "Overview");
    }
    
    // Vérifier et afficher les empty states
    setTimeout(checkEmptyStates, 100); // Petit délai pour s'assurer que le DOM est chargé
});

function generateHeader(title, subtitle) {
    const headerContainer = document.getElementById('header-container');
    if (!headerContainer) return;

    // Vérifier si nous sommes sur la page paiements.html
    const isPaiementsPage = window.location.pathname.includes('paiements.html') || window.location.pathname.endsWith('/paiements');

    headerContainer.innerHTML = `
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <!-- TITRE DYNAMIQUE -->
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">${title}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">${subtitle}</p>
            </div>

            <!-- BOUTON SPÉCIFIQUE POUR PAIEMENTS ou SÉLECTEUR DE ZONE -->
            ${isPaiementsPage ? `
                <!-- Bouton Historique Retraits pour la page paiements (style secondaire) -->
                <button onclick="openWithdrawalHistoryModal()" class="bg-white dark:bg-slate-800 text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm border border-gray-200 dark:border-slate-600 flex items-center gap-2">
                    <i class="fas fa-clock w-4 h-4"></i>
                    Historique Retraits
                </button>
            ` : `
                <!-- SÉLECTEUR DE ZONE (pour les autres pages) -->
                <div class="relative group z-20">
                    <button class="flex items-center gap-3 bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3 rounded-xl shadow-lg border border-transparent hover:brightness-110 transition">
                        <div class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></div>
                        <span class="text-sm font-bold">Zone: Bar Central</span>
                        <i class="fas fa-chevron-down w-4 h-4 text-white/70"></i>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-48 bg-brand-cardLight dark:bg-brand-cardDark rounded-xl shadow-xl p-2 hidden group-hover:block border border-gray-100 dark:border-slate-700">
                        <div class="p-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium text-gray-800 dark:text-white">Bar Central</div>
                        <div class="p-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer text-sm font-medium text-gray-500 dark:text-gray-400">Campus Nord</div>
                    </div>
                </div>
            `}
            
            <!-- BOUTON MOBILE (Menu Hamburger) -->
            <button onclick="toggleMobileMenu()" class="md:hidden absolute top-0 right-0 p-2 text-gray-500">
                <i class="fas fa-bars w-8 h-8"></i>
            </button>
        </header>
    `;
    
    // Ajouter l'overlay mobile s'il n'existe pas déjà
    if (!document.querySelector('.mobile-overlay')) {
        const overlay = document.createElement('div');
        overlay.className = 'mobile-overlay';
        overlay.onclick = closeMobileMenu;
        document.body.appendChild(overlay);
    }
}

function generateSidebar() {
    const sidebarContainer = document.getElementById('sidebar-container');
    if (!sidebarContainer) return;

    // 1. Détecter la page actuelle
    const path = window.location.pathname;
    const page = path.split("/").pop() || "index.html"; // Par défaut index.html

    // 2. Définir les classes (Actif vs Inactif)
    const activeClass = "nav-item-active flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all shadow-md";
    const inactiveClass = "nav-item-inactive flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all hover:text-white";

    // 3. Fonction helper pour générer un lien
    const getLinkClass = (targetPage) => {
        return page === targetPage ? activeClass : inactiveClass;
    };

    // 4. Le HTML de la Sidebar (Exactement ton design validé)
    const sidebarHTML = `
    <aside class="w-64 flex-shrink-0 flex flex-col justify-between py-6 px-4 h-full bg-brand-sidebarLight dark:bg-brand-sidebarDark transition-colors duration-300 text-white">
        <div>
            <!-- LOGO -->
            <div class="flex items-center gap-3 px-4 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-brand-blue to-brand-green rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/50">
                    <i class="fas fa-wifi text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-tight leading-none">WiFi<span class="text-brand-blue">Profit</span></h1>
                    <p class="text-[10px] text-blue-200 uppercase tracking-widest">Manager</p>
                </div>
            </div>

            <!-- MENU -->
            <nav class="space-y-2">
                <a href="index.html" class="${getLinkClass('index.html')}">
                    <i class="fas fa-th-large w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="wifi-zones.html" class="${getLinkClass('wifi-zones.html')}">
                    <i class="fas fa-map-marker-alt w-5 h-5"></i>
                    <span class="font-medium">Wifi Zones</span>
                </a>
                <a href="tickets.html" class="${getLinkClass('tickets.html')}">
                    <i class="fas fa-ticket-alt w-5 h-5"></i>
                    <span class="font-medium">Forfaits & Tickets</span>
                </a>
                <a href="clients.html" class="${getLinkClass('clients.html')}">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span class="font-medium">Clients</span>
                </a>
                <a href="paiements.html" class="${getLinkClass('paiements.html')}">
                    <i class="fas fa-credit-card w-5 h-5"></i>
                    <span class="font-medium">Paiements</span>
                </a>
                
                <a href="settings.html" class="${getLinkClass('settings.html')} mt-8">
                    <i class="fas fa-cog w-5 h-5"></i>
                    <span class="font-medium">Paramètres</span>
                </a>
            </nav>
        </div>

        <!-- FOOTER SIDEBAR (Dark Mode + Profil) -->
        <div class="mt-auto">
            <button onclick="toggleTheme()" class="w-full flex items-center justify-between bg-black/20 hover:bg-black/30 px-4 py-2 rounded-xl mb-4 text-xs font-bold transition">
                <span>Mode Apparence</span>
                <div class="flex items-center gap-2">
                    <i class="fas fa-sun w-4 h-4 text-yellow-300 block dark:hidden"></i>
                    <i class="fas fa-moon w-4 h-4 text-white hidden dark:block"></i>
                </div>
            </button>

            <p class="px-4 text-[10px] text-blue-200 font-mono mb-2 opacity-60">v2.4.0-stable</p>
            <div class="pt-4 border-t border-white/10">
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/5 cursor-pointer transition">
                    <div class="w-10 h-10 rounded-full bg-brand-light border-2 border-brand-blue flex items-center justify-center overflow-hidden"><img src="https://i.pravatar.cc/150?img=11"></div>
                    <div><p class="text-sm font-bold text-white">Caleb G.</p><p class="text-[10px] text-blue-200">Propriétaire</p></div>
                </div>
            </div>
        </div>
    </aside>
    `;

    sidebarContainer.innerHTML = sidebarHTML;
}

// Fonction pour gérer le menu mobile
function toggleMobileMenu() {
    const sidebar = document.querySelector('aside');
    const overlay = document.querySelector('.mobile-overlay');
    
    if (sidebar && overlay) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }
}

// Fonction pour fermer le menu mobile (appelée depuis l'overlay)
function closeMobileMenu() {
    const sidebar = document.querySelector('aside');
    const overlay = document.querySelector('.mobile-overlay');
    
    if (sidebar && overlay) {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }
}

// Fonction pour générer un Empty State (état vide)
function generateEmptyState(title, description, buttonText, buttonAction) {
    return `
        <div class="flex flex-col items-center justify-center py-16 text-center animate-fade-in">
            <div class="w-24 h-24 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">${title}</h3>
            <p class="text-sm text-gray-400 dark:text-gray-500 max-w-xs mx-auto mt-1">
                ${description}
            </p>
            <button onclick="${buttonAction}" class="mt-6 px-6 py-2 bg-brand-sidebarLight dark:bg-brand-blue text-white rounded-xl text-sm font-bold shadow-lg hover:brightness-110 transition">
                ${buttonText}
            </button>
        </div>
    `;
}

// Fonction pour vérifier et afficher les empty states
function checkEmptyStates() {
    // Tableau Clients
    const clientTable = document.querySelector('#client-table tbody');
    if (clientTable && clientTable.children.length === 0) {
        clientTable.innerHTML = generateEmptyState(
            "Aucun client trouvé",
            "Il n'y a aucun client enregistré pour le moment. Commencez par ajouter votre premier client.",
            "Ajouter un client",
            "showToast('Fonctionnalité d\'ajout de client à implémenter')"
        );
    }

    // Tableau Transactions (paiements.html)
    const transactionTable = document.querySelector('table tbody');
    if (transactionTable && transactionTable.children.length === 0) {
        transactionTable.innerHTML = generateEmptyState(
            "Aucune transaction",
            "Aucune transaction n'a été effectuée pour le moment. Les transactions apparaîtront ici dès qu'un client achète un ticket.",
            "Vendre un ticket",
            "window.location.href='tickets.html'"
        );
    }

    // Tableau Tickets (tickets.html, onglet Liste)
    const ticketsTable = document.querySelector('#content-list table tbody');
    if (ticketsTable && ticketsTable.children.length === 0) {
        ticketsTable.innerHTML = generateEmptyState(
            "Aucun ticket vendu",
            "Aucun ticket n'a été vendu pour le moment. Commencez par importer des tickets ou vendre votre premier forfait.",
            "Importer des tickets",
            "switchTab('stock')"
        );
    }

    // Grille des Zones (wifi-zones.html)
    const zonesGrid = document.querySelector('#zone-list .grid');
    if (zonesGrid && zonesGrid.children.length === 0) {
        zonesGrid.innerHTML = generateEmptyState(
            "Aucune zone WiFi",
            "Vous n'avez pas encore configuré de zone WiFi. Commencez par créer votre première zone pour commencer à vendre des tickets.",
            "Créer une zone",
            "openWizard()"
        );
    }
}

// Fonction pour gérer les onglets (Tabs)
function switchTab(tabName) {
    // 1. Désactiver tous les boutons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    // 2. Cacher tous les contenus
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // 3. Activer l'élément demandé (si existant)
    const activeBtn = document.getElementById('tab-' + tabName);
    const activeContent = document.getElementById('content-' + tabName);

    if (activeBtn && activeContent) {
        activeBtn.classList.add('active');
        activeContent.classList.remove('hidden');
    }
    
    // 4. Vérifier les empty states après le changement d'onglet
    setTimeout(checkEmptyStates, 100);
}

// Fonction pour la page Wifi Zones (Liste <-> Détail)
const listDiv = document.getElementById('zone-list');
const detailDiv = document.getElementById('zone-detail');
const zoneTitle = document.getElementById('zone-title');
const displayID = document.getElementById('display-zone-id');
const codeZoneID = document.getElementById('code-zone-id');

function showDetail(name, id) {
    if(listDiv && detailDiv) {
        listDiv.classList.add('hidden');
        detailDiv.classList.remove('hidden');
        if(zoneTitle) zoneTitle.innerText = name;
        if(displayID) displayID.innerText = id ? id : 'WZ-XXXX';
        if(codeZoneID) codeZoneID.innerText = id ? id : 'WZ-XXXX';

        // LOGIQUE ICONE DYNAMIQUE
        // On remet le bleu par défaut
        let iconHtml = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
        let bgClass = 'bg-brand-blue';
        let textClass = 'text-white';
        let borderClass = 'border-transparent';

        // Si c'est Campus Nord (Cas Alerte)
        if(name === 'Campus Nord') {
            // Icône Warning
            iconHtml = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            bgClass = 'bg-orange-100';
            textClass = 'text-orange-500';
            borderClass = 'border-orange-200';
        }

        // Appliquer les classes
        const zoneIconContainer = document.querySelector('#zone-detail .w-16');
        if(zoneIconContainer) {
            zoneIconContainer.className = `w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg ${bgClass} ${textClass} border ${borderClass}`;
            zoneIconContainer.innerHTML = iconHtml;
        }
        
        // Reset tab to general par défaut
        switchTab('general');
    }
}

function showList() {
    if(listDiv && detailDiv) {
        detailDiv.classList.add('hidden');
        listDiv.classList.remove('hidden');
    }
}

// Fonction pour la page Clients (Liste <-> Détail)
const clientListDiv = document.getElementById('client-list');
const clientDetailDiv = document.getElementById('client-detail');
const detailName = document.getElementById('detail-name');
const detailPhone = document.getElementById('detail-phone');
const detailSpent = document.getElementById('detail-spent');
const detailAvatar = document.getElementById('detail-avatar');

function showClientDetail(name, phone, spent) {
    if(clientListDiv && clientDetailDiv) {
        clientListDiv.classList.add('hidden');
        clientDetailDiv.classList.remove('hidden');
        
        // Remplir les données
        if(detailName) detailName.innerText = name;
        if(detailPhone) detailPhone.innerText = phone;
        if(detailSpent) detailSpent.innerText = spent;
        // Initiales - deux lettres
        if(detailAvatar) {
            const initials = name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
            detailAvatar.innerText = initials;
        }
    }
}

function showClientList() {
    if(clientListDiv && clientDetailDiv) {
        clientDetailDiv.classList.add('hidden');
        clientListDiv.classList.remove('hidden');
    }
}

// --- GESTION DU DARK MODE ---

// 1. Vérifier la préférence au chargement
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

// 2. Fonction pour basculer le thème (appelée par le bouton)
function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
}

/* --- SYSTÈME DE NOTIFICATION (TOAST) --- */
function showToast(message, type = 'success') {
    // 1. Créer le conteneur s'il n'existe pas
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }

    // 2. Définir les couleurs
    const colors = type === 'success' ? 'bg-[#083e5f] text-white' : 'bg-red-500 text-white';
    const icon = type === 'success' 
        ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' 
        : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

    // 3. Créer l'élément Toast
    const toast = document.createElement('div');
    toast.className = `${colors} px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px] border border-white/10`;
    toast.innerHTML = `
        <div class="bg-white/20 rounded-full p-1">${icon}</div>
        <p class="text-sm font-bold">${message}</p>
    `;

    // 4. Ajouter au DOM et Animer
    container.appendChild(toast);
    
    // Animation Entrée
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    });

    // 5. Suppression auto après 3s
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

/* --- GESTION DES TICKETS (ACTIONS) --- */

// 1. Copier le code
function copyCode(code) {
    // Utilisation de l'API Clipboard moderne
    navigator.clipboard.writeText(code).then(() => {
        showToast(`Code <strong>${code}</strong> copié !`, 'success');
    }).catch(err => {
        console.error('Erreur copie', err);
        showToast('Erreur lors de la copie', 'error');
    });
}

/* --- GESTION PAGINATION ET FILTRES TABLEAU IMPORTS CSV --- */

// Variables globales pour la pagination
let currentPage = 1;
let rowsPerPage = 10;
let filteredData = [];
let allData = [];

// Initialisation du tableau des imports
function initImportsTable() {
    const tbody = document.getElementById('imports-tbody');
    if (!tbody) return;

    // Récupérer toutes les données du tableau
    allData = Array.from(tbody.querySelectorAll('tr')).map(row => ({
        element: row,
        date: row.cells[0].textContent.trim(),
        fichier: row.cells[1].textContent.trim(),
        zone: row.cells[2].textContent.trim(),
        forfait: row.cells[3].textContent.trim(),
        quantite: row.cells[4].textContent.trim(),
        statut: row.cells[5].textContent.trim(),
        forfaitValue: row.dataset.forfait || '',
        statutValue: row.dataset.statut || '',
        zoneValue: row.dataset.zone || ''
    }));

    filteredData = [...allData];
    
    // Appliquer les filtres et la pagination
    applyFilters();
    updatePagination();
}

// Appliquer les filtres
function applyFilters() {
    const forfaitFilter = document.getElementById('filter-forfait')?.value || '';
    const statutFilter = document.getElementById('filter-statut')?.value || '';
    const zoneFilter = document.getElementById('filter-zone')?.value || '';

    filteredData = allData.filter(item => {
        const forfaitMatch = !forfaitFilter || item.forfaitValue === forfaitFilter;
        const statutMatch = !statutFilter || item.statutValue === statutFilter;
        const zoneMatch = !zoneFilter || item.zoneValue === zoneFilter;
        
        return forfaitMatch && statutMatch && zoneMatch;
    });

    currentPage = 1; // Reset à la première page après filtrage
    updateTableDisplay();
    updatePagination();
}

// Mettre à jour l'affichage du tableau
function updateTableDisplay() {
    const tbody = document.getElementById('imports-tbody');
    if (!tbody) return;

    // Cacher toutes les lignes
    allData.forEach(item => {
        item.element.style.display = 'none';
    });

    // Calculer les indices de début et de fin
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;

    // Afficher les lignes filtrées pour la page actuelle
    const pageData = filteredData.slice(startIndex, endIndex);
    pageData.forEach(item => {
        item.element.style.display = '';
    });

    // Mettre à jour les informations d'affichage
    updateDisplayInfo(startIndex + 1, Math.min(endIndex, filteredData.length), filteredData.length);
}

// Mettre à jour les informations d'affichage
function updateDisplayInfo(start, end, total) {
    const startRecord = document.getElementById('start-record');
    const endRecord = document.getElementById('end-record');
    const totalRecord = document.getElementById('total-records');

    if (startRecord) startRecord.textContent = total > 0 ? start : 0;
    if (endRecord) endRecord.textContent = end;
    if (totalRecord) totalRecord.textContent = total;
}

// Mettre à jour la pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    const pageNumbers = document.getElementById('page-numbers');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');

    if (!pageNumbers || !prevBtn || !nextBtn) return;

    // Vider les numéros de page existants
    pageNumbers.innerHTML = '';

    // Générer les numéros de page
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    // Ajuster le début si on est près de la fin
    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    // Ajouter la première page et "..." si nécessaire
    if (startPage > 1) {
        addPageButton(1);
        if (startPage > 2) {
            const dots = document.createElement('span');
            dots.className = 'px-2 text-gray-400';
            dots.textContent = '...';
            pageNumbers.appendChild(dots);
        }
    }

    // Ajouter les pages visibles
    for (let i = startPage; i <= endPage; i++) {
        addPageButton(i);
    }

    // Ajouter "..." et la dernière page si nécessaire
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dots = document.createElement('span');
            dots.className = 'px-2 text-gray-400';
            dots.textContent = '...';
            pageNumbers.appendChild(dots);
        }
        addPageButton(totalPages);
    }

    // Activer/désactiver les boutons précédent/suivant
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;
}

// Ajouter un bouton de page
function addPageButton(pageNum) {
    const pageNumbers = document.getElementById('page-numbers');
    if (!pageNumbers) return;

    const button = document.createElement('button');
    button.textContent = pageNum;
    button.className = `px-3 py-1 text-sm rounded-lg transition ${
        pageNum === currentPage 
            ? 'bg-brand-sidebarLight dark:bg-brand-blue text-white' 
            : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700'
    }`;
    button.onclick = () => goToPage(pageNum);
    pageNumbers.appendChild(button);
}

// Changer de page
function changePage(direction) {
    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    const newPage = currentPage + direction;

    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        updateTableDisplay();
        updatePagination();
    }
}

// Aller à une page spécifique
function goToPage(pageNum) {
    currentPage = pageNum;
    updateTableDisplay();
    updatePagination();
}

// Changer le nombre de lignes par page
function changeRowsPerPage() {
    const rowsSelect = document.getElementById('rows-per-page');
    if (!rowsSelect) return;

    rowsPerPage = parseInt(rowsSelect.value);
    currentPage = 1; // Reset à la première page
    updateTableDisplay();
    updatePagination();
}

// Ajouter les écouteurs d'événements pour les filtres
function setupImportsFilters() {
    const forfaitFilter = document.getElementById('filter-forfait');
    const statutFilter = document.getElementById('filter-statut');
    const zoneFilter = document.getElementById('filter-zone');
    const rowsSelect = document.getElementById('rows-per-page');

    if (forfaitFilter) forfaitFilter.addEventListener('change', applyFilters);
    if (statutFilter) statutFilter.addEventListener('change', applyFilters);
    if (zoneFilter) zoneFilter.addEventListener('change', applyFilters);
    if (rowsSelect) rowsSelect.addEventListener('change', changeRowsPerPage);
}

// Initialiser le tableau des imports au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser après un petit délai pour s'assurer que le DOM est chargé
    setTimeout(() => {
        initImportsTable();
        setupImportsFilters();
    }, 200);
});

/* --- GESTION SUPPRESSION --- */
let rowToDelete = null; // Variable pour stocker quelle ligne on veut supprimer

// 1. Ouvrir la modale
function deleteTicket(btn) {
    rowToDelete = btn.closest('tr'); // On garde la ligne en mémoire
    document.getElementById('delete-modal').classList.remove('hidden');
}

// 2. Fermer
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    rowToDelete = null;
}

// 3. Confirmer
function confirmDelete() {
    if (rowToDelete) {
        // Animation
        rowToDelete.style.transition = "all 0.3s";
        rowToDelete.style.opacity = "0";
        setTimeout(() => {
            rowToDelete.remove();
            showToast('Ticket supprimé définitivement', 'error'); // Toast Rouge
        }, 300);
    }
    closeDeleteModal();
}

/* --- GESTION HISTORIQUE --- */
function openHistoryModal() {
    const modal = document.getElementById('history-modal');
    if (modal) modal.classList.remove('hidden');
}

function closeHistoryModal() {
    const modal = document.getElementById('history-modal');
    if (modal) modal.classList.add('hidden');
}

/* --- TRI DES TABLEAUX --- */
function sortTable(n, tableId) {
    let table = document.getElementById(tableId);
    let rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    switching = true;
    dir = "asc";
    while (switching) {
        switching = false;
        rows = table.rows;
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true; break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    shouldSwitch = true; break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount++;
        } else {
            if (switchcount == 0 && dir == "asc") {
                dir = "desc"; switching = true;
            }
        }
    }
}

/* --- GESTION CLIENTS --- */
function toggleBlock() {
    const btn = document.getElementById('block-btn');
    if (btn.textContent.trim() === 'BLOQUER CE CLIENT') {
        btn.textContent = 'DÉBLOQUER LE CLIENT';
        btn.className = 'px-4 py-2 bg-gray-400 text-white rounded-xl text-xs font-bold transition';
        showToast('Client restreint', 'error');
    } else {
        btn.textContent = 'BLOQUER CE CLIENT';
        btn.className = 'px-4 py-2 bg-red-500 text-white rounded-xl text-xs font-bold transition';
        showToast('Accès rétabli', 'success');
    }
}

function viewTicket(user, pass) {
    document.getElementById('t-user').textContent = user;
    document.getElementById('t-pass').textContent = pass;
    document.getElementById('ticket-info-modal').classList.remove('hidden');
}

function closeTicketModal() {
    document.getElementById('ticket-info-modal').classList.add('hidden');
}

/* --- GESTION ÉDITION CLIENT --- */
function toggleEditMode() {
    const editForm = document.getElementById('edit-form');
    if(editForm) {
        editForm.classList.toggle('hidden');
    }
}

function saveClientChanges() {
    const newName = document.getElementById('edit-name').value;
    const newPhone = document.getElementById('edit-phone').value;
    
    // Mettre à jour l'affichage
    const detailName = document.getElementById('detail-name');
    const detailPhone = document.getElementById('detail-phone');
    const detailAvatar = document.getElementById('detail-avatar');
    
    if(detailName) detailName.textContent = newName;
    if(detailPhone) detailPhone.textContent = newPhone;
    
    // Mettre à jour l'avatar (premières lettres)
    if(detailAvatar) {
        const initials = newName.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
        detailAvatar.textContent = initials;
    }
    
    // Cacher le formulaire
    const editForm = document.getElementById('edit-form');
    if(editForm) editForm.classList.add('hidden');
    
    // Afficher un message de succès
    showToast('Informations client mises à jour avec succès !', 'success');
}

function cancelEdit() {
    // Réinitialiser les valeurs du formulaire
    const editName = document.getElementById('edit-name');
    const editPhone = document.getElementById('edit-phone');
    const detailName = document.getElementById('detail-name');
    const detailPhone = document.getElementById('detail-phone');
    
    if(editName && detailName) editName.value = detailName.textContent;
    if(editPhone && detailPhone) editPhone.value = detailPhone.textContent;
    
    // Cacher le formulaire
    const editForm = document.getElementById('edit-form');
    if(editForm) editForm.classList.add('hidden');
}

/* --- GESTION SÉLECTEUR DE PAYS POUR MODALE RETRAIT --- */
function toggleCountryMenu(context = 'withdrawal') {
    const menuId = context === 'withdrawal' ? 'withdrawal-country-menu' : 'country-menu';
    const menu = document.getElementById(menuId);
    if(menu) menu.classList.toggle('hidden');
}

function selectCountry(code, dial, context = 'withdrawal') {
    let flagId, codeId, inputId, menuId;
    
    if (context === 'withdrawal') {
        flagId = 'withdrawal-flag';
        codeId = 'withdrawal-code';
        inputId = 'withdrawal-phone';
        menuId = 'withdrawal-country-menu';
    }

    // 1. Mettre à jour l'affichage
    document.getElementById(flagId).src = `https://flagcdn.com/w40/${code}.png`;
    document.getElementById(codeId).innerText = dial;
    
    // 2. Fermer le menu
    document.getElementById(menuId).classList.add('hidden');
    
    // 3. Focus
    document.getElementById(inputId).focus();
}

// Fermeture au clic dehors pour la modal de retrait
document.addEventListener('click', function(event) {
    const container = document.getElementById('withdrawal-phone-container');
    const menu = document.getElementById('withdrawal-country-menu');
    if (container && !container.contains(event.target) && menu) {
        menu.classList.add('hidden');
    }
});

/* --- GESTION MODALE RETRAIT --- */
function openWithdrawalModal() {
    const withdrawSelect = document.getElementById('withdraw-select');
    const modal = document.getElementById('withdrawal-modal');
    
    if (modal && withdrawSelect) {
        // Récupérer et formater le montant sélectionné
        const amount = withdrawSelect.value;
        const formattedAmount = parseInt(amount).toLocaleString('fr-FR');
        
        // Mettre à jour le texte dans le modal
        const modalAmountDisplay = document.getElementById('modal-amount-display');
        if (modalAmountDisplay) {
            modalAmountDisplay.innerText = formattedAmount + ' F';
        }
        
        // Afficher la modale et réinitialiser à l'étape 1
        modal.classList.remove('hidden');
        document.getElementById('withdrawal-step-1').classList.remove('hidden');
        document.getElementById('withdrawal-step-2').classList.add('hidden');
        
        // Réinitialiser les champs
        document.getElementById('withdrawal-phone').value = '';
        document.getElementById('withdrawal-name').value = '';
        
        // Réinitialiser les radio buttons
        const radios = document.querySelectorAll('input[name="network"]');
        radios.forEach(radio => radio.checked = false);
        document.querySelector('input[name="network"][value="mtn"]').checked = true;
    }
}

function closeWithdrawalModal() {
    const modal = document.getElementById('withdrawal-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function proceedToWithdrawalStep2() {
    const phone = document.getElementById('withdrawal-phone');
    const name = document.getElementById('withdrawal-name');
    const withdrawSelect = document.getElementById('withdraw-select');
    const networkSelect = document.getElementById('withdrawal-network');
    const countryCode = document.getElementById('withdrawal-code');
    
    // Validation simple
    if (!phone.value.trim() || !name.value.trim()) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    // Récupérer les valeurs
    const selectedOption = withdrawSelect.options[withdrawSelect.selectedIndex];
    const amount = selectedOption.text;
    
    // Récupérer le réseau sélectionné (dropdown)
    const networkValue = networkSelect.value;
    const networkText = networkSelect.options[networkSelect.selectedIndex].text;
    
    // Utiliser le code pays sélectionné
    const phoneWithCode = countryCode.textContent + ' ' + phone.value.trim();
    
    // Remplir le récapitulatif
    document.getElementById('withdrawal-amount-display').textContent = amount;
    document.getElementById('withdrawal-name-display').textContent = name.value.trim();
    document.getElementById('withdrawal-phone-display').textContent = phoneWithCode;
    document.getElementById('withdrawal-network-display').textContent = networkText;
    
    document.getElementById('withdrawal-amount-highlight').textContent = amount;
    document.getElementById('withdrawal-name-highlight').textContent = name.value.trim();
    
    // Passer à l'étape 2
    document.getElementById('withdrawal-step-1').classList.add('hidden');
    document.getElementById('withdrawal-step-2').classList.remove('hidden');
}

function backToWithdrawalStep1() {
    document.getElementById('withdrawal-step-2').classList.add('hidden');
    document.getElementById('withdrawal-step-1').classList.remove('hidden');
}

function confirmWithdrawal() {
    const name = document.getElementById('withdrawal-name').value.trim();
    const phone = document.getElementById('withdrawal-phone').value.trim();
    const withdrawSelect = document.getElementById('withdraw-select');
    const networkSelect = document.getElementById('withdrawal-network');
    const countryCode = document.getElementById('withdrawal-code');
    const selectedOption = withdrawSelect.options[withdrawSelect.selectedIndex];
    const amount = parseInt(withdrawSelect.value);
    const amountText = selectedOption.text;
    
    // Récupérer le réseau sélectionné (dropdown)
    const networkValue = networkSelect.value;
    const networkText = networkSelect.options[networkSelect.selectedIndex].text;
    
    // Utiliser le code pays sélectionné
    const phoneWithCode = countryCode.textContent + ' ' + phone;
    
    // Calcul du nouveau solde
    const soldeDisplay = document.getElementById('solde-display');
    const currentSoldeText = soldeDisplay.innerText;
    const currentSolde = parseInt(currentSoldeText.replace(/[^\d]/g, ''));
    const nouveauSolde = currentSolde - amount;
    
    // Mettre à jour le solde affiché
    soldeDisplay.innerHTML = `${nouveauSolde.toLocaleString()} <span class="text-2xl text-brand-blue font-normal">F</span>`;
    
    // Ajouter la transaction à l'historique
    const transactionTable = document.querySelector('#withdrawal-history-modal tbody');
    if (transactionTable) {
        const now = new Date();
        const dateStr = now.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const ref = 'WDR-' + now.getFullYear() + '-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
        
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50/50 dark:hover:bg-slate-700 transition group';
        newRow.innerHTML = `
            <td class="p-4 text-gray-600 dark:text-gray-300 text-xs">${dateStr}</td>
            <td class="p-4 font-bold text-red-600">-${amountText}</td>
            <td class="p-4"><span class="text-[10px] font-bold text-${networkValue === 'mtn' ? 'yellow-600 bg-yellow-50' : networkValue === 'moov' ? 'orange-600 bg-orange-50' : 'indigo-600 bg-indigo-50'} px-2 py-1 rounded border border-${networkValue === 'mtn' ? 'yellow-100' : networkValue === 'moov' ? 'orange-100' : 'indigo-100'}">${networkText}</span></td>
            <td class="p-4 font-medium text-gray-800 dark:text-white">${name}</td>
            <td class="p-4 font-mono text-gray-600 dark:text-gray-300 text-xs">${phoneWithCode}</td>
            <td class="p-4 font-mono text-gray-400 text-xs">${ref}</td>
            <td class="p-4"><span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-2 py-1 rounded-lg text-[10px] font-bold">⏳ En cours</span></td>
            <td class="p-4 text-right flex justify-end gap-2">
                <button onclick="viewWithdrawalDetails('${ref}')" class="p-2 text-brand-blue hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Détails"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                <button onclick="cancelWithdrawal('${ref}', this)" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Annuler"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
            </td>
        `;
        
        // Insérer au début du tableau
        transactionTable.insertBefore(newRow, transactionTable.firstChild);
    }
    
    // Fermer la modale
    closeWithdrawalModal();
    
    // Afficher le toast de succès
    showToast('Demande de retrait transmise', 'success');
    // Afficher l'historique des retraits
    openWithdrawalHistoryModal();
}

/* --- GESTION HISTORIQUE RETRAITS --- */
function openWithdrawalHistoryModal() {
    document.getElementById('withdrawal-history-modal').classList.remove('hidden');
}

function closeWithdrawalHistoryModal() {
    document.getElementById('withdrawal-history-modal').classList.add('hidden');
}

/* --- GESTION DÉTAILS RETRAITS --- */
function viewWithdrawalDetails(refId) {
    // Données de démonstration - dans une vraie application, faire un appel API
    const withdrawalData = {
        'WDR-001': {
            date: '22 Jan, 14:30',
            amount: '50.000 F',
            operator: 'MTN',
            beneficiary: 'John Doe',
            phone: '66 77 88 99',
            status: 'En cours',
            statusClass: 'bg-yellow-100 text-yellow-700'
        },
        'WDR-002': {
            date: '21 Jan, 10:15',
            amount: '25.000 F',
            operator: 'MOOV',
            beneficiary: 'Alice Smith',
            phone: '91 23 45 67',
            status: 'Payé',
            statusClass: 'bg-green-100 text-green-700'
        },
        'WDR-003': {
            date: '20 Jan, 16:45',
            amount: '100.000 F',
            operator: 'Celtiis',
            beneficiary: 'Mike K.',
            phone: '58 99 11 22',
            status: 'Payé',
            statusClass: 'bg-green-100 text-green-700'
        },
        'WDR-004': {
            date: '19 Jan, 09:20',
            amount: '75.000 F',
            operator: 'MTN',
            beneficiary: 'Sarah Johnson',
            phone: '44 55 66 77',
            status: 'En cours',
            statusClass: 'bg-yellow-100 text-yellow-700'
        }
    };
    
    const data = withdrawalData[refId] || withdrawalData['WDR-001'];
    
    // Remplir le modal avec les données
    document.getElementById('detail-ref').innerText = refId;
    document.getElementById('detail-date').innerText = data.date;
    document.getElementById('detail-amount').innerText = data.amount;
    document.getElementById('detail-operator').innerText = data.operator;
    document.getElementById('detail-beneficiary').innerText = data.beneficiary;
    document.getElementById('detail-phone').innerText = data.phone;
    
    const statusElement = document.getElementById('detail-status');
    statusElement.innerText = data.status;
    statusElement.className = data.statusClass + ' px-2 py-1 rounded text-[10px] font-bold';
    
    // Ouvrir le modal
    document.getElementById('withdrawal-details-modal').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('withdrawal-details-modal').classList.add('hidden');
}

// Recherche dans le tableau (Simple filtre JS)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('history-search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#withdrawal-table-body tr');

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function cancelWithdrawal(reference, buttonElement) {
    // Trouver la ligne du tableau
    const row = buttonElement.closest('tr');
    const statusCell = row.querySelector('td:nth-child(7)');
    const actionCell = row.querySelector('td:nth-child(8)');
    const amountCell = row.querySelector('td:nth-child(2)');
    
    // Mettre à jour le statut
    statusCell.innerHTML = '<span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-[10px] font-bold">❌ Annulé</span>';
    
    // Remplacer le bouton par un tiret
    actionCell.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-[10px]">-</span>';
    
    // Récupérer le montant pour le rembourser
    const amountText = amountCell.textContent;
    const amount = parseInt(amountText.replace(/[^0-9]/g, ''));
    
    // Rembourser le solde
    const soldeDisplay = document.getElementById('solde-display');
    const currentSoldeText = soldeDisplay.innerText;
    const currentSolde = parseInt(currentSoldeText.replace(/[^\d]/g, ''));
    const nouveauSolde = currentSolde + amount;
    
    // Mettre à jour le solde affiché
    soldeDisplay.innerHTML = `${nouveauSolde.toLocaleString()} <span class="text-2xl text-brand-blue font-normal">F</span>`;
    
    // Afficher le toast de confirmation
    showToast(`Demande ${reference} annulée. Montant remboursé : ${amountText}`, 'success');
}

/* --- GESTION PAIEMENTS --- */

document.addEventListener('DOMContentLoaded', () => {
    // Logique pour l'affichage du warning montant élevé
    const withdrawSelect = document.getElementById('withdraw-select');
    const highAmountNote = document.getElementById('high-amount-note');
    
    if (withdrawSelect && highAmountNote) {
        withdrawSelect.addEventListener('change', () => {
            const amount = parseInt(withdrawSelect.value);
            if (amount > 100000) {
                highAmountNote.classList.remove('hidden');
            } else {
                highAmountNote.classList.add('hidden');
            }
        });
    }

    // Attacher l'événement au bouton d'importation
    const importButton = document.getElementById('import-button');
    if (importButton) {
        importButton.addEventListener('click', startImportAnalysis);
    }

    /* --- LOGIQUE IMPORTATION CSV --- */
    let importInterval;

    function startImportAnalysis() {
        const modal = document.getElementById('import-process-modal');
        if (!modal) return;

        const stepLoading = document.getElementById('import-step-loading');
        const stepConfirm = document.getElementById('import-step-confirm');
        const progressBar = document.getElementById('import-progress-bar');
        const progressText = document.getElementById('import-percent');

        // 1. Reset et Affichage Forcé
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        modal.style.width = '';
        modal.style.height = '';
        stepLoading.classList.remove('hidden');
        stepConfirm.classList.add('hidden');

        // Reset Barre (Rouge au début)
        progressBar.style.width = '0%';

        progressBar.className = 'h-full bg-red-500 rounded-full transition-all duration-300 linear';
        progressText.innerText = '0%';
        progressText.className = 'text-xs font-bold text-red-500 mt-2 text-right';

        // 2. Animation
        let width = 0;

        importInterval = setInterval(() => {
            if (width >= 100) {
                clearInterval(importInterval);

                setTimeout(() => {
                    stepLoading.classList.add('hidden');
                    stepConfirm.classList.remove('hidden');
                }, 500);
            } else {
                width += 1;
                if(width > 100) width = 100;
                
                progressBar.style.width = width + '%';
                progressText.innerText = width + '%';

                // --- LOGIQUE COULEURS ---
                progressBar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-brand-green', 'bg-brand-blue');
                progressText.classList.remove('text-red-500', 'text-yellow-500', 'text-brand-green', 'text-brand-blue');

                if (width < 30) {
                    progressBar.classList.add('bg-red-500');
                    progressText.classList.add('text-red-500');
                } else if (width < 60) {
                    progressBar.classList.add('bg-yellow-500');
                    progressText.classList.add('text-yellow-500');
                } else if (width < 80) {
                    progressBar.classList.add('bg-brand-green');
                    progressText.classList.add('text-brand-green');
                } else {
                    progressBar.classList.add('bg-brand-blue');
                    progressText.classList.add('text-brand-blue');
                }
            }
        }, 100);
    }

    function closeImportModal() {
        clearInterval(importInterval);
        const modal = document.getElementById('import-process-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = '';
        }
    }

    function finalizeImport() {
        closeImportModal();
        showToast('Succès ! 48 tickets ajoutés au stock.', 'success');
    }

    // Exposer les fonctions à la fenêtre globale pour les boutons onclick
    window.startImportAnalysis = startImportAnalysis;
    window.closeImportModal = closeImportModal;
    window.finalizeImport = finalizeImport;
});