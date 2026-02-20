<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Réinitialiser le mot de passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }
        .bg-custom-blue { background-color: #083e5f; }
        .bg-custom-blue:hover { background-color: #062b42; }
        .text-custom-blue { color: #083e5f; }
        .input-floating-group { position: relative; margin-bottom: 1rem; }
        .input-floating {
            width: 100%;
            background: #F8FAFC;
            border: 2px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 1.5rem 1rem 0.5rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1E293B;
            outline: none;
            transition: all 0.3s ease;
        }
        .input-floating:focus {
            border-color: #0EA5E9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }
        .floating-label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #64748B;
            transition: all 0.3s ease;
            pointer-events: none;
        }
        .input-floating:focus ~ .floating-label,
        .input-floating:not(:placeholder-shown) ~ .floating-label {
            top: 0.25rem;
            left: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0EA5E9;
            background: #FFFFFF;
            padding: 0 0.25rem;
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-6 py-10">

    <!-- Logo & Header -->
    <div class="flex flex-col items-center mb-10 w-full max-w-xs">
        <div class="w-20 h-20 bg-[#0EA5E9] rounded-3xl shadow-xl shadow-blue-500/20 flex items-center justify-center mb-6">
            <i class="fas fa-key text-white text-3xl"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-900 text-center leading-tight">
            Réinitialiser le mot de passe
        </h1>
        <p class="text-sm text-gray-500 mt-2 font-medium text-center">
            Créez un nouveau mot de passe pour votre compte
        </p>
    </div>

    <div class="w-full max-w-xs">
        @if($error)
            <!-- Erreur: lien invalide -->
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-red-800 mb-2">Lien invalide</h3>
                <p class="text-sm text-red-600">{{ $error }}</p>
            </div>
        @else
            <!-- Formulaire de réinitialisation -->
            <form id="reset-form" method="POST" action="{{ url('/portal/reset-password') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="telephone" value="{{ $telephone }}">

                <!-- Nouveau mot de passe -->
                <div class="input-floating-group">
                    <input type="password" name="password" id="password_input" required
                        class="input-floating font-bold tracking-widest"
                        placeholder=" ">
                    <label class="floating-label">Nouveau mot de passe</label>
                    <button type="button" onclick="togglePasswordVisibility('password_input')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <i class="far fa-eye" id="icon-password_input"></i>
                    </button>
                </div>

                <!-- Confirmer mot de passe -->
                <div class="input-floating-group">
                    <input type="password" name="password_confirmation" id="password_confirmation_input" required
                        class="input-floating font-bold tracking-widest"
                        placeholder=" ">
                    <label class="floating-label">Confirmer le mot de passe</label>
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation_input')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <i class="far fa-eye" id="icon-password_confirmation_input"></i>
                    </button>
                </div>

                <!-- Bouton -->
                <div class="pt-2">
                    <button type="submit" id="submit-btn" class="w-full bg-custom-blue text-white py-3.5 rounded-xl text-sm font-bold hover:bg-[#062b42] transition shadow-lg shadow-blue-900/10 uppercase tracking-wider">
                        Réinitialiser le mot de passe
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
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

        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
                document.body.appendChild(container);
            }
            let colors;
            switch(type) {
                case 'success': colors = 'bg-[#083e5f] text-white'; break;
                case 'error': colors = 'bg-red-500 text-white'; break;
                default: colors = 'bg-[#083e5f] text-white';
            }
            const toast = document.createElement('div');
            toast.className = `${colors} px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-500 translate-y-10 opacity-0 pointer-events-auto min-w-[300px]`;
            toast.innerHTML = `<p class="text-sm font-bold">${message}</p>`;
            container.appendChild(toast);
            requestAnimationFrame(() => { toast.classList.remove('translate-y-10', 'opacity-0'); });
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        const form = document.getElementById('reset-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.innerText;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Chargement...';

                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Mot de passe réinitialisé !', 'success');
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.history.back();
                            }
                        }, 2000);
                    } else {
                        const errorMsg = data.errors && data.errors.length > 0 
                            ? data.errors.join(' ') 
                            : data.message || 'Erreur lors de la réinitialisation';
                        showToast(errorMsg, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalText;
                    }
                })
                .catch(error => {
                    showToast('Erreur de connexion. Veuillez réessayer.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                });
            });
        }
    </script>
</body>
</html>
