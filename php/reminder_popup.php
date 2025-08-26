<?php
require 'config_session.php'; // Start the session
include "db.php"; // Include database connection
require "check_session.php"; // Include session checking functions

php_check_session(); // Verify session is active

date_default_timezone_set("Asia/Singapore"); // Set timezone to Singapore

// Get the current date
$current_date = date("Y-m-d");

// Create a DateTime object and add 1 day to it
$current_datetime = new DateTime($current_date);
$current_datetime->modify("+1 day");
$day_before_date = $current_datetime->format("Y-m-d");

$user_id = $_SESSION["user_id"];

// Prepare SQL query to fetch reminders for today or the next day
$query = $conn->prepare("SELECT u.user_id AS user_id, u.user_name AS user_name, c.name AS name, c.email AS email, r.reminder_id AS reminder_id, r.reminder_date AS reminder_date, r.notes AS notes, r.day_before_reminder, r.same_day_reminder, i.interaction_type_name AS interaction_type_name FROM reminder AS r
                        JOIN users AS u
                            ON r.creator_user_id = u.user_id
                        JOIN customers_leads AS c
                            ON r.customer_lead_id = c.customer_lead_id
                        JOIN interaction_type AS i
                            ON r.interaction_type_id = i.interaction_type_id
                        WHERE creator_user_id = ? AND (reminder_date = ? OR reminder_date = ?) AND day_before_reminder = 0 AND r.same_day_reminder = 0");
$query->bind_param("iss", $user_id, $current_date, $day_before_date);
$query->execute();
$result = $query->get_result();
$reminders = [];

while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}
// Close the statement
$query->close();


// Return reminders as JSON
echo json_encode($reminders);