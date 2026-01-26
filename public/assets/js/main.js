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
        // Initiales
        if(detailAvatar) {
            const initials = name.split(' ').map(n => n[0]).join('');
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