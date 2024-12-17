<?php
// mark_all_as_read.php
header('Content-Type: application/json');

// Include database connection
include('connect.php');

// Fetch all unread notifications
$query = "UPDATE vrf SET notif = 'read' WHERE notif = 'unread'";
if ($conn->query($query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
?>