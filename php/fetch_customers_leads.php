<?php
require 'config_session.php';
require_once 'check_session.php';
require "db.php"; 

php_check_session();// Ensure the user is logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $userId = $_SESSION["user_id"];
    $roleId = $_SESSION["role_type_id"];
    $customerLeadType = $_POST['customer_lead_type'] ?? 1; // Default to customers

    if ($customerLeadType == 1) { // Customers
        if ($roleId == 1) { // Admin: fetch all customers
            $query = $conn->prepare("SELECT customer_lead_id, name, company, email, phone_num, notes FROM Customers_Leads WHERE customer_type = ?");
            $query->bind_param("i", $customerLeadType);
        } else { // Sales Role : fetch only own customers
            $query = $conn->prepare("SELECT customer_lead_id, name, company, email, phone_num, notes FROM Customers_Leads WHERE user_id = ? AND customer_type = ?");
            $query->bind_param("ii", $userId, $customerLeadType);
        }
    } else { // Leads
        if ($roleId == 1) { // Admin: fetch all leads with status info
            $query = $conn->prepare("SELECT cl.customer_lead_id, cl.name, cl.company, cl.email, cl.phone_num, cl.notes, s.status_type 
                FROM Customers_Leads cl
                LEFT JOIN Status s ON cl.status_id = s.status_id
                WHERE cl.customer_type = ?
            ");
            $query->bind_param("i", $customerLeadType);
        } else { // Sales role: fetch own leads with status info
            $query = $conn->prepare("SELECT cl.customer_lead_id, cl.name, cl.company, cl.email, cl.phone_num, cl.notes, s.status_type 
                FROM Customers_Leads cl
                LEFT JOIN Status s ON cl.status_id = s.status_id
                WHERE cl.user_id = ? AND cl.customer_type = ?
            ");
            $query->bind_param("ii", $userId, $customerLeadType);
        }
    }
    if (!$query) {
        // If query preparation fails, return error as JSON
        echo json_encode(["error" => "Query preparation failed", "message" => $conn->error]);
        exit();
    }

    $query->execute();
    $result = $query->get_result();
    $customers = [];

    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }

    $query->close();
    $conn->close();

     // Output the customers as a JSON response
    header('Content-Type: application/json');
    echo json_encode($customers, JSON_PRETTY_PRINT);
}
else{
    // If not POST request, redirect to dashboard
    header("Location:../dashboard_main.php");
}