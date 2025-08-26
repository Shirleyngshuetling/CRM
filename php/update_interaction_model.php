<?php

function updateInteraction($conn, $interaction_history_id, $customer_lead_id, $interaction_type_id, $interaction_date, $interaction_details) {
    
    $query = $conn->prepare("UPDATE Interaction_History SET customer_lead_id=?, interaction_type_id=?, interaction_date=?, interaction_details=? WHERE interaction_history_id=?");
    $query->bind_param("iissi",  $customer_lead_id, $interaction_type_id, $interaction_date, $interaction_details, $interaction_history_id);

    return $query->execute(); // Return true if successful, false otherwise
}
