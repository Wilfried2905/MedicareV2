<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

start_secure_session();

// Redirection si déjà connecté en tant qu'admin
if (Auth::isLoggedIn('tensiocare') && Auth::getCurrentUser()['role'] === 'admin') {
    header('Location: admin-dashboard.php');
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
        
        if ($result['success'] && $result['user']['role'] === 'admin') {
            header('Location: admin-dashboard.php');
            exit;
        } else {
            $error_message = 'Accès administrateur refusé. Vérifiez vos identifiants.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TensioCare - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'tensio-blue': '#1e40af',
                        'admin-red': '#dc2626'
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-red-50 via-white to-red-100">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="../index.php" class="inline-flex items-center text-admin-red hover:text-red-800 transition-colors mb-4">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7v4H5.83l3.58-3.59L8 6l-6 6 6 6 1.41-1.41L5.83 13H21V7z"/>
                    </svg>
                    <span class="hidden sm:inline">Retour à l'accueil</span>
                    <span class="sm:hidden">Retour</span>
                </a>
                
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-admin-red rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Administration TensioCare</h1>
                <p class="text-gray-600 mt-2">Accès sécurisé administrateur</p>
            </div>

            <!-- Formulaire -->
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <?php if ($error_message): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom d'utilisateur administrateur
                        </label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-admin-red focus:border-transparent"
                               placeholder="admin"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe administrateur
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-admin-red focus:border-transparent"
                               placeholder="Mot de passe sécurisé">
                    </div>

                    <button type="submit"
                            class="w-full bg-admin-red hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Accéder à l'administration
                    </button>
                </form>

                <!-- Info admin -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <div>
                                <p class="text-yellow-700 text-sm font-medium">Accès administrateur sécurisé</p>
                                <p class="text-yellow-600 text-sm mt-1">Compte démo: admin / admin</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>