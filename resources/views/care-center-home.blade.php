@extends('layout-care')

@section('title', 'Podo-Reflex - Centre de Soins Podologiques')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Section -->
    <section class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <div class="mb-8">
                <h2 class="text-5xl font-bold text-gray-900 mb-6">
                    Soins podologiques<br>
                    <span class="text-blue-600">professionnels</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Prenez rendez-vous en moins de 3 minutes avec nos praticiens spécialisés en pédicurie médicale, réflexologie plantaire et soins des ongles.
                </p>
            </div>

            <!-- Barre de recherche principale -->
            <div class="max-w-2xl mx-auto mb-8">
                <div class="relative">
                    <div class="flex items-center bg-white rounded-2xl shadow-lg border border-gray-200 p-2">
                        <div class="flex-1 flex items-center">
                            <svg class="w-6 h-6 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input 
                                type="text" 
                                id="main-search"
                                placeholder="Quel soin recherchez-vous ?"
                                class="w-full px-4 py-3 text-lg focus:outline-none"
                                onkeyup="handleSearch(event)"
                            >
                        </div>
                        <button 
                            onclick="performSearch()"
                            class="bg-blue-600 text-white px-8 py-3 rounded-xl font-medium hover:bg-blue-700 transition-colors"
                        >
                            Rechercher
                        </button>
                    </div>
                    
                    <!-- Suggestions de recherche -->
                    <div id="search-suggestions" class="absolute w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200 hidden">
                        <div class="p-4">
                            <p class="text-sm text-gray-500 mb-2">Suggestions populaires :</p>
                            <div class="space-y-2">
                                <div class="search-suggestion px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="selectSuggestion('Pédicurie médicale')">
                                    <span class="text-gray-700">🦶 Pédicurie médicale</span>
                                </div>
                                <div class="search-suggestion px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="selectSuggestion('Réflexologie plantaire')">
                                    <span class="text-gray-700">🌿 Réflexologie plantaire</span>
                                </div>
                                <div class="search-suggestion px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="selectSuggestion('Manucure médicale')">
                                    <span class="text-gray-700">💅 Manucure médicale</span>
                                </div>
                                <div class="search-suggestion px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="selectSuggestion('Modelage d\'ongles')">
                                    <span class="text-gray-700">💎 Modelage d'ongles</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons rapides -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-12">
                <button 
                    onclick="quickSelect('Pédicurie médicale')"
                    class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-105 border border-gray-100"
                >
                    <div class="text-3xl mb-3">🦶</div>
                    <h3 class="font-semibold text-gray-800">Pédicurie médicale</h3>
                    <p class="text-sm text-gray-500 mt-1">Soins des pieds</p>
                </button>
                
                <button 
                    onclick="quickSelect('Réflexologie plantaire')"
                    class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-105 border border-gray-100"
                >
                    <div class="text-3xl mb-3">🌿</div>
                    <h3 class="font-semibold text-gray-800">Réflexologie plantaire</h3>
                    <p class="text-sm text-gray-500 mt-1">Massage thérapeutique</p>
                </button>
                
                <button 
                    onclick="quickSelect('Manucure médicale')"
                    class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-105 border border-gray-100"
                >
                    <div class="text-3xl mb-3">💅</div>
                    <h3 class="font-semibold text-gray-800">Manucure médicale</h3>
                    <p class="text-sm text-gray-500 mt-1">Soins des ongles</p>
                </button>
                
                <button 
                    onclick="quickSelect('Modelage d\'ongles')"
                    class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-105 border border-gray-100"
                >
                    <div class="text-3xl mb-3">💎</div>
                    <h3 class="font-semibold text-gray-800">Modelage d'ongles</h3>
                    <p class="text-sm text-gray-500 mt-1">Onglerie esthétique</p>
                </button>
            </div>

            <!-- Bouton principal -->
            <button 
                onclick="goToBooking()"
                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-4 rounded-2xl text-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-xl"
            >
              Prendre rendez-vous
            </button>
        </div>
    </section>

    <!-- Section Praticiens Disponibles -->
    <section id="practitioners" class="py-16 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Nos Praticiens</h3>
                <p class="text-lg text-gray-600">Des experts certifiés à votre service</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Praticien 1 -->
                <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">MM</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Marie Martin</h4>
                            <p class="text-sm text-gray-600">Podologue - 15 ans d'expérience</p>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Disponible aujourd'hui
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">⭐</span>
                            4.9/5 (127 avis)
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">📍</span>
                            Liège Centre
                        </div>
                    </div>
                    <button 
                        onclick="bookPractitioner('Marie Martin')"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Réserver
                    </button>
                </div>

                <!-- Praticien 2 -->
                <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-purple-600">JD</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Jean Dubois</h4>
                            <p class="text-sm text-gray-600">Réflexologue - 12 ans d'expérience</p>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Disponible demain
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">⭐</span>
                            4.8/5 (89 avis)
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">📍</span>
                            Seraing
                        </div>
                    </div>
                    <button 
                        onclick="bookPractitioner('Jean Dubois')"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Réserver
                    </button>
                </div>

                <!-- Praticien 3 -->
                <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">SB</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Sophie Bernard</h4>
                            <p class="text-sm text-gray-600">Spécialiste ongles - 8 ans d'expérience</p>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Disponible jeudi
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">⭐</span>
                            4.7/5 (156 avis)
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="mr-2">📍</span>
                            Ans
                        </div>
                    </div>
                    <button 
                        onclick="bookPractitioner('Sophie Bernard')"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Réserver
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Services -->
    <section id="services" class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Nos Services</h3>
                <p class="text-lg text-gray-600">Une gamme complète de soins pour vos pieds</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="text-4xl mb-4">🦶</div>
                    <h4 class="font-semibold text-gray-900 mb-2">Pédicurie Médicale</h4>
                    <p class="text-sm text-gray-600">Soins complets des pieds, traitement des cors et durillons</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="text-4xl mb-4">🌿</div>
                    <h4 class="font-semibold text-gray-900 mb-2">Réflexologie Plantaire</h4>
                    <p class="text-sm text-gray-600">Massage thérapeutique pour le bien-être général</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="text-4xl mb-4">💅</div>
                    <h4 class="font-semibold text-gray-900 mb-2">Manucure Médicale</h4>
                    <p class="text-sm text-gray-600">Soins des ongles des mains et des pieds</p>
                </div>
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="text-4xl mb-4">💎</div>
                    <h4 class="font-semibold text-gray-900 mb-2">Modelage d'Ongles</h4>
                    <p class="text-sm text-gray-600">Onglerie esthétique et pose de vernis</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">Podo-Reflex</h4>
                    <p class="text-gray-400">Votre centre de soins podologiques de confiance depuis 2010.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>📞 +32 4 123 45 67</p>
                        <p>✉️ contact@podo-reflex.be</p>
                        <p>📍 Rue du Pont 123, 4000 Liège</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Horaires</h4>
                    <div class="space-y-1 text-gray-400">
                        <p>Lundi - Vendredi: 9h - 19h</p>
                        <p>Samedi: 9h - 17h</p>
                        <p>Dimanche: Fermé</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Podo-Reflex. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal de réservation -->
<div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Réservation</h3>
            <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="booking-content">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedService = '';
let selectedPractitioner = '';

// Gestion de la recherche
function handleSearch(event) {
    const query = event.target.value.toLowerCase();
    const suggestions = document.getElementById('search-suggestions');
    
    if (query.length > 2) {
        suggestions.classList.remove('hidden');
    } else {
        suggestions.classList.add('hidden');
    }
    
    if (event.key === 'Enter') {
        performSearch();
    }
}

function performSearch() {
    const searchValue = document.getElementById('main-search').value;
    if (searchValue.trim()) {
        selectedService = searchValue;
        showBookingModal();
    }
}

function selectSuggestion(service) {
    document.getElementById('main-search').value = service;
    document.getElementById('search-suggestions').classList.add('hidden');
    selectedService = service;
    showBookingModal();
}

function quickSelect(service) {
    selectedService = service;
    document.getElementById('main-search').value = service;
    showBookingModal();
}

function bookPractitioner(practitioner) {
    selectedPractitioner = practitioner;
    showBookingModal();
}

function goToBooking() {
    showBookingModal();
}

function showBookingModal() {
    const modal = document.getElementById('booking-modal');
    const content = document.getElementById('booking-content');
    
    let html = `
        <div class="space-y-4">
    `;
    
    if (selectedService) {
        html += `
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-sm text-blue-800">Service sélectionné:</p>
                <p class="font-semibold text-blue-900">${selectedService}</p>
            </div>
        `;
        
        // Afficher la liste des praticiens disponibles pour ce service
        html += `
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-900">Praticiens disponibles pour ${selectedService}</h4>
                
                <!-- Tableau des praticiens -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Praticien</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Spécialité</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Lieu</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Distance</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Praticien 1 - Plus proche -->
                            <tr class="border-b border-gray-100 hover:bg-green-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-900">Marie Martin</span>
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Plus proche</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">Podologue</td>
                                <td class="py-3 px-4 text-gray-600">Liège Centre</td>
                                <td class="py-3 px-4 text-gray-600">1.2 km</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        onclick="bookWithPractitioner('Marie Martin', 'Liège Centre')"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium text-sm"
                                    >
                                        Réserver
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Praticien 2 -->
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-900">Jean Dubois</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">Réflexologue</td>
                                <td class="py-3 px-4 text-gray-600">Seraing</td>
                                <td class="py-3 px-4 text-gray-600">8.5 km</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        onclick="bookWithPractitioner('Jean Dubois', 'Seraing')"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm"
                                    >
                                        Réserver
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Praticien 3 -->
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-900">Sophie Bernard</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">Spécialiste ongles</td>
                                <td class="py-3 px-4 text-gray-600">Ans</td>
                                <td class="py-3 px-4 text-gray-600">12.3 km</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        onclick="bookWithPractitioner('Sophie Bernard', 'Ans')"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm"
                                    >
                                        Réserver
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Praticien 4 -->
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-900">Pierre Lefebvre</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">Podologue</td>
                                <td class="py-3 px-4 text-gray-600">Verviers</td>
                                <td class="py-3 px-4 text-gray-600">25.7 km</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        onclick="bookWithPractitioner('Pierre Lefebvre', 'Verviers')"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm"
                                    >
                                        Réserver
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    } else {
        // Si aucun service sélectionné, afficher le formulaire de réservation classique
        html += `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Choisissez une date</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Choisissez une heure</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>09:00</option>
                    <option>10:00</option>
                    <option>11:00</option>
                    <option>14:00</option>
                    <option>15:00</option>
                    <option>16:00</option>
                    <option>17:00</option>
                    <option>18:00</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Votre nom</label>
                <input type="text" placeholder="Nom complet" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Votre téléphone</label>
                <input type="tel" placeholder="Numéro de téléphone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button onclick="confirmBooking()" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Confirmer la réservation
            </button>
        `;
    }
    
    html += `</div>`;
    
    content.innerHTML = html;
    modal.classList.remove('hidden');
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.add('hidden');
}

function bookWithPractitioner(practitioner, location) {
    selectedPractitioner = practitioner;
    const modal = document.getElementById('booking-modal');
    const content = document.getElementById('booking-content');
    
    const html = `
        <div class="space-y-4">
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-sm text-blue-800">Service sélectionné:</p>
                <p class="font-semibold text-blue-900">${selectedService}</p>
            </div>
            
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-sm text-green-800">Praticien:</p>
                <p class="font-semibold text-green-900">${practitioner}</p>
                <p class="text-sm text-green-600">📍 ${location}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Choisissez une date</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Choisissez une heure</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>09:00</option>
                    <option>10:00</option>
                    <option>11:00</option>
                    <option>14:00</option>
                    <option>15:00</option>
                    <option>16:00</option>
                    <option>17:00</option>
                    <option>18:00</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Votre nom</label>
                <input type="text" placeholder="Nom complet" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Votre téléphone</label>
                <input type="tel" placeholder="Numéro de téléphone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button onclick="confirmBooking()" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                Confirmer la réservation avec ${practitioner}
            </button>
        </div>
    `;
    
    content.innerHTML = html;
}

function confirmBooking() {
    // Simulation de confirmation
    const modal = document.getElementById('booking-modal');
    const content = document.getElementById('booking-content');
    
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="text-5xl mb-4">✅</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Réservation confirmée!</h3>
            <p class="text-gray-600 mb-6">Vous recevrez un SMS de confirmation dans quelques minutes.</p>
            <button onclick="closeBookingModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Fermer
            </button>
        </div>
    `;
    
    // Réinitialiser les sélections
    setTimeout(() => {
        selectedService = '';
        selectedPractitioner = '';
        document.getElementById('main-search').value = '';
    }, 2000);
}

// Fermer les suggestions en cliquant ailleurs
document.addEventListener('click', function(event) {
    const searchContainer = document.querySelector('.relative');
    const suggestions = document.getElementById('search-suggestions');
    
    if (!searchContainer.contains(event.target)) {
        suggestions.classList.add('hidden');
    }
});
</script>
@push('scripts')
<script>
// Script additionnel si nécessaire
</script>
@endpush
