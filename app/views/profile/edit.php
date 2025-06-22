<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Religion.php';
require_once __DIR__ . '/../../models/Education.php';

// Initialize models
$userModel = new User($db);
$religionModel = new Religion($db);
$educationModel = new Education($db);

// Get user data
$user = $userModel->getById($_SESSION['user_id']);
if (!$user) {
    header('Location: /404.php');
    exit();
}

// Get dropdown options
$religions = $religionModel->getAllReligions();
$educationLevels = $educationModel->getAllLevels();

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'edit_profile' => 'Edit Profile',
        'name' => 'Full Name',
        'dob' => 'Date of Birth',
        'religion' => 'Religion',
        'caste' => 'Caste',
        'education' => 'Education',
        'job' => 'Occupation',
        'marital_status' => 'Marital Status',
        'photo' => 'Profile Photo',
        'current_photo' => 'Current Photo',
        'change_photo' => 'Change Photo',
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
        'required' => 'This field is required',
        'invalid_date' => 'Please enter a valid date',
        'min_age' => 'You must be at least 18 years old',
        'photo_size' => 'Photo size must be less than 5MB',
        'photo_type' => 'Only JPG, JPEG, and PNG files are allowed'
    ],
    // Add Sinhala and Tamil translations here
];

// Get translations for current language
$t = $translations[$currentLang];

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-8"><?= htmlspecialchars($t['edit_profile']) ?></h2>

            <form id="profileEditForm" action="/api/profile/update.php" method="POST" enctype="multipart/form-data">
                <!-- Photo Upload Section -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['photo']) ?>
                    </label>
                    <div class="flex items-center space-x-6">
                        <div class="relative h-32 w-32">
                            <img id="previewImage" 
                                 src="<?= htmlspecialchars($user['profile_photo'] ?? '/assets/images/default-avatar.jpg') ?>" 
                                 alt="Profile photo" 
                                 class="h-32 w-32 rounded-full object-cover">
                            <div id="photoError" class="text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                        <div class="flex flex-col">
                            <input type="file" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/jpeg,image/png" 
                                   class="hidden">
                            <button type="button" 
                                    onclick="document.getElementById('photo').click()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <?= htmlspecialchars($t['change_photo']) ?>
                            </button>
                            <p class="text-xs text-gray-500 mt-2">JPG, JPEG or PNG. Max 5MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['name']) ?> *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= htmlspecialchars($user['name']) ?>" 
                           class="form-input block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                    <div class="text-red-500 text-sm mt-1 hidden" id="nameError"></div>
                </div>

                <!-- Date of Birth Field -->
                <div class="mb-6">
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['dob']) ?> *
                    </label>
                    <input type="date" 
                           id="dob" 
                           name="dob" 
                           value="<?= htmlspecialchars($user['dob']) ?>" 
                           class="form-input block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                    <div class="text-red-500 text-sm mt-1 hidden" id="dobError"></div>
                </div>

                <!-- Religion Field -->
                <div class="mb-6">
                    <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['religion']) ?> *
                    </label>
                    <select id="religion" 
                            name="religion" 
                            class="form-select block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        <option value="">Select Religion</option>
                        <?php foreach ($religions as $religion): ?>
                            <option value="<?= htmlspecialchars($religion['id']) ?>" 
                                    <?= $user['religion_id'] == $religion['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($religion['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-red-500 text-sm mt-1 hidden" id="religionError"></div>
                </div>

                <!-- Caste Field -->
                <div class="mb-6">
                    <label for="caste" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['caste']) ?>
                    </label>
                    <input type="text" 
                           id="caste" 
                           name="caste" 
                           value="<?= htmlspecialchars($user['caste']) ?>" 
                           class="form-input block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                </div>

                <!-- Education Field -->
                <div class="mb-6">
                    <label for="education" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['education']) ?> *
                    </label>
                    <select id="education" 
                            name="education" 
                            class="form-select block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        <option value="">Select Education Level</option>
                        <?php foreach ($educationLevels as $level): ?>
                            <option value="<?= htmlspecialchars($level['id']) ?>" 
                                    <?= $user['education_id'] == $level['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($level['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-red-500 text-sm mt-1 hidden" id="educationError"></div>
                </div>

                <!-- Job Field -->
                <div class="mb-6">
                    <label for="job" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['job']) ?> *
                    </label>
                    <input type="text" 
                           id="job" 
                           name="job" 
                           value="<?= htmlspecialchars($user['occupation']) ?>" 
                           class="form-input block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                    <div class="text-red-500 text-sm mt-1 hidden" id="jobError"></div>
                </div>

                <!-- Marital Status Field -->
                <div class="mb-8">
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= htmlspecialchars($t['marital_status']) ?> *
                    </label>
                    <select id="marital_status" 
                            name="marital_status" 
                            class="form-select block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300">
                        <option value="">Select Marital Status</option>
                        <option value="never_married" <?= $user['marital_status'] == 'never_married' ? 'selected' : '' ?>>Never Married</option>
                        <option value="divorced" <?= $user['marital_status'] == 'divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="widowed" <?= $user['marital_status'] == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="separated" <?= $user['marital_status'] == 'separated' ? 'selected' : '' ?>>Separated</option>
                    </select>
                    <div class="text-red-500 text-sm mt-1 hidden" id="maritalStatusError"></div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="/profile.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?= htmlspecialchars($t['cancel']) ?>
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?= htmlspecialchars($t['save']) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Photo preview
    $('#photo').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                $('#photoError').text('<?= $t['photo_type'] ?>').removeClass('hidden');
                this.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                $('#photoError').text('<?= $t['photo_size'] ?>').removeClass('hidden');
                this.value = '';
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
            $('#photoError').addClass('hidden');
        }
    });

    // Form validation
    $('#profileEditForm').submit(function(e) {
        e.preventDefault();
        let isValid = true;

        // Reset error messages
        $('.text-red-500').addClass('hidden');

        // Validate name
        if (!$('#name').val().trim()) {
            $('#nameError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Validate date of birth
        const dob = new Date($('#dob').val());
        const today = new Date();
        const age = today.getFullYear() - dob.getFullYear();
        
        if (!$('#dob').val()) {
            $('#dobError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        } else if (isNaN(dob.getTime())) {
            $('#dobError').text('<?= $t['invalid_date'] ?>').removeClass('hidden');
            isValid = false;
        } else if (age < 18) {
            $('#dobError').text('<?= $t['min_age'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Validate religion
        if (!$('#religion').val()) {
            $('#religionError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Validate education
        if (!$('#education').val()) {
            $('#educationError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Validate job
        if (!$('#job').val().trim()) {
            $('#jobError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Validate marital status
        if (!$('#marital_status').val()) {
            $('#maritalStatusError').text('<?= $t['required'] ?>').removeClass('hidden');
            isValid = false;
        }

        // Submit form if valid
        if (isValid) {
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        window.location.href = '/profile.php?updated=1';
                    } else {
                        alert(response.message || 'An error occurred while saving your profile.');
                    }
                },
                error: function() {
                    alert('An error occurred while saving your profile. Please try again.');
                }
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 