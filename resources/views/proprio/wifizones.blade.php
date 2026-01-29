

@extends('layout')

@section('title', ' Dashboard')



@section('content')


    <!-- MAIN CONTENT -->
    <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            
           <!-- VUE 1 : LISTE DES ZONES -->
<div id="zone-list" class="animate-fade-in">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Mes Zones</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Gérez vos emplacements physiques.</p>
        </div>
        <button onclick="openWizard()" class="bg-brand-sidebarLight dark:bg-brand-sidebarDark text-white px-5 py-3 rounded-xl text-sm font-bold shadow-lg hover:brightness-110 transition flex items-center gap-2">
            <i class="fas fa-plus w-4 h-4"></i>
            Ajouter une Wifi Zone
        </button>
    </div>

    <!-- GRILLE DES ZONES -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @forelse($zones as $zone)
            @php
                $totalStock = $zone->forfaits->sum('tickets_count');
                $totalSales = $zone->forfaits->sum('sales_today_count');
                // Seuil de 500 pour 100% (arbitraire pour la barre de progression)
                $stockPercentage = $totalStock > 0 ? min(100, ($totalStock / 500) * 100) : 0;
                $isLowStock = $totalStock < 10;
            @endphp
            <!-- CARD DYNAMIQUE -->
            <div onclick="showDetail({{ json_encode($zone) }})" class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border {{ $isLowStock ? 'border-orange-100 dark:border-orange-900/30 hover:border-orange-200' : 'border-transparent hover:border-brand-blue/30' }} transition-all group relative cursor-pointer flex flex-col justify-between">
                
                <!-- En-tête : Titre + Badge -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            @if($isLowStock)
                                <i class="fas fa-exclamation-triangle text-orange-500"></i>
                            @else
                                <i class="fas fa-wifi text-brand-blue"></i>
                            @endif
                            {{ $zone->nom_zone }}
                        </h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-location-dot w-3 h-3 inline mr-1"></i>
                            {{ $zone->adresse ?? 'Adresse non spécifiée' }}
                        </p>
                    </div>
                    @if($isLowStock)
                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-md shadow-orange-500/20">
                            ALERTE STOCK
                        </span>
                    @else
                        <span class="bg-brand-green text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-md shadow-green-500/20">
                            STOCK OK
                        </span>
                    @endif
                </div>

                <!-- Corps : Ventes & Stock -->
                <div class="space-y-4 mb-6">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400">Ventes jour</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $totalSales }}</span>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-xs">
                            @if($isLowStock)
                                <span class="text-red-500 font-bold">⚠️ Stock Critique</span>
                                <span class="font-bold text-orange-500">{{ $totalStock }} tickets</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Niveau de Stock</span>
                                <span class="font-bold text-brand-green">{{ $totalStock }} tickets</span>
                            @endif
                        </div>
                    </div>

                    <!-- Lien Magique -->
                    <button onclick="event.stopPropagation(); copyToClipboard('{{ url('/portal/login?z=' . $zone->token) }}')" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-brand-blue hover:underline py-2">
                        <i class="fas fa-copy w-4 h-4"></i>
                        Copier lien d'intégration
                    </button>
                </div>

                <!-- Pied : Actions -->
                <div class="flex gap-2 mt-auto">
                    <a href="{{ route('forfait_ticket', ['filter_zone' => $zone->id]) }}#stock" onclick="event.stopPropagation();" class="flex-1 bg-brand-sidebarLight dark:bg-slate-700 text-white py-3 rounded-xl text-xs font-bold hover:brightness-110 transition shadow-lg text-center">
                        GÉRER LE STOCK
                    </a>
                    <button onclick="event.stopPropagation(); showDetail({{ json_encode($zone) }});" class="w-10 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-xl hover:text-brand-blue transition">
                        <i class="fas fa-cog w-5 h-5"></i>
                    </button>
                </div>
            </div>

        @empty
            <!-- ÉTAT VIDE : AUCUNE ZONE -->
            <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white/50 dark:bg-brand-cardDark/50 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-[3rem]">
                <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-wifi w-16 h-16 text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Aucune zone WiFi</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-center max-w-xs">Vous n'avez pas encore ajouté de zone WiFi à votre compte propriétaire.</p>
                <button onclick="openWizard()" class="bg-brand-blue text-white px-6 py-3 rounded-xl font-bold hover:scale-105 transition shadow-lg">
                    Créer ma première zone
                </button>
            </div>
        @endforelse

    </div>
</div>

<!-- JS Simple pour la copie -->
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Lien d\'intégration copié !');
        });
    }
</script>

            <!-- VUE 2 : DÉTAIL D'UNE ZONE -->
            <div id="zone-detail" class="hidden animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <button onclick="showList()" class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-brand-dark transition text-sm font-bold"><i class="fas fa-arrow-left w-4 h-4"></i>Retour aux zones</button>
                    <div class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 font-mono text-xs px-3 py-1.5 rounded-lg">ID: <span id="display-zone-id" class="text-gray-800 dark:text-white font-bold">WZ-XXXX</span></div>
                </div>
                <div class="flex items-start gap-5 mb-8">
                    <div id="detail-zone-icon-container" class="w-16 h-16 rounded-2xl bg-brand-blue flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i id="detail-zone-icon" class="fas fa-wifi w-7 h-7"></i>
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
                        <form id="zone-profile-form" class="space-y-6">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="detail-zone-id-input">
                            <div class="header-no-icon mb-6">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Informations de la Zone</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Nom de la zone</label>
                                    <input type="text" name="nom_zone" id="detail-zone-name" disabled class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-brand-blue/20 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Type de lieu (Demo)</label>
                                    <select disabled class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium outline-none disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                                        <option>Restaurant / Bar</option>
                                        <option>Hôtel</option>
                                        <option>Cyber Café</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Adresse physique</label>
                                <input type="text" name="adresse" id="detail-zone-address" disabled class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-brand-blue/20 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                            </div>
                            <div class="pt-4 flex justify-between items-center">
                                <div class="flex gap-3">
                                    <button type="button" id="edit-zone-btn" onclick="toggleZoneEdit()" class="bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">Modifier</button>
                                    <button type="button" id="cancel-zone-btn" onclick="cancelZoneEdit()" class="hidden border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-gray-400 px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-800 transition">Annuler</button>
                                </div>
                                <button type="button" onclick="handleDeleteZone()" class="text-red-500 hover:text-red-600 text-xs font-bold flex items-center gap-2">
                                    <i class="fas fa-trash-alt"></i> Supprimer cette zone
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="content-integration" class="tab-content hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <i class="fas fa-sync w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Lien d'authentification</h3>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">À configurer dans le Hotspot Mikrotik (Login URL).</p>
                                </div>
                            </div>
                            <div class="bg-slate-900 dark:bg-black rounded-2xl p-5 relative group border border-slate-700 shadow-inner">
                                <code id="auth-link-code" class="text-brand-blue font-mono text-xs break-all block pr-10 leading-relaxed">{{ url('/portal/login') }}?z=<span class="text-white font-bold" id="code-zone-id">WZ-8821-XJ</span>&mac=$(mac)&ip=$(ip)</code>
                                <button onclick="copyToClipboard(document.getElementById('auth-link-code').innerText)" class="absolute top-4 right-4 text-gray-400 hover:text-white" title="Copier">
                                    <i class="fas fa-copy w-5 h-5"></i>
                                </button>
                            </div>
                            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-100 dark:border-yellow-800 rounded-xl p-4 flex gap-3">
                                <i class="fas fa-exclamation-triangle w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0"></i>
                                <p class="text-xs text-yellow-800 dark:text-yellow-200">Assuez-vous que le <strong>Walled Garden</strong> est configuré.</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Walled Garden 🛡️</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Domaines à autoriser (Allow) IP List.</p>
                            <ul id="walled-garden-list" class="space-y-3 text-xs font-mono text-gray-600 dark:text-gray-300">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                                    {{ request()->getHost() }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                                    *.cinetpay.com
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                                    *.kkiapay.me
                                </li>
                            </ul>
                            <button onclick="copyToClipboard('{{ request()->getHost() }}\n*.cinetpay.com\n*.kkiapay.me')" class="mt-6 w-full py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700">Copier la liste</button>
                        </div>
                    </div>
                </div>
                <div id="content-custom" class="tab-content hidden">
                    <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm max-w-4xl flex flex-col md:flex-row gap-10">
                        <div class="flex-1 space-y-6">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Apparence Portail Captif</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Nom affiché</label>
                                <input type="text" id="custom-display-name" oninput="updatePreview()" value="Wifi Bar Central" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium outline-none focus:ring-2 focus:ring-brand-blue/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Message de bienvenue</label>
                                <textarea id="custom-welcome-msg" oninput="updatePreview()" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium outline-none h-24 resize-none">Bienvenue !</textarea>
                            </div>
                            <div class="pt-4">
                                <button onclick="saveCustomization()" class="bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">Sauvegarder l'apparence</button>
                            </div>
                        </div>
                        
                        <!-- APERÇU MOBILE -->
                        <div class="w-64 h-[500px] border-[8px] border-gray-800 dark:border-slate-700 rounded-[2.5rem] overflow-hidden bg-gray-50 dark:bg-slate-900 relative shadow-2xl flex-shrink-0 mx-auto md:mx-0">
                            <!-- Notch -->
                            <div class="absolute top-0 w-full h-6 flex justify-center z-20">
                                <div class="w-24 h-4 bg-gray-800 dark:bg-slate-700 rounded-b-2xl"></div>
                            </div>
                            
                            <!-- Phone Content -->
                            <div class="h-full overflow-y-auto no-scrollbar pt-10 pb-4 px-4 text-center">
                                <div class="w-16 h-16 bg-brand-blue rounded-2xl mx-auto mb-4 flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-wifi text-2xl"></i>
                                </div>
                                <h4 id="preview-display-name" class="font-bold text-gray-800 dark:text-white text-base truncate">Wifi Bar Central</h4>
                                <p id="preview-welcome-msg" class="text-[10px] text-gray-500 dark:text-gray-400 mt-2 line-clamp-3">Bienvenue !</p>
                                
                                <div class="mt-8 space-y-3">
                                    <div class="h-10 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700"></div>
                                    <div class="h-10 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700"></div>
                                    <div class="h-12 bg-brand-blue/10 rounded-xl border border-dashed border-brand-blue/30 mt-6 flex items-center justify-center">
                                        <div class="w-20 h-2 bg-brand-blue/20 rounded"></div>
                                    </div>
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
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Ajouter un wifizone 🚀</h3>
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
        <label class="floating-label">Nom du WIFIZONE</label>
        <p class="text-xs text-gray-400 mt-1">Ex: Routeur Maquis</p>
    </div>
    <div class="input-floating-group">
        <input type="text" id="new-zone-address" placeholder=" " class="input-floating">
        <label class="floating-label">Lieu / Adresse (Optionnel)</label>
        <p class="text-xs text-gray-400 mt-1">Ex: Cocody, Rue des Jardins</p>
    </div>
    <div class="pt-4 flex justify-between items-center">
        <button onclick="closeWizard()" class="text-xs font-bold text-gray-400 hover:text-gray-600">Annuler</button>
        <button id="btn-next-wizard" onclick="goToStep2()" class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
            <span id="btn-next-text">Suivant</span>
            <i id="btn-next-icon" class="fas fa-arrow-right w-4 h-4"></i>
        </button>
    </div>
</div>

<!-- ÉTAPE 2 : TECHNIQUE -->
<div id="step-2" class="hidden space-y-6 animate-fade-in">
    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl flex items-center gap-3 border border-green-100 dark:border-green-800">
        <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center text-brand-green">✓</div>
        <div>
            <p class="text-sm font-bold text-gray-800 dark:text-white">Zone créée avec succès !</p>
            <p class="text-xs text-gray-500">Token: <span id="display-token" class="font-mono font-bold text-green-600"></span></p>
        </div>
    </div>

    <!-- URL -->
    <div>
        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">1. URL de Redirection (Login)</label>
        <div onclick="copyUrl()" class="bg-brand-sidebarLight dark:bg-slate-900 p-4 rounded-xl flex justify-between items-center group cursor-pointer hover:ring-2 ring-brand-blue/50 transition">
            <code id="display-url" class="text-xs text-blue-200 font-mono truncate mr-4">Chargement...</code>
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
        <button onclick="closeWizard()" class="text-xs font-bold text-gray-400 hover:text-gray-600">Annuler</button>
        <button onclick="finishWizard()" class="bg-[#114c6c] text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-lg">
            Terminer & Voir la liste
        </button>
    </div>
</div>

<!-- MODALE DE SUPPRESSION SÉCURISÉE (2 ÉTAPES) -->
<div id="delete-zone-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[110] hidden">
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 dark:border-slate-700">
        <div id="delete-step-1" class="text-center space-y-6">
            <div class="w-20 h-20 bg-orange-100 dark:bg-orange-900/30 rounded-3xl flex items-center justify-center mx-auto text-orange-500 shadow-inner">
                <i class="fas fa-exclamation-triangle text-4xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Attention !</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Vous êtes sur le point de supprimer une zone WiFi. Cette action entraînera la suppression de :</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-2xl">
                    <span id="impact-forfaits" class="block text-2xl font-bold text-gray-800 dark:text-white">0</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Forfaits</span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-700/50 p-4 rounded-2xl">
                    <span id="impact-tickets" class="block text-2xl font-bold text-gray-800 dark:text-white">0</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Tickets</span>
                </div>
            </div>

            <div class="flex gap-4 pt-2">
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-4 border border-gray-200 dark:border-slate-600 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
                <button onclick="showDeleteStep2()" class="flex-1 px-6 py-4 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl text-sm font-bold transition shadow-lg shadow-orange-500/20">Continuer</button>
            </div>
        </div>

        <div id="delete-step-2" class="text-center space-y-6 hidden">
            <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-3xl flex items-center justify-center mx-auto text-red-500 shadow-inner">
                <i class="fas fa-trash-alt text-4xl"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Confirmation finale</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Êtes-vous absolument sûr ? Cette action est irréversible et toutes les données seront perdues.</p>
            </div>
            
            <div class="flex gap-4 pt-2">
                <button onclick="confirmFinalDelete()" class="flex-1 px-6 py-4 bg-red-500 hover:bg-red-600 text-white rounded-2xl text-sm font-bold transition shadow-lg shadow-red-500/20">Supprimer définitivement</button>
                <button onclick="closeDeleteModal()" class="flex-1 px-6 py-4 border border-gray-200 dark:border-slate-600 rounded-2xl text-sm font-bold text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- MODALE D'ERREUR PERSONNALISÉE -->
<div id="error-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] hidden">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-100 dark:border-slate-700">
        <div class="text-center">
            <div id="error-icon-container" class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500">
                <i id="error-icon" class="fas fa-exclamation-triangle text-3xl"></i>
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
    function showErrorModal(title, message, type = 'error') {
        const modal = document.getElementById('error-modal');
        if (!modal) return;
        
        document.getElementById('error-title').innerText = title;
        document.getElementById('error-message').innerText = message;
        
        const iconContainer = document.getElementById('error-icon-container');
        const icon = document.getElementById('error-icon');
        const btn = iconContainer.parentElement.querySelector('button');

        if (type === 'success') {
            iconContainer.className = "w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-brand-green";
            icon.className = "fas fa-check text-3xl";
            if(btn) btn.className = "w-full bg-brand-green hover:bg-green-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-lg";
        } else {
            iconContainer.className = "w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-red-500";
            icon.className = "fas fa-exclamation-triangle text-3xl";
            if(btn) btn.className = "w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl text-sm font-bold transition shadow-lg";
        }
        
        modal.classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    function openWizard() {
        document.getElementById('add-zone-modal').classList.remove('hidden');
        document.getElementById('step-1').classList.remove('hidden');
        document.getElementById('step-2').classList.add('hidden');
        document.getElementById('wizard-progress').style.width = '50%';
        document.getElementById('wizard-step-num').innerText = '1';
    }

    function closeWizard() {
        document.getElementById('add-zone-modal').classList.add('hidden');
    }

    async function goToStep2() {
        const nom = document.getElementById('new-zone-name').value;
        const adresse = document.getElementById('new-zone-address').value;
        const btn = document.getElementById('btn-next-wizard');
        const btnText = document.getElementById('btn-next-text');
        const btnIcon = document.getElementById('btn-next-icon');

        if (!nom) {
            showErrorModal("Oups !", "Veuillez donner un nom à la zone !");
            return;
        }

        // 1. Verrouillage & Chargement
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        btnText.innerText = "Création...";
        btnIcon.className = "fas fa-spinner fa-spin w-4 h-4";

        try {
            // 2. Envoi de la commande (AJAX)
            const response = await fetch("{{ route('wifizones.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    nom_zone: nom,
                    adresse: adresse
                })
            });

            const data = await response.json();

            // 3 & 4 (Côté Serveur) -> Réponse reçue ici
            if (data.success) {
                // 5. Affichage du Résultat
                document.getElementById('display-token').innerText = data.token;
                document.getElementById('display-url').innerText = data.url;
                
                // Transition visuelle
                document.getElementById('step-1').classList.add('hidden');
                document.getElementById('step-2').classList.remove('hidden');
                document.getElementById('wizard-progress').style.width = '100%';
                document.getElementById('wizard-step-num').innerText = '2';
            } else {
                showErrorModal("Erreur", data.message || "Erreur lors de la création");
                // Déverrouillage en cas d'erreur
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.innerText = "Suivant";
                btnIcon.className = "fas fa-arrow-right w-4 h-4";
            }
        } catch (error) {
            showErrorModal("Erreur de connexion", "Impossible de joindre le serveur. Vérifiez votre connexion.");
            // Déverrouillage en cas d'erreur
            btn.disabled = false;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
            btnText.innerText = "Suivant";
            btnIcon.className = "fas fa-arrow-right w-4 h-4";
        }
    }

    function finishWizard() {
        window.location.reload();
    }

    function copyUrl() {
        const url = document.getElementById('display-url').innerText;
        navigator.clipboard.writeText(url).then(() => {
            showErrorModal("Copié !", "Lien d'intégration copié !", 'success');
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showErrorModal("Copié !", "Lien d'intégration copié !", 'success');
        });
    }

    let isEditingZone = false;
    let initialZoneData = {};

    function showDetail(zone) {
        document.getElementById('zone-list').classList.add('hidden');
        document.getElementById('zone-detail').classList.remove('hidden');
        
        document.getElementById('display-zone-id').innerText = 'WZ-' + zone.id;
        document.getElementById('zone-title').innerText = zone.nom_zone;
        document.getElementById('detail-zone-id-input').value = zone.id;
        document.getElementById('detail-zone-name').value = zone.nom_zone;
        document.getElementById('detail-zone-address').value = zone.adresse || '';
        document.getElementById('code-zone-id').innerText = zone.token;

        // Sync Icon with stock status
        const totalStock = zone.forfaits.reduce((acc, f) => acc + (f.tickets_count || 0), 0);
        const isLowStock = totalStock < 10;
        const iconContainer = document.getElementById('detail-zone-icon-container');
        const icon = document.getElementById('detail-zone-icon');
        
        if (isLowStock) {
            iconContainer.className = "w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/30";
            icon.className = "fas fa-exclamation-triangle w-7 h-7";
        } else {
            iconContainer.className = "w-16 h-16 rounded-2xl bg-brand-blue flex items-center justify-center text-white shadow-lg shadow-blue-500/30";
            icon.className = "fas fa-wifi w-7 h-7";
        }
        
        // Customization preview
        document.getElementById('custom-display-name').value = zone.nom_zone;
        updatePreview();

        cancelZoneEdit(); // Reset edit state when showing details
        switchTab('general');
    }

    function showList() {
        document.getElementById('zone-detail').classList.add('hidden');
        document.getElementById('zone-list').classList.remove('hidden');
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    function toggleZoneEdit() {
        const btn = document.getElementById('edit-zone-btn');
        const cancelBtn = document.getElementById('cancel-zone-btn');
        const form = document.getElementById('zone-profile-form');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');

        if (!isEditingZone) {
            isEditingZone = true;
            initialZoneData = {
                nom_zone: document.getElementById('detail-zone-name').value,
                adresse: document.getElementById('detail-zone-address').value
            };
            
            inputs.forEach(input => input.disabled = false);
            btn.innerText = 'Enregistrer';
            btn.disabled = true;
            cancelBtn.classList.remove('hidden');
            
            inputs.forEach(input => {
                input.addEventListener('input', checkZoneChanges);
            });
        } else {
            saveZoneChanges();
        }
    }

    function checkZoneChanges() {
        const currentName = document.getElementById('detail-zone-name').value;
        const currentAddress = document.getElementById('detail-zone-address').value;
        const btn = document.getElementById('edit-zone-btn');

        const hasChanged = currentName !== initialZoneData.nom_zone || 
                           currentAddress !== initialZoneData.adresse;
        
        btn.disabled = !hasChanged;
    }

    function cancelZoneEdit() {
        const btn = document.getElementById('edit-zone-btn');
        if (!btn) return;
        
        const cancelBtn = document.getElementById('cancel-zone-btn');
        const form = document.getElementById('zone-profile-form');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');

        isEditingZone = false;
        
        if (initialZoneData.nom_zone) {
            document.getElementById('detail-zone-name').value = initialZoneData.nom_zone;
            document.getElementById('detail-zone-address').value = initialZoneData.adresse;
        }
        
        inputs.forEach(input => {
            input.disabled = true;
            input.removeEventListener('input', checkZoneChanges);
        });
        
        btn.innerText = 'Modifier';
        btn.disabled = false;
        cancelBtn.classList.add('hidden');
    }

    async function saveZoneChanges() {
        const id = document.getElementById('detail-zone-id-input').value;
        const btn = document.getElementById('edit-zone-btn');
        const cancelBtn = document.getElementById('cancel-zone-btn');
        const name = document.getElementById('detail-zone-name').value;
        const address = document.getElementById('detail-zone-address').value;
        
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Enregistrement...';
        
        try {
            const response = await fetch("{{ url('/wifizones') }}/" + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    nom_zone: name,
                    adresse: address
                })
            });
            
            const data = await response.json();
            if (data.success) {
                showErrorModal("Succès", data.message, 'success');
                document.getElementById('zone-title').innerText = name;
                
                isEditingZone = false;
                btn.innerText = 'Modifier';
                btn.disabled = false;
                cancelBtn.classList.add('hidden');
                document.querySelectorAll('#zone-profile-form input, #zone-profile-form select').forEach(input => input.disabled = true);
                
                // Update the original data for next edit
                initialZoneData = { nom_zone: name, adresse: address };
            } else {
                showErrorModal("Erreur", data.message || "Erreur lors de la mise à jour");
                btn.disabled = false;
                btn.innerText = originalText;
            }
        } catch (error) {
            showErrorModal("Erreur", "Une erreur réseau est survenue");
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    function updatePreview() {
        const name = document.getElementById('custom-display-name').value;
        const msg = document.getElementById('custom-welcome-msg').value;
        
        document.getElementById('preview-display-name').innerText = name || 'Wifi Bar Central';
        document.getElementById('preview-welcome-msg').innerText = msg || 'Bienvenue !';
    }

    function saveCustomization() {
        showErrorModal("Succès", "Apparence mise à jour (Simulation)", 'success');
    }

    async function handleDeleteZone() {
        const id = document.getElementById('detail-zone-id-input').value;
        
        try {
            // 1. Fetch impact analysis
            const response = await fetch("{{ url('/wifizones') }}/" + id + "/impact", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                document.getElementById('impact-forfaits').innerText = data.forfaits_count;
                document.getElementById('impact-tickets').innerText = data.tickets_count;
                
                // Show modal step 1
                document.getElementById('delete-zone-modal').classList.remove('hidden');
                document.getElementById('delete-step-1').classList.remove('hidden');
                document.getElementById('delete-step-2').classList.add('hidden');
            } else {
                showErrorModal("Erreur", data.message || "Impossible d'analyser l'impact.");
            }
        } catch (error) {
            showErrorModal("Erreur", "Une erreur réseau est survenue");
        }
    }

    function showDeleteStep2() {
        document.getElementById('delete-step-1').classList.add('hidden');
        document.getElementById('delete-step-2').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-zone-modal').classList.add('hidden');
    }

    async function confirmFinalDelete() {
        const id = document.getElementById('detail-zone-id-input').value;
        closeDeleteModal();
        
        try {
            const response = await fetch("{{ url('/wifizones') }}/" + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            });
            
            const data = await response.json();
            if (data.success) {
                showErrorModal("Succès", "Zone supprimée avec succès", 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showErrorModal("Erreur", data.message || "Erreur lors de la suppression");
            }
        } catch (error) {
            showErrorModal("Erreur", "Une erreur réseau est survenue");
        }
    }
</script>
@endsection