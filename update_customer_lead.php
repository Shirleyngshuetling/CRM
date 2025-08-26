<?php
require "php/config_session.php";
require_once 'php/check_session.php';
require "php/db.php"; // Database connection
require "php/update_customer_lead_view.php";

// Check session validity
check_session();

// Fetch role type from session
$role_type_id = $_SESSION['role_type_id'];

$isCustomer = 1;
$isLead = 2;

// Handling form submission for updating customer/lead
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_lead_id'])) {
    $customer_id = (int)$_POST['customer_lead_id'];
    
    // Prepare SQL statement to get customer details
    $query = $conn->prepare("SELECT * FROM Customers_Leads WHERE customer_lead_id = ?");
    $query->bind_param("i", $customer_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc(); // Fetch the customer data
    } else {
        echo "Customer not found.";
        exit;
    }
}
// Handling session data for customer or lead update
else if (isset($_SESSION['customer_update_data']) && !empty($_SESSION['customer_update_data'])) {
    $customer = $_SESSION["customer_update_data"];
    $customer_type = 1; // Force customer type
    unset($_SESSION["customer_update_data"]);
} 
else if (isset($_SESSION['lead_update_data']) && !empty($_SESSION['lead_update_data'])) {
    $customer = $_SESSION["lead_update_data"];
    $customer_type = 2; // Force lead type
    unset($_SESSION["lead_update_data"]);
}
// Handling direct access with an ID parameter in the URL
else if (isset($_GET['id'])) {
    // This handles direct access to the page with an ID parameter
    $customer_id = $_GET['id'];
    $query = $conn->prepare("SELECT * FROM Customers_Leads WHERE customer_lead_id = ?");
    $query->bind_param("i", $customer_id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found.";
        exit;
    }
}
else {
    echo "Invalid request.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<!-- Header Section -->
<section class="header">
    <div class="logo-part">
        <div class="menu-icon">
            <img src="./assets/menu-fold-line.png" alt="Menu">
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
                <div class="profile_icon"><img src="./assets/user-line.png" alt="User Icon"></div>
            </div>
        
            <div class="profile-menu" id="profileMenu">
                <!-- Navigation links -->
                <a href="dashboard_main.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard_main.php' ? 'active-link' : '' ?>">
                    <i class="fa-solid fa-house-user"></i> Home
                </a>
                
                <!-- Admin links -->
                <?php if ($role_type_id == 1): ?>
                    <a href="user_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'user_list.php' ? 'active-link' : '' ?>">
                        <i class="fa-solid fa-user-tie"></i> Users
                    </a>
                <?php endif; ?>
                
                <!-- Customer and Lead links -->
                <a href="customer_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'customer_list.php' ? 'active-link' : '' ?>">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
                <a href="lead_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'lead_list.php' ? 'active-link' : '' ?>">
                    <i class="fa-solid fa-users-line"></i> Leads
                </a>
                
                <!-- Other links -->
                <a href="interaction_list.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'interaction_list.php' ? 'active-link' : '' ?>">
                    <i class="fa-solid fa-circle-check"></i> Interactions
                </a>
                <a href="reminder_list.php" class="<?php echo basename($_SERVER ['PHP_SELF']) === 'reminder_list.php' ? 'active-link' : '' ?>">
                    <i class="fa-regular fa-note-sticky"></i> Reminders
                </a>
                <a href="reminder_calender.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reminder_calender.php' ? 'active-link' : '' ?>">
                    <i class="fa-regular fa-calendar-days"></i> Calendar
                </a>
                
                <!-- Sign Out link -->
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
            <li><a href="dashboard_main.php"><span class="icon"><i class="fa-solid fa-house-user"></i></span><span class="title">Home</span></a></li>
            
            <?php // Only show this option if the user is an admin
            if ($role_type_id == 1): ?>
                <li><a href="user_list.php"><span class="icon"><i class="fa-solid fa-user-tie"></i></span><span class="title">Users</span></a></li>
            <?php endif; ?>

            <li><a href="customer_list.php" <?= isset($customer['customer_type']) && $customer['customer_type'] == 1 ? 'class="active-link"' : '' ?>>
                <span class="icon"><i class="fa-solid fa-users"></i></span><span class="title">Customers</span></a></li>

            <li><a href="lead_list.php" <?= isset($customer['customer_type']) && $customer['customer_type'] == 2 ? 'class="active-link"' : '' ?>>
                <span class="icon"><i class="fa-solid fa-users-line"></i></span><span class="title">Leads</span></a></li>

            <li><a href="interaction_list.php"><span class="icon"><i class="fa-solid fa-circle-check"></i></span><span class="title">Interactions</span></a></li>
            <li><a href="reminder_list.php"><span class="icon"><i class="fa-regular fa-note-sticky"></i></span><span class="title">Reminders</span></a></li>
            <li><a href="reminder_calender.php"><span class="icon"><i class="fa-regular fa-calendar-days"></i></span><span class="title">Calendar</span></a></li>
            <li><a href="#" onclick="logout()"><span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span><span class="title">Sign Out</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="middle-part">
            <h2 class="main-title"><b><?= $customer['customer_type'] == 1 ? 'Customer' : 'Lead' ?> Management</b></h2>
        </div>

        <div class="customer-table">
            <h2 id="tableTitle">Update <?= $customer['customer_type'] == 1 ? 'Customer' : 'Lead' ?></h2>
        </div>

        <!-- Customer/Lead Update Form -->
        <div class="addCustomerForm">
            <form id="customerForm" action="php/update_customer_lead.php" method="POST">
                <!-- Hidden fields to pass customer ID and type -->
                <input type="hidden" name="customer_lead_id" value="<?= isset($customer['customer_lead_id']) ? htmlspecialchars($customer['customer_lead_id']) : (isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '') ?>">
                <input type="hidden" name="customerType" value="<?= $customer['customer_type'] ?>">

                <!-- Customer/Lead Details -->
                <div class="user-details">
                    <div class="input-box">
                        <label for="name" class="details">Name:</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['name']) ?>" placeholder="Enter name" required>
                    </div>  

                    <div class="input-box">
                        <label for="company" class="details">Company:</label>
                        <input type="text" id="company" name="company" placeholder="Enter company name" value="<?= htmlspecialchars($customer['company']) ?>" required>
                    </div>  

                    <div class="input-box">
                        <label for="customerEmail" class="details">Email:</label>
                        <input type="email" id="customerEmail" name="email" placeholder="Enter email" value="<?= htmlspecialchars($customer['email']) ?>" required>
                    </div>  

                    <div class="input-box">
                        <label for="phone" class="details">Phone:</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{7,15}" placeholder="Enter phone" value="<?= htmlspecialchars($customer['phone_num']) ?? 'Null' ?>" required>
                    </div>

                    <?php if ($customer['customer_type'] == $isLead): ?>
                        <div class="input-box">
                            <label for="status">Status:</label>
                            <select id="status" name="status_id" required>
                                <option value="1" <?= ($customer['status_id'] == 1) ? "selected" : "" ?>>New</option>
                                <option value="2" <?= ($customer['status_id'] == 2) ? "selected" : "" ?>>Contacted</option>
                                <option value="3" <?= ($customer['status_id'] == 3) ? "selected" : "" ?>>In Progress</option>
                                <option value="4" <?= ($customer['status_id'] == 4) ? "selected" : "" ?>>Closed</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Address and Notes -->
                <div class="input-address">
                    <label for="address" class="details">Address:</label>
                    <textarea name="address" id="address" placeholder="Enter address"><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                </div>

                <div class="input-notes">
                    <label for="notes" class="notes">Notes:</label>
                    <textarea name="notes" id="notes" placeholder="Enter notes"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                </div>

                <!-- Error Message Display -->
                <div class="errorMsg">
                    <p class="error-message">
                        <?php
                            if($customer['customer_type']==1){
                                check_customer_update_errors();
                            }
                            else{
                                check_lead_update_errors();
                            }
                        ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="addBtnGrp">
                    <a href="<?= $customer['customer_type'] == 1 ? 'customer_list.php' : 'lead_list.php' ?>" class="submitBtn" style="text-decoration: none">Back</a>
                    <button type="button" id="undoChanges" class="submitBtn">Undo Changes</button>
                    <button type="submit" class="submitBtn">Update <?= $customer['customer_type'] == 1 ? 'Customer' : 'Lead' ?></button>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- JavaScript Files -->
<script src="js/undo_update_customer_lead.js"></script>
<script src="js/dashboard_main.js"></script>
<script src="js/signout.js"></script>
<script>
    const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
</script>

</body>
</html>