/**
 * ZoneX SuperAdmin - Main JavaScript
 * Design System Interactions
 */

(function () {
    'use strict';

    // ═══════════════════════════════════════
    // Theme Toggle (Dark/Light Mode)
    // ═══════════════════════════════════════

    const themeToggle = () => {
        const html = document.documentElement;
        const sunIcon = document.querySelector('.fa-sun');
        const moonIcon = document.querySelector('.fa-moon');

        // Check for saved theme preference or system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            html.classList.add('dark');
            if (sunIcon) sunIcon.classList.add('hidden');
            if (moonIcon) moonIcon.classList.remove('hidden');
        } else {
            html.classList.remove('dark');
            if (sunIcon) sunIcon.classList.remove('hidden');
            if (moonIcon) moonIcon.classList.add('hidden');
        }
    };

    window.toggleTheme = function () {
        const html = document.documentElement;
        const sunIcon = document.querySelector('.fa-sun');
        const moonIcon = document.querySelector('.fa-moon');

        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            if (sunIcon) sunIcon.classList.remove('hidden');
            if (moonIcon) moonIcon.classList.add('hidden');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            if (sunIcon) sunIcon.classList.add('hidden');
            if (moonIcon) moonIcon.classList.remove('hidden');
        }
    };

    // ═══════════════════════════════════════
    // Sidebar Toggle (Mobile)
    // ═══════════════════════════════════════

    const sidebarToggle = () => {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleBtn = document.getElementById('sidebar-toggle');

        if (sidebar && mainContent && toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                mainContent.classList.toggle('ml-0');
                mainContent.classList.toggle('ml-64');
            });
        }
    };

    // ═══════════════════════════════════════
    // Active Navigation Highlight
    // ═══════════════════════════════════════

    const setActiveNav = () => {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href)) {
                link.classList.add('active');
            }
        });
    };

    // ═══════════════════════════════════════
    // Confirmation Dialogs
    // ═══════════════════════════════════════

    const initConfirmations = () => {
        const confirmButtons = document.querySelectorAll('[data-confirm]');

        confirmButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = btn.dataset.confirm || 'Êtes-vous sûr de vouloir effectuer cette action ?';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    };

    // ═══════════════════════════════════════
    // Toast Notifications
    // ═══════════════════════════════════════

    window.showToast = function (message, type = 'info', duration = 5000) {
        const container = document.getElementById('toast-container') || createToastContainer();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type} animate-slide-up`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas fa-${getToastIcon(type)}"></i>
                <span>${message}</span>
                <button class="toast-close ml-auto" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('animate-slide-down');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
    }

    function getToastIcon(type) {
        const icons = {
            success: 'check-circle text-green-500',
            error: 'exclamation-circle text-red-500',
            warning: 'exclamation-triangle text-yellow-500',
            info: 'info-circle text-blue-500'
        };
        return icons[type] || icons.info;
    }

    // ═══════════════════════════════════════
    // Modal Management
    // ═══════════════════════════════════════

    window.openModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closeModal = function (modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };

    // Close modal on backdrop click
    const initModals = () => {
        const modals = document.querySelectorAll('[id^="modal-"]');
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });

        // Close buttons
        const closeBtns = document.querySelectorAll('.modal-close');
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('[id^="modal-"]');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    };

    // ═══════════════════════════════════════
    // Form Validation
    // ═══════════════════════════════════════

    const initFormValidation = () => {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const invalidFields = form.querySelectorAll(':invalid');

                if (invalidFields.length > 0) {
                    e.preventDefault();

                    // Focus first invalid field
                    invalidFields[0].focus();

                    // Show error toast
                    showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
                }
            });

            // Add visual feedback
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    if (input.validity.valid) {
                        input.classList.remove('border-red-500');
                    } else {
                        input.classList.add('border-red-500');
                    }
                });
            });
        });
    };

    // ═══════════════════════════════════════
    // Data Tables Enhancements
    // ═══════════════════════════════════════

    const initDataTables = () => {
        const tables = document.querySelectorAll('.table-container');

        tables.forEach(container => {
            const table = container.querySelector('.table');
            if (!table) return;

            // Search functionality
            const searchInput = container.querySelector('.table-search');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const term = e.target.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            }

            // Sort functionality
            const headers = table.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    const direction = header.dataset.direction === 'asc' ? 'desc' : 'asc';

                    // Update header classes
                    headers.forEach(h => {
                        h.dataset.direction = '';
                        h.classList.remove('sorted-asc', 'sorted-desc');
                    });

                    header.dataset.direction = direction;
                    header.classList.add(`sorted-${direction}`);

                    // Sort rows
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    rows.sort((a, b) => {
                        const aVal = a.querySelector(`td[data-col="${column}"]`)?.textContent || '';
                        const bVal = b.querySelector(`td[data-col="${column}"]`)?.textContent || '';

                        return direction === 'asc'
                            ? aVal.localeCompare(bVal)
                            : bVal.localeCompare(aVal);
                    });

                    tbody.append(...rows);
                });
            });
        });
    };

    // ═══════════════════════════════════════
    // Loading States
    // ═══════════════════════════════════════

    window.setLoading = function (button, loading) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Chargement...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalContent;
        }
    };

    // ═══════════════════════════════════════
    // AJAX Helper
    // ═══════════════════════════════════════

    window.ajaxRequest = async function (url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            ...options
        };

        try {
            const response = await fetch(url, defaultOptions);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Une erreur est survenue');
            }

            return data;
        } catch (error) {
            console.error('AJAX Error:', error);
            showToast(error.message, 'error');
            throw error;
        }
    };

    // ═══════════════════════════════════════
    // Initialize on DOM Ready
    // ═══════════════════════════════════════

    const init = () => {
        themeToggle();
        sidebarToggle();
        setActiveNav();
        initConfirmations();
        initModals();
        initFormValidation();
        initDataTables();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ═══════════════════════════════════════
    // Utility Functions
    // ═══════════════════════════════════════

    window.formatCurrency = function (amount, currency = 'CFA') {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 0
        }).format(amount);
    };

    window.formatDate = function (date, format = 'short') {
        const d = new Date(date);
        const options = format === 'short'
            ? { day: 'numeric', month: 'short', year: 'numeric' }
            : { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };

        return d.toLocaleDateString('fr-FR', options);
    };

    window.truncateText = function (text, maxLength = 50) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    };

})();
