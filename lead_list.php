<?php
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php';
require 'php/update_customer_lead_view.php';
require 'php/db.php';

// Check session validity
check_session();

// Get the role of the user
$role_type_id = $_SESSION['role_type_id'];

// Fetch interaction types from the database
$query = $conn->prepare("SELECT * FROM Interaction_Type");
$query->execute();
$result = $query->get_result();
$interaction_types = [];

while ($row = $result->fetch_assoc()) {
    $interaction_types[] = $row;
}

$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/customer_lead_list.css">
    <link rel="stylesheet" href="css/customer_lead_details.css"> <!-- Updated CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <!-- Success message toast if session variable exists -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_lead_update_success(); ?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
    <?php endif; unset($_SESSION['success']); ?>

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
                <div class='profileAlign'>
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <div class="profile-menu" id="profileMenu">
                    <!-- Navigation links -->
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>

                    <!-- Users (only for admin) -->
                    <?php if ($role_type_id == 1): ?>
                        <a href="user_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-tie"></i> Users
                        </a>
                    <?php endif; ?>

                    <!-- Customers -->
                    <a href="customer_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>

                    <!-- Leads -->
                    <a href="lead_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users-line"></i> Leads
                    </a>

                    <!-- Interactions -->
                    <a href="interaction_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'interaction_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-circle-check"></i> Interactions
                    </a>

                    <!-- Reminders -->
                    <a href="reminder_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-note-sticky"></i> Reminders
                    </a>

                    <!-- Calendar -->
                    <a href="reminder_calender.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_calender.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-calendar-days"></i> Calendar
                    </a>

                    <!-- Sign Out -->
                    <a href="#" onclick="logout()">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content Section -->
    <section class="container">
        <!-- Left sidebar -->
        <div class="navigation">
            <ul class="nav-menu">
                <!-- Navigation Menu Items -->
                <li><a href="dashboard_main.php"><span class="icon"><i class="fa-solid fa-house-user"></i></span><span class="title">Home</span></a></li>
                <?php if ($role_type_id == 1): ?>
                    <li><a href="user_list.php"><span class="icon"><i class="fa-solid fa-user-tie"></i></span><span class="title">Users</span></a></li>
                <?php endif; ?>
                <li><a href="customer_list.php"><span class="icon"><i class="fa-solid fa-users"></i></span><span class="title">Customers</span></a></li>
                <li><a href="lead_list.php" class="active-link"><span class="icon"><i class="fa-solid fa-users-line"></i></span><span class="title">Leads</span></a></li>
                <li><a href="interaction_list.php"><span class="icon"><i class="fa-solid fa-circle-check"></i></span><span class="title">Interactions</span></a></li>
                <li><a href="reminder_list.php"><span class="icon"><i class="fa-regular fa-note-sticky"></i></span><span class="title">Reminders</span></a></li>
                <li><a href="reminder_calender.php"><span class="icon"><i class="fa-regular fa-calendar-days"></i></span><span class="title">Calendar</span></a></li>
                <li><a href="#" onclick="logout()"><span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span><span class="title">Sign Out</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <!-- Header for lead management -->
            <div class='middle-part'>
                <h2 class='main-title'><b>Leads Management</b></h2>
                <div class="button-group">
                    <a href="add_lead.php" class="button submitBtn">Add Lead</a>
                </div>
            </div>

            <!-- Table Section -->
            <div class="customer-table">
                <h2 id="tableTitle">Lead List</h2>
                <div class='customerOptions'>
                    <!-- Search Section -->
                    <div class="search">
                        <input id="search" type="text" placeholder="Search Any Field" name="nameSearch">
                        <button><img src="./assets/search-line.png" class="search_icon"></button>
                    </div>

                    <!-- Display Options Section -->
                    <div>
                        <div class='displayOptions displayOptionsBtn'>
                            Display Options
                            <img src="./assets/arrow-down-s-line.png" alt="">
                        </div>

                        <div class='showOptionsBox'>
                            <div class='showOptions'>
                                <label class='showOption border-bottom'><input type="checkbox" id="toggleEmail" checked> Email</label>
                                <label class='showOption border-bottom'><input type="checkbox" id="togglePhone" checked> Phone</label>
                                <label class='showOption border-bottom'><input type="checkbox" id="toggleCompany" checked> Company</label>
                                <label class='showOption border-bottom'><input type="checkbox" id="toggleNotes" checked> Notes</label>
                                <label class='showOption'><input type="checkbox" id="toggleStatus" checked> Status</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Scroll Container -->
                <div class="table-scroll-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="customer_lead_id">ID <i class="fa fa-sort" id="icon-customer_lead_id" aria-hidden="true"></i></th>
                                <th class="sortable" data-sort="name">Name <i class="fa fa-sort" id="icon-name" aria-hidden="true"></i></th>
                                <th class="sortable" data-sort="company">Company <i class="fa fa-sort" id="icon-company" aria-hidden="true"></i></th>
                                <th class="email-column">Email</th>
                                <th class="phone-column">Phone Number</th>
                                <th class="notes-column">Notes</th>
                                <th class="status-column">Status</th>
                                <th>Setting</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Customer Details Popup (hidden by default) -->
            <div id="customer-details" class="hidden">
                <div class="details-container">
                    <div class="details-header">
                        <div class="details-title-area">
                            <h2 id="details-name">Customer Name</h2>
                            <p id="details-company">Company Name</p>
                        </div>
                        <div class="details-actions">
                            <button id="edit-customer" title="Edit Customer">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button id="close-details" title="Close">×</button>
                        </div>
                    </div>
                    <div class="details-body">
                        <!-- Customer Information, Status, and Quick Actions -->
                        <div id="customer-info"></div>
                        <div id="status-progress"></div>
                        <div id="quick-actions"></div>

                        <!-- Tabs for Interactions and Reminders -->
                        <div class="details-tabs">
                            <button class="details-tab active" data-tab="interactions">Interactions</button>
                            <button class="details-tab" data-tab="reminders">Reminders</button>
                        </div>

                        <!-- Interaction and Reminder Lists -->
                        <div class="list-container" id="interactions-container">
                            <h4>Interactions</h4>
                            <div id="interaction-list"></div>
                        </div>

                        <div class="list-container" id="reminders-container" style="display: none;">
                            <h4>Reminders</h4>
                            <div id="reminder-list"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scripts -->
            <script>
                // Tab switching functionality
                document.addEventListener('DOMContentLoaded', function () {
                    const tabs = document.querySelectorAll('.details-tab');
                    if (tabs.length) {
                        tabs.forEach(tab => {
                            tab.addEventListener('click', function () {
                                // Remove active class from all tabs
                                tabs.forEach(t => t.classList.remove('active'));
                                
                                // Add active class to clicked tab
                                this.classList.add('active');
                                
                                // Hide all tab content
                                document.getElementById('interactions-container').style.display = 'none';
                                document.getElementById('reminders-container').style.display = 'none';
                                
                                // Show selected tab content
                                const tabName = this.getAttribute('data-tab');
                                document.getElementById(`${tabName}-container`).style.display = 'block';
                            });
                        });
                    }
                });
            </script>

            <script src="js/toggleColumns.js">toggleColumn("toggleStatus", "status-column", 6);</script>
            <script src="js/customers_leads_list.js"></script>
            <script src="js/signout.js"></script>

            <!-- Pass PHP interaction types to JavaScript -->
            <script>
                const interactionTypes = <?= json_encode($interaction_types, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            </script>

            <!-- Set base URL for JS -->
            <script>
                const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
            </script>

        </div>
    </section>
</body>
</html>
