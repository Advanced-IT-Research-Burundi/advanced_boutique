<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - StockManager Pro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .logo-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .login-body {
            padding: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-floating .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: #2E7DB8;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
            transform: translateY(-2px);
        }

        .form-floating label {
            padding-left: 3rem;
            color: #6b7280;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 4;
            font-size: 1.1rem;
        }

        .btn-login {
            background: linear-gradient(135deg, #2E7DB8 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0;
            font-size: 0.9rem;
        }

        .form-check-input:checked {
            background-color: #2E7DB8;
            border-color: #2E7DB8;
        }

        .forgot-link {
            color: #2E7DB8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }



        .loading-spinner {
            display: none;
            margin-right: 0.5rem;
        }

        .btn-login.loading .loading-spinner {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 10px;
                border-radius: 15px;
            }



            .login-body {
                padding: 1.5rem;
            }

            .logo-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            {{-- Affichage du message d'erreur --}}
            @if (session('errors'))
                <div class="alert alert-danger text-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $message = session('errors')->first() }}
                </div>
            @endif
            <div class="login-body">
                <form id="loginForm" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-floating">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input name="email" type="email" class="form-control" id="email" placeholder="Email" required>
                        <label for="email">Adresse Email</label>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-floating">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input name="password" type="password" class="form-control" id="password" placeholder="Mot de passe" required>
                        <label for="password">Mot de Passe</label>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="remember-forgot">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="#" class="forgot-link">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <span class="loading-spinner">
                            <i class="bi bi-arrow-repeat"></i>
                        </span>
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Se connecter
                        </span>
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.querySelector('.btn-login');
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validation simple
            if (!email || !password) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            // Animation de chargement
            btn.classList.add('loading');
            btn.disabled = true;

            document.getElementById('loginForm').submit();

        // Animation des champs au focus
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Animation de rotation pour l'icône de chargement
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .loading-spinner i {
                animation: spin 1s linear infinite;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
