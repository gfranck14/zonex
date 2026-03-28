@extends('layout')
@section('title', 'Paramètres')

@php
    /**
     * Parse un numéro pour extraire le code pays et le numéro local
     */
    function parsePhone($fullNumber) {
        $fullNumber = trim($fullNumber ?? '');
        $code = '+229'; // Par défaut
        $number = $fullNumber;

        if ($fullNumber !== '') {
            if (str_contains($fullNumber, ' ')) {
                $parts = explode(' ', $fullNumber, 2);
                $code = $parts[0];
                $number = $parts[1];
            } elseif (str_starts_with($fullNumber, '+')) {
                $code = substr($fullNumber, 0, 4);
                $number = substr($fullNumber, 4);
            } elseif (strlen($fullNumber) > 8) {
                $code = '+' . substr($fullNumber, 0, 3);
                $number = substr($fullNumber, 3);
            }
        }
        return [$code, $number];
    }

    [$phoneCode, $phoneLocal] = parsePhone($proprio->numero);
    [$waPhoneCode, $waPhoneLocal] = parsePhone($proprio->wa_numero);

    $countryMap = [
        '+229' => 'bj',
        '+228' => 'tg',
        '+225' => 'ci',
        '+221' => 'sn',
    ];
    $phoneFlag = $countryMap[$phoneCode] ?? 'bj';
    $waPhoneFlag = $countryMap[$waPhoneCode] ?? 'bj';
@endphp

@section('content')
<div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-4 md:p-6 pb-24 md:pb-10 transition-colors duration-300">
    <header class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Paramètres</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gérez votre profil et vos préférences.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('proprio.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300 px-5 py-3 rounded-xl shadow-sm transition font-bold text-sm hidden md:flex">
                    <i class="fas fa-sign-out-alt"></i> Se déconnecter
                </button>
            </form>
        </div>
    </header>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-fade-in">
        
        <!-- 1. MON PROFIL -->
        <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 relative z-20 flex flex-col h-full">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-user text-lg text-brand-blue"></i>Informations Personnelles
            </h3>
            <form id="profile-form" class="space-y-6 flex-1 flex flex-col">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="input-floating-group">
                            <input type="text" name="nom" value="{{ $proprio->nom }}" placeholder=" " class="input-floating" required disabled>
                            <label class="floating-label">Nom</label>
                        </div>
                        <div class="input-floating-group">
                            <input type="text" name="prenom" value="{{ $proprio->prenom }}" placeholder=" " class="input-floating" required disabled>
                            <label class="floating-label">Prénom</label>
                        </div>
                        <div class="input-floating-group">
                            <input type="email" name="email" value="{{ $proprio->email }}" placeholder=" " class="input-floating" disabled>
                            <label class="floating-label">Email de contact</label>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Téléphone</label>
                            <div class="flex relative group">
                                <!-- ZONE GAUCHE : SÉLECTEUR PAYS -->
                                <button type="button" id="settings-country-btn" onclick="toggleCountryMenu('settings')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20 opacity-50 cursor-not-allowed" disabled>
                                    <img id="settings-flag" src="https://flagcdn.com/w40/{{ $phoneFlag }}.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                    <span id="settings-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $phoneCode }}</span>
                                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                                </button>

                                <!-- MENU DÉROULANT -->
                                <div id="settings-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-brand-cardDark border border-gray-200 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                                    <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                        <li onclick="selectCountry('bj', '+229', 'settings')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                        </li>
                                        <li onclick="selectCountry('tg', '+228', 'settings')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                        </li>
                                        <li onclick="selectCountry('ci', '+225', 'settings')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                            <img src="https://flagcdn.com/w40/ci.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Côte d'Ivoire (+225)</span>
                                        </li>
                                        <li onclick="selectCountry('sn', '+221', 'settings')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition-colors group">
                                            <img src="https://flagcdn.com/w40/sn.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Sénégal (+221)</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- INPUT -->
                                <div class="relative flex-1">
                                    <input type="tel" name="numero" id="settings-phone" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition" value="{{ $phoneLocal }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required disabled>
                                    <i class="fas fa-mobile-alt absolute right-4 top-0 bottom-0 flex items-center text-gray-400"></i>
                                </div>
                                <input type="hidden" name="phone_code" id="settings-phone-code" value="{{ $phoneCode }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-gray-50 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" id="cancel-profile-btn" onclick="cancelProfileEdit()" class="hidden px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                            Annuler
                        </button>
                        <button type="button" id="edit-profile-btn" onclick="toggleProfileEdit()" class="bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg shadow-blue-500/20">
                            Modifier
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- 2. SÉCURITÉ (Modification de Mot de Passe) -->
            <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 relative z-20 flex flex-col h-full">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-lock text-lg text-brand-blue"></i>Sécurité
                </h3>
                
                <form id="password-form" class="space-y-6 flex-1 flex flex-col">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="input-floating-group md:col-span-2">
                            <input type="password" name="current_password" id="current_password" placeholder=" " class="input-floating pr-10" required>
                            <label class="floating-label">Mot de passe actuel</label>
                            <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                                <i id="icon-current_password" class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="input-floating-group">
                            <input type="password" name="new_password" id="new_password" placeholder=" " class="input-floating pr-10" required minlength="8">
                            <label class="floating-label">Nouveau mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('new_password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                                <i id="icon-new_password" class="far fa-eye"></i>
                            </button>
                        </div>
                        <div class="input-floating-group">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder=" " class="input-floating pr-10" required minlength="8">
                            <label class="floating-label">Confirmer nouveau mot de passe</label>
                            <button type="button" onclick="togglePasswordVisibility('new_password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                                <i id="icon-new_password_confirmation" class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-6 border-t border-gray-50 dark:border-slate-700 flex justify-end">
                        <button type="submit" id="update-password-btn" class="bg-gray-800 dark:bg-slate-700 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-gray-700 dark:hover:bg-slate-600 transition shadow-lg">
                            Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>


        <!-- DANGER ZONE -->
        <div class="lg:col-span-2 mt-4 bg-red-50/50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-3xl p-8 mb-4">
            <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Zone de Danger
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Ces actions sont irrémédiables ou affectent directement votre accès au système.</p>
            
            <div class="bg-white dark:bg-brand-cardDark rounded-2xl border border-red-100 dark:border-red-900/30 shadow-sm overflow-hidden divide-y divide-red-50 dark:divide-red-900/20">
                @if($proprio->is_active)
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-gray-200">Désactiver le compte</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Suspendre temporairement l'accès à ce compte. Vos données seront conservées.</p>
                        </div>
                        <button onclick="openDeactivateModal()" class="px-5 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 rounded-xl font-bold text-sm transition text-center whitespace-nowrap md:w-auto">
                            Désactiver le compte
                        </button>
                    </div>
                @else
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-gray-200">Activer le compte</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Restaurer l'accès à ce compte pour recommencer à gérer vos zones.</p>
                        </div>
                        <button onclick="openActivateModal()" class="px-5 py-2.5 bg-brand-green/10 hover:bg-brand-green/20 text-brand-green rounded-xl font-bold text-sm transition text-center whitespace-nowrap md:w-auto">
                            Activer le compte
                        </button>
                    </div>
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-red-600 dark:text-red-400">Supprimer définitivement le compte</h4>
                            <p class="text-xs text-red-500 dark:text-red-500 mt-1">Vos données seront effacées. Cette action est irréversible.</p>
                        </div>
                        <button onclick="openDeleteModal()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-500/20 transition text-center whitespace-nowrap md:w-auto">
                            Supprimer le compte
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- CARD DÉCONNEXION (MOBILE ONLY) -->
        <div class="md:hidden mb-safe px-4">
            <form action="{{ route('proprio.logout') }}" method="POST" class="bg-red-500 hover:bg-red-600 rounded-3xl p-6 shadow-lg shadow-red-500/20 transition-all duration-300 transform hover:scale-[1.02]">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 text-white font-bold text-lg">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                    <span>Se déconnecter</span>
                </button>
            </form>
        </div>
    </div>
</div>
<!-- MODAL RÉACTIVATION -->
<div id="activate-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-brand-green">
                    <i class="fas fa-user-check text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Réactiver le compte ?</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 leading-relaxed">
                    Souhaitez-vous réactiver votre accès ? Vous pourrez à nouveau gérer vos zones et tickets immédiatement.
                </p>
                <div class="flex flex-col gap-3">
                    <button id="confirm-activate-btn" onclick="submitActivation()" class="w-full bg-brand-green hover:brightness-110 text-white py-4 rounded-2xl font-bold transition shadow-lg shadow-green-500/20">
                        Oui, activer
                    </button>
                    <button onclick="closeActivateModal()" class="w-full bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 py-4 rounded-2xl font-bold hover:bg-gray-100 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // --- GESTION ÉDITION PROFIL ---
    let initialProfileValues = {};
    let isEditingProfile = false;

    function getProfileFormData() {
        const form = document.getElementById('profile-form');
        return {
            nom: form.querySelector('input[name="nom"]').value,
            prenom: form.querySelector('input[name="prenom"]').value,
            email: form.querySelector('input[name="email"]').value,
            numero: form.querySelector('input[name="numero"]').value,
            phone_code: form.querySelector('input[name="phone_code"]').value,
            flag: document.getElementById('settings-flag').src
        };
    }

    function toggleProfileEdit() {
        const form = document.getElementById('profile-form');
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-profile-btn');
        const inputs = form.querySelectorAll('input:not([type="hidden"])');
        const countryBtn = document.getElementById('settings-country-btn');

        if (!isEditingProfile) {
            // Passer en mode ÉDITION
            isEditingProfile = true;
            initialProfileValues = getProfileFormData();
            
            inputs.forEach(input => input.disabled = false);
            countryBtn.disabled = false;
            countryBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            editBtn.innerText = 'Sauvegarder les modifications';
            editBtn.disabled = true; // Désactivé tant qu'il n'y a pas de changement
            cancelBtn.classList.remove('hidden');
            
            // Ajouter les listeners de changement
            inputs.forEach(input => {
                input.addEventListener('input', checkProfileChanges);
            });
        } else {
            // Soumettre le formulaire (le bouton est activé seulement si changement)
            saveProfileChanges();
        }
    }

    function checkProfileChanges() {
        if (!isEditingProfile) return;
        
        const currentValues = getProfileFormData();
        const hasChanged = 
            currentValues.nom !== initialProfileValues.nom ||
            currentValues.prenom !== initialProfileValues.prenom ||
            currentValues.email !== initialProfileValues.email ||
            currentValues.numero !== initialProfileValues.numero ||
            currentValues.phone_code !== initialProfileValues.phone_code;

        const editBtn = document.getElementById('edit-profile-btn');
        editBtn.disabled = !hasChanged;
    }

    function cancelProfileEdit() {
        const form = document.getElementById('profile-form');
        const editBtn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-profile-btn');
        const inputs = form.querySelectorAll('input:not([type="hidden"])');
        const countryBtn = document.getElementById('settings-country-btn');

        // Restaurer les valeurs
        form.querySelector('input[name="nom"]').value = initialProfileValues.nom;
        form.querySelector('input[name="prenom"]').value = initialProfileValues.prenom;
        form.querySelector('input[name="email"]').value = initialProfileValues.email;
        form.querySelector('input[name="numero"]').value = initialProfileValues.numero;
        form.querySelector('input[name="phone_code"]').value = initialProfileValues.phone_code;
        document.getElementById('settings-flag').src = initialProfileValues.flag;
        document.getElementById('settings-code').innerText = initialProfileValues.phone_code;

        // Désactiver les champs
        inputs.forEach(input => input.disabled = true);
        countryBtn.disabled = true;
        countryBtn.classList.add('opacity-50', 'cursor-not-allowed');

        // Reset boutons
        isEditingProfile = false;
        editBtn.innerText = 'Modifier';
        editBtn.disabled = false;
        cancelBtn.classList.add('hidden');
        
        // Retirer les listeners
        inputs.forEach(input => {
            input.removeEventListener('input', checkProfileChanges);
        });
    }

    // Wrap selectCountry to check for changes if prefix is 'settings'
    const originalSelectCountry = window.selectCountry;
    window.selectCountry = function(countryCode, phoneCode, prefix) {
        if (typeof originalSelectCountry === 'function') {
            originalSelectCountry(countryCode, phoneCode, prefix);
        }
        if (prefix === 'settings') {
            checkProfileChanges();
        }
    };

    async function saveProfileChanges() {
        const form = document.getElementById('profile-form');
        const btn = document.getElementById('edit-profile-btn');
        const cancelBtn = document.getElementById('cancel-profile-btn');
        
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Enregistrement...';

        const formData = new FormData(form);
        
        try {
            const response = await fetch("{{ route('proprio.settings.profile.update') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                // Sortir du mode édition après succès
                isEditingProfile = false;
                btn.innerText = 'Modifier';
                btn.disabled = false;
                cancelBtn.classList.add('hidden');
                form.querySelectorAll('input:not([type="hidden"])').forEach(input => input.disabled = true);
                document.getElementById('settings-country-btn').disabled = true;
                document.getElementById('settings-country-btn').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                btn.disabled = false;
                btn.innerText = originalText;
            }
        } catch (error) {
            showToast('Erreur réseau', 'error');
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    // Retirer l'ancien submit listener du profil car on utilise toggleProfileEdit
    // Le formulaire peut quand même être soumis par "Entrée", donc on le gère aussi
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (isEditingProfile && !document.getElementById('edit-profile-btn').disabled) {
            saveProfileChanges();
        }
    });



    // --- GESTION DU COMPTE (DÉSACTIVATION / SUPPRESSION) ---
    
    function openDeactivateModal() {
        document.getElementById('deactivate-modal').classList.remove('hidden');
        document.getElementById('deactivate-step-1').classList.remove('hidden');
        document.getElementById('deactivate-step-2').classList.add('hidden');
    }

    function closeDeactivateModal() {
        document.getElementById('deactivate-modal').classList.add('hidden');
    }

    function goToDeactivateStep2() {
        document.getElementById('deactivate-step-1').classList.add('hidden');
        document.getElementById('deactivate-step-2').classList.remove('hidden');
    }

    async function submitDeactivation() {
        const reason = document.getElementById('deactivation-reason').value;
        if (!reason.trim()) {
            showToast('Veuillez indiquer une raison', 'error');
            return;
        }

        const btn = document.getElementById('confirm-deactivate-btn');
        btn.disabled = true;
        btn.innerText = 'Désactivation...';

        try {
            const response = await fetch("{{ route('proprio.settings.deactivate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ reason: reason })
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Erreur', 'error');
                btn.disabled = false;
                btn.innerText = 'Désactiver mon compte';
            }
        } catch (error) {
            showToast('Erreur lors de la désactivation', 'error');
            btn.disabled = false;
            btn.innerText = 'Désactiver mon compte';
        }
    }

    function openActivateModal() {
        document.getElementById('activate-modal').classList.remove('hidden');
    }

    function closeActivateModal() {
        document.getElementById('activate-modal').classList.add('hidden');
    }

    async function submitActivation() {
        const btn = document.getElementById('confirm-activate-btn');
        btn.disabled = true;
        btn.innerText = 'Activation...';

        try {
            const response = await fetch("{{ route('proprio.settings.activate') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Erreur', 'error');
                btn.disabled = false;
                btn.innerText = 'Oui, activer';
            }
        } catch (error) {
            showToast('Erreur lors de la réactivation', 'error');
            btn.disabled = false;
            btn.innerText = 'Oui, activer';
        }
    }

    function openDeleteModal() {
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    async function submitDeletion() {
        const btn = document.getElementById('confirm-delete-btn');
        btn.disabled = true;
        btn.innerText = 'Suppression en cours...';

        try {
            const response = await fetch("{{ route('proprio.settings.delete') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = "{{ route('proprio.login') }}", 1500);
            } else {
                showToast(data.message || 'Erreur', 'error');
                btn.disabled = false;
                btn.innerText = 'Supprimer définitivement';
            }
        } catch (error) {
            showToast('Erreur lors de la suppression', 'error');
            btn.disabled = false;
            btn.innerText = 'Supprimer définitivement';
        }
    }
    function togglePasswordVisibility(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // --- GESTION MISE À JOUR MOT DE PASSE ---
    document.getElementById('password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('update-password-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Mise à jour...';

        const formData = new FormData(this);

        try {
            const response = await fetch("{{ route('proprio.settings.password.update') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                this.reset();
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour', 'error');
            }
        } catch (error) {
            showToast('Erreur réseau', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>

<!-- MODAL DÉSACTIVATION -->
<div id="deactivate-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-zoom-in">
            <!-- Step 1: Confirmation -->
            <div id="deactivate-step-1" class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Désactiver le compte ?</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 leading-relaxed">
                    Votre accès sera suspendu, mais vos données seront conservées. Vous pourrez réactiver votre compte à tout moment.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="goToDeactivateStep2()" class="w-full bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl font-bold transition shadow-lg shadow-red-500/20">
                        Oui, continuer
                    </button>
                    <button onclick="closeDeactivateModal()" class="w-full bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 py-4 rounded-2xl font-bold hover:bg-gray-100 transition">
                        Annuler
                    </button>
                </div>
            </div>

            <!-- Step 2: Reason -->
            <div id="deactivate-step-2" class="p-8 hidden">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 text-center">Pourquoi nous quittez-vous ?</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 text-center">
                    Aidez-nous à nous améliorer en indiquant la raison du départ.
                </p>
                <textarea id="deactivation-reason" class="w-full p-4 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-brand-blue/20 outline-none transition dark:text-white mb-6" rows="4" placeholder="Votre raison..."></textarea>
                
                <div class="flex gap-4">
                    <button id="confirm-deactivate-btn" onclick="submitDeactivation()" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-4 rounded-2xl font-bold transition shadow-lg shadow-red-500/20">
                        Désactiver mon compte
                    </button>
                    <button onclick="closeDeactivateModal()" class="flex-1 bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 py-4 rounded-2xl font-bold hover:bg-gray-100 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SUPPRESSION DÉFINITIVE -->
<div id="delete-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-brand-cardDark w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-8 text-center text-red-500 border-b border-gray-50 dark:border-slate-800">
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trash-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Supprimer définitivement ?</h3>
                <p class="text-red-500 font-bold text-xs uppercase tracking-widest mb-4">Action Irréversible</p>
            </div>
            <div class="p-8">
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 leading-relaxed text-center">
                    Toutes vos données (zones, tickets, paiements) seront supprimées pour toujours. Cette action ne peut pas être annulée.
                </p>
                <div class="flex flex-col gap-3">
                    <button id="confirm-delete-btn" onclick="submitDeletion()" class="w-full bg-red-700 hover:bg-red-800 text-white py-4 rounded-2xl font-bold transition shadow-lg shadow-red-900/20">
                        Supprimer définitivement
                    </button>
                    <button onclick="closeDeleteModal()" class="w-full bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 py-4 rounded-2xl font-bold hover:bg-gray-100 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection