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
    $interactionSearch = trim($_POST['interactionSearch'] ?? ""); // Default to empty string

    // Prepare SQL Query based on user role
    if ($roleTypeId == 1) { // Admin: Search across all interaction histories
        if ($interactionSearch === "") {
            $query = $conn->prepare("SELECT CT.customer_type_name, U.user_id, U.user_name, CL.name, CL.company, 
                                           IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                    FROM Interaction_History AS IH 
                                    LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                    LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id");
        } else {
            $query = $conn->prepare("SELECT CT.customer_type_name, U.user_id, U.user_name, CL.name, CL.company, 
                                           IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                    FROM Interaction_History AS IH 
                                    LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                    LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                    WHERE (CL.name LIKE ? OR CL.company LIKE ? OR U.user_name LIKE ? OR 
                                          IT.interaction_type_name LIKE ? OR IH.interaction_date LIKE ? OR 
                                          IH.interaction_details LIKE ? OR CT.customer_type_name LIKE ?)");
            $likeInteractionName = "%$interactionSearch%";
            $query->bind_param("sssssss", $likeInteractionName, $likeInteractionName, $likeInteractionName, 
                              $likeInteractionName, $likeInteractionName, $likeInteractionName, $likeInteractionName);
        }
    } else { // Sales: Search only own interaction histories
        if ($interactionSearch === "") {
            $query = $conn->prepare("SELECT CT.customer_type_name, U.user_id, U.user_name, CL.name, CL.company, 
                                           IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                    FROM Interaction_History AS IH 
                                    LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                    LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                    WHERE IH.creator_user_id = ?");
            $query->bind_param("i", $userId);
        } else {
            $query = $conn->prepare("SELECT CT.customer_type_name, U.user_id, U.user_name, CL.name, CL.company, 
                                           IH.interaction_date, IH.interaction_details, IT.interaction_type_name 
                                    FROM Interaction_History AS IH 
                                    LEFT JOIN Users AS U ON IH.creator_user_id = U.user_id
                                    LEFT JOIN Customers_Leads AS CL ON IH.customer_lead_id = CL.customer_lead_id
                                    LEFT JOIN Customer_Type AS CT ON CL.customer_type = CT.customer_type_id
                                    LEFT JOIN Interaction_Type AS IT ON IH.interaction_type_id = IT.interaction_type_id
                                    WHERE (CL.name LIKE ? OR CL.company LIKE ? OR U.user_name LIKE ? OR 
                                          IT.interaction_type_name LIKE ? OR IH.interaction_date LIKE ? OR 
                                          IH.interaction_details LIKE ? OR CT.customer_type_name LIKE ?) 
                                          AND IH.creator_user_id = ?");
            $likeInteractionName = "%$interactionSearch%";
            $query->bind_param("sssssssi", $likeInteractionName, $likeInteractionName, $likeInteractionName, 
                              $likeInteractionName, $likeInteractionName, $likeInteractionName, $likeInteractionName, $userId);
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
    $interaction_histories = $result->fetch_all(MYSQLI_ASSOC);

    // Close database connections
    $query->close();
    $conn->close();

    // Capture unexpected output before sending JSON
    $output = ob_get_clean();
    if (!empty($output)) {
        echo json_encode(["error" => "Unexpected Output", "message" => $output]);
        exit();
    }

    // Return results as JSON
    echo json_encode($interaction_histories);
    exit();
} else {
    header("Location: ../interaction_list.php"); // lead user back to interaction list page
    exit();
}