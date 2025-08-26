<?php

// Insert a new interaction history record into the database
function insertInteractionHistory($conn, $creator_user_id, $customer_lead_id, $interaction_details, $interaction_type_id, $interaction_history_date) {
    // Prepare the SQL query to insert interaction history
    $query = "INSERT INTO Interaction_History (creator_user_id, customer_lead_id, created_time, interaction_details, interaction_type_id, interaction_date) VALUES (?, ?, NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($query); // Prepare statement
    
    // Bind parameters to the SQL query
    $stmt->bind_param("iisis", $creator_user_id, $customer_lead_id, $interaction_details, $interaction_type_id, $interaction_history_date);

    return $stmt->execute(); // Execute the query, return true if successful, false otherwise
}
