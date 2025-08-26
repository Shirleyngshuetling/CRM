<?php
// Include necessary files
require "php/config_session.php";
require_once 'php/check_session.php';
require "php/db.php";
require "php/update_user_view.php";

// Check if the session is valid and the user has admin privileges
check_session();

if ($_SESSION["role_type_id"] != 1) {
    $_SESSION['login_error'] = "Only admin can access this page.";
    header("Location: index.php");
    exit();
}

// Fetch user data for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $user_id = (int)$_POST['userId'];

    $query = $conn->prepare("SELECT * FROM Users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    // If user is found, store the data
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit;
    }
} elseif (isset($_SESSION['user_update_data']) && !empty($_SESSION['user_update_data'])) {
    // Use data from session if available
    $user = $_SESSION["user_update_data"];
    unset($_SESSION["user_update_data"]);
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <!-- Header section with logo and profile menu -->
    <section class="header">
        <div class="logo-part">
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png" alt="Menu Icon">
            </div>

            <a href="dashboard_main.php">
                <span class="icon"><i class="fa-solid fa-robot"></i></span>
                <span class="title"><h2>ABB Robotics CRM</h2></span>
            </a>
        </div>

        <div class="header-right">
            <div class="profile" id="profile">
                <div class="profileAlign">
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png" alt="Profile Icon"></div>
                </div>
            
                <!-- Profile menu with navigation links -->
                <div class="profile-menu" id="profileMenu">
                    <!-- Home link -->
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>
                
                    <!-- Admin link (Users) -->
                    <?php if ($_SESSION['role_type_id'] == 1): ?>
                        <a href="user_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-tie"></i> Users
                        </a>
                    <?php endif; ?>
                
                    <!-- Customers link -->
                    <a href="customer_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>
                
                    <!-- Leads link -->
                    <a href="lead_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users-line"></i> Leads
                    </a>
                        
                    <!-- Interactions link -->
                    <a href="interaction_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'interaction_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-circle-check"></i> Interactions
                    </a>
                        
                    <!-- Reminders link -->
                    <a href="reminder_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-note-sticky"></i> Reminders
                    </a>
                        
                    <!-- Calendar link -->
                    <a href="reminder_calender.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_calender.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-calendar-days"></i> Calendar
                    </a>
                
                    <!-- Sign out link -->
                    <a href="#" onclick="logout()">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main container for page content -->
    <section class="container">
        <!-- Left sidebar navigation -->
        <div class="navigation">
            <ul class="nav-menu">
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

        <!-- Main content section -->
        <div class="main">
            <div class="middle-part">
                <h2 class="main-title"><b>User Management</b></h2>
            </div>

            <!-- Table for user update -->
            <div class="customer-table">
                <h2 id="tableTitle">Update User</h2>
            </div>

            <!-- Form for updating user details -->
            <div class="addCustomerForm">
                <form id="customerForm" action="php/update_user.php" method="POST">
                    <!-- Hidden input for user ID -->
                    <input type="hidden" name="userId" value="<?= htmlspecialchars($user['user_id'] ?? '') ?>">

                    <div class="user-details">
                        <!-- Name input field -->
                        <div class="input-box">
                            <label for="name" class="details">Name:</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['user_name']) ?>" placeholder="Enter user's name" required>
                        </div>

                        <!-- Email input field -->
                        <div class="input-box">
                            <label for="customerEmail" class="details">Email:</label>
                            <input type="email" id="customerEmail" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Enter user's email" required>
                        </div>

                        <!-- Role selection input -->
                        <div class="input-box">
                            <label for="role">Role:</label>
                            <?php if ($_SESSION['user_id'] != $user['user_id']): ?>
                                <!-- Dropdown for admin to select role -->
                                <select id="role" name="role" required>
                                    <option value="1" <?= ($user['role_type_id'] == 1) ? "selected" : "" ?>>Admin</option>
                                    <option value="2" <?= ($user['role_type_id'] == 2) ? "selected" : "" ?>>Sales Representative</option>
                                </select>
                            <?php else: ?>
                                <!-- Display role for the user viewing their own profile -->
                                <input type="text" 
                                    value="<?= htmlspecialchars($user['role_type_id'] == 1 ? 'Admin' : 'Sales Representative') ?>" 
                                    disabled>
                                <input type="hidden" name="role" value="<?= htmlspecialchars($user['role_type_id']) ?>">
                            <?php endif; ?>
                        </div>

                        <!-- Error message section -->
                        <div class="errorMsg">
                            <p class="error-message">
                                <?php check_user_update_errors(); ?>
                            </p>
                        </div>

                        <!-- Buttons for user management actions -->
                        <div class="addBtnGrp">
                            <a href="user_list.php" class="submitBtn" style="text-decoration: none">Back</a>
                            <button type="button" id="undoChanges" class="submitBtn">Undo Changes</button>
                            <button type="submit" class="submitBtn">Update User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- External JS files -->
    <script src="js/undo_update_customer_lead.js"></script>
    <script src="js/dashboard_main.js"></script>
    <script src="js/signout.js"></script>

    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>
