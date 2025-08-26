<?php
require "config_session.php";
require_once 'check_session.php';
require "db.php"; 

php_check_session();

// Check if it's a POST request and customer_lead_id is provided
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['customer_lead_id'])) {
    $customerId = $_POST['customer_lead_id'];
    $userId = $_SESSION["user_id"];
    $roleId = $_SESSION["role_type_id"];

    // Prepare response array
    $response = ["interactions" => [], "reminders" => []];

    // ----------- Fetch Interactions -----------
    if ($roleId == 1) { // Admin: can see all interactions for this customer
        $interactionQuery = $conn->prepare(
            "SELECT DATE_FORMAT(IH.interaction_date, '%Y-%m-%d') AS formatted_date, IH.interaction_details AS description, IT.interaction_type_name AS type, U.user_name
            FROM Interaction_History AS IH
            LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
            LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
            WHERE IH.customer_lead_id = ?
            ORDER BY IH.interaction_date DESC"
        );
        $interactionQuery->bind_param("i", $customerId);
    } else { // Sales user: can only see their own interactions
        $interactionQuery = $conn->prepare(
            "SELECT DATE_FORMAT(IH.interaction_date, '%Y-%m-%d')AS formatted_date, IH.interaction_details AS description, IT.interaction_type_name AS type, U.user_name
            FROM Interaction_History AS IH
            LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
            LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
            WHERE IH.customer_lead_id = ? AND IH.creator_user_id = ?
            ORDER BY IH.interaction_date DESC"
        );
        $interactionQuery->bind_param("ii", $customerId, $userId);
    }

    if ($interactionQuery->execute()) {
        $result = $interactionQuery->get_result();
        while ($row = $result->fetch_assoc()) {
            $response["interactions"][] = $row;
        }
    }
    $interactionQuery->close();

    // ----------- Fetch Reminders -----------
    if ($roleId == 1) {
        $reminderQuery = $conn->prepare(
            "SELECT DATE_FORMAT(R.reminder_date, '%Y-%m-%d') AS formatted_date, R.notes AS description, IT.interaction_type_name AS type, U.user_name
            FROM Reminder AS R
            LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
            LEFT JOIN Users AS U ON R.creator_user_id = U.user_id
            WHERE R.customer_lead_id = ?
            ORDER BY R.reminder_date DESC"
        );
        $reminderQuery->bind_param("i", $customerId);
    } else {
        $reminderQuery = $conn->prepare(
            "SELECT DATE_FORMAT(R.reminder_date, '%Y-%m-%d') AS formatted_date, R.notes AS description, IT.interaction_type_name AS type, U.user_name
            FROM Reminder AS R
            LEFT JOIN Interaction_Type AS IT ON R.interaction_type_id = IT.interaction_type_id
            LEFT JOIN Users AS U ON R.creator_user_id = U.user_id
            WHERE R.customer_lead_id = ? AND R.creator_user_id = ?
            ORDER BY R.reminder_date DESC"
        );
        $reminderQuery->bind_param("ii", $customerId, $userId);
    }

    if ($reminderQuery->execute()) {
        $result = $reminderQuery->get_result();
        while ($row = $result->fetch_assoc()) {
            $response["reminders"][] = $row;
        }
    }
    $reminderQuery->close();
    $conn->close();

    // Return response as JSON
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
} else {
    // If not a valid POST, redirect to dashboard
    header("Location: ../dashboard_main.php");
    exit();
}
?>