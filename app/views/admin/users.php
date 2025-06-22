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

// Initialize models
$userModel = new User($db);

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Get filter parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$verificationStatus = $_GET['verification'] ?? 'all';
$sortBy = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'desc';

// Get users with filters
$users = $userModel->getUsers($page, $perPage, [
    'search' => $search,
    'status' => $status,
    'verification' => $verificationStatus,
    'sort' => $sortBy,
    'order' => $sortOrder
]);

$totalUsers = $userModel->getTotalUsersCount([
    'search' => $search,
    'status' => $status,
    'verification' => $verificationStatus
]);

$totalPages = ceil($totalUsers / $perPage);

// Translations
$translations = [
    'en' => [
        'user_management' => 'User Management',
        'search' => 'Search users...',
        'all_users' => 'All Users',
        'active' => 'Active',
        'banned' => 'Banned',
        'pending' => 'Pending',
        'verified' => 'Verified',
        'unverified' => 'Unverified',
        'name' => 'Name',
        'email' => 'Email',
        'status' => 'Status',
        'verification' => 'Verification',
        'joined_date' => 'Joined Date',
        'actions' => 'Actions',
        'view_profile' => 'View Profile',
        'verify_user' => 'Verify User',
        'ban_user' => 'Ban User',
        'unban_user' => 'Unban User',
        'confirm_verify' => 'Are you sure you want to verify this user?',
        'confirm_ban' => 'Are you sure you want to ban this user?',
        'confirm_unban' => 'Are you sure you want to unban this user?',
        'no_users_found' => 'No users found',
        'previous' => 'Previous',
        'next' => 'Next',
        'of' => 'of',
        'apply_filters' => 'Apply Filters',
        'reset_filters' => 'Reset Filters',
        'sort_by' => 'Sort by'
    ],
    // Add Sinhala and Tamil translations here
];

// Get translations for current language
$t = $translations[$currentLang];

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= htmlspecialchars($t['user_management']) ?>
        </h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <form id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text" 
                           name="search" 
                           value="<?= htmlspecialchars($search) ?>"
                           placeholder="<?= htmlspecialchars($t['search']) ?>"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['all_users']) ?>
                        </option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['active']) ?>
                        </option>
                        <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['banned']) ?>
                        </option>
                    </select>
                </div>

                <!-- Verification Filter -->
                <div>
                    <select name="verification" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" <?= $verificationStatus === 'all' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['all_users']) ?>
                        </option>
                        <option value="verified" <?= $verificationStatus === 'verified' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['verified']) ?>
                        </option>
                        <option value="unverified" <?= $verificationStatus === 'unverified' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['unverified']) ?>
                        </option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <select name="sort" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['joined_date']) ?>
                        </option>
                        <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?>
                        </option>
                        <option value="email" <?= $sortBy === 'email' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['email']) ?>
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <?= htmlspecialchars($t['apply_filters']) ?>
                </button>
                <button type="button" 
                        id="resetFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <?= htmlspecialchars($t['reset_filters']) ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['name']) ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['email']) ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['status']) ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['verification']) ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['joined_date']) ?>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?= htmlspecialchars($t['actions']) ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                <?= htmlspecialchars($t['no_users_found']) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="<?= htmlspecialchars($user['profile_photo'] ?? '/images/default-avatar.png') ?>" 
                                                 alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($user['name']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= htmlspecialchars($user['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $user['is_verified'] ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= $user['is_verified'] ? $t['verified'] : $t['unverified'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="/profile/view.php?id=<?= $user['id'] ?>" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <?= htmlspecialchars($t['view_profile']) ?>
                                        </a>
                                        <?php if (!$user['is_verified']): ?>
                                            <button type="button"
                                                    data-action="verify"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    class="text-green-600 hover:text-green-900 action-btn">
                                                <?= htmlspecialchars($t['verify_user']) ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <button type="button"
                                                    data-action="ban"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    class="text-red-600 hover:text-red-900 action-btn">
                                                <?= htmlspecialchars($t['ban_user']) ?>
                                            </button>
                                        <?php else: ?>
                                            <button type="button"
                                                    data-action="unban"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    class="text-green-600 hover:text-green-900 action-btn">
                                                <?= htmlspecialchars($t['unban_user']) ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_filter(['search' => $search, 'status' => $status, 'verification' => $verificationStatus, 'sort' => $sortBy, 'order' => $sortOrder])) ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <?= htmlspecialchars($t['previous']) ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_filter(['search' => $search, 'status' => $status, 'verification' => $verificationStatus, 'sort' => $sortBy, 'order' => $sortOrder])) ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <?= htmlspecialchars($t['next']) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            <?= $page ?> <?= htmlspecialchars($t['of']) ?> <?= $totalPages ?>
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_filter(['search' => $search, 'status' => $status, 'verification' => $verificationStatus, 'sort' => $sortBy, 'order' => $sortOrder])) ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only"><?= htmlspecialchars($t['previous']) ?></span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&<?= http_build_query(array_filter(['search' => $search, 'status' => $status, 'verification' => $verificationStatus, 'sort' => $sortBy, 'order' => $sortOrder])) ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $i === $page ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_filter(['search' => $search, 'status' => $status, 'verification' => $verificationStatus, 'sort' => $sortBy, 'order' => $sortOrder])) ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only"><?= htmlspecialchars($t['next']) ?></span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        const queryString = $(this).serialize();
        window.location.href = '?' + queryString;
    });

    // Handle reset filters
    $('#resetFilters').on('click', function() {
        window.location.href = window.location.pathname;
    });

    // Handle user actions (verify, ban, unban)
    $('.action-btn').on('click', function() {
        const userId = $(this).data('user-id');
        const action = $(this).data('action');
        let confirmMessage = '';

        switch(action) {
            case 'verify':
                confirmMessage = '<?= $t['confirm_verify'] ?>';
                break;
            case 'ban':
                confirmMessage = '<?= $t['confirm_ban'] ?>';
                break;
            case 'unban':
                confirmMessage = '<?= $t['confirm_unban'] ?>';
                break;
        }

        if (confirm(confirmMessage)) {
            $.ajax({
                url: '/admin/actions/user-action.php',
                method: 'POST',
                data: {
                    user_id: userId,
                    action: action,
                    csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 