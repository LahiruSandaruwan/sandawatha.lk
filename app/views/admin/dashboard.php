<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /login.php');
    exit();
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Report.php';
require_once __DIR__ . '/../../models/Stats.php';

// Initialize models
$userModel = new User($db);
$reportModel = new Report($db);
$statsModel = new Stats($db);

// Get statistics
$totalUsers = $userModel->getTotalUsers();
$pendingVerifications = $userModel->getPendingVerificationsCount();
$activeReports = $reportModel->getActiveReportsCount();
$newUsersToday = $userModel->getNewUsersCount('today');
$newUsersWeek = $userModel->getNewUsersCount('week');
$newUsersMonth = $userModel->getNewUsersCount('month');

// Get gender distribution
$genderStats = $statsModel->getGenderDistribution();

// Get age distribution
$ageStats = $statsModel->getAgeDistribution();

// Get monthly registrations for the past 6 months
$monthlyRegistrations = $statsModel->getMonthlyRegistrations(6);

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'dashboard' => 'Admin Dashboard',
        'total_users' => 'Total Users',
        'pending_verifications' => 'Pending Verifications',
        'active_reports' => 'Active Reports',
        'new_users_today' => 'New Users Today',
        'new_users_week' => 'New Users This Week',
        'new_users_month' => 'New Users This Month',
        'gender_distribution' => 'Gender Distribution',
        'age_distribution' => 'Age Distribution',
        'monthly_registrations' => 'Monthly Registrations',
        'view_all' => 'View All',
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other'
    ],
    // Add Sinhala and Tamil translations here
];

// Get translations for current language
$t = $translations[$currentLang];

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= htmlspecialchars($t['dashboard']) ?>
        </h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= htmlspecialchars($t['total_users']) ?>
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    <?= number_format($totalUsers) ?>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <a href="/admin/users.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        <?= htmlspecialchars($t['view_all']) ?> <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Verifications Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= htmlspecialchars($t['pending_verifications']) ?>
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    <?= number_format($pendingVerifications) ?>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <a href="/admin/verifications.php" class="font-medium text-yellow-600 hover:text-yellow-500">
                        <?= htmlspecialchars($t['view_all']) ?> <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Reports Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                <?= htmlspecialchars($t['active_reports']) ?>
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    <?= number_format($activeReports) ?>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <div class="text-sm">
                    <a href="/admin/reports.php" class="font-medium text-red-600 hover:text-red-500">
                        <?= htmlspecialchars($t['view_all']) ?> <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gender Distribution Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <?= htmlspecialchars($t['gender_distribution']) ?>
            </h2>
            <div class="relative h-64">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <!-- Age Distribution Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <?= htmlspecialchars($t['age_distribution']) ?>
            </h2>
            <div class="relative h-64">
                <canvas id="ageChart"></canvas>
            </div>
        </div>

        <!-- Monthly Registrations Chart -->
        <div class="bg-white rounded-lg shadow-lg p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <?= htmlspecialchars($t['monthly_registrations']) ?>
            </h2>
            <div class="relative h-80">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize charts when document is ready
$(document).ready(function() {
    // Gender Distribution Chart
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: [
                '<?= $t['male'] ?>', 
                '<?= $t['female'] ?>', 
                '<?= $t['other'] ?>'
            ],
            datasets: [{
                data: [
                    <?= $genderStats['male'] ?>, 
                    <?= $genderStats['female'] ?>, 
                    <?= $genderStats['other'] ?>
                ],
                backgroundColor: [
                    '#4F46E5',
                    '#EC4899',
                    '#6B7280'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Age Distribution Chart
    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(<?= json_encode($ageStats) ?>),
            datasets: [{
                label: 'Users',
                data: Object.values(<?= json_encode($ageStats) ?>),
                backgroundColor: '#4F46E5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Monthly Registrations Chart
    new Chart(document.getElementById('registrationsChart'), {
        type: 'line',
        data: {
            labels: Object.keys(<?= json_encode($monthlyRegistrations) ?>),
            datasets: [{
                label: 'New Registrations',
                data: Object.values(<?= json_encode($monthlyRegistrations) ?>),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 