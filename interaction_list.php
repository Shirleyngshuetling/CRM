<?php
// Start the session and check login
require 'php/config_session.php'; 
require_once 'php/check_session.php';
require "php/db.php";
require 'php/update_interaction_view.php';

check_session();

$role_type_id = $_SESSION['role_type_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactions - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/customer_lead_list.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
</head>
<body>

    <!-- Toast notification for successful update -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class="toast-left">
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_interaction_update_success(); ?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id="close-button" style="cursor:pointer;"></i>
        </div>
    <?php 
        endif;
        unset($_SESSION['success']);
    ?>

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

        <div class="header-right">
            <div class="profile" id="profile">
                <div class="profileAlign">
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon">
                        <img src="./assets/user-line.png">
                    </div>
                </div>

                <!-- Profile Dropdown Menu -->
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

        <!-- Left Sidebar Navigation -->
        <div class="navigation">
            <ul class="nav-menu">
                <li>
                    <a href="dashboard_main.php">
                        <span class="icon"><i class="fa-solid fa-house-user"></i></span>
                        <span class="title">Home</span>
                    </a>
                </li>

                <?php if ($role_type_id == 1): ?>
                    <li>
                        <a href="user_list.php">
                            <span class="icon"><i class="fa-solid fa-user-tie"></i></span>
                            <span class="title">Users</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="customer_list.php">
                        <span class="icon"><i class="fa-solid fa-users"></i></span>
                        <span class="title">Customers</span>
                    </a>
                </li>

                <li>
                    <a href="lead_list.php">
                        <span class="icon"><i class="fa-solid fa-users-line"></i></span>
                        <span class="title">Leads</span>
                    </a>
                </li>

                <li>
                    <a href="interaction_list.php" class="active-link">
                        <span class="icon"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="title">Interactions</span>
                    </a>
                </li>

                <li>
                    <a href="reminder_list.php">
                        <span class="icon"><i class="fa-regular fa-note-sticky"></i></span>
                        <span class="title">Reminders</span>
                    </a>
                </li>

                <li>
                    <a href="reminder_calender.php">
                        <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                        <span class="title">Calendar</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="logout()">
                        <span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main">

            <div class="middle-part">
                <h2 class="main-title"><b>Interaction History</b></h2>

                <div class="button-group">
                    <a href="add_interaction.php" class="button submitBtn">Add Interaction History</a>
                </div>
            </div>

            <div class="customer-table">
                <h2 id="tableTitle">Interaction List</h2>

                <div class="customerOptions">

                    <!-- Search Box -->
                    <div class="search">
                        <input id="search" type="text" placeholder="Search Any Field" name="nameSearch">
                        <button><img src="./assets/search-line.png" class="search_icon"></button>
                    </div>

                    <!-- Display Options -->
                    <div>
                        <div class="displayOptions displayOptionsBtn">
                            Display Options
                            <img src="./assets/arrow-down-s-line.png" alt="">
                        </div>

                        <div class="showOptionsBox">
                            <div class="showOptions">
                                <label class="showOption border-bottom">
                                    <input type="checkbox" id="toggleInteractionCompany" checked> Company
                                </label>
                                <label class="showOption border-bottom">
                                    <input type="checkbox" id="toggleInteractionDetails" checked> Details
                                </label>
                                <label class="showOption">
                                    <input type="checkbox" id="toggleInteractionDate" checked> Date
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Interaction Table -->
            <div class="interaction-table">
                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable date-column" data-sort="interaction_date">
                                    Date <i class="fa fa-sort" id="icon-interaction_date"></i>
                                </th>
                                <th class="sortable" data-sort="interaction_type_name">
                                    Interaction Type <i class="fa fa-sort" id="icon-interaction_type_name"></i>
                                </th>
                                <th class="sortable" data-sort="customer_type_name">
                                    Customer/Lead <i class="fa fa-sort" id="icon-customer_type_name"></i>
                                </th>
                                <th class="sortable" data-sort="name">
                                    Customer/Lead Name <i class="fa fa-sort" id="icon-name"></i>
                                </th>
                                <th class="sortable company-column" data-sort="company">
                                    Customer/Lead Company <i class="fa fa-sort" id="icon-company"></i>
                                </th>
                                <th class="sortable" data-sort="user_name">
                                    Staff In-charge <i class="fa fa-sort" id="icon-user_name"></i>
                                </th>
                                <th class="details-column">Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table data populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- End of Main -->
    </section> <!-- End of Container -->

    <!-- Scripts -->
    <script src="./js/dashboard_main.js"></script>
    <script src="js/toggleColumns.js"></script>
    <script src="js/interaction_list.js"></script>
    <script src="js/signout.js"></script>

    <!-- Base URL Script -->
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>

</body>
</html>
