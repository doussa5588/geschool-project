<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeSchool - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Développé par SADOU MBALLO - Responsable GeSchool */
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card login-card">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="text-primary">GeSchool</h2>
                                <p class="text-muted">Système de Gestion Scolaire</p>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <p class="mb-0">{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
                            </form>

                            <div class="text-center">
                                <a href="{{ route('register') }}" class="text-decoration-none">Créer un compte</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>