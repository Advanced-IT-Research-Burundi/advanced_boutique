
<style>
    /* Styles existants + améliorations pour le loading */
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .product-card:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.02);
        transform: translateY(-2px);
    }

    .cart-item:hover {
        background-color: rgba(var(--bs-light), 0.5);
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, var(--bs-primary), #0056b3);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, var(--bs-success), #20c997);
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, var(--bs-info), #17a2b8);
    }

    /* Loading states */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 0.375rem;
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 1rem;
        border-radius: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .skeleton-text:last-child {
        margin-bottom: 0;
    }

    .skeleton-product {
        height: 100px;
        border-radius: 0.375rem;
    }

    /* Amélioration des transitions */
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-up {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Spinner personnalisé */
    .spinner-border-custom {
        width: 1.5rem;
        height: 1.5rem;
        border-width: 0.2em;
    }

    /* States pour les boutons de catégorie */
    .category-btn {
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .category-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .category-btn.active {
        background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    /* Amélioration du scroll */
    .smooth-scroll {
        scroll-behavior: smooth;
    }

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(13, 110, 253, 0.3) transparent;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(13, 110, 253, 0.3);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: rgba(13, 110, 253, 0.5);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .hover-lift:hover {
            transform: none;
        }

        .product-card:hover {
            transform: none;
        }

        .category-btn:hover {
            transform: none;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.25rem 0.4rem;
            font-size: 0.8rem;
        }
    }

    /* Loading state pour les cartes */
    .card-loading {
        position: relative;
    }

    .card-loading::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(1px);
        z-index: 5;
        border-radius: inherit;
    }

    .card-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 2rem;
        height: 2rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid var(--bs-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 6;
    }

    @keyframes spin {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    /* Pulse effect pour les éléments interactifs */
    .pulse-on-load {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    /* Amélioration des badges */
    .badge-pulse {
        animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        }

        50% {
            box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
        }
    }

    .badge-sm {
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
    }

    /* Optimisation pour les transitions entre états */
    .content-transition {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .content-fade-out {
        opacity: 0;
        transform: translateY(-10px);
    }

    .content-fade-in {
        opacity: 1;
        transform: translateY(0);
    }

</style>
