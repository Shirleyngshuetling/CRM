<?php
// Start session and include necessary files
require 'php/config_session.php';
require_once 'php/check_session.php';
require "php/db.php";
require 'php/add_reminders_view.php';

// Retrieve reminder insertion data if available
$reminder_insertion_data = $_SESSION["reminder_insertion_data"] ?? null;
unset($_SESSION["reminder_insertion_data"]);

// Check user session
check_session();

// Check user session
$user_id = $_SESSION["user_id"];
$role_id = $_SESSION["role_type_id"];

// Fetch customers or leads based on user role
if ($role_id == 2) {
    // Fetch only the customers/leads assigned to this sales representative
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads WHERE user_id = ?");
    $query->bind_param("i", $user_id);
} else {
    // Fetch all customers/leads for admin
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads");
}

// Execute the query
$query->execute();
$result = $query->get_result();

$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

// Close the statement
$query->close();


//Fetch Interaction Type
$query = $conn->prepare("Select * from Interaction_Type");
// Execute the query
$query->execute();
$result = $query->get_result();
$interaction_types = [];

while ($row = $result->fetch_assoc()) {
    $interaction_types[] = $row;
}

// Close the statement
$query->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reminder - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>
<body>

    <!-- Success Toast Notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
            <i class="fa-solid fa-circle-check"></i>
            <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                <?php check_reminder_insertion_errors()?>
            </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
    <?php endif;
        unset($_SESSION['success']);
    ?>

    <!-- Header Section -->
    <section class="header">
        <div class = "logo-part">

            <div class="menu-icon">
                <img src="./assets/menu-fold-line.png" >
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

                <!-- Profile Dropdown Menu -->
                <div class="profile-menu" id="profileMenu">
                    <!-- Home -->
                    <a href="dashboard_main.php" class="<?php echo basename($_SERVER    ['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-house-user"></i> Home
                    </a>
                
                    <!-- Users (only for admin) -->
                    <?php if ($role_type_id == 1): ?>
                        <a href="user_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-tie"></i> Users
                        </a>
                    <?php endif; ?>
                
                    <!-- Customers -->
                    <a href="customer_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>
                
                    <!-- Leads -->
                    <a href="lead_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-users-line"></i> Leads
                    </a>
                        
                    <!-- Interactions -->
                    <a href="interaction_list.php" class="<?php echo basename   ($_SERVER['PHP_SELF']) === 'interaction_list.php' ?    'active-link' : '' ?>">
                        <i class="fa-solid fa-circle-check"></i> Interactions
                    </a>
                        
                    <!-- Reminders -->
                    <a href="reminder_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-regular fa-note-sticky"></i> Reminders
                    </a>
                        
                    <!-- Calendar -->
                    <a href="reminder_calender.php" class="<?php echo basename  ($_SERVER['PHP_SELF']) === 'reminder_calender.php' ?  'active-link' : '' ?>">
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

    <section class="container">

        <!-- Left sidebar -->
        <div class="navigation">
            <ul class="nav-menu">
                <li>
                    <a href="dashboard_main.php" >
                        <span class="icon"><i class="fa-solid   fa-house-user"></i></span>
                        <span class="title">Home</span>
                    </a>
                </li>

                <?php //only show this selection in nav bar is user is admin
                    if ($role_id == 1):?> 
                        <li>
                            <a href="user_list.php">
                                <span class="icon"><i class="fa-solid fa-user-tie"></i></span>
                                <span class="title">Users</span>
                            </a>
                        </li>
                <?php endif;?>

                <li>
                    <a href="customer_list.php">
                        <span class="icon"><i class="fa-solid fa-users"></i></span>
                        <span class="title">Customers</span>
                    </a>
                </li>

                <li>
                    <a href="lead_list.php" >
                        <span class="icon"><i class="fa-solid   fa-users-line"></i></span>
                        <span class="title">Leads</span>
                    </a>
                </li>

                <li>
                    <a href="interaction_list.php">
                        <span class="icon"><i class="fa-solid   fa-circle-check"></i></span>
                        <span class="title">Interactions</span>
                    </a>
                </li>

                <li>
                    <a href="reminder_list.php" class="active-link">
                        <span class="icon"><i class="fa-regular     fa-note-sticky"></i></span>
                        <span class="title">Reminders</span>
                    </a>
                </li>

                <li>
                    <a href="reminder_calender.php">
                        <span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
                    <span class="title">Calender</span>
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

        <div class="main">

            <div class='middle-part'>
                <h2 class='main-title'><b>Reminders Management</b></h1>
            </div>

            <div class="customer-table">
                <h2 id="tableTitle">Add Reminder</h2>
            </div>
            
            <!-- Add Reminder Form Section -->

            <div class='addCustomerForm'>
                <form id="reminderForm" action="php/add_reminders.php" method="POST">

                    <div class="user-details">

                        <!-- Customer/Lead Selection -->
                        <div class='input-box'>
                            <label for="customer_lead_id">Customer/Lead:</label>
                            <select id="customer_lead_id" name="customer_lead_id" required>
                                <optgroup label="Customers">
                                    <?php foreach ($customers as $customer): ?>
                                        <?php if ($customer['customer_type'] == 1): //1 is for customers ?>
                                            <option value="<?= $customer['customer_lead_id'] ?>" 
                                                <?= (isset($reminder_insertion_data['customer_lead_id']) && $reminder_insertion_data['customer_lead_id'] == $customer['customer_lead_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($customer['name'].' (ID: '.(string)($customer['customer_lead_id']) . ')') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Leads">
                                    <?php foreach ($customers as $customer): ?>
                                        <?php if ($customer['customer_type'] == 2): // 2 is for leads ?>
                                            <option value="<?= $customer['customer_lead_id'] ?>" 
                                                <?= (isset($reminder_insertion_data['customer_lead_id']) && $reminder_insertion_data['customer_lead_id'] == $customer['customer_lead_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($customer['name'].' (ID: '.(string)($customer['customer_lead_id']) . ')') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div> 
                    
                        <!-- Reminder Date -->
                        <div class='input-box'>
                            <label for="reminderDate">Reminder Date:</label>
                            <input type="date" id="reminderDate" name="reminderDate" value="<?= htmlspecialchars($reminder_insertion_data['reminder_date'] ?? '') ?>" min="<?= date('Y-m-d') ?>" required>
                        </div>  
                        
                        <!-- Interaction Type Selection -->
                        <div class='input-box'>
                            <label for="interactionType">Interaction Type:</label>
                            <select id="interactionType" name="interactionType" required>
                                <?php foreach($interaction_types as $type): ?>
                                    <option value="<?= $type['interaction_type_id'] ?>" 
                                        <?= ($reminder_insertion_data['interactionType'] ?? '') == $type['interaction_type_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['interaction_type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>  

                    </div>
                
                    <!-- Reminder Notes -->
                    <div class='input-address'>
                        <label for="reminderNotes" class='details'>Notes:</label>
                        <textarea name="reminderNotes" id="reminderNotes" placeholder="Enter reminder notes"><?= htmlspecialchars($reminder_insertion_data['notes'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Form Buttons -->
                    <div class='addBtnGrp'>
                        <a href="reminder_list.php" class="submitBtn" style="text-decoration: none;">Back</a>
                        <button type="submit" class="submitBtn">Add Reminder</button>
                    </div>

                </form>

            </div>

        </div>

    </section>


    <script src="js/dashboard_main.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>

