<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare+ - Écosystème de Santé Digital</title>
    <meta name="description" content="MediCare+ - Plateforme médicale complète avec TensioCare (tension), DiabetoCare (diabète) et Consultations médicales">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'medical-blue': '#2563eb',
                        'medical-red': '#dc2626',
                        'medical-green': '#16a34a'
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
        }
        .service-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="h-full gradient-bg">
    <div class="min-h-screen flex flex-col">
        <!-- Header avec bouton Administration -->
        <header class="bg-white/10 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <!-- Logo MediCare+ -->
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-medical-blue" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-white">MediCare+</h1>
                                <p class="text-sm text-white/70">Écosystème de Santé Digital</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton Administration -->
                    <div class="relative">
                        <button id="adminMenuBtn" class="bg-white/20 hover:bg-white/30 text-white px-3 sm:px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 transition-all">
                            <span class="hidden sm:inline">Administration</span>
                            <span class="sm:hidden">Admin</span>
                        </button>
                        
                        <!-- Menu déroulant Administration -->
                        <div id="adminMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-700">Accès Administration</p>
                            </div>
                            <a href="tensiocare/admin-login.php" class="flex items-center px-4 py-3 hover:bg-blue-50 transition-colors">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <div class="w-4 h-4 bg-blue-600 rounded"></div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">TensioCare Admin</p>
                                    <p class="text-sm text-gray-500">Gestion tension artérielle</p>
                                </div>
                            </a>
                            <a href="diabetocare/admin-login.php" class="flex items-center px-4 py-3 hover:bg-red-50 transition-colors">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <div class="w-4 h-4 bg-red-600 rounded"></div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">DiabetoCare Admin</p>
                                    <p class="text-sm text-gray-500">Gestion diabète</p>
                                </div>
                            </a>
                            <a href="consultations/admin-login.php" class="flex items-center px-4 py-3 hover:bg-green-50 transition-colors">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <div class="w-4 h-4 bg-green-600 rounded"></div>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Consultations Admin</p>
                                    <p class="text-sm text-gray-500">Gestion rendez-vous</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu principal -->
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto text-center">
                <!-- Titre principal -->
                <div class="mb-12">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6">
                        MediCare<span class="text-blue-200">+</span>
                    </h1>
                    <p class="text-xl sm:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                        Plateforme médicale complète pour la télésurveillance et le suivi patient
                    </p>
                </div>

                <!-- Services médicaux -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <!-- TensioCare -->
                    <div class="service-card bg-white rounded-2xl p-6 lg:p-8 shadow-xl">
                        <div class="mb-6">
                            <img src="assets/images/tensiocare-logo.png" alt="TensioCare" class="h-20 sm:h-24 lg:h-32 mx-auto object-contain">
                        </div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">TensioCare</h3>
                        <p class="text-gray-600 mb-6">
                            Surveillance de la tension artérielle avec classification OMS 2023 et suivi médical professionnel
                        </p>
                        <a href="tensiocare/login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block w-full">
                            Accéder à TensioCare
                        </a>
                    </div>

                    <!-- DiabetoCare -->
                    <div class="service-card bg-white rounded-2xl p-6 lg:p-8 shadow-xl">
                        <div class="mb-6">
                            <img src="assets/images/diabetocare-logo.png" alt="DiabetoCare" class="h-20 sm:h-24 lg:h-32 mx-auto object-contain">
                        </div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">DiabetoCare</h3>
                        <p class="text-gray-600 mb-6">
                            Gestion complète du diabète selon standards ADA 2025 avec suivi glycémique et conseils personnalisés
                        </p>
                        <a href="diabetocare/login.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block w-full">
                            Accéder à DiabetoCare
                        </a>
                    </div>

                    <!-- Consultations -->
                    <div class="service-card bg-white rounded-2xl p-6 lg:p-8 shadow-xl">
                        <div class="mb-6">
                            <img src="assets/images/consultations-logo.png" alt="Consultations" class="h-20 sm:h-24 lg:h-32 mx-auto object-contain">
                        </div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">Consultations</h3>
                        <p class="text-gray-600 mb-6">
                            Plateforme de rendez-vous médicaux et communication sécurisée avec les professionnels de santé
                        </p>
                        <a href="consultations/login.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block w-full">
                            Accéder aux Consultations
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="mt-16 text-white/70 text-sm">
                    <p>&copy; 2025 MediCare+ - Tous droits réservés | Plateforme médicale sécurisée</p>
                </footer>
            </div>
        </main>
    </div>

    <script>
        // Menu administration
        document.getElementById('adminMenuBtn').addEventListener('click', function() {
            const menu = document.getElementById('adminMenu');
            menu.classList.toggle('hidden');
        });

        // Fermer le menu en cliquant ailleurs
        document.addEventListener('click', function(event) {
            const adminBtn = document.getElementById('adminMenuBtn');
            const adminMenu = document.getElementById('adminMenu');
            
            if (!adminBtn.contains(event.target) && !adminMenu.contains(event.target)) {
                adminMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>