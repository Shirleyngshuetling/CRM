<?php
// --- PHP Section: Setup ---
// Include session handling and database connection
require 'php/config_session.php';
require_once 'php/check_session.php';
require 'php/db.php';
require 'php/add_interactions_view.php'; // View for interaction insertion messages

// Retrieve any previous form submission data if exists
$interaction_history_insertion_data = $_SESSION["interaction_history_insertion_data"] ?? null;
unset($_SESSION["interaction_history_insertion_data"]);

check_session(); // Ensure user is logged in

// Get session variables
$user_id = $_SESSION["user_id"];
$role_id = $_SESSION["role_type_id"];

// Fetch customers/leads based on user role
if ($role_id == 2) { // Sales representative
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads WHERE user_id = ?");
    $query->bind_param("i", $user_id);
} else { // Admin and others
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads");
}
$query->execute();
$result = $query->get_result();
$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
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
    <title>Add Interactions - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <!-- Toast Message for Success -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_interaction_history_insertion_errors()?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
    <?php endif;unset($_SESSION['success']);?>
    
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

         <!-- Profile Section -->
        <div class="header-right">
            <div class="profile" id="profile">
                <div class='profileAlign'>
                    <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <div class="profile_icon"><img src="./assets/user-line.png"></div>
                </div>

                <!-- Profile Menu Dropdown -->
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
    
    <!-- Main Container -->
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
                    <a href="interaction_list.php" class="active-link">
                        <span class="icon"><i class="fa-solid   fa-circle-check"></i></span>
                        <span class="title">Interactions</span>
                    </a>
                </li>

                <li>
                    <a href="reminder_list.php">
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
        
        <!-- Main Content Area -->
        <div class="main">

            <div class='middle-part'>
                <h2 class='main-title'><b>Interaction Histories</b></h1>
            </div>

            <div class="customer-table">
                <h2 id="tableTitle">Add Interactions</h2>
            </div>
            
            <!-- Add Interaction Form Section -->
        
            <div class='addCustomerForm'>
                <form id="interactionForm" action="php/add_interactions.php" method="POST">

                    <div class="user-details">

                        <!-- Select Customer or Lead -->
                        <div class='input-box'>
                            <label for="customer_lead_id">Customer/Lead:</label>
                            <select id="customer_lead_id" name="customer_lead_id" required>
                                <optgroup label="Customers">
                                    <?php foreach ($customers as $customer): ?>
                                        <?php if ($customer['customer_type'] == 1): //1 is for customers ?>
                                            <option value="<?= $customer['customer_lead_id'] ?>" 
                                                <?= (isset($interaction_history_insertion_data['customer_lead_id']) && $interaction_history_insertion_data['customer_lead_id'] == $customer['customer_lead_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($customer['name'].' (ID: '.(string)($customer['customer_lead_id']) . ')') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Leads">
                                    <?php foreach ($customers as $customer): ?>
                                        <?php if ($customer['customer_type'] == 2): // 2 is for leads ?>
                                            <option value="<?= $customer['customer_lead_id'] ?>" 
                                                <?= (isset($interaction_history_insertion_data['customer_lead_id']) && $interaction_history_insertion_data['customer_lead_id'] == $customer['customer_lead_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($customer['name'].' (ID: '.(string)($customer['customer_lead_id']) . ')') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                        </div>  
                    
                        <!-- Select Interaction Type -->
                        <div class='input-box'>
                            <label for="interactionType">Interaction Type:</label>
                            <select id="interactionType" name="interactionType" required>
                            <?php foreach($interaction_types as $type): ?>
                            <option value="<?= $type['interaction_type_id'] ?>" 
                                <?= ($interaction_history_insertion_data['interactionType'] ?? '') == $type['interaction_type_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['interaction_type_name']) ?>
                            </option>
                            <?php endforeach; ?>
                            </select>
                        </div>  
                        
                        <!-- Select Date -->
                        <div class='input-box'>
                            <label for="interactionHistoryDate">Interaction History Date:</label>
                            <input type="date" id="interactionHistoryDate" name="interactionHistoryDate"  value="<?= htmlspecialchars($interaction_history_insertion_data['interactionHistoryDate'] ?? '') ?>"   max="<?= date('Y-m-d') ?>" required><!-- This adds client-side validation --> 
                        </div> 

                    </div>
                    
                    <!-- Interaction Details -->
                    <div class='input-address'>
                        
                        <label for="interactionDetails" class='details'>Details</label>

                        <textarea name="interactionDetails" id="interactionDetails"  placeholder="Enter interactions details"><?php echo htmlspecialchars($interaction_history_insertion_data['interactionDetails'] ?? ''); ?></textarea>
                    </div> 
                    
                    <!-- Submit Buttons -->
                    <div class='addBtnGrp'>
                        <a href="interaction_list.php" class="submitBtn" style="text-decoration: none;">Back</a>
                        <button type="submit" class="submitBtn">Add Interaction</button>
                    </div>

                </form>

            </div>

        </div>

    </section>

    <!-- JavaScript -->
    <script src="js/dashboard_main.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>
