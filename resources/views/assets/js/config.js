tailwind.config = {
    darkMode: 'class', // Active le mode sombre via une classe CSS sur le <html>
    theme: {
        extend: {
            fontFamily: { sans: ['Outfit', 'sans-serif'] },
            colors: {
                // Couleurs de la marque
                brand: {
                    sidebarLight: '#072b47', // Bleu Profond (Light Mode)
                    sidebarDark: '#0f172a',  // Bleu Nuit (Dark Mode)
                    
                    bgLight: '#E2E8F0',      // Fond Blanc cassé/Bleuté
                    bgDark: '#020617',       // Fond Noir Profond
                    
                    cardLight: '#FFFFFF',    // Carte Blanche
                    cardDark: '#1E293B',     // Carte Gris Foncé (Slate 800)
                    
                    blue: '#0EA5E9',         // Cyan Logo (Action)
                    green: '#84CC16',        // Vert Logo (Succès)
                    red: '#EF4444',
                    orange: '#F59E0B'
                }
            },
            borderRadius: {
                '3xl': '1.5rem',
                '4xl': '2rem'
            }
        }
    }
}