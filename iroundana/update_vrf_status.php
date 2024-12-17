<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_vrf = $_POST['id_vrf'];
    $status = $_POST['status'];

    if (!empty($id_vrf) && !empty($status)) {
        $query = "UPDATE vrf SET status = ? WHERE id_vrf = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('si', $status, $id_vrf);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    }
    exit;
}
?>