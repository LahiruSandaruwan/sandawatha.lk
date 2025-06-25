<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit();
}

require_once '../config/database.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['id'], $token, $expires]);
                
                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
            }
            
            // Update last login timestamp
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute(['id' => $user['id']]);
            
            // Redirect to dashboard
            header('Location: /dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } catch (PDOException $e) {
        $error = 'Login failed. Please try again.';
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
    <title>Login - Sandawatha.lk</title>
    
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

            <!-- Login Form Container -->
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-8 sm:p-12">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-display font-bold text-gray-900">Welcome Back</h1>
                    <p class="mt-2 text-gray-600">Sign in to continue your journey to love</p>
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
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>

                <!-- Login Form -->
                <form id="loginForm" method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email or Phone
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="email" 
                                   name="email" 
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
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500 focus:border-romantic-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   name="remember" 
                                   class="h-4 w-4 text-romantic-600 focus:ring-romantic-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        <a href="/forgot-password.php" class="text-sm text-romantic-600 hover:text-romantic-500">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-romantic-600 hover:bg-romantic-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 transition-colors">
                        Sign In
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account? 
                    <a href="/register.php" class="font-medium text-romantic-600 hover:text-romantic-500">
                        Create one now
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php require_once '../app/views/shared/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Custom validation method for email or phone
            $.validator.addMethod("emailOrPhone", function(value, element) {
                // Email regex
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                // Phone regex (Sri Lankan format)
                const phoneRegex = /^(?:\+94|0)?[0-9]{9,10}$/;
                
                return this.optional(element) || emailRegex.test(value) || phoneRegex.test(value);
            }, "Please enter a valid email address or phone number");

            // Initialize form validation
            $("#loginForm").validate({
                rules: {
                    email: {
                        required: true,
                        emailOrPhone: true,
                        minlength: 3
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email or phone",
                        minlength: "Please enter at least 3 characters"
                    },
                    password: {
                        required: "Please enter your password",
                        minlength: "Password must be at least 6 characters long"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('text-romantic-500 text-xs mt-1 block');
                    error.insertAfter(element);
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
                           .html('<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...');
                    
                    // Submit the form
                    form.submit();
                }
            });
        });
    </script>
</body>
</html> 