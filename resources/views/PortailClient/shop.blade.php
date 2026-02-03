<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Boutique - {{ $wifizone->display_name ?? $wifizone->nom_zone ?? 'Zone WiFi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F3F4F6; }
        .tap-highlight-transparent { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="min-h-screen pb-10">

    <!-- Header -->
    <div class="px-6 pt-10 pb-6 text-center">
        <h2 class="text-xl font-bold text-gray-900">Choisissez votre offre</h2>
        <p class="text-sm text-gray-500 mt-1">Sélectionnez un forfait adapté</p>
    </div>

    <!-- Grid des Offres -->
    <div class="px-6 space-y-6 max-w-md mx-auto">
        
        @forelse($forfaits as $forfait)
        <!-- Carte : Style Mockup -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden transform transition active:scale-95 duration-200">
            
            <!-- Header Couleur (Prix) -->
            <!-- Note: On utilise color_class qui est genre 'bg-red-500' ou 'bg-blue-500' -->
            <!-- Si color_class n'est pas défini, défaut rouge comme sur mockup -->
            <div class="{{ $forfait->color_class ?? 'bg-red-500' }} py-6 text-center">
                <h3 class="text-3xl font-black text-white tracking-tighter">
                    {{ number_format($forfait->prix, 0, ',', ' ') }} <span class="text-lg font-bold opacity-80">FCFA</span>
                </h3>
            </div>

            <!-- Corps -->
            <div class="p-6 text-center">
                <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $forfait->nom }}</h4>
                
                <p class="text-[10px] font-bold text-blue-400 tracking-widest uppercase mb-4">
                    VALIDE {{ $forfait->validite }}
                </p>

                @if($forfait->description)
                <p class="text-gray-400 text-xs mb-6 px-4 leading-relaxed">
                    {{ $forfait->description }}
                </p>
                @else
                <p class="text-gray-400 text-xs mb-6 px-4">Idéal pour surfer sans limites.</p>
                @endif
                
                <form action="{{ route('client.buy', $forfait->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition shadow-sm text-sm">
                        Choisir
                    </button>
                </form>
            </div>

        </div>
        @empty
        <div class="text-center py-10 opacity-50">
            <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-400">Aucun forfait disponible.</p>
        </div>
        @endforelse

    </div>

    <!-- Retour (Optionnel) -->
    <div class="mt-8 text-center">
         <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button class="text-xs font-bold text-gray-400 hover:text-red-500 uppercase tracking-wide">
                Se déconnecter
            </button>
        </form>
    </div>

</body>
</html>
