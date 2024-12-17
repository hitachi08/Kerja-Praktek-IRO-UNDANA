<?php
require 'connect.php';

// Inisialisasi array untuk data dashboard
$dashboardData = [
    'total_user' => 0,
    'submissions_pending' => 0,
    'total_reviews' => 0,
    'submissions_complete' => 0,
];

// Query untuk masing-masing card
$queries = [
    'total_user' => "SELECT COUNT(*) AS total FROM user",
    'submissions_pending' => "SELECT COUNT(*) AS total FROM vrf WHERE status = ?",
    'total_reviews' => "SELECT COUNT(*) AS total FROM review",
    'submissions_complete' => "SELECT COUNT(*) AS total FROM vrf WHERE status = ?",
];

// Status yang perlu dicek
$statuses = ['pending', 'approved'];

foreach ($queries as $key => $query) {
    if (in_array($key, ['submissions_pending', 'submissions_complete'])) {
        // Query dengan parameter (untuk 'pending' dan 'complete')
        $stmt = $conn->prepare($query);
        $status = ($key === 'submissions_pending') ? $statuses[0] : $statuses[1];
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Query tanpa parameter
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    // Ambil hasil dan masukkan ke array
    if ($row = $result->fetch_assoc()) {
        $dashboardData[$key] = $row['total'];
    }

    // Tutup statement
    $stmt->close();
}

// Kembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($dashboardData);
?>
