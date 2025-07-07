<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

Auth::requireAuth('tensiocare');
$user = Auth::getCurrentUser();
$role = $user['role'];

// Données de démonstration
$demo_measurements = [
    ['systolic' => 128, 'diastolic' => 82, 'date' => '2025-01-07 09:30', 'context' => 'Matin, à jeun'],
    ['systolic' => 145, 'diastolic' => 88, 'date' => '2025-01-06 14:15', 'context' => 'Après repas'],
    ['systolic' => 135, 'diastolic' => 85, 'date' => '2025-01-05 19:20', 'context' => 'Soir, repos'],
    ['systolic' => 142, 'diastolic' => 90, 'date' => '2025-01-04 08:45', 'context' => 'Matin, stress'],
    ['systolic' => 130, 'diastolic' => 78, 'date' => '2025-01-03 16:00', 'context' => 'Après-midi']
];

$latest_measurement = $demo_measurements[0];
$classification = classify_blood_pressure($latest_measurement['systolic'], $latest_measurement['diastolic']);

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $response = ['success' => true, 'message' => ''];
        
        switch ($_POST['action']) {
            case 'add_measurement':
                $response['message'] = 'Mesure ajoutée avec succès';
                break;
            case 'add_medication':
                $response['message'] = 'Médicament ajouté avec succès';
                break;
            case 'send_message':
                $response['message'] = 'Message envoyé au médecin';
                break;
            case 'add_payment':
                $response['message'] = 'Paiement ajouté avec succès';
                break;
            default:
                $response['success'] = false;
                $response['message'] = 'Action inconnue';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TensioCare - Dashboard <?php echo ucfirst($role); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'tensio-blue': '#1e40af',
                        'tensio-light': '#eff6ff'
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gray-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar" class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex-1 flex flex-col min-h-0 bg-tensio-blue">
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0 px-4 mb-6">
                        <a href="../index.php" class="flex items-center">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-tensio-blue" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <span class="text-white text-lg font-semibold">TensioCare</span>
                        </a>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <?php if ($role === 'patient'): ?>
                            <a href="#" class="bg-white/10 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                Dashboard
                            </a>
                            <a href="measurements.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                                Mes Mesures
                            </a>
                            <a href="medications.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6.5 10.5c0 1.1.9 2 2 2s2-.9 2-2-.9-2-2-2-2 .9-2 2zm6 6c0 1.1.9 2 2 2s2-.9 2-2-.9-2-2-2-2 .9-2 2z"/>
                                </svg>
                                Médicaments
                            </a>
                            <a href="communication.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Communication
                            </a>
                            <a href="subscription.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                Abonnement
                            </a>
                        <?php elseif ($role === 'doctor'): ?>
                            <a href="#" class="bg-white/10 text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                Dashboard
                            </a>
                            <a href="patients.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-7.5c0-.83.67-1.5 1.5-1.5S12 9.67 12 10.5V18h2v-4h3v4h4v2H4v-2z"/>
                                </svg>
                                Mes Patients
                            </a>
                            <a href="consultations.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                                Consultations
                            </a>
                            <a href="prescriptions.php" class="text-white hover:bg-white/10 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h8c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                </svg>
                                Prescriptions
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                
                <!-- User info -->
                <div class="flex-shrink-0 flex bg-white/10 p-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                            <span class="text-tensio-blue font-semibold"><?php echo strtoupper($user['username'][0]); ?></span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></p>
                            <p class="text-xs text-white/70"><?php echo ucfirst($role); ?></p>
                        </div>
                    </div>
                    <a href="logout.php" class="ml-auto text-white hover:text-gray-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div id="mobile-sidebar" class="fixed inset-0 flex z-40 md:hidden hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleMobileSidebar()"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-tensio-blue">
                <!-- Contenu identique au sidebar desktop -->
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button onclick="toggleMobileSidebar()" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <span class="text-white text-lg font-semibold">TensioCare</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <!-- Navigation identique -->
                    </nav>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 overflow-hidden">
            <div class="md:hidden">
                <div class="flex items-center justify-between bg-white shadow-sm px-4 py-2">
                    <button onclick="toggleMobileSidebar()" class="text-gray-500 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold">TensioCare</h1>
                    <div class="w-6"></div>
                </div>
            </div>

            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- En-tête -->
                        <div class="mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">
                                Dashboard <?php echo $role === 'patient' ? 'Patient' : ($role === 'doctor' ? 'Médecin' : 'Admin'); ?>
                            </h1>
                            <p class="text-gray-600">Surveillance de la tension artérielle</p>
                        </div>

                        <?php if ($role === 'patient'): ?>
                            <!-- Dashboard Patient -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Dernière mesure -->
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-<?php echo $classification['color']; ?>-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-<?php echo $classification['color']; ?>-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Dernière mesure</dt>
                                                    <dd class="text-lg font-medium text-gray-900">
                                                        <?php echo $latest_measurement['systolic']; ?>/<?php echo $latest_measurement['diastolic']; ?> mmHg
                                                    </dd>
                                                    <dd class="text-sm text-<?php echo $classification['color']; ?>-600 font-medium">
                                                        <?php echo $classification['label']; ?>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions rapides -->
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
                                        <div class="space-y-3">
                                            <button onclick="openModal('addMeasurementModal')" class="w-full bg-tensio-blue text-white px-4 py-2 rounded-md hover:bg-blue-800 transition-colors">
                                                Ajouter une mesure
                                            </button>
                                            <button onclick="openModal('calendarModal')" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                                                Voir le calendrier
                                            </button>
                                            <button onclick="openModal('contactDoctorModal')" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                                Contacter le médecin
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tendance -->
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tendance (7 derniers jours)</h3>
                                        <canvas id="trendChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($role === 'doctor'): ?>
                            <!-- Dashboard Médecin -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-7.5c0-.83.67-1.5 1.5-1.5S12 9.67 12 10.5V18h2v-4h3v4h4v2H4v-2z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Patients actifs</dt>
                                                    <dd class="text-lg font-medium text-gray-900">24</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Alertes critiques</dt>
                                                    <dd class="text-lg font-medium text-gray-900">3</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Consultations aujourd'hui</dt>
                                                    <dd class="text-lg font-medium text-gray-900">8</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h8c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Prescriptions en attente</dt>
                                                    <dd class="text-lg font-medium text-gray-900">12</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modals pour Patient -->
    <?php if ($role === 'patient'): ?>
        <!-- Modal Ajouter une mesure -->
        <div id="addMeasurementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter une mesure</h3>
                    <form onsubmit="submitForm(event, 'add_measurement')">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Systolique (mmHg)</label>
                            <input type="number" name="systolic" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diastolique (mmHg)</label>
                            <input type="number" name="diastolic" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contexte</label>
                            <select name="context" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option>Matin, à jeun</option>
                                <option>Après repas</option>
                                <option>Soir, repos</option>
                                <option>Stress</option>
                                <option>Effort physique</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('addMeasurementModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-tensio-blue text-white rounded-md hover:bg-blue-800">
                                Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Calendrier -->
        <div id="calendarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Calendrier des mesures</h3>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Prochaines mesures programmées :</p>
                        <ul class="space-y-2">
                            <li class="bg-blue-50 p-2 rounded">Demain 9h00 - Mesure matinale</li>
                            <li class="bg-blue-50 p-2 rounded">Demain 18h00 - Mesure soirée</li>
                        </ul>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button onclick="closeModal('calendarModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Contact médecin -->
        <div id="contactDoctorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contacter le médecin</h3>
                    <form onsubmit="submitForm(event, 'send_message')">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Objet</label>
                            <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('contactDoctorModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Gestion des modals
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Gestion du sidebar mobile
        function toggleMobileSidebar() {
            document.getElementById('mobile-sidebar').classList.toggle('hidden');
        }

        // Soumission des formulaires
        async function submitForm(event, action) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', action);

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    closeModal(event.target.closest('.fixed').id);
                    location.reload();
                } else {
                    alert('Erreur: ' + result.message);
                }
            } catch (error) {
                alert('Erreur de communication avec le serveur');
            }
        }

        // Graphique de tendance
        <?php if ($role === 'patient'): ?>
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'J-1', 'Aujourd\'hui'],
                datasets: [{
                    label: 'Systolique',
                    data: [135, 140, 142, 138, 145, 135, <?php echo $latest_measurement['systolic']; ?>],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Diastolique',
                    data: [85, 88, 90, 83, 88, 85, <?php echo $latest_measurement['diastolic']; ?>],
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 60,
                        max: 200
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>