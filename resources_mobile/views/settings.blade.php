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
            @if($proprio->is_active)
                <button onclick="openDeactivateModal()" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-3 rounded-xl shadow-lg transition font-bold text-sm">
                    <i class="fas fa-user-slash"></i> Désactiver mon compte
                </button>
            @else
                <button onclick="openDeleteModal()" class="flex items-center gap-2 bg-red-900/80 hover:bg-red-900 text-white px-5 py-3 rounded-xl shadow-lg transition font-bold text-sm">
                    <i class="fas fa-trash-alt"></i> Supprimer mon compte
                </button>
                <button onclick="openActivateModal()" class="flex items-center gap-2 bg-brand-green hover:brightness-110 text-white px-5 py-3 rounded-xl shadow-lg transition font-bold text-sm">
                    <i class="fas fa-user-check"></i> Activer mon compte
                </button>
            @endif
        </div>
    </header>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
        
        <!-- 1. MON PROFIL & SÉCURITÉ -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Informations Personnelles -->
            <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 relative z-20">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-user w-5 h-5 text-brand-blue"></i>Informations Personnelles
                </h3>
                
                <form id="profile-form" class="space-y-6">
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
                                    <i class="fas fa-mobile-alt absolute right-4 top-3.5 text-gray-400"></i>
                                </div>
                                <input type="hidden" name="phone_code" id="settings-phone-code" value="{{ $phoneCode }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-50 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" id="cancel-profile-btn" onclick="cancelProfileEdit()" class="hidden px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                            Annuler
                        </button>
                        <button type="button" id="edit-profile-btn" onclick="toggleProfileEdit()" class="bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg shadow-blue-500/20">
                            Modifier
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Sécurité -->
            <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 relative z-10">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-shield-alt w-5 h-5 text-red-400"></i>Sécurité
                </h3>
                
                <form id="password-form" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    @csrf
                    <div class="input-floating-group md:col-span-1 relative">
                        <input type="password" name="current_password" id="current_password" placeholder=" " class="input-floating pr-10" required>
                        <label class="floating-label">Mot de passe actuel</label>
                        <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <i class="far fa-eye" id="icon-current_password"></i>
                        </button>
                    </div>
                    <div class="input-floating-group md:col-span-1 relative">
                        <input type="password" name="new_password" id="new_password" placeholder=" " class="input-floating pr-10" required>
                        <label class="floating-label">Nouveau mot de passe</label>
                        <button type="button" onclick="togglePasswordVisibility('new_password')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <i class="far fa-eye" id="icon-new_password"></i>
                        </button>
                    </div>
                    <div class="input-floating-group md:col-span-1">
                        <input type="password" name="new_password_confirmation" placeholder=" " class="input-floating" required>
                        <label class="floating-label">Confirmer</label>
                    </div>
                    <button type="submit" class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition h-[50px]">
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>
        
        <!-- 2. NOTIFICATIONS WHATSAPP -->
        <div class="space-y-8">
            <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 h-full flex flex-col">
                
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-bell w-6 h-6 text-brand-green"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Alertes Stock & Ventes</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed">
                        Soyez notifié sur WhatsApp dès qu'un stock devient critique ou qu'une anomalie est détectée.
                    </p>
                </div>

                <form id="whatsapp-form" class="space-y-8 flex-1">
                    @csrf
                    <!-- Toggle Switch -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Activer les notifications</span>
                        <div class="relative inline-block w-10 h-6 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" name="wa_notifications_enabled" id="wa-toggle" value="1" {{ $proprio->wa_notifications_enabled ? 'checked' : '' }} class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-brand-green right-0 transition-all duration-300"/>
                            <label for="wa-toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-brand-green cursor-pointer transition-colors duration-300"></label>
                        </div>
                    </div>

                    <!-- Input WhatsApp -->
                    <div>
                        <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">Numéro WhatsApp</label>
                        <div class="flex relative group">
                            <button type="button" onclick="toggleCountryMenu('wa')" class="flex items-center gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-600 border-r-0 rounded-l-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition relative z-20">
                                <img id="wa-flag" src="https://flagcdn.com/w40/{{ $waPhoneFlag }}.png" class="w-5 h-auto rounded-sm shadow-sm" alt="Flag">
                                <span id="wa-code" class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $waPhoneCode }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
                            </button>

                            <div id="wa-country-menu" class="hidden absolute top-full left-0 mt-2 w-72 bg-white dark:bg-brand-cardDark border border-gray-100 dark:border-slate-600 rounded-xl shadow-2xl z-50 overflow-hidden animate-fade-in ring-1 ring-black/5">
                                <ul class="max-h-56 overflow-y-auto custom-scrollbar">
                                    <li onclick="selectCountry('bj', '+229', 'wa')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                        <img src="https://flagcdn.com/w40/bj.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Bénin (+229)</span>
                                    </li>
                                    <li onclick="selectCountry('tg', '+228', 'wa')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                        <img src="https://flagcdn.com/w40/tg.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Togo (+228)</span>
                                    </li>
                                    <li onclick="selectCountry('ci', '+225', 'wa')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer border-b border-gray-50 dark:border-slate-700/50 transition-colors group">
                                        <img src="https://flagcdn.com/w40/ci.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Côte d'Ivoire (+225)</span>
                                    </li>
                                    <li onclick="selectCountry('sn', '+221', 'wa')" class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer transition-colors group">
                                        <img src="https://flagcdn.com/w40/sn.png" class="w-8 h-auto rounded shadow-sm group-hover:scale-110 transition-transform">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Sénégal (+221)</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="relative flex-1">
                                <input type="tel" name="wa_numero" id="wa-phone" class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-800 dark:text-white border border-gray-200 dark:border-slate-600 rounded-r-xl text-sm font-bold focus:ring-2 focus:ring-brand-blue/20 outline-none transition" value="{{ $waPhoneLocal }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                <i class="fab fa-whatsapp absolute right-4 top-3.5 text-gray-400"></i>
                            </div>
                            <input type="hidden" name="wa_phone_code" id="wa-phone-code" value="{{ $waPhoneCode }}">
                        </div>
                    </div>

                    <!-- Slider Seuil Critique -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Seuil d'alerte Stock</label>
                            <span class="bg-brand-blue/10 text-brand-blue px-3 py-1 rounded-lg text-xs font-bold">
                                <span id="slider-value">{{ $proprio->wa_alert_threshold }}</span> tickets
                            </span>
                        </div>
                        
                        <input type="range" name="wa_alert_threshold" min="5" max="50" value="{{ $proprio->wa_alert_threshold }}" 
                               class="w-full h-2 bg-gray-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-brand-blue"
                               oninput="document.getElementById('slider-value').innerText = this.value">
                        
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-3 flex items-center gap-2">
                            <i class="fas fa-info-circle w-3 h-3"></i>
                            Une alerte sera envoyée si un forfait a moins de <span id="info-slider-value">{{ $proprio->wa_alert_threshold }}</span> tickets.
                        </p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-50 dark:border-slate-700 text-right">
                        <button type="submit" class="bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg shadow-blue-500/20">
                            Sauvegarder WhatsApp
                        </button>
                    </div>
                </form>
            </div>
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
            const response = await fetch("{{ route('settings.profile.update') }}", {
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

    // 2. Mise à jour du Mot de passe
    document.getElementById('password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Mise à jour...';

        const formData = new FormData(this);
        
        try {
            const response = await fetch("{{ route('settings.password.update') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                this.reset();
            } else {
                showToast(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            showToast('Erreur lors du changement de mot de passe', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    // 3. Mise à jour WhatsApp
    document.getElementById('whatsapp-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;

        const formData = new FormData(this);
        // Gérer le cas du checkbox non coché (non présent dans FormData)
        if (!formData.has('wa_notifications_enabled')) {
            formData.append('wa_notifications_enabled', '0');
        }

        try {
            const response = await fetch("{{ route('settings.whatsapp.update') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Erreur', 'error');
            }
        } catch (error) {
            showToast('Erreur WhatsApp', 'error');
        } finally {
            btn.disabled = false;
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
            const response = await fetch("{{ route('settings.deactivate') }}", {
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
            const response = await fetch("{{ route('settings.activate') }}", {
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
            const response = await fetch("{{ route('settings.delete') }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.href = "{{ route('login') }}", 1500);
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