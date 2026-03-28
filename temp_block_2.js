
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // S'assurer que le formulaire de pagination conserve bien l'onglet actif
                                        const perPageForm = document.querySelector('select[name="per_page"]').closest('form');
                                        if (perPageForm) {
                                            perPageForm.addEventListener('submit', function(e) {
                                                // Vérifier que le champ tab est bien présent
                                                let tabInput = this.querySelector('input[name="tab"]');
                                                if (!tabInput) {
                                                    // Si le champ tab n'existe pas, l'ajouter
                                                    tabInput = document.createElement('input');
                                                    tabInput.type = 'hidden';
                                                    tabInput.name = 'tab';
                                                    tabInput.value = 'list';
                                                    this.appendChild(tabInput);
                                                }
                                                tabInput.value = 'list'; // Forcer la valeur
                                            });
                                        }
                                        
                                        // Intercepter tous les clics sur les liens de pagination pour conserver l'onglet
                                        const paginationLinks = document.querySelectorAll('a[href*="page="]');
                                        paginationLinks.forEach(link => {
                                            link.addEventListener('click', function(e) {
                                                const url = new URL(this.href);
                                                // S'assurer que le paramètre tab est présent
                                                url.searchParams.set('tab', 'list');
                                                this.href = url.toString();
                                            });
                                        });
                                    });
                                