<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

start_secure_session();

// Redirection si déjà connecté
if (Auth::isLoggedIn('tensiocare')) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error_message = 'Veuillez remplir tous les champs.';
    } else {
        $result = Auth::authenticate($username, $password, 'tensiocare');
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TensioCare - Connexion</title>
    <meta name="description" content="Connexion à TensioCare - Surveillance de la tension artérielle">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'medical-blue': '#2563eb',
                        'tensio-blue': '#1e40af',
                        'tensio-light': '#eff6ff'
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-blue-100">
    <div class="min-h-full flex">
        <!-- Colonne gauche - Formulaire -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Header avec logo -->
                <div class="text-center mb-8">
                    <a href="../index.php" class="inline-flex items-center text-tensio-blue hover:text-blue-800 transition-colors mb-4">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z"/>
                        </svg>
                        <span class="hidden sm:inline">Retour à l'accueil</span>
                        <span class="sm:hidden">Retour</span>
                    </a>
                    
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-tensio-blue rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">TensioCare</h1>
                    <p class="text-gray-600 mt-2">Surveillance de la tension artérielle</p>
                </div>

                <!-- Formulaire de connexion -->
                <div class="bg-white rounded-xl shadow-xl p-6 lg:p-8">
                    <form method="POST" class="space-y-6">
                        <?php if ($error_message): ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom d'utilisateur
                            </label>
                            <input type="text" id="username" name="username" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-tensio-blue focus:border-transparent"
                                   placeholder="Entrez votre nom d'utilisateur"
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Mot de passe
                            </label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-tensio-blue focus:border-transparent"
                                   placeholder="Entrez votre mot de passe">
                        </div>

                        <button type="submit"
                                class="w-full bg-tensio-blue hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                            Se connecter
                        </button>
                    </form>

                    <!-- Comptes de démonstration -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Comptes de démonstration :</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Patient :</span>
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded">patient / patient</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Médecin :</span>
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded">medecin / medecin</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Image hero (cachée sur mobile) -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 bg-gradient-to-br from-tensio-blue to-blue-800 flex items-center justify-center">
                <div class="text-center text-white p-8">
                    <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                        <svg class="w-20 h-20 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Surveillez votre tension</h2>
                    <p class="text-xl text-blue-100 max-w-md mx-auto">
                        Suivi professionnel selon les standards OMS 2023 avec classification médicale précise
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>