<?php
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php'; // Session check function
require 'php/signup_auth_view.php'; // Include view for signup messages

check_session();

// Only allow admin (role_type_id = 1) to access this page
if (isset($_SESSION["role_type_id"]) != 1) {
    $_SESSION['login_error'] = "Only admin can access this page.";
    header("Location: index.php");
    exit();
}

$role_type_id = $_SESSION['role_type_id'];
$user_registration_data = $_SESSION["signup_data"] ?? null;
unset($_SESSION["signup_data"]);

$user_registration_errors = get_signup_errors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customers - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <!-- Success Toast Message -->
    <?php if (isset($_SESSION['success_add_user'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_add_user_success(); ?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
        <?php unset($_SESSION['success_add_user']); ?>
    <?php endif; ?>

    <!-- Header Section -->
    <section class="header">
        <div class="logo-part">
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png">
            </div>

            <a href="dashboard_main.php">
                <span class="icon"><i class="fa-solid fa-robot"></i></span>
                <span class="title"><h2>ABB Robotics CRM</h2></span>
            </a>
        </div>

        <!-- Profile Menu -->
        <div class="header-right">
            <div class="profile" id="profile">
                <div class='profileAlign'>
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <div class="profile-menu" id="profileMenu">
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>

                    <?php if ($role_type_id == 1): ?>
                        <a href="user_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-tie"></i> Users
                        </a>
                    <?php endif; ?>

                    <a href="customer_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>

                    <a href="lead_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users-line"></i> Leads
                    </a>

                    <a href="interaction_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'interaction_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-circle-check"></i> Interactions
                    </a>

                    <a href="reminder_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-note-sticky"></i> Reminders
                    </a>

                    <a href="reminder_calender.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_calender.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-calendar-days"></i> Calendar
                    </a>

                    <a href="#" onclick="logout()">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Container -->
    <section class="container">

        <!-- Sidebar Navigation -->
        <div class="navigation">
            <ul class="nav-menu">
                <li><a href="dashboard_main.php"><span class="icon"><i class="fa-solid fa-house-user"></i></span><span class="title">Home</span></a></li>
                <?php if ($role_type_id == 1): ?>
                    <li><a href="user_list.php" class="active-link"><span class="icon"><i class="fa-solid fa-user-tie"></i></span><span class="title">Users</span></a></li>
                <?php endif; ?>
                <li><a href="customer_list.php"><span class="icon"><i class="fa-solid fa-users"></i></span><span class="title">Customers</span></a></li>
                <li><a href="lead_list.php"><span class="icon"><i class="fa-solid fa-users-line"></i></span><span class="title">Leads</span></a></li>
                <li><a href="interaction_list.php"><span class="icon"><i class="fa-solid fa-circle-check"></i></span><span class="title">Interactions</span></a></li>
                <li><a href="reminder_list.php"><span class="icon"><i class="fa-regular fa-note-sticky"></i></span><span class="title">Reminders</span></a></li>
                <li><a href="reminder_calender.php"><span class="icon"><i class="fa-regular fa-calendar-days"></i></span><span class="title">Calendar</span></a></li>
                <li><a href="#" onclick="logout()"><span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main">
            <div class='middle-part'>
                <h2 class='main-title'><b>Users Management</b></h2>
            </div>

            <div class="customer-table">
                <h2 id="tableTitle">Add User</h2>
            </div>

            <!-- Add User Form -->
            <div class='addCustomerForm'>
                <form id="signupForm" action="php/signup_auth.php" method="POST">

                    <div class="user-details">
                        <div class="input-box">
                            <label for="username" class='details'>User's Name:</label>
                            <input type="text" class="input-field" id="username" name="username" placeholder="user's name" value="<?php echo htmlspecialchars($user_registration_data['username'] ?? ''); ?>">
                        </div>

                        <div class="input-box">
                            <label for="signupPassword" class='details'>Password:</label>
                            <input type="password" class="input-field" id="signupPassword" name="password" placeholder="password">
                        </div>

                        <div class="input-box">
                            <label for="signupEmail" class='details'>Email:</label>
                            <input type="email" class="input-field" id="signupEmail" name="email" placeholder="email" value="<?php echo htmlspecialchars($user_registration_data['email'] ?? ''); ?>">
                        </div>

                        <div class="input-box">
                            <label for="role">Role:</label>
                            <select class="role-field" id="role" name="role">
                                <option value="" disabled selected>click to select...</option>
                                <option value="1" <?php echo (isset($user_registration_data['role_type_id']) && $user_registration_data['role_type_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?php echo (isset($user_registration_data['role_type_id']) && $user_registration_data['role_type_id'] == 2) ? 'selected' : ''; ?>>Sales Representative</option>
                            </select>
                        </div>
                    </div>

                    <!-- Display signup errors if any -->
                    <div class="errorMsg">
                        <p class="error-message">
                            <?php 
                            if (!empty($user_registration_errors)) {
                                foreach ($user_registration_errors as $error) {
                                    echo htmlspecialchars($error) . "<br>";
                                }
                            }
                            ?>
                        </p>
                    </div>

                    <input type="hidden" name="currentPage" value="add_user">

                    <!-- Form Buttons -->
                    <div class='addBtnGrp'>
                        <a href="user_list.php" class="submitBtn" style="text-decoration: none;">Back</a>
                        <button type="submit" class="submitBtn">Add User</button>
                    </div>

                </form>
            </div>

        </div>

    </section>

    <!-- JavaScript Files -->
    <script src="js/dashboard_main.js"></script>
    <script src="js/signout.js"></script>
    <script src="js/validate_add_user.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>

</body>
</html>
