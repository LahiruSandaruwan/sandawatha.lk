<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Email already registered';
            } else {
                // Insert new user
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, gender, date_of_birth, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $name,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $gender,
                    $date_of_birth
                ]);

                $success = 'Registration successful! You can now login.';
            }
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

// Social login configuration (placeholders)
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => 'YOUR_GOOGLE_CLIENT_ID',
    'redirect_uri' => 'https://sandawatha.lk/auth/google/callback',
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'offline',
    'prompt' => 'consent'
]);

$facebookAuthUrl = 'https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query([
    'client_id' => 'YOUR_FACEBOOK_APP_ID',
    'redirect_uri' => 'https://sandawatha.lk/auth/facebook/callback',
    'scope' => 'email',
    'response_type' => 'code'
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Sandawatha.lk</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'romantic': {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery and Validation -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-romantic-50">
    <!-- Include Navigation -->
    <?php require_once '../app/views/shared/navbar.php'; ?>

    <div class="min-h-screen flex items-center justify-center px-4 py-32">
        <div class="relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-white/30 backdrop-blur-xl rounded-2xl"></div>

            <!-- Registration Form Container -->
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl p-8 sm:p-12">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-display font-bold text-gray-900">Find Your Perfect Match</h1>
                    <p class="mt-2 text-gray-600">Create your account and start your journey to love</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
                                <p class="mt-2">
                                    <a href="/login.php" class="text-green-700 font-bold hover:text-green-800">
                                        Click here to login
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Social Login Buttons -->
                <div class="space-y-3 mb-6">
                    <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" 
                       class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 transition-colors">
                        <img src="https://www.google.com/favicon.ico" alt="Google" class="w-5 h-5 mr-2">
                        Continue with Google
                    </a>
                    <a href="<?php echo htmlspecialchars($facebookAuthUrl); ?>" 
                       class="flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-lg text-white bg-[#1877F2] hover:bg-[#166fe5] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1877F2] transition-colors">
                        <i class="fab fa-facebook text-xl mr-2"></i>
                        Continue with Facebook
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or register with email</span>
                    </div>
                </div>

                <!-- Registration Form -->
                <form id="registerForm" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Full Name
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       required 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       required 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                            </div>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">
                                I am a
                            </label>
                            <div class="mt-1 grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 focus:outline-none hover:border-romantic-500">
                                    <input type="radio" 
                                           name="gender" 
                                           value="male" 
                                           class="sr-only" 
                                           required>
                                    <span class="flex items-center">
                                        <i class="fas fa-mars text-romantic-500 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">Male</span>
                                    </span>
                                    <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent peer-checked:border-romantic-500"></span>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 focus:outline-none hover:border-romantic-500">
                                    <input type="radio" 
                                           name="gender" 
                                           value="female" 
                                           class="sr-only" 
                                           required>
                                    <span class="flex items-center">
                                        <i class="fas fa-venus text-romantic-500 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">Female</span>
                                    </span>
                                    <span class="pointer-events-none absolute -inset-px rounded-lg border-2 border-transparent peer-checked:border-romantic-500"></span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                                Date of Birth
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" 
                                       id="date_of_birth" 
                                       name="date_of_birth" 
                                       required 
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       minlength="8"
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                            </div>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                                Confirm Password
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required 
                                       minlength="8"
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="terms" 
                               name="terms" 
                               required
                               class="h-4 w-4 text-romantic-600 focus:ring-romantic-500 border-gray-300 rounded">
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            I agree to the 
                            <a href="/terms" class="text-romantic-600 hover:text-romantic-500">Terms of Service</a>
                            and
                            <a href="/privacy" class="text-romantic-600 hover:text-romantic-500">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-romantic-600 hover:bg-romantic-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 transition-colors">
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Already have an account? 
                    <a href="/login.php" class="font-medium text-romantic-600 hover:text-romantic-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once '../app/views/shared/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Custom validation method for full name
            $.validator.addMethod("fullName", function(value, element) {
                return this.optional(element) || /^[a-zA-Z]+(?: [a-zA-Z]+)+$/.test(value);
            }, "Please enter your full name (first & last name)");

            // Custom validation method for minimum age
            $.validator.addMethod("minAge", function(value, element, minAge) {
                if (this.optional(element)) return true;
                
                var today = new Date();
                var birthDate = new Date(value);
                var age = today.getFullYear() - birthDate.getFullYear();
                
                if (today.getMonth() < birthDate.getMonth() || 
                    (today.getMonth() == birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                return age >= minAge;
            }, "You must be at least {0} years old");

            // Initialize form validation
            $("#registerForm").validate({
                rules: {
                    name: {
                        required: true,
                        fullName: true,
                        minlength: 5
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    gender: {
                        required: true
                    },
                    date_of_birth: {
                        required: true,
                        minAge: 18
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password"
                    },
                    terms: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter your full name",
                        minlength: "Name must be at least 5 characters long"
                    },
                    email: {
                        required: "Please enter your email address",
                        email: "Please enter a valid email address"
                    },
                    gender: {
                        required: "Please select your gender"
                    },
                    date_of_birth: {
                        required: "Please enter your date of birth"
                    },
                    password: {
                        required: "Please enter a password",
                        minlength: "Password must be at least 8 characters long"
                    },
                    confirm_password: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    },
                    terms: {
                        required: "You must accept the terms and conditions"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    if (element.attr("type") == "radio") {
                        error.insertAfter(element.closest(".grid"));
                    } else if (element.attr("type") == "checkbox") {
                        error.insertAfter(element.closest(".flex"));
                    } else {
                        error.addClass('text-romantic-500 text-xs mt-1 block');
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('border-romantic-500').removeClass('border-gray-300');
                },
                unhighlight: function(element) {
                    $(element).removeClass('border-romantic-500').addClass('border-gray-300');
                },
                submitHandler: function(form) {
                    // Show loading state
                    const submitBtn = $(form).find('button[type="submit"]');
                    const originalText = submitBtn.html();
                    submitBtn.prop('disabled', true)
                           .html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating account...');
                    
                    // Submit the form
                    form.submit();
                }
            });

            // Style radio buttons on change
            $('input[type="radio"]').change(function() {
                $('input[type="radio"]').each(function() {
                    const label = $(this).closest('label');
                    if (this.checked) {
                        label.addClass('border-romantic-500 ring-2 ring-romantic-500');
                    } else {
                        label.removeClass('border-romantic-500 ring-2 ring-romantic-500');
                    }
                });
            });
        });
    </script>
</body>
</html> 