<?php
// Including necessary PHP files
require "php/config_session.php";
require_once 'php/check_session.php';
require "php/db.php"; // Database connection
require "php/update_interaction_view.php";

// Check if the session is valid
check_session();

// Handle POST request to fetch the interaction history
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['interaction_history_id'])) {
    $interaction_history_id = (int)$_POST['interaction_history_id'];
    
    // Prepare SQL statement to get the interaction history details
    $query = $conn->prepare("SELECT * FROM Interaction_History WHERE interaction_history_id = ?");
    $query->bind_param("i", $interaction_history_id);
    $query->execute();
    $result = $query->get_result();
    
    // Check if the interaction history exists
    if ($result->num_rows > 0) {
        $interaction = $result->fetch_assoc(); // Fetch the interaction details
    } else {
        echo "Interaction History not found.";
        exit;
    }
}
// Handle session data if available for interaction update
else if (isset($_SESSION['interaction_update_data']) && !empty($_SESSION['interaction_update_data'])) {
    $interaction = $_SESSION["interaction_update_data"];
    unset($_SESSION["interaction_update_data"]);
} 
else {
    echo "Invalid request.";
    exit;
}

// Fetch user and role information from the session
$user_id = $_SESSION["user_id"];
$role_id = $_SESSION["role_type_id"];

// Fetch customers based on the user role
if ($role_id == 2) {
    // Fetch only the customers/leads assigned to this sales representative
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads WHERE user_id = ?");
    $query->bind_param("i", $user_id);
} else {
    // Fetch all customers/leads
    $query = $conn->prepare("SELECT customer_lead_id, name, customer_type FROM Customers_Leads");
}

// Execute the query
$query->execute();
$result = $query->get_result();
$customers = [];

// Store the fetched customers/leads in an array
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

// Close the statement
$query->close();

// Fetch interaction types from the database
$query = $conn->prepare("SELECT * FROM Interaction_Type");
$query->execute();
$result = $query->get_result();
$interaction_types = [];

// Store the interaction types in an array
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
    <title>Update Interaction - ABB Robotics CRM</title>
    <link rel="stylesheet" href="css/add_customer_lead.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

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

    <div class="header-right">
        <div class="profile" id="profile">
            <div class='profileAlign'>
                <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <div class="profile_icon"><img src="./assets/user-line.png" alt="Profile Icon"></div>
            </div>

            <div class="profile-menu" id="profileMenu">
                <!-- Home -->
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

<section class="container">

    <!-- Left sidebar -->
    <div class="navigation">
        <ul class="nav-menu">
            <li>
                <a href="dashboard_main.php">
                    <span class="icon"><i class="fa-solid fa-house-user"></i></span>
                    <span class="title">Home</span>
                </a>
            </li>

            <li>
                <a href="user_list.php">
                    <span class="icon"><i class="fa-solid fa-user-tie"></i></span>
                    <span class="title">Users</span>
                </a>
            </li>

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

    <!-- Main content -->
    <div class="main">
        <div class='middle-part'>
            <h2 class='main-title'><b>Interaction History Management</b></h2>
        </div>

        <div class="customer-table">
            <h2 id="tableTitle">Update Interaction History</h2>
        </div>

        <div class='addCustomerForm'>
            <!-- Form to update interaction history -->
            <form id="customerForm" action="php/update_interaction.php" method="POST">

                <!-- Hidden field to pass the interaction history ID -->
                <input type="hidden" name="interaction_history_id" value="<?= htmlspecialchars($interaction['interaction_history_id'] ?? '') ?>">

                <div class="user-details">
                    <div class='input-box'>
                        <label for="customer_lead_id">Customer/Lead:</label>
                        <!-- Customer/Lead Dropdown -->
                        <select id="customer_lead_id" name="customer_lead_id" required>
                            <?php
                                // Fetch the selected customer/lead based on interaction
                                $selected_customer_lead_id = $interaction['customer_lead_id'] ?? null;
                                $selected_customer = null;

                                // Search for the customer/lead with the matching ID
                                foreach ($customers as $customer) {
                                    if ($customer['customer_lead_id'] == $selected_customer_lead_id) {
                                        $selected_customer = $customer;
                                        break;
                                    }
                                }

                                // Only show the selected customer/lead in the dropdown
                                if ($selected_customer) {
                                    echo "<option value=\"" . $selected_customer['customer_lead_id'] . "\" selected>";
                                    echo htmlspecialchars($selected_customer['name'] . ' (ID: ' . (string)($selected_customer['customer_lead_id']) . ')');
                                    echo "</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <div class='input-box'>
                        <label for="interactionType">Interaction Type:</label>
                        <!-- Interaction Type Dropdown -->
                        <select id="interactionType" name="interactionType" required>
                            <?php foreach ($interaction_types as $type): ?>
                            <option value="<?= $type['interaction_type_id'] ?>" 
                                <?= ($interaction['interaction_type_id'] ?? '') == $type['interaction_type_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['interaction_type_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class='input-box'>
                        <label for="interactionHistoryDate">Interaction History Date:</label>
                        <!-- Date Field -->
                        <input type="date" id="interactionHistoryDate" name="interactionHistoryDate" 
                            value="<?= htmlspecialchars($interaction['interaction_date'] ?? '') ?>" 
                            max="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class='input-address'>
                    <label for="interactionDetails" class='details'>Details</label>
                    <!-- Details Textarea -->
                    <textarea name="interactionDetails" id="interactionDetails" placeholder="Enter interactions details"><?= htmlspecialchars($interaction['interaction_details'] ?? '') ?></textarea>
                </div>

                <div class="errorMsg">
                    <p class="error-message">
                        <?php check_interaction_update_errors(); ?>
                    </p>
                </div>

                <div class="addBtnGrp">
                    <a href="interaction_list.php" class="submitBtn" style="text-decoration: none">Back</a>
                    <button type="button" id="undoChanges" class="submitBtn">Undo Changes</button>
                    <button type="submit" class="submitBtn">Update Interaction History</button>
                </div>
            </form>
        </div>
    </div>

</section>

<!-- JS files -->
<script src="js/undo_update_customer_lead.js"></script>
<script src="js/dashboard_main.js"></script>
<script src="js/signout.js"></script>

<script>
    const BASE_URL = "<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/'); ?>";
</script>

</body>
</html>
