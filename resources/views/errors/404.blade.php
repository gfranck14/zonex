<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page introuvable | WiFiProfit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-dark: #072b47;
            --bg-darker: #041c30;
            --brand-blue: #0EA5E9;
            --brand-green: #84CC16;
            --text-white: #F1F5F9;
            --text-muted: #94A3B8;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            color: var(--text-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* --- Background Ambiance --- */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(14, 165, 233, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(14, 165, 233, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 0;
            animation: float 12s ease-in-out infinite;
        }
        .bg-orb--blue {
            width: 500px; height: 500px;
            background: var(--brand-blue);
            top: -10%; left: -5%;
            animation-delay: 0s;
        }
        .bg-orb--green {
            width: 400px; height: 400px;
            background: var(--brand-green);
            bottom: -15%; right: -10%;
            animation-delay: -4s;
        }
        .bg-orb--purple {
            width: 300px; height: 300px;
            background: #7c3aed;
            top: 60%; left: 50%;
            animation-delay: -8s;
            opacity: 0.08;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 15px) scale(0.95); }
        }

        /* --- Particles --- */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            background: var(--brand-blue);
            border-radius: 50%;
            opacity: 0;
            animation: drift 8s linear infinite;
        }

        @keyframes drift {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        /* --- Main Content --- */
        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 560px;
            padding: 2rem;
        }

        /* WiFi Signal Animation */
        .signal-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2.5rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signal-icon__ring {
            position: absolute;
            border: 2px solid rgba(14, 165, 233, 0.15);
            border-radius: 50%;
            animation: ring-pulse 3s ease-out infinite;
        }
        .signal-icon__ring:nth-child(1) { width: 120px; height: 120px; animation-delay: 0s; }
        .signal-icon__ring:nth-child(2) { width: 90px; height: 90px; animation-delay: 0.4s; }
        .signal-icon__ring:nth-child(3) { width: 60px; height: 60px; animation-delay: 0.8s; }

        @keyframes ring-pulse {
            0% { opacity: 0.6; transform: scale(0.8); border-color: rgba(14, 165, 233, 0.3); }
            50% { opacity: 0.2; transform: scale(1.2); border-color: rgba(14, 165, 233, 0.05); }
            100% { opacity: 0.6; transform: scale(0.8); border-color: rgba(14, 165, 233, 0.3); }
        }

        .signal-icon__core {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 30px rgba(14, 165, 233, 0.3),
                0 0 60px rgba(14, 165, 233, 0.1);
            position: relative;
            z-index: 2;
            animation: core-float 4s ease-in-out infinite;
        }
        .signal-icon__core i {
            font-size: 1.4rem;
            color: white;
        }
        .signal-icon__line {
            position: absolute;
            width: 2px;
            height: 20px;
            background: linear-gradient(to bottom, var(--brand-blue), transparent);
            top: 50%;
            left: 50%;
            transform-origin: center;
            opacity: 0;
            animation: line-shoot 4s ease-out infinite;
        }
        .signal-icon__line:nth-child(5) { animation-delay: 0s; transform: rotate(0deg) translateY(-50px); }
        .signal-icon__line:nth-child(6) { animation-delay: 0.5s; transform: rotate(60deg) translateY(-50px); }
        .signal-icon__line:nth-child(7) { animation-delay: 1s; transform: rotate(120deg) translateY(-50px); }
        .signal-icon__line:nth-child(8) { animation-delay: 1.5s; transform: rotate(180deg) translateY(-50px); }
        .signal-icon__line:nth-child(9) { animation-delay: 2s; transform: rotate(240deg) translateY(-50px); }
        .signal-icon__line:nth-child(10) { animation-delay: 2.5s; transform: rotate(300deg) translateY(-50px); }

        @keyframes line-shoot {
            0% { opacity: 0; height: 0; }
            20% { opacity: 0.5; height: 20px; }
            100% { opacity: 0; height: 0; }
        }

        @keyframes core-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        /* 404 Title */
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-green) 50%, var(--brand-blue) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s linear infinite;
            letter-spacing: -4px;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-white);
        }

        .error-description {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.9rem 2rem;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
        }

        .btn--primary {
            background: linear-gradient(135deg, var(--brand-blue), #0284C7);
            color: white;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);
        }
        .btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(14, 165, 233, 0.4);
        }
        .btn--primary:active { transform: translateY(0); }

        .btn--ghost {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .btn--ghost:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-white);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Footer hint */
        .footer-hint {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: rgba(148, 163, 184, 0.5);
        }
        .footer-hint i {
            font-size: 0.65rem;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        /* Entrance animation */
        .container {
            animation: entrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        @keyframes entrance {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .error-code { font-size: 5rem; letter-spacing: -2px; }
            .error-title { font-size: 1.2rem; }
            .error-description { font-size: 0.9rem; }
            .actions { flex-direction: column; align-items: stretch; }
            .btn { justify-content: center; }
            .signal-icon { width: 90px; height: 90px; margin-bottom: 2rem; }
            .signal-icon__ring:nth-child(1) { width: 90px; height: 90px; }
            .signal-icon__ring:nth-child(2) { width: 70px; height: 70px; }
            .signal-icon__ring:nth-child(3) { width: 50px; height: 50px; }
            .signal-icon__core { width: 40px; height: 40px; }
            .signal-icon__core i { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-grid"></div>
    <div class="bg-orb bg-orb--blue"></div>
    <div class="bg-orb bg-orb--green"></div>
    <div class="bg-orb bg-orb--purple"></div>

    <!-- Floating Particles -->
    <div class="particles" id="particles"></div>

    <!-- Main Content -->
    <div class="container">
        <!-- Animated WiFi Signal Icon -->
        <div class="signal-icon">
            <div class="signal-icon__ring"></div>
            <div class="signal-icon__ring"></div>
            <div class="signal-icon__ring"></div>
            <div class="signal-icon__core">
                <i class="fas fa-wifi"></i>
            </div>
            <div class="signal-icon__line"></div>
            <div class="signal-icon__line"></div>
            <div class="signal-icon__line"></div>
            <div class="signal-icon__line"></div>
            <div class="signal-icon__line"></div>
            <div class="signal-icon__line"></div>
        </div>

        <!-- Error Code -->
        <h1 class="error-code">404</h1>

        <!-- Title -->
        <h2 class="error-title">Signal perdu</h2>

        <!-- Description -->
        <p class="error-description">
            La page que vous recherchez n'est pas disponible. 
            Elle a peut-être été déplacée ou n'existe plus.
        </p>

        <!-- Action Buttons -->
        <div class="actions">
            <a href="{{ url('/dashboard') }}" class="btn btn--primary">
                <i class="fas fa-home"></i>
                Retour au Dashboard
            </a>
            <button onclick="history.back()" class="btn btn--ghost">
                <i class="fas fa-arrow-left"></i>
                Page précédente
            </button>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-hint">
        <i class="fas fa-circle"></i>
        WiFiProfit &mdash; Signal perdu sur cette fréquence
    </div>

    <!-- Particle Generator -->
    <script>
        (function() {
            const container = document.getElementById('particles');
            const count = 25;
            for (let i = 0; i < count; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = (6 + Math.random() * 6) + 's';
                p.style.animationDelay = Math.random() * 8 + 's';
                p.style.width = p.style.height = (2 + Math.random() * 3) + 'px';
                if (Math.random() > 0.5) p.style.background = '#84CC16';
                container.appendChild(p);
            }
        })();
    </script>
</body>
</html>
