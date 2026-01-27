

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
            <!-- CARD DYNAMIQUE -->
            <div class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm border border-transparent hover:border-brand-blue/30 transition-all group relative cursor-pointer flex flex-col justify-between">
                
                <!-- En-tête : Titre + Badge -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $zone->nom_zone }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-location-dot w-3 h-3 inline mr-1"></i>
                            {{ $zone->adresse ?? 'Adresse non spécifiée' }}
                        </p>
                    </div>
                </div>

                <!-- Corps : Token -->
                <div class="space-y-4 mb-6">
                    <div class="p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl border border-dashed border-gray-200 dark:border-slate-600">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block mb-1">ID Technique (Token)</span>
                        <span class="text-sm font-mono font-bold text-brand-blue">{{ $zone->token }}</span>
                    </div>
                    
                    <!-- Lien Magique -->
                    <button onclick="copyToClipboard('{{ url('/portal/login?z=' . $zone->token) }}')" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-brand-blue hover:underline py-2">
                        <i class="fas fa-sync w-4 h-4"></i>
                        Copier lien d'intégration
                    </button>
                </div>

                <!-- Pied : Actions -->
                <div class="flex gap-2 mt-auto">
                    <button class="flex-1 bg-brand-sidebarLight dark:bg-slate-700 text-white py-3 rounded-xl text-xs font-bold hover:brightness-110 transition shadow-lg">
                        GÉRER LE STOCK
                    </button>
                    <button class="w-10 flex items-center justify-center bg-gray-100 dark:bg-slate-700 text-gray-500 rounded-xl hover:text-brand-blue transition">
                        <i class="fas fa-cog w-5 h-5"></i>
                    </button>
                </div>
            </div>

        @empty
            <!-- ÉTAT VIDE : AUCUNE ZONE -->
            <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white/50 dark:bg-brand-cardDark/50 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-[3rem]">
                <div class="w-20 h-20 bg-gray-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-wifi w-10 h-10 text-gray-400"></i>
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
                    <div class="w-16 h-16 rounded-2xl bg-brand-blue flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i class="fas fa-location-dot w-8 h-8"></i>
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
                                <div><label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Nom de la zone</label><input type="text" value="Bar Central" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none focus:ring-2 focus:ring-brand-green/20"></div>
                                <div><label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Type de lieu</label><select class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none"><option>Restaurant / Bar</option><option>Hôtel</option></select></div>
                            </div>
                            <div><label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Adresse physique</label><input type="text" value="Quartier Haie Vive, Rue 12, Cotonou" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none focus:ring-2 focus:ring-brand-green/20"></div>
                            <div class="pt-4"><button class="bg-brand-dark text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-800 transition">Enregistrer</button></div>
                        </div>
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
                                <code class="text-brand-blue font-mono text-xs break-all block pr-10 leading-relaxed">https://auth.wifizone-manager.com/connect?zone_id=<span class="text-white font-bold" id="code-zone-id">WZ-8821-XJ</span>&mac=$(mac)&ip=$(ip)</code>
                                <button class="absolute top-4 right-4 text-gray-400 hover:text-white" title="Copier">
                                    <i class="fas fa-copy w-5 h-5"></i>
                                </button>
                            </div>
                            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-100 dark:border-yellow-800 rounded-xl p-4 flex gap-3">
                                <i class="fas fa-exclamation-triangle w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0"></i>
                                <p class="text-xs text-yellow-800 dark:text-yellow-200">Assurez-vous que le <strong>Walled Garden</strong> est configuré.</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Walled Garden 🛡️</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Domaines à autoriser (Allow) IP List.</p>
                            <ul class="space-y-3 text-xs font-mono text-gray-600 dark:text-gray-300">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                                    *.wifizone-manager.com
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                                    *.cinetpay.com
                                </li>
                            </ul>
                            <button class="mt-6 w-full py-2 border border-gray-200 dark:border-slate-600 rounded-lg text-xs font-bold hover:bg-gray-50 dark:hover:bg-slate-700">Copier la liste</button>
                        </div>
                    </div>
                </div>
                <div id="content-custom" class="tab-content hidden">
                    <div class="bg-white dark:bg-brand-cardDark p-8 rounded-3xl shadow-sm max-w-4xl flex gap-10">
                        <div class="flex-1 space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Apparence Portail Captif</h3>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Nom affiché</label>
                                <input type="text" value="Wifi Bar Central" class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none focus:ring-2 focus:ring-brand-green/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Message de bienvenue</label>
                                <textarea class="w-full bg-gray-50 dark:bg-slate-800 dark:text-white border-none rounded-xl p-3 text-sm font-medium text-gray-800 dark:text-white outline-none h-24">Bienvenue !</textarea>
                            </div>
                            <button class="bg-brand-dark text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-800 transition">Sauvegarder</button>
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
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white"> WiFi-Zone 🚀</h3>
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
        <input type="text" id="new-zone-address" placeholder=" " class="input-floating">
        <label class="floating-label">Lieu / Adresse (Optionnel)</label>
        <p class="text-xs text-gray-400 mt-1">Ex: Cocody, Rue des Jardins</p>
    </div>
    <div class="pt-4 flex justify-end">
        <button onclick="goToStep2()" class="bg-brand-blue text-white px-6 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition flex items-center gap-2">
            Suivant 
            <i class="fas fa-arrow-right w-4 h-4"></i>
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

<script>
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

        if (!nom) {
            alert("Veuillez donner un nom à la zone !");
            return;
        }

        try {
            const response = await fetch("{{ route('wifizones.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    nom_zone: nom,
                    adresse: adresse
                })
            });

            const data = await response.json();

            if (data.success) {
                // 1. Injecter les données reçues dans le HTML de l'étape 2
                document.getElementById('display-token').innerText = data.token;
                document.getElementById('display-url').innerText = data.url;

                // 2. Faire la transition visuelle (Cacher étape 1, Afficher étape 2)
                document.getElementById('step-1').classList.add('hidden');
                document.getElementById('step-2').classList.remove('hidden');

                // 3. Mettre à jour la barre de progression
                document.getElementById('wizard-progress').style.width = '100%';
                document.getElementById('wizard-step-num').innerText = '2';
            } else {
                alert("Erreur lors de la création : " + (data.message || "Erreur inconnue"));
            }

        } catch (error) {
            console.error("Détails de l'erreur:", error);
            alert("Erreur de connexion : " + error.message);
        }
    }

    function finishWizard() {
        // Recharge la page pour afficher la nouvelle carte dans la liste
        window.location.reload();
    }

    function copyUrl() {
        const url = document.getElementById('display-url').innerText;
        navigator.clipboard.writeText(url).then(() => {
            alert("Lien d'intégration copié !");
        });
    }
</script>
    <script src="assets/js/main.js"></script>



@endsection