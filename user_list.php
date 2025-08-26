<?php
// Include necessary files for session management, user check, and error handling
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php';
require 'php/signup_auth_view.php';
require 'php/update_user_view.php';

// Ensure the session is valid and the user has admin privileges
check_session();

if ($_SESSION['role_type_id'] != 1) {
    // If the user is not an admin, redirect to the login page
    $_SESSION['login_error'] = "Only admin can access this page.";
    header("Location: index.php"); // Redirect unauthorized users
    exit();
}

// Get any registration errors if present
$user_registration_errors = get_signup_errors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - ABB Robotics CRM</title>
    <!-- Link to external CSS stylesheets -->
    <link rel="stylesheet" href="css/customer_lead_list.css">
    <link rel="stylesheet" href="css/user_list.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body data-user-id="<?php echo $_SESSION['user_id']; ?>">

    <!-- Display success message if there is a successful update -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_user_update_success(); ?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
    <?php endif; 
        unset($_SESSION['success']);
    ?>

    <!-- Header Section -->
    <section class="header">
        <div class="logo-part">
            <!-- Menu Icon -->
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png" >
            </div>

            <!-- Link to Dashboard -->
            <a href="dashboard_main.php">
                <span class="icon"><i class="fa-solid fa-robot"></i></span>
                <span class="title"><h2>ABB Robotics CRM</h2></span>
            </a>
        </div>

        <div class="header-right">
            <div class="profile" id="profile">
                <div class='profileAlign'>
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <!-- Profile Menu with links -->
                <div class="profile-menu" id="profileMenu">
                    <!-- Home Link -->
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>
                    <!-- Users Link (Only for admin) -->
                    <a href="user_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-user-tie"></i> Users
                    </a>
                    <!-- Customers Link -->
                    <a href="customer_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>
                    <!-- Leads Link -->
                    <a href="lead_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users-line"></i> Leads
                    </a>
                    <!-- Interactions Link -->
                    <a href="interaction_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'interaction_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-circle-check"></i> Interactions
                    </a>
                    <!-- Reminders Link -->
                    <a href="reminder_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-note-sticky"></i> Reminders
                    </a>
                    <!-- Calendar Link -->
                    <a href="reminder_calender.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_calender.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-calendar-days"></i> Calendar
                    </a>
                    <!-- Sign Out Link -->
                    <a href="#" onclick="logout()">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Container Section -->
    <section class="container">
        <!-- Left Sidebar Navigation -->
        <div class="navigation">
            <ul class="nav-menu">
                <!-- Navigation Links -->
                <li><a href="dashboard_main.php"><span class="icon"><i class="fa-solid fa-house-user"></i></span><span class="title">Home</span></a></li>
                <li><a href="user_list.php" class="active-link"><span class="icon"><i class="fa-solid fa-user-tie"></i></span><span class="title">Users</span></a></li>
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
            <div class="middle-part">
                <h2 class="main-title"><b>Users Management</b></h2>

                <!-- Add User Button -->
                <div class="button-group">
                    <a href="add_user.php" class="button submitBtn">Add User</a>
                </div>
            </div>

            <!-- Users List Table Section -->
            <div class="customer-table">
                <h2 id="tableTitle">Users List</h2>

                <div class='customerOptions'>
                    <!-- Search Box for Users -->
                    <div class="search">
                        <input id="search" type="text" placeholder="Search Any Field" name="nameSearch">
                        <button><img src="./assets/search-line.png" class="search_icon"></button>
                    </div>

                    <!-- Sort Controls for Users List -->
                    <div class="sort-controls">
                        <label for="sortCombined">Sort by:</label>
                        <select id="sortCombined">
                            <option value="user_name|asc">Name A-Z</option>
                            <option value="user_name|desc">Name Z-A</option>
                            <option value="user_id|asc">ID Low to High</option>
                            <option value="user_id|desc">ID High to Low</option>
                            <option value="role_type_name|asc">Role A-Z</option>
                            <option value="role_type_name|desc">Role Z-A</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- User Card Grid for Dynamic Content -->
            <div class="user-card-grid">
                <!-- Cards will be dynamically inserted here -->
            </div>
        </div>
    </section>

    <!-- Include JavaScript Files -->
    <script src="js/users_list.js"></script>
    <script src="js/signout.js"></script>
    
    <script>
        // Set the base URL for the current page
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>
