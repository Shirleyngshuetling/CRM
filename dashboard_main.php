<?php
require_once 'php/db.php';
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php';

check_session();

$role_type_id = $_SESSION['role_type_id'];
$user_id = $_SESSION['user_id'];

// Load appropriate records based on role
if ($role_type_id == 1) { // Admin
    require 'php/get_total_records_admin.php';
} else { // Sales representative
    require 'php/get_total_records_sales.php';
}

// ===== customer infos =====
//Get customer count in this month
$currentCustomers = getTotalCustomersThistMonth($conn, $user_id);
// Get previous period count (last month)
$previousCustomers = getTotalCustomersLastMonth($conn, $user_id);
// Calculate percentage change
$customerGrowth = getCustomerGrowth($currentCustomers, $previousCustomers);
// Determine trend indicators
$trendIconCustomer = ($customerGrowth >= 0) ? 'fa-angle-double-up' : 'fa-angle-double-down';
$trendClassCustomer = ($customerGrowth >= 0) ? 'positive' : 'negative';


// ===== lead infos =====
//Get customer count in this month
$currentLeads = getTotalLeadsThistMonth($conn, $user_id);
// Get previous period count (last month)
$previousLeads = getTotalLeadsLastMonth($conn, $user_id);
// Calculate percentage change
$leadGrowth = getLeadGrowth($currentLeads, $previousLeads);
// Determine trend indicators
$trendIconLead = ($leadGrowth >= 0) ? 'fa-angle-double-up' : 'fa-angle-double-down';
$trendClassLead = ($leadGrowth >= 0) ? 'positive' : 'negative';

// ===== interaction infos =====
$currentInteractions = getTotalInteractionsThisMonth($conn, $user_id);
$previousInteractions = getTotalInteractionsLastMonth($conn, $user_id);
$interactionGrowth = getInteractionGrowth($currentInteractions, $previousInteractions);
$trendIconInteraction = ($interactionGrowth >= 0) ? 'fa-angle-double-up' : 'fa-angle-double-down';
$trendClassInteraction = ($interactionGrowth >= 0) ? 'positive' : 'negative';

// ===== reminder infos =====
$currentReminders = getTotalRemindersThisMonth($conn, $user_id);
$previousReminders = getTotalRemindersLastMonth($conn, $user_id);
$reminderGrowth = getReminderGrowth($currentReminders, $previousReminders);
$trendIconReminder = ($reminderGrowth >= 0) ? 'fa-angle-double-up' : 'fa-angle-double-down';
$trendClassReminder = ($reminderGrowth >= 0) ? 'positive' : 'negative';

// Get a list of recent interaction with date in current month
$recent_interactions = getRecentInteractionThistMonth($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ABB Robotics CRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <!-- HEADER SECTION -->
    <section class="header">
        <div class="logo-part">
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png">
            </div>

            <a href="#">
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
                
                <div class="profile-menu" id="profileMenu">
                    <!-- Navigation Links -->
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
                        <i class="fa-regular fa-note-sticky" style="color: white;"></i> Reminders
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

    <!-- MAIN CONTENT SECTION-->
    <section class="container">
        <!-- LEFT SIDEBAR NAVIGATION -->
        <div class="navigation">
            <ul class="nav-menu">
                <li>
                    <a href="dashboard_main.php" class="active-link">
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
                    <a href="interaction_list.php">
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

        <!-- MAIN CONTENT AREA -->
        <div class="main">
            <!-- OVERVIEW SECTION -->
            <div class="overview">
                <div class="overview-title">
                    <h2 class="main-title"><b>Overview</b></h2>
                </div>
            </div>

            <!-- STATISTICS CARDS -->
            <div class="cards">
                <!-- CUSTOMERS CARD -->
                <div class="card card1" onclick="window.location.href='customer_list.php'">
                    <div class="card-data">
                        <div class="card-content">
                            <h5 class="card-title">Total Customers</h5>
                            <h1><?php echo htmlspecialchars($currentCustomers); ?></h1>
                        </div>
                        <i class="fa-solid fa-users card-icon-lg"></i>
                    </div>
                    <div class="card-stats">
                        <span class="trend-indicator <?php echo $trendClassCustomer; ?>">
                            <i class="fa <?php echo $trendIconCustomer; ?> card-icon"></i>
                            <?php echo abs($customerGrowth); ?>%
                        </span>
                        <span>vs last month</span>
                    </div>
                </div>

                <!-- LEADS CARD -->
                <div class="card card2" onclick="window.location.href='lead_list.php'">
                    <div class="card-data">
                        <div class="card-content">
                            <h5 class="card-title">Total Leads</h5>
                            <h1><?php echo htmlspecialchars($currentLeads); ?></h1>
                        </div>
                        <i class="fa-solid fa-users-line card-icon-lg"></i>
                    </div>
                    <div class="card-stats">
                        <span class="trend-indicator <?php echo $trendClassLead; ?>">
                            <i class="fa <?php echo $trendIconLead; ?> card-icon"></i>
                            <?php echo abs($leadGrowth); ?>%
                        </span>
                        <span>vs last month</span>
                    </div>
                </div>

                <!-- INTERACTIONS CARD -->
                <div class="card card3" onclick="window.location.href='interaction_list.php'">
                    <div class="card-data">
                        <div class="card-content">
                            <h5 class="card-title">Total Interaction Histories</h5>
                            <h1><?php echo htmlspecialchars($currentInteractions); ?></h1>
                        </div>
                        <i class="fa-solid fa-circle-check card-icon-lg"></i>
                    </div>
                    <div class="card-stats">
                        <span class="trend-indicator <?php echo $trendClassInteraction; ?>">
                            <i class="fa <?php echo $trendIconInteraction; ?> card-icon"></i>
                            <?php echo abs($interactionGrowth); ?>%
                        </span>
                        <span>vs last month</span>
                    </div>
                </div>

                <!-- REMINDERS CARD -->
                <div class="card card4" onclick="window.location.href='reminder_list.php'">
                    <div class="card-data">
                        <div class="card-content">
                            <h5 class="card-title">Total Reminders</h5>
                            <h1><?php echo htmlspecialchars($currentReminders); ?></h1>
                        </div>
                        <i class="fa-solid fa-note-sticky card-icon-lg"></i>
                    </div>
                    <div class="card-stats">
                        <span class="trend-indicator <?php echo $trendClassReminder; ?>">
                            <i class="fa <?php echo $trendIconReminder; ?> card-icon"></i>
                            <?php echo abs($reminderGrowth); ?>%
                        </span>
                        <span>vs last month</span>
                    </div>
                </div>
            </div>
            
            <!-- RECENT INTERACTIONS SECTION -->
            <div class="recent-interactions">
                <div class="overview-title">
                    <h2 class="main-title"><b>Recent Interactions</b></h2>
                    <button class="add" onclick="window.location.href='add_interaction.php'">
                        <i class="fa fa-plus" aria-hidden="true"></i> Interaction
                    </button>
                </div>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Customer/Lead Name</th>
                                <th>Interaction Date</th>
                                <th>Interaction Details</th>
                                <th>Interaction Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recent_interactions) > 0): ?>
                                <?php foreach ($recent_interactions as $interaction): ?>
                                    <tr>
                                        <td><?php echo !empty($interaction['user_name']) ? htmlspecialchars($interaction['user_name']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($interaction['customer_lead_name']) ? htmlspecialchars($interaction['customer_lead_name']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($interaction['interaction_date']) ? htmlspecialchars($interaction['interaction_date']) : 'N/A'; ?></td>
                                        <td><?php echo !empty($interaction['interaction_details']) ? htmlspecialchars($interaction['interaction_details']) : 'No details'; ?></td>
                                        <td><?php echo !empty($interaction['interaction_type_name']) ? htmlspecialchars($interaction['interaction_type_name']) : 'N/A'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">No interaction history found for current month</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- REMINDER POPUP -->
    <div id="customer-popup" class="reminder">
        <span class="close" onclick="closePopup()"><i class="fa fa-times" aria-hidden="true"></i></span>
        <div class="reminder-content">
            <h2>Meeting Reminder 📌</h2>
            <img src="./assets/mail-send-fill.png" alt="" width="120px" height="110px">
            <div id="popup-details"></div>
        </div>
    </div>

    <!-- JAVASCRIPT FILES-->
    <script src="./js/dashboard_main.js"></script>
    <script src="./js/interaction_list.js"></script>
    <script src="./js/send_email.js"></script>
    <script src="js/reminder_popup.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>