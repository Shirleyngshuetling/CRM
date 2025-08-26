<?php

php_check_session(); // Ensure the session is valid

// General function to get total count based on a query
function getTotal($conn, $query){
    $total = 0;
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        // Handle prepare error
        error_log("Prepare failed: " . $conn->error);
        return $total;
    }
    
    if (!$stmt->execute()) {
        // Handle execute error
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return $total;
    }
    
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total = (int)$row['total'];
    }
    
    $stmt->close();
    
    return $total;
}

//Customer Part
// Get total customers added this month
function getTotalCustomersThistMonth($conn, $user_id) {

    $query = "SELECT COUNT(*) as total
              FROM Customers_Leads 
              WHERE customer_type = 1 
              AND MONTH(account_created_time) = MONTH(CURDATE()) 
              AND YEAR(account_created_time) = YEAR(CURDATE())";

    return getTotal($conn, $query);
}

// Get total customers added last month
function getTotalCustomersLastMonth($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM Customers_Leads 
          WHERE customer_type = 1 
          AND account_created_time >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
          AND account_created_time < DATE_FORMAT(CURDATE(), '%Y-%m-01')";

    return getTotal($conn, $query);
}

// Calculate customer growth percentage
function getCustomerGrowth($currentCustomers, $previousCustomers){

    // Calculate percentage change
    $customerGrowth = $previousCustomers > 0 
        ? round((($currentCustomers - $previousCustomers) / $previousCustomers) * 100, 2)
        : 0;
    return $customerGrowth;
}


// Lead Part
// Get total leads added this month
function getTotalLeadsThistMonth($conn, $user_id) {

    $query = "SELECT COUNT(*) as total
              FROM Customers_Leads 
              WHERE customer_type = 2 
              AND MONTH(account_created_time) = MONTH(CURDATE()) 
              AND YEAR(account_created_time) = YEAR(CURDATE())";

    return getTotal($conn, $query);
}

// Get total leads added last month
function getTotalLeadsLastMonth($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM Customers_Leads 
            WHERE customer_type = 2
            AND account_created_time >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
            AND account_created_time < DATE_FORMAT(CURDATE(), '%Y-%m-01')";
    return getTotal($conn, $query);
}

function getLeadGrowth($currentLeads, $previousLeads){

    // Calculate percentage change
    $leadGrowth = $previousLeads > 0 
        ? round((($currentLeads - $previousLeads) / $previousLeads) * 100, 2)
        : 0;
    return $leadGrowth;
}


// Interaction History Part
// Get recent interactions of this month
function getRecentInteractionThistMonth($conn, $user_id) {

    $userId = $_SESSION["user_id"];
    $roleId = $_SESSION["role_type_id"];

    if ($roleId == 1) {  // Admin role

        $query = $conn->prepare("SELECT U.user_name AS user_name, CL.name AS customer_lead_name, IH.interaction_date AS interaction_date, IH.interaction_details AS interaction_details, IT.interaction_type_name AS interaction_type_name
                                FROM Interaction_History AS IH 
                                LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                WHERE MONTH(interaction_date) = MONTH(CURDATE()) 
                                AND YEAR(interaction_date) = YEAR(CURDATE())");
    } else { // Sales Role
        $query = $conn->prepare("SELECT U.user_name AS user_name, CL.name AS customer_lead_name, IH.interaction_date AS interaction_date, IH.interaction_details AS interaction_details, IT.interaction_type_name AS interaction_type_name
                                FROM Interaction_History AS IH 
                                LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                WHERE MONTH(interaction_date) = MONTH(CURDATE()) 
                                AND YEAR(interaction_date) = YEAR(CURDATE())
                                AND U.user_id = ?");
        $query->bind_param("i", $userId);
    }

    if (!$query) {
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute();
    $result = $query->get_result();
    $recent_interactions = [];

    while ($row = $result->fetch_assoc()) {
        $recent_interactions[] = $row;
    }

    $query->close();
    $conn->close();
    return $recent_interactions;
    
}

// Get total interactions of this month
function getTotalInteractionsThisMonth($conn, $user_id) {

    $query = "SELECT COUNT(*) as total
              FROM Interaction_History
              WHERE MONTH(interaction_date) = MONTH(CURDATE()) 
              AND YEAR(interaction_date) = YEAR(CURDATE())";

    return getTotal($conn, $query);
}

// Get recent interactions of last month
function getTotalInteractionsLastMonth($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM Interaction_History
              WHERE interaction_date >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
              AND interaction_date < DATE_FORMAT(CURDATE(), '%Y-%m-01')";
    return getTotal($conn, $query);
}

function getInteractionGrowth($currentInteractions, $previousInteractions){

    // Calculate percentage change
    $interactionGrowth = $previousInteractions > 0 
        ? round((($currentInteractions - $previousInteractions) / $previousInteractions) * 100, 2)
        : 0;
    return $interactionGrowth;
}


// Reminder Part
// Get total reminders this month
function getTotalRemindersThisMonth($conn, $user_id) {

    $query = "SELECT COUNT(*) as total
              FROM Reminder
              WHERE MONTH(reminder_date) = MONTH(CURDATE()) 
              AND YEAR(reminder_date) = YEAR(CURDATE())";

    return getTotal($conn, $query);
}

// Get total reminders last month
function getTotalRemindersLastMonth($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM Reminder 
              WHERE reminder_date >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
              AND reminder_date < DATE_FORMAT(CURDATE(), '%Y-%m-01')";
    return getTotal($conn, $query);
}

function getReminderGrowth($currentReminders, $previousReminders){

    // Calculate percentage change
    $reminderGrowth = $previousReminders > 0 
        ? round((($currentReminders - $previousReminders) / $previousReminders) * 100, 2)
        : 0;
    return $reminderGrowth;
}