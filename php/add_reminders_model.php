<?php
    // Insert a new reminder into the database
    function insertReminder($conn, $creator_user_id, $customer_lead_id, $reminder_date, $notes, $interaction_type_id) {
        $query = "INSERT INTO Reminder (creator_user_id, customer_lead_id, reminder_date, reminder_created_time, notes, interaction_type_id) VALUES (?, ?, ?, NOW(), ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissi", $creator_user_id, $customer_lead_id, $reminder_date, $notes, $interaction_type_id);
    

        return $stmt->execute(); // Return true if successful, false otherwise
    }