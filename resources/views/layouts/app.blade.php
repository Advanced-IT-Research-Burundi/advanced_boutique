<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css">
    <!-- Font Awesome (optionnel) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Custom Styles -->



    @stack('styles')
     @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-cpu me-2"></i>
            Advanced IT
        </div>

        <ul class="sidebar-nav list-unstyled">
        <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    Ventes
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('stocks.index') }}" class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    Stocks
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check"></i>
                    Produits
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    Catégories
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="bi bi-person"></i>
                    Clients
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    Fournisseurs
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus"></i>
                    Achats
                </a>
            </li>



            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    Utilisateurs
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('cash-registers.index') }}" class="nav-link {{ request()->routeIs('cash-registers.*') ? 'active' : '' }}">
                    <i class="bi bi-currency-dollar"></i>
                    Caisse
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('cash-transactions.index') }}" class="nav-link {{ request()->routeIs('cash-transactions.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    Transactions
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    Dépenses
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('expense-types.index') }}" class="nav-link {{ request()->routeIs('expense-types.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    Types de dépenses
                </a>
            </li>
        </ul>
    </nav>

    <!-- Overlay pour mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary sidebar-toggle me-3" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>

                <h5 class="mb-0 d-none d-md-block text-primary">@yield('page-title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center ms-auto">
                <button class="btn btn-outline-secondary position-relative me-3" type="button" >
                        <i class="bi bi-cart-plus"></i>

                        <span class="top-0 position-absolute start-100 translate-middle badge rounded-pill bg-danger">
                            {{ Cart::getContent()->count() }}
                            <span class="visually-hidden">notifications aux element du panier</span>
                        </span>
                    </button>
                <!-- Notifications (optionnel) -->
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="top-0 position-absolute start-100 translate-middle badge rounded-pill bg-danger">
                            3
                            <span class="visually-hidden">notifications non lues</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">Nouvelle commande reçue</a></li>
                        <li><a class="dropdown-item" href="#">Stock faible pour le produit X</a></li>
                        <li><a class="dropdown-item" href="#">Paiement en attente</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="text-center dropdown-item" href="#">Voir toutes les notifications</a></li>
                    </ul>
                </div>

                <!-- User Menu -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ Auth::user()->email ?? 'email@example.com' }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') ?? '#' }}">
                                <i class="bi bi-person me-2"></i>Mon Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('parametres')  }}">
                                <i class="bi bi-gear me-2"></i>Paramètres
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Breadcrumb (optionnel) -->
            @if(isset($breadcrumbs) || View::hasSection('breadcrumb'))
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="bi bi-house"></i> Accueil
                            </a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            {{-- <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif --}}

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    @vite(['resources/js/app.js'])

    <script>
        // Fonction améliorée pour afficher les notifications toast
        function showToast(type, message, title = null, duration = 5000) {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            // Déclencher la sonnerie si le type est 'info'
            if (type === 'info') {
                playAlarmSound();
            }

            const toastId = 'toast-' + Date.now();
            const iconMap = {
                'success': 'bi-check-circle-fill',
                'warning': 'bi-exclamation-triangle-fill',
                'danger': 'bi-x-circle-fill',
                'error': 'bi-x-circle-fill',
                'info': 'bi-info-circle-fill'
            };
            const titleMap = {
                'success': 'Succès',
                'warning': 'Attention',
                'danger': 'Erreur',
                'error': 'Erreur',
                'info': 'Information'
            };

            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast modern-toast toast-${type} align-items-center border-0 mb-2`;
            toast.setAttribute('role', 'alert');
            toast.style.minWidth = '300px';

            const toastTitle = title || titleMap[type];
            toast.innerHTML = `
                <div class="d-flex w-100">
                    <div class="toast-body text-white">
                        <div class="d-flex align-items-start">
                            <i class="bi ${iconMap[type]} me-3 mt-1 fs-5"></i>
                            <div class="flex-grow-1">
                                ${toastTitle ? `<div class="fw-semibold mb-1">${toastTitle}</div>` : ''}
                                <div>${message}</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                delay: duration,
                autohide: true
            });

            bsToast.show();

            // Animation d'entrée
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 10);

            toast.addEventListener('hidden.bs.toast', () => {
                toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            });
        }
        // Gestion des messages de session Laravel
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', "{{ session('success') }}", 'Succès');
            @endif

            @if(session('error'))
                showToast('error', "{{ session('error') }}", 'Erreur');
            @endif

            @if(session('warning'))
                showToast('warning', "{{ session('warning') }}", 'Attention');
            @endif

            @if(session('info'))
                showToast('info', "{{ session('info') }}", 'Information');
            @endif

            // Animation au scroll pour le header
            let lastScrollTop = 0;
            const header = document.querySelector('.modern-header');

            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // Scroll vers le bas
                    header.style.transform = 'translateY(-100%)';
                } else {
                    // Scroll vers le haut
                    header.style.transform = 'translateY(0)';
                }
                lastScrollTop = scrollTop;
            });

            // Gestion des dropdowns au hover sur desktop
            if (window.innerWidth > 991) {
                const dropdowns = document.querySelectorAll('.dropdown');

                dropdowns.forEach(dropdown => {
                    let timeout;

                    dropdown.addEventListener('mouseenter', function() {
                        clearTimeout(timeout);
                        const dropdownToggle = this.querySelector('.dropdown-toggle');
                        const dropdownMenu = this.querySelector('.dropdown-menu');

                        if (dropdownToggle && dropdownMenu) {
                            dropdownMenu.classList.add('show');
                            dropdownToggle.classList.add('show');
                            dropdownToggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    dropdown.addEventListener('mouseleave', function() {
                        const dropdownToggle = this.querySelector('.dropdown-toggle');
                        const dropdownMenu = this.querySelector('.dropdown-menu');

                        timeout = setTimeout(() => {
                            if (dropdownToggle && dropdownMenu) {
                                dropdownMenu.classList.remove('show');
                                dropdownToggle.classList.remove('show');
                                dropdownToggle.setAttribute('aria-expanded', 'false');
                            }
                        }, 300);
                    });
                });
            }

            // Animation des liens de navigation
            const navLinks = document.querySelectorAll('.modern-nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Créer un effet ripple
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Notification en temps réel (WebSocket simulation)
            function simulateRealTimeNotifications() {
                const notifications = [
                    { type: 'info', message: 'Nouveau rendez-vous programmé', title: 'Rendez-vous' },
                    { type: 'warning', message: 'Stock faible détecté', title: 'Alerte Stock' },
                    { type: 'success', message: 'Paiement reçu', title: 'Paiement' },
                    { type: 'info', message: 'Nouveau patient enregistré', title: 'Patient' }
                ];

                // Simuler une notification toutes les 30 secondes (à des fins de démonstration)
                setInterval(() => {
                    if (Math.random() < 0.3) { // 30% de chance
                        const randomNotif = notifications[Math.floor(Math.random() * notifications.length)];
                        showToast(randomNotif.type, randomNotif.message, randomNotif.title);

                        // Mettre à jour le badge de notification
                        updateNotificationBadge();
                    }
                }, 30000);
            }

            function updateNotificationBadge() {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    let currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;

                    // Animation du badge
                    badge.style.animation = 'none';
                    badge.offsetHeight; // Trigger reflow
                    badge.style.animation = 'pulse 2s infinite';
                }
            }

            // Activer les notifications en temps réel (décommenter pour la production)
            // simulateRealTimeNotifications();
        });

        // Fonction utilitaire pour les requêtes AJAX avec feedback
        function makeAjaxRequest(url, options = {}) {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            };

            const finalOptions = { ...defaultOptions, ...options };

            return fetch(url, finalOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Ajax request failed:', error);
                    showToast('error', 'Erreur de connexion au serveur', 'Erreur');
                    throw error;
                });
        }

        // Sidebar Toggle for Mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }

            // Close sidebar when clicking on a link (mobile)
            const sidebarLinks = sidebar.querySelectorAll('.nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.classList.contains('show')) {
                        alert.classList.remove('show');
                        setTimeout(() => alert.remove(), 150);
                    }
                }, 5000);
            });
        });

        // Theme Toggle (pour future implémentation du mode sombre)
        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
        }

        // Load saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>

    @stack('scripts')
    @livewireScripts
</body>
</html>
