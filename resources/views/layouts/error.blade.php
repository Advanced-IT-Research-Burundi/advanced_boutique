<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Erreur') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>


        .error-container {
            min-height: 100vh;
        }

        .error-card {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .error-icon {
            font-size: 6rem;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            .error-icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid error-container d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card error-card">
                    <div class="card-body text-center py-5">
                        @yield('content')

                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary btn-lg me-3">
                                <i class="bi bi-house-door me-2"></i>
                                Retour à l'accueil
                            </a>
                            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>
                                Retour
                            </button>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                Si le problème persiste, contactez l'administrateur système.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
