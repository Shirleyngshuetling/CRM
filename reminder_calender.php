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
    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/customer_lead_list.css">
    <link rel="stylesheet" href="css/reminder_calendar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>

<body>

    <!-- Header Section -->
    <section class="header">
        <div class="logo-part">
            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png">
            </div>
            <a href="dashboard_main.php">
                <span class="icon"><i class="fa-solid fa-robot"></i></span>
                <span class="title">
                    <h2>ABB Robotics CRM</h2>
                </span>
            </a>
        </div>

        <div class="header-right">
            <div class="profile" id="profile">
                <div class="profileAlign">
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <!-- Profile Menu -->
                <div class="profile-menu" id="profileMenu">
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>

                    <!-- Admin only: Users section -->
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

    <!-- Main Container -->
    <section class="container">

        <!-- Left Sidebar -->
        <div class="navigation">
            <ul class="nav-menu">
                <li>
                    <a href="dashboard_main.php">
                        <span class="icon"><i class="fa-solid fa-house-user"></i></span>
                        <span class="title">Home</span>
                    </a>
                </li>

                <!-- Admin only: Users Section -->
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
                    <a href="reminder_calender.php" class="active-link">
                        <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                        <span class="title">Calendar</span>
                    </a>
                </li>

                <!-- Sign Out -->
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

            <!-- Middle Part -->
            <div class="middle-part">
                <h2 class='main-title'><b>Reminder Calendar</b></h2>
                <div class="button-group">
                    <a href="add_reminder.php" class="button submitBtn">Add Reminder</a>
                </div>
            </div>

            <!-- FullCalendar Component -->
            <div id="calendar"></div>

            <!-- Modal Overlay -->
            <div id="modalOverlay" class="modal-overlay"></div>

            <!-- Reminder Details Modal -->
            <div id="reminderModal" class="reminder-modal">
                <h2 id="modalTitle">Reminder Details</h2>
                <div class="modal-row"><strong>Date:</strong> <span id="modalDate"></span></div>
                <div class="modal-row"><strong>Customer/Lead Type:</strong> <span id="modalCustomerType"></span></div>
                <div class="modal-row"><strong>Name:</strong> <span id="modalName"></span></div>
                <div class="modal-row"><strong>Company:</strong> <span id="modalCompany"></span></div>
                <div class="modal-row"><strong>Notes:</strong> <span id="modalNotes"></span></div>
                <div class="modal-row"><strong>Staff-in-charged:</strong> <span id="modalUserName"></span></div>
                <div class="modal-footer">
                    <button id="closeModal" class="modal-close-btn">Close</button>
                </div>
            </div>

        </div>

    </section>

    <!-- JavaScript -->
    <script src="js/reminder_calender.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>

</body>

</html>