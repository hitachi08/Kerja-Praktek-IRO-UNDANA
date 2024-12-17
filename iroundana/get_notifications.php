<?php

require 'connect.php';

// Query untuk mengambil semua notifikasi
$query = "SELECT id_vrf, nama_pemohon, institusi_pemohon, tanggal_submit, notif FROM vrf ORDER BY tanggal_submit DESC";
$result = $conn->query($query);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Kirim data dalam format JSON
header('Content-Type: application/json');
echo json_encode($notifications);

?>
