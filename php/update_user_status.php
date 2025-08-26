<?php
require_once 'db.php';
require_once 'config_session.php';

// Check if the request method is POST and user is logged in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_POST['userId']; // ID of the user to modify
    $action = $_POST['action']; // Action to perform (activate/deactivate)
    $currentUserId = $_SESSION['user_id'];// ID of the current logged-in user
    
    // Prevent users from modifying active status of their own account
    if ($userId == $currentUserId) {
        echo json_encode(['success' => false, 'error' => 'Cannot modify your own account']);
        exit();
    }
    
    try {
        // Determine new status based on action
        $newStatus = ($action === 'activate') ? 'active' : 'inactive';
        $stmt = $conn->prepare("UPDATE Users SET user_status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $newStatus, $userId);
        $stmt->execute();
        
        // Respond with success
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
         // Respond with error message if an exception occurs
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    // Respond with error if request is invalid
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>