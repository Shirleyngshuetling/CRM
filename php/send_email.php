<?php
// Start session and include necessary files
require 'config_session.php'; 
require 'db.php';
require 'check_session.php';
php_check_session();

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default timezone
date_default_timezone_set("Asia/Singapore");

// Get today's date
$current_date = date("Y-m-d");

// Get tomorrow's date
$current_datetime = new DateTime($current_date);
$current_datetime->modify("+1 day");
$day_before_date = $current_datetime->format("Y-m-d");

// Get current logged-in user ID
$user_id = $_SESSION["user_id"];

// Prepare and execute query to fetch reminders for today or tomorrow
$query = $conn->prepare("
    SELECT u.user_name, u.email, c.name, c.customer_type, r.reminder_id, r.reminder_date, r.notes, r.day_before_reminder, r.same_day_reminder, i.interaction_type_name
    FROM reminder AS r
    JOIN users AS u ON r.creator_user_id = u.user_id
    JOIN customers_leads AS c ON r.customer_lead_id = c.customer_lead_id
    JOIN interaction_type AS i ON r.interaction_type_id = i.interaction_type_id
    WHERE creator_user_id = ? 
      AND (reminder_date = ? OR reminder_date = ?) 
      AND day_before_reminder = 0 
      AND same_day_reminder = 0
");
$query->bind_param("iss", $user_id, $current_date, $day_before_date);
$query->execute();
$result = $query->get_result();

// Store fetched reminders into an array
$reminders = [];
while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}
$query->close(); // Close the select query

// Loop through each reminder and send an email
foreach ($reminders as $reminder) {
    $mail = new PHPMailer(true); // Create a new PHPMailer object inside the loop

    try {
        // PHPMailer SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';         // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abb.robotics0000@gmail.com'; // Your Gmail address
        $mail->Password   = 'goljuhxfrfsmxhul';        // App password (not Gmail login password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Set sender and recipient
        $mail->setFrom('abb.robotics0000@gmail.com', 'ABB Robotics');
        $mail->addAddress($reminder['email'], $reminder['user_name']);

        // Set email format to HTML
        $mail->isHTML(true);

        // Check if reminder is for today
        $isToday = ($reminder['reminder_date'] == $current_date);

        if ($isToday) {
            // Today's meeting
            $mail->Subject = "Meeting Scheduled for Today with {$reminder['name']}";

            // Update database: mark same_day_reminder = 1 and day_before_reminder = 1
            $query = $conn->prepare("UPDATE reminder SET same_day_reminder = 1, day_before_reminder = 1 WHERE reminder_id = ?");
        } else {
            // Tomorrow's meeting
            $mail->Subject = "Meeting Scheduled for Tomorrow with {$reminder['name']}";

            // Update database: mark day_before_reminder = 1
            $query = $conn->prepare("UPDATE reminder SET day_before_reminder = 1 WHERE reminder_id = ?");
        }

        // Bind reminder_id and execute update
        $query->bind_param("i", $reminder['reminder_id']);
        $query->execute();
        $query->close(); // Close the update query

        // Determine if it is a customer or a lead
        $typeLabel = ($reminder['customer_type'] == 1) ? 'Customer' : 'Lead';

        // Set the body of the email
        $mail->Body = "
            <h2>Meeting Reminder 📌</h2>
            <p><strong>$typeLabel Name:</strong> " . (!empty($reminder['name']) ? htmlspecialchars($reminder['name']) : 'N/A') . "</p>
            <p><strong>Interaction Type:</strong> " . (!empty($reminder['interaction_type_name']) ? htmlspecialchars($reminder['interaction_type_name']) : 'N/A') . "</p>
            <p><strong>Date of Interaction:</strong> " . (!empty($reminder['reminder_date']) ? htmlspecialchars($reminder['reminder_date']) : 'N/A') . "</p>
            <p><strong>Notes:</strong> " . (!empty($reminder['notes']) ? htmlspecialchars($reminder['notes']) : 'N/A') . "</p>
        ";

        // Send the email
        $mail->send();

    } catch (Exception $e) {
        // If email sending fails, log the error
        error_log("Failed to send reminder email to {$reminder['email']}: " . $mail->ErrorInfo);
    }
}

// End of script
exit();
?>
