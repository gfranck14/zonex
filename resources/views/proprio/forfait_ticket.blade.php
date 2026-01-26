
@extends('layout')

@section('title', ' Forfaits-Tickets')



@section('content')


    <!-- MAIN CONTENT -->
    <main class="flex-1 py-4 pr-4 pl-0 h-full relative">
        <div class="bg-brand-bgLight dark:bg-brand-bgDark w-full h-full rounded-2xl shadow-2xl overflow-y-auto no-scrollbar relative p-6 pb-10 transition-colors duration-300">
            
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Forfaits & Tickets</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Configurez vos offres et alimentez le stock.</p>
                </div>
            </header>

            <div class="border-b border-gray-200 dark:border-slate-700 mb-8 flex gap-8">
                <button onclick="switchTab('catalogue')" id="tab-catalogue" class="tab-btn active text-sm">Catalogue des Offres</button>
                <button onclick="switchTab('stock')" id="tab-stock" class="tab-btn text-sm">Stock & Import</button>
            </div>

            <!-- CATALOGUE -->
          <div id="content-catalogue" class="tab-content animate-fade-in space-y-10">

         <!-- Si 0 WIFIZONE-->
        @if($etat === 'no_wifizone')
    <div class="flex flex-col items-center justify-center text-gray-400 py-20">
        <svg class="w-12 h-12 mb-4" ...></svg>
        <p class="font-medium">Aucune zone WiFi créée pour le moment.</p>
    </div>
@endif
    
          <!-- WIFI ZONE CARD -->
   @foreach($wifizones as $zone)
    <div class="bg-white dark:bg-brand-cardDark p-6 rounded-3xl shadow-sm">

        <!-- Nom Zone -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">{{ $zone->nom_zone }}</h3>

            <button
                class="btn-ajouter"
    onclick="openAddForfaitModal({{ $zone->id }}, '{{ $zone->nom_zone }}')">
    + Ajouter un forfait
            </button>
        </div>

        <!-- Cas : aucun forfait -->
        @if($zone->forfaits->isEmpty())
            <div class="text-sm text-gray-400 italic flex items-center gap-2">
                <svg class="w-4 h-4" ...></svg>
                Aucun forfait pour cette zone.
            </div>
        @endif

        <!-- Cas : forfaits existants -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @foreach($zone->forfaits as $forfait)
                <div class="border rounded-xl p-4">
                    <p class="font-bold">{{ $forfait->nom }}</p>
                    <p>{{ $forfait->prix }} F</p>

                    <button
                        class="btn-modifier"
                        data-forfait-id="{{ $forfait->id }}">
                        Modifier
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endforeach


    <!-- Répéter autant de Wifi Zones que nécessaire -->

</div>


            <!-- CONTENU : STOCK & IMPORT -->
            <div id="content-stock" class="tab-content hidden animate-fade-in space-y-8">
                
                <!-- État du Stock par Forfait -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Exemple OK -->
                    <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-transparent hover:border-brand-blue/20 transition-all flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-slate-700 flex flex-col items-center justify-center">
                            <span class="text-xs font-bold text-brand-sidebarLight dark:text-brand-blue">4H</span>
                            <span class="text-[8px] text-gray-400 uppercase">Profil</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-end mb-1">
                                <p class="font-bold text-gray-800 dark:text-white text-lg">Forfait 4 Heures</p>
                                <span class="text-[10px] font-bold text-brand-green">OK</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-brand-green h-full" style="width: 65%"></div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium"><span class="text-brand-dark dark:text-white font-bold">320</span> / 500 restants</p>
                        </div>
                    </div>
                    
                    <!-- Forfait en alerte -->
                    <div class="bg-brand-cardLight dark:bg-brand-cardDark p-5 rounded-3xl shadow-sm border border-red-50 dark:border-red-900/30 flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 flex flex-col items-center justify-center">
                            <span class="text-xs font-bold text-red-600 dark:text-red-400">1H</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-end mb-1">
                                <p class="font-bold text-gray-800 dark:text-white text-lg">Forfait 1 Heure</p>
                                <span class="text-[10px] font-bold text-red-500">BAS</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-red-500 h-full" style="width: 10%"></div>
                            </div>
                            <p class="text-[10px] text-red-500 mt-1 font-bold">12 tickets restants !</p>
                        </div>
                    </div>
                </div>

                <!-- ZONE D'IMPORTATION MIKHMON -->
                <div class="bg-white dark:bg-brand-cardDark p-8 rounded-4xl shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Importation de nouveaux tickets</h3>
                        <span class="text-[10px] bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-300 px-3 py-1 rounded-full font-bold uppercase tracking-widest">Format Mikhmon / CSV</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <!-- ÉTAPE 1 -->
                        <div class="space-y-4">
                            <p class="text-xs font-bold text-brand-sidebarLight dark:text-brand-blue flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-brand-sidebarLight dark:bg-brand-blue text-white flex items-center justify-center text-[10px]">1</span>
                                Destination
                            </p>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Wifi Zone cible</label>
                                <select class="w-full bg-gray-50 dark:bg-slate-700 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 text-sm font-bold text-gray-700 outline-none focus:ring-2 focus:ring-brand-blue/20 transition-all cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                                    <option>Bar Central</option>
                                    <option>Campus Nord</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Choisir le Forfait</label>
                                <select class="w-full bg-gray-50 dark:bg-slate-700 dark:text-white border border-gray-200 dark:border-slate-600 rounded-xl p-3 text-sm font-bold text-gray-700 outline-none focus:ring-2 focus:ring-brand-blue/20 transition-all cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600">
                                    <option>Forfait 1 Heure</option>
                                    <option>Forfait 24 Heures</option>
                                </select>
                            </div>
                        </div>

                        <!-- ÉTAPE 2 -->
                        <div class="lg:col-span-2 space-y-4">
                            <p class="text-xs font-bold text-brand-sidebarLight dark:text-brand-blue flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-brand-sidebarLight dark:bg-brand-blue text-white flex items-center justify-center text-[10px]">2</span>
                                Fichier Mikhmon
                            </p>
                            
                            <div class="border-2 border-dashed border-gray-200 dark:border-slate-600 rounded-3xl p-8 flex flex-col items-center justify-center text-center hover:border-brand-blue hover:bg-blue-50/10 transition-all cursor-pointer group relative">
                                <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700 rounded-xl flex items-center justify-center mb-3 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-600 dark:text-gray-300">Glissez votre fichier CSV ici</p>
                                <p class="text-xs text-gray-400 mt-1">ou cliquez pour parcourir</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-gray-50 dark:border-slate-700 flex flex-wrap items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                             <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="custom-checkbox w-4 h-4 rounded border-gray-300 appearance-none border">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Ignorer les doublons</span>
                             </label>
                             <button class="bg-brand-sidebarLight dark:bg-brand-blue text-white px-10 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition shadow-xl">
                                Confirmer l'importation
                             </button>
                        </div>
                    </div>
                </div>

                <!-- Détails du Stock (Smart Table) -->
                <div class="bg-brand-cardLight dark:bg-brand-cardDark rounded-3xl shadow-sm overflow-hidden mt-8">
                    <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Derniers Tickets Importés</h3>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Chercher un code..." class="bg-gray-50 dark:bg-slate-700 border-none rounded-xl py-2 px-4 text-xs w-48 outline-none dark:text-white">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 dark:bg-slate-800 text-gray-400 text-[10px] uppercase font-bold">
                                <tr>
                                    <th class="p-5">Username</th>
                                    <th class="p-5">Password</th>
                                    <th class="p-5">Profil</th>
                                    <th class="p-5">Client</th>
                                    <th class="p-5">Date</th>
                                    <th class="p-5">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50 dark:divide-slate-700">
                                <!-- Ligne 1 -->
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition">
                                    <td class="p-5 font-mono font-bold text-brand-sidebarLight dark:text-brand-blue">RBRMT</td>
                                    <td class="p-5 font-mono text-gray-500 dark:text-gray-400">93372</td>
                                    <td class="p-5 text-[10px] text-gray-400 font-mono">4H-24H</td>
                                    <td class="p-5 text-gray-300 italic">-</td>
                                    <td class="p-5 text-gray-300 italic">-</td>
                                    <td class="p-5"><span class="bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">Disponible</span></td>
                                </tr>
                                <!-- Ligne 2 -->
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition bg-green-50/30 dark:bg-green-900/10">
                                    <td class="p-5 font-mono font-bold text-brand-sidebarLight dark:text-brand-blue">azerty</td>
                                    <td class="p-5 font-mono text-gray-500 dark:text-gray-400">1234</td>
                                    <td class="p-5 text-[10px] text-gray-400 font-mono">4H-24H</td>
                                    <td class="p-5 font-bold text-gray-700 dark:text-gray-300 underline cursor-pointer">@johndoe</td>
                                    <td class="p-5 text-gray-500 dark:text-gray-400">22 Jan 14:30</td>
                                    <td class="p-5"><span class="bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">Vendu</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </main>
     <!-- MODAL FORFAIT -->
<div id="forfaitModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">

    <div class="bg-white dark:bg-brand-cardDark w-full max-w-lg rounded-3xl shadow-2xl p-8 relative">

        <!-- CLOSE -->
        <button onclick="closeForfaitModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            ✕
        </button>

        <!-- TITRE -->
        <h3 id="modalTitle"
            class="text-xl font-bold text-gray-800 dark:text-white mb-6">
            Ajouter un forfait
        </h3>

        <!-- FORM -->
        <form id="forfaitForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="wifi_zone_id" id="wifi_zone_id">

            <!-- ZONE (LOCKED) -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                    Wifi Zone
                </label>
                <input type="text"
                       id="zone_nom"
                       disabled
                       class="w-full bg-gray-100 dark:bg-slate-700 border-none rounded-xl p-3 text-sm font-bold text-gray-600 dark:text-gray-300">
            </div>

            <!-- NOM -->
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                    Nom du forfait
                </label>
                <input type="text" name="nom" id="nom"
                       required
                       class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white p-3 text-sm">
            </div>

            <!-- TIME / VALIDITE -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                        Time limit (min)
                    </label>
                    <input type="number" name="time_limit" id="time_limit"
                           required
                           class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white p-3 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                        Validité (h)
                    </label>
                    <input type="number" name="validite" id="validite"
                           required
                           class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white p-3 text-sm">
                </div>
            </div>

            <!-- PRIX -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">
                    Prix (FCFA)
                </label>
                <input type="number" name="prix" id="prix"
                       required
                       class="w-full rounded-xl border-gray-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white p-3 text-sm">
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-4">
                <button type="button"
                        onclick="closeForfaitModal()"
                        class="px-6 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700">
                    Annuler
                </button>

                <button type="submit"
                 onclick="this.closest('form').submit();"
                        class="bg-brand-sidebarLight dark:bg-brand-blue text-white px-8 py-3 rounded-xl text-sm font-bold hover:brightness-110 transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openAddForfaitModal(zoneId, zoneNom) {
    document.getElementById('modalTitle').innerText = 'Ajouter un forfait';

    const form = document.getElementById('forfaitForm');
    form.action = '/forfaits';
    document.getElementById('formMethod').value = 'POST';

    document.getElementById('wifi_zone_id').value = zoneId;
    document.getElementById('zone_nom').value = zoneNom;

    // reset champs
    form.reset();
    document.getElementById('zone_nom').value = zoneNom;

    showModal();
}

async function openEditForfaitModal(forfaitId) {
    const res = await fetch(`/forfaits/${forfaitId}/edit`);
    const data = await res.json();

    document.getElementById('modalTitle').innerText = 'Modifier le forfait';

    const form = document.getElementById('forfaitForm');
    form.action = `/forfaits/${forfaitId}`;
    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('wifi_zone_id').value = data.wifi_zone_id;
    document.getElementById('zone_nom').value = data.wifi_zone.nom;

    document.getElementById('nom').value = data.nom;
    document.getElementById('time_limit').value = data.time_limit;
    document.getElementById('validite').value = data.validite;
    document.getElementById('prix').value = data.prix;

    showModal();
}

function showModal() {
    document.getElementById('forfaitModal').classList.remove('hidden');
    document.getElementById('forfaitModal').classList.add('flex');
}

function closeForfaitModal() {
    document.getElementById('forfaitModal').classList.add('hidden');
    document.getElementById('forfaitModal').classList.remove('flex');
}
</script>

    <script src="assets/js/main.js"></script>
</body>
</html>

@endsection