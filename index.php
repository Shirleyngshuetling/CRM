<?php
require 'php/config_session.php'; // Start the session
require 'php/login_auth_view.php'; // Include the View for login messages
require 'php/signup_auth_view.php'; // Include the View for signup messages

// Check if there's an error message in session
if (isset($_SESSION["login_error"])) {
    echo '<p class="error-message">' . $_SESSION["login_error"] . '</p>';

    // Clear the message so it doesn't persist after refresh
    unset($_SESSION["login_error"]);
}

// get login error message in login form
$errors_login = get_login_errors();

// get signup error message in login form
$errors_signup = get_signup_errors();

//get signup success message
$success_signup = get_signup_success();

// Store previous user input if available
$signup_data = $_SESSION["signup_data"] ?? null;
unset($_SESSION["signup_data"]);

$login_data = $_SESSION["login_data"] ?? null;
unset($_SESSION["login_data"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/index.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>

    <!-- SVG for success alert icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
    </svg>

    <!-- Signup Success Toast -->
    <?php if (isset($_SESSION['success_signup'])): ?>
        <div class="alert alert-primary d-flex align-items-center" role="alert" style="height:60px; padding-right:30px; position:absolute; top:1px; right:6px; z-index:10;">
            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:" style="width:16px">
                <use xlink:href="#check-circle-fill"/>
            </svg>
            <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                <?php echo htmlspecialchars($success_signup); ?>
            </div>
        </div>
        <?php unset($_SESSION["success_signup"]); ?>
    <?php endif; ?>

    <!-- Header -->
    <header>
        <div class="logo">
            <img src="./assets/output-onlinepngtools.png" alt="ABB Logo">
        </div>
        <h1>ABB Robotics CRM</h1>
    </header>

    <!-- Main Container -->
    <div class="container">

        <!-- Login Form -->
        <div class="form-box login">
            <form id="loginForm" action="php/login_auth.php" method="POST">
                <h1>Login</h1>

                <!-- Email Input -->
                <div class="input-box">
                    <input type="email" class="input-field" id="signinEmail" name="email" placeholder="Email" value="<?php echo htmlspecialchars($login_data['email'] ?? ''); ?>">
                    <i class="bx bx-envelope"></i>
                </div>

                <!-- Password Input -->
                <div class="input-box">
                    <input type="password" class="input-field" id="signinPassword" name="password" placeholder="Password">
                    <i class="bx bx-show toggle-password-signin" onclick="togglePasswordVisibility()"></i>
                </div>

                <!-- Login Errors -->
                <div class="errorMsg">
                    <p class="error-message">
                        <?php 
                        if (!empty($errors_login)) {
                            foreach ($errors_login as $error) {
                                echo htmlspecialchars($error) . "<br>";
                            }
                        }
                        ?>
                    </p>
                </div>

                <!-- Login Submit Button -->
                <button type="submit" class="submitBtn" name="Login">Login</button>

                <!-- Link to Signup -->
                <div class="sm-signup-btn">
                    <p>Don't have an account? <span class="signUpClick">Sign Up</span></p>
                </div>
            </form>
        </div>

        <!-- Signup Form -->
        <div class="form-box signup">
            <form id="signupForm" action="php/signup_auth.php" method="POST">
                <h1>Sign Up</h1>

                <!-- Username Input -->
                <div class="input-box">
                    <input type="text" class="input-field" id="username" name="username" placeholder="Your Name" value="<?php echo htmlspecialchars($signup_data['username'] ?? ''); ?>">
                    <i class="bx bx-user"></i>
                </div>

                <!-- Email Input -->
                <div class="input-box">
                    <input type="email" class="input-field" id="signupEmail" name="email" placeholder="Email" value="<?php echo htmlspecialchars($signup_data['email'] ?? ''); ?>">
                    <i class="bx bx-envelope"></i>
                </div>

                <!-- Password Input -->
                <div class="input-box">
                    <input type="password" class="input-field" id="signupPassword" name="password" placeholder="Password">
                    <i class="bx bx-show toggle-password-signup" onclick="togglePasswordVisibility()"></i>
                </div>

                <!-- Role Selection -->
                <div class="input-box">
                    <select class="role-field" id="role" name="role">
                        <option value="" disabled selected>Click to select...</option>
                        <option value="1" <?php echo (isset($signup_data['role_type_id']) && $signup_data['role_type_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                        <option value="2" <?php echo (isset($signup_data['role_type_id']) && $signup_data['role_type_id'] == 2) ? 'selected' : ''; ?>>Sales Representative</option>
                    </select>
                    <img src="./assets/arrow-down-s-line.png" alt="Dropdown Arrow">
                </div>

                <!-- Signup Errors -->
                <div class="errorMsg">
                    <p class="error-message">
                        <?php 
                        if (!empty($errors_signup)) {
                            foreach ($errors_signup as $error) {
                                echo htmlspecialchars($error) . "<br>";
                            }
                        }
                        ?>
                    </p>
                </div>

                <!-- Hidden Field to send current page -->
                <input type="hidden" name="currentPage" value="index">

                <!-- Signup Submit Button -->
                <button type="submit" class="submitBtn" name="signUp">Register</button>

                <!-- Link to Login -->
                <div class="sm-login-btn">
                    <p>Already have an account? <span class="loginClick">Login</span></p>
                </div>
            </form>
        </div>

        <!-- Toggle Box for transitions -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Welcome!</h1>
                <p>Don't have an account?</p>
                <div class="toggleBtn signUp-btn">Sign Up Now!</div>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome back!</h1>
                <p>Already have an account?</p>
                <div class="toggleBtn login-btn">Login Now!</div>
            </div>
        </div>

    </div>

    <!-- Pass Data to Frontend -->
    <div id="loginApp" data-show-login="<?php echo isset($_SESSION["show_login_upon_login_error"]) ? 'true' : 'false'; ?>"></div>
    <?php unset($_SESSION["show_login_upon_login_error"]); ?>

    <div id="signupApp" data-show-signup="<?php echo isset($_SESSION["show_signup_upon_signup_error"]) ? 'true' : 'false'; ?>"></div>
    <?php unset($_SESSION["show_signup_upon_signup_error"]); ?>

    <!-- Scripts -->
    <script src="js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>