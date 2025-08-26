<?php
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php';


check_session();

$role_type_id = $_SESSION['role_type_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/customer_lead_list.css"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <!-- Header Section -->
    <section class="header">
        <div class="logo-part">
            <!-- Menu Icon -->
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png">
            </div>

            <!-- Logo and title link -->
            <a href="dashboard_main.php">
                <span class="icon"><i class="fa-solid fa-robot"></i></span>
                <span class="title"><h2>ABB Robotics CRM</h2></span>
            </a>
        </div>

        <div class="header-right">
            <!-- Profile Section -->
            <div class="profile" id="profile">
                <div class='profileAlign'>
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <!-- Profile Menu with navigation links -->
                <div class="profile-menu" id="profileMenu">
                    <!-- Home Link -->
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>

                    <!-- Users Link (Only visible for admins) -->
                    <?php if ($role_type_id == 1): ?>
                        <a href="user_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-tie"></i> Users
                        </a>
                    <?php endif; ?>

                    <!-- Customers, Leads, Interactions, Reminders, Calendar Links -->
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

        <!-- Left Sidebar with Navigation Menu -->
        <div class="navigation">
            <ul class="nav-menu">
                <li>
                    <a href="dashboard_main.php">
                        <span class="icon"><i class="fa-solid fa-house-user"></i></span>
                        <span class="title">Home</span>
                    </a>
                </li>

                <!-- Only show "Users" menu for admins -->
                <?php if ($role_type_id == 1): ?>
                    <li>
                        <a href="user_list.php">
                            <span class="icon"><i class="fa-solid fa-user-tie"></i></span>
                            <span class="title">Users</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Other Navigation Links -->
                <li><a href="customer_list.php"><span class="icon"><i class="fa-solid fa-users"></i></span><span class="title">Customers</span></a></li>
                <li><a href="lead_list.php"><span class="icon"><i class="fa-solid fa-users-line"></i></span><span class="title">Leads</span></a></li>
                <li><a href="interaction_list.php"><span class="icon"><i class="fa-solid fa-circle-check"></i></span><span class="title">Interactions</span></a></li>
                <li><a href="reminder_list.php" class="active-link"><span class="icon"><i class="fa-regular fa-note-sticky"></i></span><span class="title">Reminders</span></a></li>
                <li><a href="reminder_calender.php"><span class="icon"><i class="fa-regular fa-calendar-days"></i></span><span class="title">Calendar</span></a></li>
                <li><a href="#" onclick="logout()"><span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Main Content Section -->
        <div class="main">

            <!-- Reminder Management Section -->
            <div class='middle-part'>
                <h2 class='main-title'><b>Reminder Management</b></h2>

                <div class="button-group">
                    <a href="add_reminder.php" class="button submitBtn">Add Reminder</a>
                </div>
            </div>

            <!-- Reminder List Section -->
            <div class="customer-table">
                <h2 id="tableTitle">Reminder List</h2>

                <div class='customerOptions'>
                    <!-- Search and Display Options -->
                    <div class="search">
                        <input id="search" type="text" placeholder="Search Any Field" name="reminderSeaech">
                        <button><img src="./assets/search-line.png" class="search_icon"></button>
                    </div>

                    <div>
                        <div class='displayOptions displayOptionsBtn'>
                            Display Options
                            <img src="./assets/arrow-down-s-line.png" alt="">
                        </div>

                        <!-- Display Options Box -->
                        <div class='showOptionsBox'>
                            <div class='showOptions'>
                                <label class='showOption border-bottom'>
                                    <input type="checkbox" id="toggleReminderCompany" checked> Company
                                </label>
                                <label class='showOption'>
                                    <input type="checkbox" id="toggleReminderNotes" checked> Notes
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reminder Table Section -->
            <div class="reminder-table">
                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="reminder_date">
                                    Reminder Date<i class="fa fa-sort" id="icon-reminder_date" aria-hidden="true"></i>
                                </th>
                                <th class="sortable" data-sort="interaction_type_name">
                                    Interaction Type <i class="fa fa-sort" id="icon-interaction_type_name" aria-hidden="true"></i>
                                </th>
                                <th class="sortable" data-sort="customer_type_name">
                                    Customer/Lead<i class="fa fa-sort" id="icon-customer_type_name" aria-hidden="true"></i>
                                </th>
                                <th class="sortable" data-sort="name">
                                    Customer/Lead Name <i class="fa fa-sort" id="icon-name" aria-hidden="true"></i>
                                </th>
                                <th class="sortable company-column" data-sort="company">
                                    Customer/Lead Company <i class="fa fa-sort" id="icon-company" aria-hidden="true"></i>
                                </th>
                                <th class="sortable" data-sort="user_name">
                                    Staff In-charge<i class="fa fa-sort" id="icon-user_name" aria-hidden="true"></i>
                                </th>
                                <th class="notes-column">Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Include necessary JavaScript files -->
    <script src="js/toggleColumns.js"></script>
    <script src="js/reminder_list.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>

</body>
</html>
