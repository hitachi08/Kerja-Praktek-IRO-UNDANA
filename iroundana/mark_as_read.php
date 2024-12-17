<?php

require 'connect.php';

// Cek jika request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $notif_id = $data['id'];

    // Update status notif to 'read'
    $sql = "UPDATE vrf SET notif = 'read' WHERE id_vrf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notif_id);
    $stmt->execute();

    // Send a success response
    echo json_encode(['status' => 'success']);
}
