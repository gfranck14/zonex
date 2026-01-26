tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: { sans: ['Outfit', 'sans-serif'] },
            colors: {
                brand: {
                    // On pointe vers les variables CSS définies dans style.css
                    // Ainsi, Tailwind utilise automatiquement la bonne couleur selon le mode Light/Dark
                    
                    sidebarLight: 'var(--bg-sidebar)', // Sert pour sidebar
                    sidebarDark:  'var(--bg-sidebar)',
                    
                    bgLight: 'var(--bg-main)',         // Sert pour le fond body
                    bgDark:  'var(--bg-main)',
                    
                    cardLight: '#F8FAFC', // Blanc cassé très doux (au lieu de #FFFFFF)
                    cardDark:  'var(--bg-card)',
                    
                    blue: '#0EA5E9',  // Cyan fixe
                    green: '#84CC16', // Vert fixe
                    accent: '#F59E0B'
                }
            },
            borderRadius: {
                '3xl': '1.5rem',
                '4xl': '2rem'
            }
        }
    }
}