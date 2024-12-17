<?php
require 'connect.php';

// Mendapatkan parameter tahun dari URL
$year = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Data untuk Pie Chart (VRF Status Breakdown)
$vrfStatusData = [
    'approved' => 0,
    'pending' => 0,
    'reschedule' => 0,
];

// Query status VRF dengan filter berdasarkan tahun yang dipilih
$statusQuery = "SELECT status, COUNT(*) as count FROM vrf WHERE YEAR(tgl_kunjungan) = ? GROUP BY status";
$statusStmt = $conn->prepare($statusQuery);
$statusStmt->bind_param("i", $year);
$statusStmt->execute();
$statusResult = $statusStmt->get_result();
while ($row = $statusResult->fetch_assoc()) {
    $vrfStatusData[strtolower(trim($row['status']))] = $row['count'];
}

// Data untuk Line Chart (VRF Records per Month)
$lineChartData = array_fill(0, 12, 0); // Inisialisasi 12 bulan

// Query untuk VRF Records per Month dengan filter berdasarkan tahun yang dipilih
$lineQuery = "SELECT MONTH(tgl_kunjungan) as month, COUNT(*) as count FROM vrf WHERE YEAR(tgl_kunjungan) = ? GROUP BY MONTH(tgl_kunjungan)";
$lineStmt = $conn->prepare($lineQuery);
$lineStmt->bind_param("i", $year);
$lineStmt->execute();
$lineResult = $lineStmt->get_result();
while ($row = $lineResult->fetch_assoc()) {
    $lineChartData[$row['month'] - 1] = $row['count']; // Bulan ke-1 adalah indeks 0
}

// Data untuk Bar Chart (New Users per Month)
$barChartData = array_fill(0, 12, 0); // Inisialisasi 12 bulan

// Query untuk New Users per Month dengan filter berdasarkan tahun yang dipilih
$barQuery = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM user WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at)";
$barStmt = $conn->prepare($barQuery);
$barStmt->bind_param("i", $year);
$barStmt->execute();
$barResult = $barStmt->get_result();
while ($row = $barResult->fetch_assoc()) {
    $barChartData[$row['month'] - 1] = $row['count']; // Bulan ke-1 adalah indeks 0
}

// Menampilkan data dalam format JSON
echo json_encode([
    'pieChart' => $vrfStatusData,
    'lineChart' => $lineChartData,
    'barChart' => $barChartData,
]);

?>