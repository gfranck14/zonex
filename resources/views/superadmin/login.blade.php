<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SuperAdmin Login - ZoneX</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
        }
        
        .login-card {
            background: rgba(26, 26, 26, 0.9);
            border: 1px solid #2a2a2a;
            backdrop-filter: blur(10px);
        }
        
        .input-field {
            background: #0f0f0f;
            border: 1px solid #2a2a2a;
            color: #e5e5e5;
        }
        
        .input-field:focus {
            border-color: #dc2626;
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md p-8 rounded-lg shadow-2xl">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="text-4xl text-red-500 mb-2">
                <i class="fas fa-crown"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">ZoneX SuperAdmin</h1>
            <p class="text-gray-400 text-sm mt-2">Accès réservé aux administrateurs</p>
        </div>
        
        <!-- Erreurs -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-900 border border-red-700 text-red-100 rounded">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ $errors->first() }}
            </div>
        @endif
        
        <!-- Messages de succès -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-100 rounded">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        
        <!-- Formulaire de login -->
        <form method="POST" action="{{ route('superadmin.login.submit') }}">
            @csrf
            
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-300 mb-2 text-sm font-medium">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus
                    class="input-field w-full px-4 py-3 rounded"
                    placeholder="god@zonex.admin"
                >
            </div>
            
            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-300 mb-2 text-sm font-medium">
                    <i class="fas fa-lock mr-2"></i>Mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="input-field w-full px-4 py-3 rounded"
                    placeholder="••••••••"
                >
            </div>
            
            <!-- Remember Me -->
            <div class="mb-6 flex items-center">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="w-4 h-4 text-red-600 bg-gray-800 border-gray-600 rounded focus:ring-red-500"
                >
                <label for="remember" class="ml-2 text-sm text-gray-300">
                    Se souvenir de moi
                </label>
            </div>
            
            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded transition-colors"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
            </button>
        </form>
        
        <!-- Warning -->
        <div class="mt-8 p-4 bg-yellow-900 bg-opacity-30 border border-yellow-700 text-yellow-200 text-sm rounded">
            <i class="fas fa-shield-alt mr-2"></i>
            <strong>Sécurité:</strong> Cette zone est protégée. Toutes les connexions sont enregistrées et tracées.
        </div>
    </div>
</body>
</html>
