<?php
/**
 * Login Page
 * Handles user authentication
 */

include 'session_config.php';

// If already logged in, redirect to dashboard
if (is_logged_in() && !is_session_expired()) {
    header('Location: index.php');
    exit;
}

$error_message = '';
$login_attempted = false;

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_attempted = true;
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } elseif (validate_credentials($username, $password)) {
        // Valid credentials - create session
        create_session($username);
        header('Location: index.php');
        exit;
    } else {
        $error_message = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MUHINGABO Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .login-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            margin-bottom: 5px;
        }
        .login-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .login-btn:active {
            transform: translateY(0);
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 12px 15px;
        }
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            font-size: 12px;
            color: #666;
        }
        .demo-credentials {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 13px;
        }
        .demo-credentials strong {
            color: #0c5460;
        }
        .credentials-list {
            list-style: none;
            padding-left: 0;
            margin: 10px 0 0 0;
        }
        .credentials-list li {
            padding: 3px 0;
        }
        .eye-icon {
            cursor: pointer;
            user-select: none;
            font-size: 16px;
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .form-group input[type="password"],
        .form-group input[type="text"] {
            padding-right: 40px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1>🏪 MUHINGABO</h1>
                <p>Hardware Inventory System</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Demo Credentials Info -->
                <div class="demo-credentials">
                    <strong>📝 Demo Credentials:</strong>
                    <ul class="credentials-list">
                        <li><strong>Username:</strong> dope</li>
                        <li><strong>Password:</strong> @1205</li>
                    </ul>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>⚠️ Login Failed</strong><br>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Enter your username" 
                               required
                               autofocus
                               value="<?php echo htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password" 
                                   required>
                            <span class="eye-icon" onclick="togglePassword()" title="Show/Hide Password">👁️</span>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">Sign In</button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p style="margin: 0;">
                    Developed by <span style="color: #667eea;">❤️</span> — NIYONKURU Gabriel
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }

        // Auto-fill demo credentials on development
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            if (usernameInput.value === '') {
                usernameInput.value = 'dope';
                document.getElementById('password').value = '@1205';
                usernameInput.focus();
            }
        });
    </script>
</body>
</html>
