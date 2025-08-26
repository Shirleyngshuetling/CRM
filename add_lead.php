<?php
require 'php/config_session.php'; // Start the session
require_once 'php/check_session.php';
require 'php/add_customer_leads_view.php'; // Include the View for lead insertion message

// Verify user session is active
check_session();

// Get user role from session
$role_type_id = $_SESSION['role_type_id'];

// Get and clear lead registration data (used for pre-filling form after an error)
$lead_registration_data = $_SESSION["lead_registration_data"] ?? null;
unset($_SESSION["lead_registration_data"]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Leads - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

    <!-- Toast Message for Successful Lead Addition -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-box">
            <div class='toast-left'>
                <i class="fa-solid fa-circle-check"></i>
                <div id="close_signup_toast" style="font-family: 'Poppins', sans-serif; font-weight:700;">
                    <?php check_customer_lead_registration_success();?>
                </div>
            </div>
            <i class="fa-solid fa-xmark" id='close-button' style="cursor:pointer;"></i>
        </div>
    <?php endif;
        unset($_SESSION['success']);
    ?>

    <!-- Header Section (Top Navigation) -->
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

                <!-- Dropdown Profile Menu -->
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
                    if ($role_type_id == 1):?> 
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
                    <a href="lead_list.php" class="active-link">
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
                <h2 class='main-title'><b>Leads Management</b></h1>
            </div>

            <div class="customer-table">
                <h2 id="tableTitle">Add Leads</h2>
            </div>

            <!-- Leads Section -->
            
            <div class='addCustomerForm'>
                <form id="leadForm" action="php/add_customer_leads.php" method="POST">

                    <div class="user-details">
                        <!-- Input: Lead Name -->
                        <div class='input-box'>
                            <label for="name" class='details'>Name:</label>
                            <input type="text" id="name" name="name" required placeholder="Enter lead's name" value="<?php echo htmlspecialchars($lead_registration_data['name'] ?? ''); ?>">
                        </div>  
                        
                        <!-- Input: Lead Company -->
                        <div class='input-box'>
                            <label for="company" class='details'>Company:</label>
                            <input type="text" id="company" name="company" placeholder="Enter lead's company name" value="<?php echo htmlspecialchars($lead_registration_data['company'] ?? ''); ?>" required>
                        </div>  

                        <!-- Input: Lead Email -->
                        <div class='input-box'>
                            <label for="customerEmail" class='details'>Email:</label>
                            <input type="email" id="customerEmail" name="email" placeholder="Enter lead's email" value="<?php echo htmlspecialchars($lead_registration_data['email'] ?? ''); ?>" required>
                        </div>  

                        <!-- Input: Lead Phone Number -->
                        <div class='input-box'>
                            <label for="phone" class='details'>Phone:</label>
                            <input type="tel" id="phone" name="phone_num" pattern="[0-9]{7,15}" placeholder="Enter lead's phone" value="<?php echo htmlspecialchars($lead_registration_data['phone_num'] ?? ''); ?>" required>
                        </div>
                        
                        <!-- Select: Lead Status -->
                        <div class="input-box">
                            <label for="status">Status:</label>
                            <select id="status" name="status_id" required>
                                <option value="1" <?= (isset($lead_registration_data['status_id']) && $lead_registration_data['status_id'] == 1) ? 'selected' : ''; ?>>New</option>
                                <option value="2" <?= (isset($lead_registration_data['status_id']) && $lead_registration_data['status_id'] == 2) ? 'selected' : ''; ?>>Contacted</option>
                                <option value="3" <?= (isset($lead_registration_data['status_id']) && $lead_registration_data['status_id'] == 3) ? 'selected' : ''; ?>>In Progress</option>
                                <option value="4" <?= (isset($lead_registration_data['status_id']) && $lead_registration_data['status_id'] == 4) ? 'selected' : ''; ?>>Closed</option>
                            </select>
                        </div>
                    

                    </div>
                    
                    <!-- Input: Address -->
                    <div class='input-address'>
                        
                        <label for="address" class='details'>Address:</label>

                        <textarea name="address" id="address"  placeholder="Enter lead's address"><?php echo htmlspecialchars($lead_registration_data['address'] ?? ''); ?></textarea>
                    </div> 
                    
                    <!-- Input: Notes -->
                    <div class='input-notes'>
                        
                        <label for="notes" class='notes'>Notes:</label>

                        <textarea name="notes" id="notes"  placeholder="Enter lead notes"><?php echo htmlspecialchars($lead_registration_data['notes'] ?? ''); ?></textarea>
                    </div> 
                    
                    <!-- Error Messages -->
                    <div class="errorMsg">
                        <p class="error-message">
                            <?php 
                               check_lead_registration_errors();
                            ?>
                        </p>

                    </div>
                    
                    <!-- Hidden Input for Customer Type -->
                    <input type="hidden" name="customerType" value="2">
                    
                    <!-- Buttons -->
                    <div class='addBtnGrp'>
                        <a href="lead_list.php" class="submitBtn" style="text-decoration: none;">Back</a>
                        <button type="submit" class="submitBtn">Add Lead</button>
                    </div>

                </form>

            </div>

        </div>
    
    </section>
    
    <!-- External Scripts -->
    <script src="js/dashboard_main.js"></script>
    <script src="js/signout.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
    </script>
</body>
</html>

