
    // Initialisation avec la valeur du serveur (Filtre actif)
    let currentSelectedZoneId = '{{ request("filter_zone", "all") }}';
    let currentSelectedZoneName = 'Toutes les zones';

    // --- GESTION DES MODALES PERSONNALISÉES (REMPLACEMENT ALERT/CONFIRM) ---
    function showErrorModal(title, message) {
        document.getElementById('error-title').innerText = title;
        document.getElementById('error-message').innerText = message;
        document.getElementById('error-modal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    function showConfirmModal(options) {
        const modal = document.getElementById('confirm-modal');
        const title = document.getElementById('confirm-title');
        const message = document.getElementById('confirm-message');
        const proceedBtn = document.getElementById('confirm-proceed-btn');
        const cancelBtn = document.getElementById('confirm-cancel-btn');
        const icon = document.getElementById('confirm-icon');
        const iconContainer = document.getElementById('confirm-icon-container');

        title.innerText = options.title || 'Confirmation';
        message.innerText = options.message || 'Êtes-vous sûr ?';
        proceedBtn.innerText = options.confirmText || 'Confirmer';
        cancelBtn.innerText = options.cancelText || 'Annuler';
        
        // Styles par défaut
        proceedBtn.className = "flex-1 py-3 text-white rounded-xl text-sm font-bold shadow-lg transition " + (options.confirmClass || "bg-brand-blue hover:bg-blue-600");
        icon.className = "fas " + (options.icon || "fa-exclamation-triangle") + " text-3xl";
        iconContainer.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 " + (options.iconBg || "bg-orange-50 dark:bg-orange-900/20 text-orange-500");

        // Action au clic
        proceedBtn.onclick = function() {
            if (options.onConfirm) options.onConfirm();
            closeConfirmModal();
        };

        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
    }

    function confirmDeleteTicket(ticketId) {
        showConfirmModal({
            title: "Supprimer ce ticket ?",
            message: "Cette action est irréversible. Le ticket sera définitivement retiré de la base de données.",
            confirmText: "Supprimer",
            confirmClass: "bg-red-500 hover:bg-red-600",
            icon: "fa-trash",
            iconBg: "bg-red-50 dark:bg-red-900/20 text-red-500",
            onConfirm: function() {
                document.getElementById(`delete-ticket-form-${ticketId}`).submit();
            }
        });
    }

    // Initialisation
    document.addEventListener("DOMContentLoaded", function() {
        // Gérer l'onglet actif depuis l'URL (pour la recherche)
        // Chercher l'onglet via paramètre URL ou Hash (#list, #stock)
        const urlParams = new URLSearchParams(window.location.search);
        let tabFromUrl = urlParams.get('tab');
        
        // Si pas en paramètre, essayer le hash
        if (!tabFromUrl && window.location.hash) {
            tabFromUrl = window.location.hash.replace('#', '');
        }
        
        // Initialiser le nom de la zone si un filtre est présent
        if(currentSelectedZoneId !== 'all') {
            const activeOption = document.querySelector(`div[onclick*="selectZoneFilter('${currentSelectedZoneId}'"]`);
            if(activeOption) {
                 const nameSpan = activeOption.querySelector('span:nth-child(2)');
                 if(nameSpan) {
                     currentSelectedZoneName = nameSpan.innerText;
                     document.getElementById('selected-zone-name').innerText = currentSelectedZoneName;
                 }
            }
        }

        if (tabFromUrl) {
            showTabContent(tabFromUrl);
        } else {
            showTabContent('catalogue');
        }
        
        // Appliquer le filtre visuel pour le catalogue
        filterByZone(currentSelectedZoneId);
        
        // Initialiser les KPIs de stock
        updateStockKPIs(currentSelectedZoneId);
    });

    // --- GESTION DU CUSTOM DROPDOWN (ZONES) ---
    function toggleDropdown() {
        const menu = document.getElementById('zone-dropdown-menu');
        
        // Force la fermeture des autres dropdowns potentiels
        document.querySelectorAll('[id$="-dropdown-menu"]').forEach(d => {
            if(d !== menu) d.classList.add('hidden');
        });
        
        menu.classList.toggle('hidden');
    }

    // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('zone-dropdown-menu');
        const button = event.target.closest('button[onclick="toggleDropdown()"]');
        
        if (!dropdown.contains(event.target) && !button) {
            dropdown.classList.add('hidden');
        }
    });

    // --- FONCTION DE FILTRAGE PAR ZONE ---
    function filterByZone(zoneId) {
        const items = document.querySelectorAll('.filterable-item');
        let hasResults = false;

        items.forEach(item => {
            const itemZoneId = item.getAttribute('data-zone-id');
            if (zoneId === 'all' || itemZoneId === zoneId) {
                item.style.display = ''; 
                if(item.classList.contains('hidden')) item.classList.remove('hidden');
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });

        const noResultsMsg = document.getElementById('no-results-msg');
        if(noResultsMsg) {
            if(!hasResults && items.length > 0) {
                noResultsMsg.classList.remove('hidden');
            } else {
                noResultsMsg.classList.add('hidden');
            }
        }
    }

    // --- GESTION DES ONGLETS ---
    
    // Fonction interne pour changer le visuel uniquement (sans logique de reload)
    function showTabContent(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'font-bold', 'text-gray-800', 'dark:text-white');
            btn.classList.remove('border-[#072b47]', 'dark:border-[#0EA5E9]');
            btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });
        const activeBtn = document.getElementById('tab-' + tabName);
        if(activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('active', 'font-bold', 'text-gray-800', 'dark:text-white');
            activeBtn.classList.add('border-[#072b47]', 'dark:border-[#0EA5E9]');
        }
        const activeContent = document.getElementById('content-' + tabName);
        if(activeContent) activeContent.classList.remove('hidden');
    }

    // Fonction appelée par le clic utilisateur
    function switchTab(tabName) {
        // LOGIQUE SPECIALE POUR L'ONGLET LISTE
        if (tabName === 'list') {
            // On vérifie si on doit recharger la page pour appliquer le filtre de zone
            const urlParams = new URLSearchParams(window.location.search);
            const urlZone = urlParams.get('filter_zone') || 'all';

            // Si la zone sélectionnée (JS) est différente de celle dans l'URL
            // On doit recharger pour avoir les bonnes données
            if (currentSelectedZoneId !== urlZone) {
                let newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', 'list');
                
                if (currentSelectedZoneId === 'all') {
                    newUrl.searchParams.delete('filter_zone');
                } else {
                    newUrl.searchParams.set('filter_zone', currentSelectedZoneId);
                }
                
                window.location.href = newUrl.toString();
                return;
            }
        }

        // Affichage standard
        showTabContent(tabName);
        
        // Mise à jour de l'URL sans rechargement pour confort (si on n'est pas dans le cas du reload ci-dessus)
        // Cela permet de garder l'état si on rafraichit
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', tabName);
        window.history.pushState({}, '', newUrl);
    }

    // --- GESTION UNIFIÉE DE LA MODALE (CRÉATION / MODIFICATION) ---
    function openPackageModal(mode, data = {}) {
        const modal = document.getElementById('create-package-modal');
        const title = document.getElementById('modal-title');
        const btn = document.getElementById('modal-submit-btn');
        const form = document.getElementById('package-form');
        const methodInput = document.getElementById('form-method');

        if (mode === 'edit') {
            // --- MODE ÉDITION ---
            title.innerText = "Modifier le Forfait";
            btn.innerText = "Sauvegarder les modifications";
            methodInput.value = "PUT"; // Force le mode PUT pour Laravel
            form.action = `/forfaits/${data.id}`; // Route d'update
            
            // Remplissage des champs
            document.getElementById('pkg-id').value = data.id;
            document.getElementById('pkg-name').value = data.nom;
            document.getElementById('pkg-price').value = data.prix;
            document.getElementById('pkg-duration').value = data.validite;
            // Note: temps_limit n'a pas de champ direct dans le formulaire, il est calculé depuis limite_temps + limite_temps_unite
            document.getElementById('pkg-desc').value = data.description || '';
            
            // Remplir la zone liée au forfait
            document.getElementById('modal_zone_id').value = data.zone_id;
            document.getElementById('modal_zone_name').innerText = data.zone_name;
            
            // Sélection couleur
            const radio = document.querySelector(`input[name="color_class"][value="${data.color_class}"]`);
            if(radio) radio.checked = true;

        } else {
            // --- MODE CRÉATION ---
            title.innerText = "Nouveau Forfait WiFi";
            btn.innerText = "Créer le Forfait";
            methodInput.value = "POST";
            form.action = "{{ route('forfaits.store') }}";
            form.reset(); // Vide tout
        }

        updatePreview(); // Rafraîchit le téléphone à droite
        modal.classList.remove('hidden');
    }

    function closeCreatePackageModal() {
        document.getElementById('create-package-modal').classList.add('hidden');
    }

    // --- MODALE CRÉATION (COMPATIBILITÉ) ---
    function openCreatePackageModal() {
        // Si l'utilisateur a filtré sur "Toutes les zones", on lui demande de choisir d'abord
        if (currentSelectedZoneId === 'all') {
            showErrorModal("Zone requise", "Veuillez sélectionner une zone dans le menu en haut avant de créer un forfait");
            return;
        }

        // On remplit les champs de la modale avec nos variables globales
        document.getElementById('modal_zone_id').value = currentSelectedZoneId;
        document.getElementById('modal_zone_name').textContent = currentSelectedZoneName;

        // On affiche la modale
        document.getElementById('create-package-modal').classList.remove('hidden');
    }

    function closeEditPackageModal() {
        document.getElementById('edit-package-modal').classList.add('hidden');
    }

    // --- GESTION SUPPRESSION FORFAIT ---
    function toggleDeleteMenu(menuId) {
        const menu = document.getElementById(menuId);
        if (menu.classList.contains('hidden')) {
            // Fermer tous les autres menus
            document.querySelectorAll('[id^="delete-menu-"]').forEach(m => {
                if (m.id !== menuId) m.classList.add('hidden');
            });
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    }
    
    // Fermer le menu quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('[id^="delete-menu-"]').forEach(m => m.classList.add('hidden'));
        }
    });
    
    // Variables pour stocker les infos de suppression
    let deleteForfaitId = null;
    let deleteForfaitName = '';
    let deleteTicketsCount = 0;
    
    // Fonction pour afficher le modal avec deux options
    function showDeleteOptionsModal(id, name, count) {
        const modal = document.getElementById('delete-package-modal');
        const btnDeleteForfait = document.getElementById('btn-delete-forfait');
        const btnDeleteTickets = document.getElementById('btn-delete-tickets');
        
        // Stocker les infos
        deleteForfaitId = id;
        deleteForfaitName = name;
        deleteTicketsCount = count;
        
        // Afficher/masquer le bouton de suppression des tickets selon s'il y a des tickets
        if (count > 0) {
            btnDeleteTickets.classList.remove('hidden');
        } else {
            btnDeleteTickets.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
    }
    
    // Confirmer la suppression du forfait
    function confirmDeleteForfait() {
        const form = document.getElementById('delete-package-form');
        form.action = `/forfaits/${deleteForfaitId}`;
        form.submit();
    }
    
    // Confirmer la suppression des tickets non vendus
    function confirmDeleteTickets() {
        // Utiliser le modal de suppression en masse
        closeDeletePackageModal();
        previewBulkDelete('forfait', deleteForfaitId, deleteForfaitName);
    }
    
    function prepDeletePackage(id, name, count, deleteType = 'forfait') {
        // Ancien code - maintenant on utilise showDeleteOptionsModal à la place
        showDeleteOptionsModal(id, name, count);
    }

    function closeDeletePackageModal() {
        document.getElementById('delete-package-modal').classList.add('hidden');
    }

    // --- GESTION SUPPRESSION EN MASSE ---
    async function previewBulkDelete(type, id, name) {
        const modal = document.getElementById('bulk-delete-modal');
        const title = document.getElementById('bulk-delete-title');
        const msg = document.getElementById('bulk-delete-message');
        const warningDiv = document.getElementById('bulk-delete-warning');
        const warningText = document.getElementById('bulk-delete-warning-text');
        const form = document.getElementById('bulk-delete-form');
        const iconBox = document.getElementById('bulk-delete-icon-box');
        const icon = document.getElementById('bulk-delete-icon');

        // Hide warning by default
        warningDiv.classList.add('hidden');

        // Determine endpoint
        let endpoint = '';
        let modalTitle = '';
        let modalMessage = '';
        let hasSoldTickets = false;

        if (type === 'forfait') {
            endpoint = `/tickets/by-forfait/${id}`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> du forfait "<strong>${name}</strong>" ?`;
            hasSoldTickets = await checkForSoldTickets(id, 'forfait');
        } else if (type === 'zone') {
            endpoint = `/tickets/by-zone/${id}`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> de la zone "<strong>${name}</strong>" ?`;
            hasSoldTickets = await checkForSoldTickets(id, 'zone');
        } else if (type === 'date') {
            endpoint = `/tickets/by-date`;
            modalTitle = "Supprimer tous les tickets ?";
            modalMessage = `Voulez-vous supprimer <strong>tous les tickets</strong> créés le <strong>${formatDate(id)}</strong> ?`;
            hasSoldTickets = await checkForSoldTickets(id, 'date');
            // Set the hidden date input for date deletion
            document.getElementById('bulk-delete-date').value = id;
            // Also add zone filter if selected
            const currentZoneId = document.getElementById('filter-zone')?.value;
            if (currentZoneId && currentZoneId !== 'all') {
                let hiddenZoneInput = document.getElementById('bulk-delete-zone');
                if (!hiddenZoneInput) {
                    hiddenZoneInput = document.createElement('input');
                    hiddenZoneInput.type = 'hidden';
                    hiddenZoneInput.id = 'bulk-delete-zone';
                    hiddenZoneInput.name = 'zone_id';
                    document.getElementById('bulk-delete-form').appendChild(hiddenZoneInput);
                }
                hiddenZoneInput.value = currentZoneId;
            }
        }

        if (hasSoldTickets) {
            warningDiv.classList.remove('hidden');
            warningText.innerText = "Attention : Suppression des tickets non vendus !";
            iconBox.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-red-50 dark:bg-red-900/20 text-red-500";
            icon.className = "fas fa-exclamation-triangle text-2xl";
        } else {
            iconBox.className = "w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-orange-100 dark:bg-orange-900/30 text-orange-500";
            icon.className = "fas fa-exclamation-triangle text-2xl";
        }

        form.action = endpoint;
        title.innerText = modalTitle;
        msg.innerHTML = modalMessage;
        modal.classList.remove('hidden');
    }

    // --- SOUMISSION SUPPRESSION EN MASSE AVEC TOAST ---
    async function submitBulkDelete(form) {
        const formData = new FormData(form);
        const url = form.action;
        
        console.log('Submitting bulk delete to:', url);
        
        // Add CSRF token and method to FormData for Laravel
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('Response status:', response.status, 'OK:', response.ok);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                // Not JSON response - might be redirect or error page
                closeBulkDeleteModal();
                if (response.ok) {
                    // Success but not JSON - reload page
                    if (typeof showToast === 'function') {
                        showToast('Opération réussie !', 'success');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof showToast === 'function') {
                        showToast('Erreur lors de la suppression', 'error');
                    }
                }
                return;
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            // Fermer le modal
            closeBulkDeleteModal();
            
            if (data.success) {
                // Afficher le toast de succès
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Suppression réussie !', 'success');
                } else {
                    alert(data.message || 'Suppression réussie !');
                }
                // Recharger la page pour mettre à jour l'affichage
                setTimeout(() => window.location.reload(), 1500);
            } else {
                // Afficher le toast d'erreur
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Erreur lors de la suppression', 'error');
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
            closeBulkDeleteModal();
            if (typeof showToast === 'function') {
                showToast('Erreur lors de la connexion au serveur', 'error');
            } else {
                alert('Erreur lors de la connexion au serveur');
            }
        }
    }

    async function checkForSoldTickets(id, type) {
        try {
            let url = `/tickets/preview?type=${type}&id=${id}`;
            if (type === 'date') {
                // For date type, get the current zone ID
                const currentZoneId = document.getElementById('filter-zone')?.value;
                if (currentZoneId && currentZoneId !== 'all') {
                    url += `&zone_id=${currentZoneId}`;
                }
            }
            
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    // Display the actual preview data
                    const warningDiv = document.getElementById('bulk-delete-warning');
                    const warningText = document.getElementById('bulk-delete-warning-text');
                    const msg = document.getElementById('bulk-delete-message');
                    
                    // Update message with actual counts
                    if (type === 'forfait') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> du forfait "<strong>${data.data.forfait_name}</strong>" ?`;
                    } else if (type === 'zone') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> de la zone "<strong>${data.data.zone_name}</strong>" ?`;
                    } else if (type === 'date') {
                        msg.innerHTML = `Voulez-vous supprimer <strong>${data.data.total_count} ticket(s)</strong> créés le <strong>${formatDate(id)}</strong> ?`;
                    }
                    
                    // Show warning if there are sold tickets
                    if (data.data.vendu_count > 0) {
                        warningDiv.classList.remove('hidden');
                        warningText.innerHTML = `Attention : <strong>${data.data.vendu_count} ticket(s) ont déjà été vendus</strong> et seront supprimés.`;
                    } else {
                        warningDiv.classList.add('hidden');
                    }
                    
                    return data.data.vendu_count > 0;
                }
            }
        } catch (error) {
            console.error('Error checking tickets:', error);
        }
        return false;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function closeBulkDeleteModal() {
        document.getElementById('bulk-delete-modal').classList.add('hidden');
    }

    function updatePreview() {
        const name = document.getElementById('pkg-name').value || 'Nom du Forfait';
        const price = document.getElementById('pkg-price').value || '0';
        const limiteTemps = document.getElementById('pkg-time-limit').value || '1';
        const limiteTempsUnite = document.getElementById('pkg-time-limit-unit')?.value || 'heure';
        const validite = document.getElementById('pkg-duration').value || '1';
        const validiteUnite = document.getElementById('pkg-duration-unit')?.value || 'heure';
        const desc = document.getElementById('pkg-desc').value || 'Description...';
        
        // Formater les textes avec unités
        const limiteTempsText = formatUniteTemps(limiteTemps, limiteTempsUnite);
        const validiteText = formatUniteTemps(validite, validiteUnite);
        
        const colorRadio = document.querySelector('input[name="color_class"]:checked');
        const colorClass = colorRadio ? colorRadio.value : 'bg-brand-blue';
        
        const colorMap = {
            'bg-brand-blue': '#0EA5E9',
            'bg-red-500': '#EF4444',
            'bg-green-500': '#84CC16',
            'bg-yellow-500': '#F59E0B',
            'bg-purple-500': '#8B5CF6'
        };
        const activeColor = colorMap[colorClass] || '#0EA5E9';

        document.getElementById('prev-name').textContent = name;
        document.getElementById('prev-price').textContent = price + ' FCFA';
        document.getElementById('prev-duration').textContent = 'Limite: ' + limiteTempsText + ' | Validité: ' + validiteText;
        document.getElementById('prev-desc').textContent = desc;
        document.getElementById('prev-header').style.backgroundColor = activeColor;
        
        console.log('🔄 APERÇU MIS À JOUR:', {
            name,
            price,
            limiteTemps: limiteTempsText,
            validite: validiteText,
            desc
        });
    }
    
    // Fonction pour formater les unités de temps
    function formatUniteTemps(valeur, unite) {
        const valeurNum = parseInt(valeur) || 1;
        const uniteText = valeurNum > 1 ? unite + 's' : unite;
        
        // Gérer les abréviations
        const abreviations = {
            'heures': 'H',
            'heure': 'H',
            'jours': 'J',
            'jour': 'J',
            'mois': 'M',
            'mois': 'M'
        };
        
        return valeurNum + ' ' + (abreviations[uniteText] || uniteText);
    }

    /* --- GESTION DE L'INPUT FICHIER (DESIGN) --- */
    const fileInput = document.getElementById('import-file');
    const dropZone = fileInput.closest('label');
    const dropText = dropZone.querySelector('p.mb-2');
    const dropIcon = dropZone.querySelector('i');

    // Quand un fichier est choisi via clic
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            updateDropZone(this.files[0].name);
        }
    });

    // Gestion du Drag & Drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Effet visuel au survol
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-brand-blue', 'bg-blue-50', 'dark:bg-slate-700');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-brand-blue', 'bg-blue-50', 'dark:bg-slate-700');
        }, false);
    });

    // Quand on relâche le fichier
    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files; // Assigner le fichier à l'input
            updateDropZone(files[0].name);
        }
    }, false);

    function updateDropZone(fileName) {
        dropText.innerHTML = `<span class="font-bold text-brand-blue">${fileName}</span>`;
        dropIcon.className = "fas fa-check-circle text-3xl text-green-500";
        dropZone.classList.add('border-green-500');
    }

    // --- FILTRAGE DYNAMIQUE DES FORFAITS PAR ZONE ---
    document.addEventListener('DOMContentLoaded', function() {
        const zoneSelect = document.getElementById('import-zone');
        const forfaitSelect = document.getElementById('import-package');
        
        if (zoneSelect && forfaitSelect) {
            zoneSelect.addEventListener('change', function() {
                const selectedZoneId = this.value;
                const options = forfaitSelect.querySelectorAll('option');
                
                // Réinitialiser le select de forfait
                forfaitSelect.value = '';
                
                // Filtrer les options
                options.forEach(function(option) {
                    if (option.value === '') {
                        // Garder l'option vide par défaut
                        option.style.display = 'block';
                    } else {
                        const zoneId = option.getAttribute('data-zone-id');
                        if (zoneId === selectedZoneId) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            });
        }
        
        // --- ÉCOUTEURS POUR LES FILTRES DU TABLEAU HISTORIQUE ---
        const filterZone = document.getElementById('filter-zone');
        const filterForfait = document.getElementById('filter-forfait');
        const filterStatut = document.getElementById('filter-statut');
        const filterDate = document.getElementById('filter-date');
        
        // Fonction pour appliquer les filtres
        function applyImportFilters() {
            if (typeof applyFilters === 'function') {
                applyFilters();
            }
        }
        
        // Attacher les écouteurs
        if (filterZone) filterZone.addEventListener('change', applyImportFilters);
        if (filterForfait) filterForfait.addEventListener('change', applyImportFilters);
        if (filterStatut) filterStatut.addEventListener('change', applyImportFilters);
        if (filterDate) filterDate.addEventListener('change', applyImportFilters);
        
        // Initialiser le tableau des imports
        if (typeof initImportsTable === 'function') {
            initImportsTable();
        }
    });

    function closeImportModal() {
        document.getElementById('import-process-modal').classList.add('hidden');
    }

    /* --- IMPORTATION CSV CORRIGÉE --- */
    let importInterval;

    function startImportAnalysis() {
        const zoneId = document.getElementById('import-zone').value;
        const packageId = document.getElementById('import-package').value;
        const fileInput = document.getElementById('import-file');
        
        // NOUVEAU : Récupérer la checkbox (Audit Point D)
        const ignoreDuplicatesCheckbox = document.getElementById('ignore-duplicates'); 
        const ignoreDuplicates = ignoreDuplicatesCheckbox ? ignoreDuplicatesCheckbox.checked : false;

        if (!zoneId || !packageId || fileInput.files.length === 0) {
            showErrorModal("Champs manquants", "Veuillez sélectionner une zone, un forfait et choisir un fichier CSV.");
            return;
        }

        const file = fileInput.files[0];
        const modal = document.getElementById('import-process-modal');
        const stepLoading = document.getElementById('import-step-loading');
        const stepConfirm = document.getElementById('import-step-confirm');
        const invalidModal = document.getElementById('invalid-format-modal'); // Assurez-vous d'avoir cette modale dans le HTML
        const bar = document.getElementById('import-progress-bar');
        const txt = document.getElementById('import-percent');

        // Reset UI
        modal.classList.remove('hidden');
        stepLoading.classList.remove('hidden');
        stepConfirm.classList.add('hidden');
        if(invalidModal) invalidModal.classList.add('hidden');

        // Barre de chargement réelle (Indéterminée)
        bar.style.width = '100%';
        bar.classList.add('animate-pulse');
        txt.innerText = 'Traitement...';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('import-zone', zoneId);
        formData.append('import-package', packageId);
        formData.append('ignore_duplicates', ignoreDuplicates); // Envoi de la checkbox
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("tickets.import") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) {
                // Gérer les erreurs HTTP (422, 403, 500, etc.)
                return response.json().then(data => {
                    throw new Error(data.message || `Erreur ${response.status}: ${response.statusText}`);
                }).catch(() => {
                    // Si la réponse n'est pas du JSON
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            bar.classList.remove('animate-pulse');
            
            // GESTION ERREUR FORMAT (Audit Point C)
            if (data.error_type === 'invalid_format') {
                modal.classList.add('hidden'); // Fermer modale chargement
                // Ouvrir modale format invalide
                const formatModal = document.getElementById('invalid-format-modal');
                if(formatModal) formatModal.classList.remove('hidden');
                else showErrorModal("Format invalide", data.message); // Fallback avec modale personnalisée
                return;
            }

            if (data.success === false) {
                closeImportModal();
                showErrorModal("Erreur d'import", data.message);
                return;
            }

            // SUCCÈS : Affichage des résultats
            bar.style.width = '100%';
            txt.innerText = '100%';
            setTimeout(() => {
                stepLoading.classList.add('hidden');
                stepConfirm.classList.remove('hidden');
                
                // MISE À JOUR DES CHIFFRES (DEBUG LOG)
                console.log("Résultat Import:", data);

                // Données brutes
                const imported = data.imported || 0;
                const duplicates = data.duplicates || 0;
                const profileErrors = data.profile_errors || 0;
                const total = imported + duplicates + profileErrors;

                // Mise à jour des chiffres
                document.getElementById('res-total').innerText = total;
                document.getElementById('res-doublons').innerText = duplicates;
                document.getElementById('res-net').innerText = '+' + imported;

                // --- INTELLIGENCE DU DIAGNOSTIC ---
                const iconContainer = document.getElementById('result-icon-container');
                const icon = document.getElementById('result-icon');
                const title = document.getElementById('result-title');
                const diagBox = document.getElementById('diagnostic-box');
                const diagText = document.getElementById('diagnostic-text');
                const diagFix = document.getElementById('diagnostic-fix');

                // Reset classes
                diagBox.className = "hidden p-4 rounded-xl mb-6 text-sm border-l-4";

                // CAS 1 : SUCCÈS TOTAL
                if (profileErrors === 0 && imported > 0) {
                    iconContainer.className = "w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-green";
                    icon.className = "fas fa-check text-3xl";
                    title.innerText = "Succès !";
                    diagBox.classList.add('hidden');
                }

                // CAS 2 : ERREUR DE PROFIL (Le plus important)
                else if (profileErrors > 0) {
                    iconContainer.className = "w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500";
                    icon.className = "fas fa-times text-3xl";
                    title.innerText = "Importation Bloquée";
                    
                    diagBox.classList.remove('hidden');
                    diagBox.classList.add('bg-red-50', 'text-red-800', 'border-red-500', 'dark:bg-red-900/20', 'dark:text-red-200');
                    
                    diagText.innerHTML = `Le fichier CSV contient le profil <strong>"${data.found_profile}"</strong>, mais le forfait sélectionné attend le profil <strong>"${data.expected_profile}"</strong>.`;
                    diagFix.innerText = "Modifiez le 'Nom du Profile' dans la configuration du forfait pour qu'il corresponde exactement au fichier CSV.";
                }

                // CAS 3 : QUE DES DOUBLONS
                else if (imported === 0 && duplicates > 0) {
                    iconContainer.className = "w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-orange-500";
                    icon.className = "fas fa-exclamation-triangle text-3xl";
                    title.innerText = "Déjà importés";
                    
                    diagBox.classList.remove('hidden');
                    diagBox.classList.add('bg-orange-50', 'text-orange-800', 'border-orange-500', 'dark:bg-orange-900/20', 'dark:text-orange-200');
                    
                    diagText.innerText = `Tous les ${duplicates} tickets de ce fichier existent déjà dans la base de données.`;
                    diagFix.innerText = "Vérifiez que vous n'avez pas déjà importé ce fichier.";
                }

                // CAS 4 : FICHIER VIDE OU AUTRE
                else if (total === 0) {
                    iconContainer.className = "w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-500";
                    icon.className = "fas fa-question text-3xl";
                    title.innerText = "Rien à importer";
                    diagBox.classList.add('hidden');
                }

            }, 500);
        })
        .catch(error => {
            bar.classList.remove('animate-pulse');
            console.error('Import error:', error); // Afficher l'erreur complète pour le diagnostic
            
            // Fermer la modale d'importation
            closeImportModal();
            
            // Extraire le code d'erreur du message (ex: "Erreur 409: Conflict")
            const errorMessage = error.message || '';
            const is409Error = errorMessage.includes('409') || errorMessage.toLowerCase().includes('conflict');
            
            // GESTION SPÉCIFIQUE DOUBLON (409)
            if (is409Error) {
                showErrorModal("Fichier déjà importé", "Ce fichier CSV a déjà été importé avec succès. Si vous souhaitez réimporter, cochez la case 'Ignorer les doublons'.");
            } else {
                showErrorModal("Erreur lors de l'import", errorMessage || "Une erreur système est survenue lors de l'importation du fichier CSV. Veuillez réessayer.");
            }
        });
    }

    function finalizeImport() {
        // Fermer la modale
        closeImportModal();
        
        // Afficher un message de succès
        if(typeof showToast === 'function') {
            const imported = document.getElementById('res-net').innerText;
            showToast(`Importation terminée ! ${imported} tickets ajoutés au stock.`, 'success');
        }
        
        // Optionnel : Recharger la page après un court délai pour voir les données mises à jour
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    function showErrorModal(title, message) {
        document.getElementById('error-title').innerText = title;
        document.getElementById('error-message').innerText = message;
        document.getElementById('error-modal').classList.remove('hidden');
    }

    function closeImportModal() {
        clearInterval(importInterval);
        document.getElementById('import-process-modal').classList.add('hidden');
    }

    function closeInvalidFormatModal() {
        document.getElementById('invalid-format-modal').classList.add('hidden');
    }

    function showInvalidFormatModal() {
        document.getElementById('invalid-format-modal').classList.remove('hidden');
    }

    /* --- KPIs DYNAMIQUES (MISE À JOUR PAR ZONE) --- */
    function updateStockKPIs(zoneId = 'all') {
        const container = document.getElementById('stock-kpi-container');
        
        // Utiliser les vraies données du contrôleur
        const zoneStats = @json($zoneStats);
        const data = zoneStats[zoneId] || zoneStats['all'] || [];
        
        // Trier par urgence (critique -> bas -> moyen -> bon)
        const statusOrder = { 'critical': 0, 'low': 1, 'medium': 2, 'good': 3 };
        data.sort((a, b) => statusOrder[a.status] - statusOrder[b.status]);
        
        // Générer le HTML des KPIs
        let kpisHtml = '';
        data.forEach(item => {
            const statusConfig = {
                'critical': { 
                    border: 'border-red-50 dark:border-red-900/30', 
                    icon: 'fa-exclamation-triangle', 
                    iconBg: 'bg-red-50 dark:bg-red-900/20', 
                    iconColor: 'text-red-500',
                    badge: 'bg-red-100 text-red-600',
                    badgeText: 'CRITIQUE',
                    barColor: 'bg-red-500',
                    textColor: 'text-red-500',
                    hoverBorder: 'hover:border-red-200'
                },
                'low': { 
                    border: 'border-orange-50 dark:border-orange-900/30', 
                    icon: 'fa-battery-quarter', 
                    iconBg: 'bg-orange-50 dark:bg-orange-900/20', 
                    iconColor: 'text-orange-500',
                    badge: 'bg-orange-100 text-orange-600',
                    badgeText: 'BAS',
                    barColor: 'bg-orange-500',
                    textColor: 'text-orange-500',
                    hoverBorder: 'hover:border-orange-200'
                },
                'medium': { 
                    border: 'border-gray-100 dark:border-slate-700', 
                    icon: 'fa-battery-half', 
                    iconBg: 'bg-blue-50 dark:bg-blue-900/20', 
                    iconColor: 'text-brand-blue',
                    badge: 'bg-blue-50 text-brand-blue',
                    badgeText: 'MOYEN',
                    barColor: 'bg-brand-blue',
                    textColor: 'text-brand-blue',
                    hoverBorder: 'hover:border-blue-200'
                },
                'good': { 
                    border: 'border-gray-100 dark:border-slate-700', 
                    icon: 'fa-battery-full', 
                    iconBg: 'bg-green-50 dark:bg-green-900/20', 
                    iconColor: 'text-brand-green',
                    badge: 'bg-green-100 text-brand-green',
                    badgeText: 'OK',
                    barColor: 'bg-brand-green',
                    textColor: 'text-brand-green',
                    hoverBorder: 'hover:border-green-200'
                }
            };

            const config = statusConfig[item.status];
            const animateClass = item.status === 'critical' ? 'animate-pulse' : '';
            
            kpisHtml += `
                <div class="bg-white dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border ${config.border} flex flex-col justify-between h-full group ${config.hoverBorder} transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl ${config.iconBg} flex items-center justify-center ${config.iconColor} font-bold text-lg">
                                <i class="fas ${config.icon}"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white leading-tight">${item.name}</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">${item.zone}</p>
                            </div>
                        </div>
                        <span class="${config.badge} px-2 py-1 rounded-lg text-[10px] font-bold ${animateClass}">${config.badgeText}</span>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1 mt-2">
                            <span class="text-gray-400">Restant</span>
                            <span class="font-bold ${config.textColor}">${item.stock} tickets</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                            <div class="${config.barColor} h-full rounded-full" style="width: ${item.percentage}%"></div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = kpisHtml;
    }

    // Mettre à jour les KPIs quand la zone change
    function selectZoneFilter(id, name) {
        currentSelectedZoneId = id;
        currentSelectedZoneName = name;

        // Mise à jour visuelle du bouton
        document.getElementById('selected-zone-name').textContent = name;
        
        // Si on est sur l'onglet Liste des Tickets, appliquer le filtre
        const listTab = document.getElementById('tab-list');
        if (listTab && listTab.classList.contains('active')) {
            // Rediriger vers la page avec le filtre de zone
            const currentUrl = new URL(window.location.href);
            if (id === 'all') {
                currentUrl.searchParams.delete('filter_zone');
            } else {
                currentUrl.searchParams.set('filter_zone', id);
            }
            currentUrl.searchParams.set('tab', 'list');
            window.location.href = currentUrl.toString();
            return;
        }
        
        // Sinon, sur l'onglet Catalogue
        // Appel du filtrage des cartes
        filterByZone(id);

        // Mettre à jour les KPIs de stock
        updateStockKPIs(id);

        // Fermer le menu
        document.getElementById('zone-dropdown-menu').classList.add('hidden');

        // Notification visuelle
        if(typeof showToast === 'function') {
            showToast('Affichage du catalogue : ' + name, 'success');
        } else {
            console.log('Affichage du catalogue : ' + name);
        }
    }

    /* --- FONCTIONS POUR LE TABLEAU D'HISTORIQUE --- */
    
    /* --- FONCTION TOAST AMÉLIORÉE --- */
    function showToast(message, type = 'success') {
        // 1. Créer le conteneur s'il n'existe pas
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(container);
        }

        // 2. Définir les couleurs selon le type
        let colors, icon;
        switch(type) {
            case 'success':
                colors = 'bg-[#083e5f] text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                break;
            case 'error':
                colors = 'bg-red-500 text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                break;
            case 'warning':
                colors = 'bg-orange-500 text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                break;
            default:
                colors = 'bg-[#083e5f] text-white';
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }

        // 3. Créer l'élément Toast
        const toast = document.createElement('div');
        toast.className = `${colors} px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px] border border-white/10`;
        toast.innerHTML = `
            <div class="bg-white/20 rounded-full p-1">${icon}</div>
            <p class="text-sm font-bold whitespace-pre-line">${message}</p>
        `;

        // 4. Ajouter au DOM et Animer
        container.appendChild(toast);
        
        // Animation Entrée
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        });

        // 5. Suppression auto après 5s (plus long pour les warnings)
        const duration = type === 'warning' ? 5000 : 3000;
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, duration);
    }
    
    function sortTable(columnIndex, tableId) {
        const table = document.getElementById(tableId);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle sort direction
        const currentSort = table.dataset.sortColumn === columnIndex.toString() ? table.dataset.sortDirection : 'asc';
        const newDirection = currentSort === 'asc' ? 'desc' : 'asc';
        
        table.dataset.sortColumn = columnIndex;
        table.dataset.sortDirection = newDirection;
        
        // Sort rows
        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent.trim();
            let bValue = b.cells[columnIndex].textContent.trim();
            
            // Handle numeric values
            if (columnIndex === 4) { // Quantité column
                aValue = parseInt(aValue.replace('+', '')) || 0;
                bValue = parseInt(bValue.replace('+', '')) || 0;
            }
            
            if (aValue < bValue) return newDirection === 'asc' ? -1 : 1;
            if (aValue > bValue) return newDirection === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
        
        // Update sort icons
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            const icon = header.querySelector('i');
            if (icon) {
                if (index === columnIndex) {
                    icon.className = `fas fa-sort-${newDirection === 'asc' ? 'up' : 'down'} text-xs ml-1`;
                } else {
                    icon.className = 'fas fa-sort text-xs ml-1';
                }
            }
        });
    }

    // Initialisation des filtres
    document.addEventListener('DOMContentLoaded', function() {
        // Code pour gérer la couleur "placeholder" des selects (Gris si vide, Noir si rempli)
        document.querySelectorAll('select').forEach(select => {
            // Fonction pour mettre à jour la couleur
            const updateColor = () => {
                if (select.value === "" || select.value === "all") {
                    select.classList.add('text-gray-500', 'dark:text-gray-400');
                    select.classList.remove('text-gray-800', 'dark:text-white');
                } else {
                    select.classList.remove('text-gray-500', 'dark:text-gray-400');
                    select.classList.add('text-gray-800', 'dark:text-white');
                }
            };

            // Appliquer au chargement
            updateColor();

            // Appliquer au changement
            select.addEventListener('change', updateColor);
        });
        
        // Filtrage du tableau d'historique - Utiliser les IDs corrects
        const searchInput = document.querySelector('input[placeholder*="Rechercher un fichier"]');
        const forfaitFilter = document.getElementById('filter-forfait');
        const statutFilter = document.getElementById('filter-statut');
        const zoneFilter = document.getElementById('filter-zone');
        const dateFilter = document.querySelector('input[type="date"]');
        
        function filterTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const forfaitValue = forfaitFilter ? forfaitFilter.value : '';
            const statutValue = statutFilter ? statutFilter.value : '';
            const dateValue = dateFilter ? dateFilter.value : '';
            
            const rows = document.querySelectorAll('#imports-tbody tr');
            
            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty row
                
                const fileName = row.cells[1].textContent.toLowerCase();
                const zone = row.cells[2].textContent.toLowerCase();
                const forfait = row.cells[3].textContent.toLowerCase();
                const statut = row.cells[5].textContent.toLowerCase();
                const date = row.cells[0].textContent;
                
                let matches = true;
                
                // Search filter
                if (searchTerm && !fileName.includes(searchTerm) && !zone.includes(searchTerm) && !forfait.includes(searchTerm)) {
                    matches = false;
                }
                
                // Status filter
                if (statutValue && !statut.includes(statutValue.toLowerCase())) {
                    matches = false;
                }
                
                // Date filter
                if (dateValue) {
                    const rowDate = new Date(date);
                    const filterDate = new Date(dateValue);
                    if (rowDate.toDateString() !== filterDate.toDateString()) {
                        matches = false;
                    }
                }
                
                row.style.display = matches ? '' : 'none';
            });
        }
        
        // Add event listeners
        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (forfaitFilter) forfaitFilter.addEventListener('change', filterTable);
        if (statutFilter) statutFilter.addEventListener('change', filterTable);
        if (dateFilter) dateFilter.addEventListener('change', filterTable);
    });
