<?php
require 'config_session.php';
require_once 'check_session.php';
require 'db.php';

// First check session
php_check_session();

// Handle only POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure JSON response
    header('Content-Type: application/json');
    ob_start(); // Capture unexpected output

    // Retrieve session and input values
    $userId = $_SESSION['user_id'];
    $roleTypeId = $_SESSION['role_type_id'];
    $userName = trim($_POST['nameSearch'] ?? ""); // Default to empty string
    $customerLeadType = (int)($_POST['customerLeadType'] ?? 1); // Default to 1 if not set

    // Prepare SQL Query based on user role
    if ($roleTypeId == 1) { // Admin: Search across all customers/leads
        if ($userName === "") {
            $query = $conn->prepare("SELECT cl.*, s.status_type 
                                    FROM Customers_Leads cl
                                    LEFT JOIN Status s ON cl.status_id = s.status_id
                                    WHERE cl.customer_type = ?");
            $query->bind_param("i", $customerLeadType);
        } else {
            $query = $conn->prepare("SELECT cl.*, s.status_type 
                                    FROM Customers_Leads cl
                                    LEFT JOIN Status s ON cl.status_id = s.status_id
                                    WHERE (customer_lead_id LIKE ? OR name LIKE ? OR company LIKE ? OR email LIKE ? OR phone_num LIKE ? OR s.status_type LIKE ? OR notes LIKE ?) AND customer_type = ?");
            $likeUserName = "%$userName%";
            $query->bind_param("sssssssi", $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $customerLeadType);
        }
    } else { // Sales: Search only within assigned customers/leads
        if ($userName === "") {
            $query = $conn->prepare("SELECT cl.*, s.status_type 
                                   FROM Customers_Leads cl
                                   LEFT JOIN Status s ON cl.status_id = s.status_id
                                   WHERE cl.user_id = ? AND cl.customer_type = ?");
            $query->bind_param("ii", $userId, $customerLeadType);
        } else {
            $query = $conn->prepare("SELECT cl.*, s.status_type 
                                    FROM Customers_Leads cl
                                    LEFT JOIN Status s ON cl.status_id = s.status_id
                                    WHERE (customer_lead_id LIKE ? OR name LIKE ? OR company LIKE ? OR email LIKE ? OR phone_num LIKE ? OR s.status_type LIKE ? OR notes LIKE ?) AND user_id = ? AND customer_type = ?");
            $likeUserName = "%$userName%";
            $query->bind_param("sssssssii", $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $likeUserName, $userId, $customerLeadType);
        }
    }

    // Check if query preparation failed
    if (!$query) {
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    // Execute query
    if (!$query->execute()) {
        echo json_encode(["error" => "Query execution failed", "message" => $query->error]);
        exit();
    }

    $result = $query->get_result();
    $customers = $result->fetch_all(MYSQLI_ASSOC);

    // Close database connections
    $query->close();
    $conn->close();

    // Capture unexpected output before sending JSON
    $output = ob_get_clean();
    if (!empty($output)) {
        echo json_encode(["error" => "Unexpected Output", "message" => $output]);
        exit();
    }

    // Return customers as JSON
    echo json_encode($customers);
    exit();
} else {
    header("Location: ../dashboard_main.php"); // lead user back to dashboard
    exit();
}