
// Variables globales pour le modal de suppression
let deleteOptions = {
    forfaitId: null,
    forfaitName: '',
    ticketsCount: 0
};

/**
 * Ouvre le modal des options de suppression
 */
function openDeleteOptionsModal(forfaitId, forfaitName, ticketsCount) {
    deleteOptions = {
        forfaitId: forfaitId,
        forfaitName: forfaitName,
        ticketsCount: ticketsCount
    };
    
    // Afficher/masquer le bouton de suppression des tickets non vendus
    const deleteUnsoldBtn = document.getElementById('delete-unsold-tickets-btn');
    if (ticketsCount > 0) {
        deleteUnsoldBtn.classList.remove('hidden');
    } else {
        deleteUnsoldBtn.classList.add('hidden');
    }
    
    document.getElementById('delete-options-modal').classList.remove('hidden');
}

/**
 * Ferme le modal des options de suppression
 */
function closeDeleteOptionsModal() {
    document.getElementById('delete-options-modal').classList.add('hidden');
    deleteOptions = {
        forfaitId: null,
        forfaitName: '',
        ticketsCount: 0
    };
}


// Définir la fonction immédiatement dans le scope global
window.createMikrotikProfile = function() {
    // Log immédiat pour vérifier que la fonction est appelée
    console.log('🔘 BOUTON CLIQUÉ - DÉBUT CRÉATION PROFIL MIKROTIK SIMPLE');
    console.log('📍 URL actuelle:', window.location.href);
    console.log('⏰ Timestamp:', new Date().toISOString());
    
    // Vérifier si les éléments nécessaires existent
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('🔑 CSRF Token trouvé:', !!csrfToken);
    if (csrfToken) {
        console.log('🔑 CSRF Token value:', csrfToken.getAttribute('content'));
    }
    
    // Demander confirmation
    if (!confirm('Voulez-vous créer un profil Mikrotik TESTMIKROTIK sur le routeur 10.10.10.1 ?')) {
        console.log('❌ UTILISATEUR A ANNULÉ - CRÉATION PROFIL MIKROTIK SIMPLE');
        return;
    }
    
    console.log('✅ UTILISATEUR A CONFIRMÉ - CONTINUER LA CRÉATION SIMPLE');
    
    // Afficher un message de chargement
    if (typeof showToast === 'function') {
        console.log('📢 showToast trouvé, affichage du message de chargement');
        showToast('Création du profil Mikrotik TESTMIKROTIK en cours...', 'info');
    } else {
        console.log('⚠️ showToast NON trouvé, utilisation de alert');
        alert('Création du profil Mikrotik TESTMIKROTIK en cours...');
    }
    
    console.log('📤 PRÉPARATION ENVOI VERS /creer-profile-simple');
    console.log('🌐 URL de destination:', '/creer-profile-simple');
    console.log('🔧 Méthode HTTP:', 'POST');
    console.log('📋 Profil cible: TESTMIKROTIK');
    console.log('🌐 Routeur cible: 10.10.10.1:8728');
    
    try {
        console.log('🚀 DÉBUT REQUÊTE FETCH SIMPLE');
        console.log('⏰ Début requête:', new Date().toISOString());
        
        const response = await fetch('/creer-profile-simple', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({}) // Pas de données nécessaires, tout est hardcodé
        });
        
        console.log('📥 RÉPONSE REÇUE SIMPLE');
        console.log('📊 Status HTTP:', response.status);
        console.log('📊 Status Text:', response.statusText);
        console.log('📊 Headers:', [...response.headers.entries()]);
        console.log('⏰ Fin requête:', new Date().toISOString());
        
        // Vérifier si la réponse est OK
        if (!response.ok) {
            console.error('❌ RÉPONSE NON OK:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        console.log('📖 LECTURE DES DONNÉES JSON SIMPLE');
        const data = await response.json();
        console.log('📊 DONNÉES RÉPONSE SIMPLE:', JSON.stringify(data, null, 2));
        
        if (data.status === 'success') {
            console.log('✅ SUCCÈS - PROFIL SIMPLE CRÉÉ');
            if (typeof showToast === 'function') {
                console.log('📢 Affichage toast de succès simple');
                showToast(data.message || 'Profil Mikrotik TESTMIKROTIK créé avec succès !', 'success');
            } else {
                console.log('📢 Affichage alert de succès simple');
                alert(data.message || 'Profil Mikrotik TESTMIKROTIK créé avec succès !');
            }
        } else {
            console.error('❌ ERREUR SERVEUR SIMPLE:', data.message);
            throw new Error(data.message || 'Erreur lors de la création du profil simple');
        }
        
    } catch (error) {
        console.error('💥 ERREUR CAPTURÉE SIMPLE:', error);
        console.error('💥 MESSAGE ERREUR SIMPLE:', error.message);
        console.error('💥 STACK TRACE SIMPLE:', error.stack);
        console.error('⏰ Timestamp erreur simple:', new Date().toISOString());
        
        if (typeof showToast === 'function') {
            console.log('📢 Affichage toast d\'erreur simple');
            showToast(error.message || 'Erreur lors de la création du profil Mikrotik', 'error');
        } else {
            console.log('📢 Affichage alert d\'erreur simple');
            alert(error.message || 'Erreur lors de la création du profil Mikrotik');
        }
    }
    
    console.log('🏁 FIN DE LA FONCTION createMikrotikProfile SIMPLE');
};

// Log pour vérifier que la fonction est bien définie au chargement
console.log('✅ FONCTION createMikrotikProfile SIMPLE DÉFINIE DANS WINDOW.SCOPE');
console.log('🔍 Vérification:', typeof window.createMikrotikProfile);

/**
 * Créer un ticket unique sur Mikrotik
 */
window.createSingleTicket = function() {
    console.log('🎫 DÉBUT CRÉATION TICKET UNIQUE');
    console.log('⏰ Timestamp clic bouton:', new Date().toISOString());
    console.log('📍 URL actuelle:', window.location.href);
    console.log('🔘 BOUTON TICKET UNIQUE CLIQUÉ - DÉMARRAGE FONCTION');
    
    // Demander les informations du ticket à l'utilisateur
    const username = prompt('Entrez le username du ticket:', 'testuser_' + Date.now());
    if (!username) {
        console.log('❌ UTILISATEUR A ANNULÉ - USERNAME NON FOURNI');
        return;
    }
    
    const password = prompt('Entrez le mot de passe du ticket:', 'pass' + Math.floor(Math.random() * 10000));
    if (!password) {
        console.log('❌ UTILISATEUR A ANNULÉ - PASSWORD NON FOURNI');
        return;
    }
    
    const profile = prompt('Entrez le profil Mikrotik (ex: TESTwire):', 'TESTwire');
    if (!profile) {
        console.log('❌ UTILISATEUR A ANNULÉ - PROFILE NON FOURNI');
        return;
    }
    
    console.log('✅ DONNÉES RECUEILLIES - CRÉATION TICKET UNIQUE');
    console.log('📋 Ticket: ' + username + ' / ' + password + ' / ' + profile);
    
    // Confirmation
    if (!confirm('Voulez-vous créer un ticket unique avec:\n\nUsername: ' + username + '\nPassword: ' + password + '\nProfile: ' + profile + '\n\nRouteur: 10.10.10.1:8728')) {
        console.log('❌ UTILISATEUR A ANNULÉ - CRÉATION TICKET UNIQUE');
        return;
    }
    
    console.log('✅ UTILISATEUR A CONFIRMÉ - CRÉATION TICKET UNIQUE');
    
    // Afficher un message de chargement
    if (typeof showToast === 'function') {
        showToast('Création du ticket unique ' + username + ' en cours...', 'info');
    } else {
        alert('Création du ticket unique ' + username + ' en cours...');
    }
    
    try {
        console.log('📤 PRÉPARATION ENVOI VERS /create-single-ticket');
        console.log('🌐 URL de destination:', '/create-single-ticket');
        console.log('🔧 Méthode HTTP:', 'POST');
        console.log('📋 Ticket: ' + username + ' / ' + password + ' / ' + profile);
        console.log('🌐 Routeur cible: 10.10.10.1:8728');
        
        const response = await fetch('/create-single-ticket', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                username: username,
                password: password,
                profile: profile
            })
        });
        
        console.log('📥 RÉPONSE REÇUE TICKET UNIQUE');
        console.log('📊 Status HTTP:', response.status);
        console.log('📊 Status Text:', response.statusText);
        
        if (!response.ok) {
            console.error('❌ RÉPONSE NON OK:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('📊 DONNÉES RÉPONSE TICKET UNIQUE:', JSON.stringify(data, null, 2));
        
        if (data.status === 'success') {
            console.log('✅ SUCCÈS - TICKET UNIQUE CRÉÉ');
            if (typeof showToast === 'function') {
                showToast(data.message || 'Ticket unique ' + username + ' créé avec succès !', 'success');
            } else {
                alert(data.message || 'Ticket unique ' + username + ' créé avec succès !');
            }
        } else {
            console.error('❌ ERREUR SERVEUR TICKET UNIQUE:', data.message);
            throw new Error(data.message || 'Erreur lors de la création du ticket unique');
        }
        
    } catch (error) {
        console.error('💥 ERREUR CAPTURÉE TICKET UNIQUE:', error);
        console.error('💥 MESSAGE ERREUR TICKET UNIQUE:', error.message);
        console.error('💥 STACK TRACE TICKET UNIQUE:', error.stack);
        console.error('⏰ Timestamp erreur ticket unique:', new Date().toISOString());
        
        if (typeof showToast === 'function') {
            showToast(error.message || 'Erreur lors de la création du ticket unique', 'error');
        } else {
            alert(error.message || 'Erreur lors de la création du ticket unique');
        }
    }
    
    console.log('🏁 FIN DE LA FONCTION createSingleTicket');
};

/**
 * Log le clic sur le bouton Ticket Unique vers le serveur
 */
window.createSingleTicketLog = function() {
    console.log('📝 ENVOI LOG CLIQUEUR TICKET UNIQUE');
    
    fetch('/log-ticket-unique-click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: 'click_create_single_ticket',
            timestamp: new Date().toISOString(),
            page: window.location.href
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ LOG CLIQUEUR TICKET UNIQUE ENVOYÉ:', data);
    })
    .catch(error => {
        console.error('❌ ERREUR ENVOI LOG CLIQUEUR:', error);
    });
};

// Log pour vérifier que la fonction est bien définie
console.log('✅ FONCTION createSingleTicket DÉFINIE DANS WINDOW.SCOPE');
console.log('🔍 Vérification:', typeof window.createSingleTicket);

// ==================== GESTION DE LA GÉNÉRATION DE TICKETS ====================

/**
 * Gérer les changements dans le formulaire de génération
 */
function setupGenerationForm() {
    const zoneSelect = document.getElementById('generate-zone');
    const packageSelect = document.getElementById('generate-package');
    const ticketCount = document.getElementById('ticket-count');
    const previewDiv = document.getElementById('generation-preview');
    
    // Mettre à jour les options de forfait selon la zone
    zoneSelect.addEventListener('change', function() {
        const selectedZone = this.value;
        const options = packageSelect.querySelectorAll('option');
        
        console.log('🔄 CHANGEMENT DE ZONE DÉTECTÉ:', {
            selectedZone: selectedZone,
            zoneName: this.options[this.selectedIndex]?.text,
            totalOptions: options.length
        });
        
        options.forEach(option => {
            const optionZone = option.getAttribute('data-zone-id');
            const shouldShow = !selectedZone || optionZone === selectedZone;
            option.style.display = shouldShow ? 'block' : 'none';
            
            if (option.value) { // Ne pas log l'option vide
                console.log(`📋 Forfait "${option.textContent}": zone=${optionZone}, visible=${shouldShow}`);
            }
        });
        
        // Réinitialiser la sélection si l'option actuelle n'est plus valide
        if (packageSelect.value && packageSelect.selectedOptions[0].style.display === 'none') {
            packageSelect.value = '';
            console.log('🔄 SÉLECTION FORFAIT RÉINITIALISÉE (plus visible)');
        }
        
        window.updatePreview();
    });
    
    // Mettre à jour la prévisualisation
    [zoneSelect, packageSelect, ticketCount].forEach(element => {
        element.addEventListener('change', window.updatePreview);
        element.addEventListener('input', window.updatePreview);
    });
    
    // Initialiser la prévisualisation
    window.updatePreview();
}

/**
 * Mettre à jour la prévisualisation de génération
 */
window.updatePreview = function() {
    const zoneSelect = document.getElementById('generate-zone');
    const packageSelect = document.getElementById('generate-package');
    const ticketCount = document.getElementById('ticket-count');
    const previewDiv = document.getElementById('generation-preview');
    
    const zoneName = zoneSelect.options[zoneSelect.selectedIndex]?.text || '-';
    const packageName = packageSelect.options[packageSelect.selectedIndex]?.text || '-';
    const count = ticketCount.value || '-';
    const profileName = packageSelect.value ? 
        packageSelect.options[packageSelect.selectedIndex].getAttribute('data-profile') || 
        packageName.split(' ')[0] + '_Profile' : '-';
    
    // Mettre à jour les éléments de prévisualisation
    document.getElementById('preview-zone').textContent = zoneName;
    document.getElementById('preview-package').textContent = packageName;
    document.getElementById('preview-count').textContent = count;
    document.getElementById('preview-profile').textContent = profileName;
    
    // Afficher/masquer la prévisualisation
    if (zoneSelect.value && packageSelect.value && ticketCount.value) {
        previewDiv.classList.remove('hidden');
    } else {
        previewDiv.classList.add('hidden');
    }
}

/**
 * Wrapper pour le clic sur le bouton générer
 */
function generateTicketsClick() {
    console.log('🔘 Clic sur bouton générer - appel startTicketGeneration');
    if (typeof window.startTicketGeneration === 'function') {
        window.startTicketGeneration();
    } else {
        console.error('❌ startTicketGeneration non définie');
        alert('Erreur: Fonction non chargée');
    }
}

/**
 * Démarrer la génération de tickets
 */
window.startTicketGeneration = async function() {
    console.log('🚀 DÉBUT GÉNÉRATION TICKETS');
    console.log('⏰ Timestamp:', new Date().toISOString());
    
    const generateBtn = document.getElementById('generate-btn');
    if (!generateBtn) {
        console.error('❌ Bouton génération introuvable');
        alert('Erreur: Bouton introuvable');
        return;
    }
    
    console.log('🔘 BOUTON GÉNÉRER LES TICKETS CLIQUÉ !');
    
    // Validation basique des champs
    const zoneSelect = document.getElementById('generate-zone');
    const packageSelect = document.getElementById('generate-package');
    const ticketCount = document.getElementById('ticket-count');
    
    if (!zoneSelect.value || !packageSelect.value || !ticketCount.value) {
        if (typeof showToast === 'function') {
            showToast('Veuillez remplir tous les champs', 'error');
        } else {
            alert('Veuillez remplir tous les champs');
        }
        return;
    }
    
    const count = parseInt(ticketCount.value);
    if (count < 1 || count > 1000) {
        if (typeof showToast === 'function') {
            showToast('Le nombre de tickets doit être entre 1 et 1000', 'error');
        } else {
            alert('Le nombre de tickets doit être entre 1 et 1000');
        }
        return;
    }
    
    // Désactiver le bouton et montrer le chargement (même spinner que le bouton "Se connecter")
    generateBtn.disabled = true;
    generateBtn.classList.add('pointer-events-none', 'opacity-75');
    generateBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Génération en cours...';
    
    try {
        const zoneName = zoneSelect.options[zoneSelect.selectedIndex].text;
        const packageName = packageSelect.options[packageSelect.selectedIndex].text;
        const autoValidate = document.getElementById('auto-validate').checked;
        
        console.log('📋 Données de génération:', {
            zone: zoneName,
            package: packageName,
            count: count,
            autoValidate: autoValidate
        });
        
        // Créer un FormData pour l'envoi (comme le formulaire de forfait)
        const formData = new FormData();
        formData.append('zone_id', zoneSelect.value);
        formData.append('forfait_id', packageSelect.value);
        formData.append('ticket_count', count);
        formData.append('auto_validate', autoValidate);
        
        // Envoyer le formulaire via fetch (comme le formulaire de forfait)
        const response = await fetch('/generate-tickets', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data && data.success === false) {
            throw new Error(data.message || 'Erreur lors de la génération');
        }
        
        // Succès
        console.log('✅ TICKETS GÉNÉRÉS AVEC SUCCÈS:', data);
        
        if (typeof showToast === 'function') {
            showToast(data.message || `${count} tickets générés avec succès !`, 'success');
        } else {
            alert(data.message || `${count} tickets générés avec succès !`);
        }
        
        // Réinitialiser le formulaire
        zoneSelect.value = '';
        packageSelect.value = '';
        ticketCount.value = '10';
        window.updatePreview();
        
    } catch (error) {
        console.error('❌ ERREUR GÉNÉRATION TICKETS:', error);
        
        if (typeof showToast === 'function') {
            showToast(error.message || 'Erreur lors de la génération des tickets', 'error');
        } else {
            alert(error.message || 'Erreur lors de la génération des tickets');
        }
    } finally {
        // Réactiver le bouton
        generateBtn.disabled = false;
        generateBtn.classList.remove('opacity-75', 'pointer-events-none');
        generateBtn.innerHTML = '<i class="fas fa-magic"></i> Générer les Tickets';
    }
}

// Ajouter des écouteurs d'événements pour les dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Écouter les changements sur les dropdowns
    const limiteTempsUnite = document.getElementById('pkg-time-limit-unit');
    const validiteUnite = document.getElementById('pkg-duration-unit');
    
    if (limiteTempsUnite) {
        limiteTempsUnite.addEventListener('change', function() {
            console.log('🔄 Changement unité limite temps:', this.value);
            updatePreview();
        });
    }
    
    if (validiteUnite) {
        validiteUnite.addEventListener('change', function() {
            console.log('🔄 Changement unité validité:', this.value);
            updatePreview();
        });
    }
    
    console.log('✅ ÉCOUTEURS DROPDOWNS INITIALISÉS');
});

// Gestionnaire du formulaire de création de forfait avec loading indicator
document.addEventListener('DOMContentLoaded', function() {
    const packageForm = document.getElementById('package-form');
    const submitBtn = document.getElementById('modal-submit-btn');
    
    if (packageForm && submitBtn) {
        packageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validation basique
            const nom = document.getElementById('pkg-name').value;
            const prix = document.getElementById('pkg-price').value;
            const limiteTemps = document.getElementById('pkg-time-limit').value;
            const validite = document.getElementById('pkg-duration').value;
            
            if (!nom || !prix || !limiteTemps || !validite) {
                if (typeof showToast === 'function') {
                    showToast('Veuillez remplir tous les champs obligatoires', 'error');
                } else {
                    alert('Veuillez remplir tous les champs obligatoires');
                }
                return;
            }
            
            // Désactiver le bouton et montrer le chargement
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Création...';
            
            // Créer un FormData pour l'envoi
            const formData = new FormData(packageForm);
            
            // Envoyer le formulaire via fetch
            fetch(packageForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Redirection normale (succès)
                    window.location.href = response.url;
                    return;
                }
                
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        // Si ce n'est pas du JSON, c'est probablement une redirection HTML
                        if (text.includes('forfait') || text.includes('succès')) {
                            window.location.reload();
                        } else {
                            throw new Error('Réponse invalide');
                        }
                    }
                });
            })
            .then(data => {
                if (data && data.success === false) {
                    throw new Error(data.message || 'Erreur lors de la création');
                }
                // Succès - redirection ou rechargement
                window.location.reload();
            })
            .catch(error => {
                console.error('❌ ERREUR CRÉATION FORFAIT:', error);
                
                // Réactiver le bouton en cas d'erreur
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Créer le Forfait';
                
                if (typeof showToast === 'function') {
                    showToast(error.message || 'Erreur lors de la création du forfait', 'error');
                } else {
                    alert(error.message || 'Erreur lors de la création du forfait');
                }
            });
        });
        
        console.log('✅ GESTIONNAIRE FORMULAIRE FORFAIT INITIALISÉ');
    }
});

// Initialiser le formulaire au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setupGenerationForm();
    console.log('✅ FORMULAIRE DE GÉNÉRATION INITIALISÉ');
    console.log('🔍 Vérification fonction startTicketGeneration:', typeof window.startTicketGeneration);
    console.log('🔍 Vérification fonction updatePreview:', typeof window.updatePreview);
    console.log('🔍 Vérification formulaire:', !!document.getElementById('generate-form'));
    console.log('🔍 Vérification bouton:', !!document.getElementById('generate-btn'));
    
    console.log('✅ BOUTON GÉNÉRATION PRÊT - onclick direct configuré');
});
