<?php

require 'connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Session ID not found']);
    exit();
}

$id_admin = $_SESSION['admin_id']; // Ambil ID admin dari session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan amankan data dari form
    $new_username = mysqli_real_escape_string($conn, $_POST['newUsername']);

    // Periksa apakah username baru kosong
    if (empty($new_username)) {
        echo json_encode(['success' => false, 'error' => 'Username cannot be empty']);
        exit();
    }

    // Update username di database
    $query = "UPDATE admin SET username = '$new_username' WHERE id_admin = '$id_admin'";

    if (mysqli_query($conn, $query)) {
        // Update session dengan username baru jika update berhasil
        $_SESSION['admin_name'] = $new_username;

        // Kirim respons sukses
        echo json_encode(['success' => true, 'username' => $new_username]);
    } else {
        // Kirim respons error jika gagal update
        echo json_encode(['success' => false, 'error' => 'Failed to update username: ' . mysqli_error($conn)]);
    }
    exit();
}
?>
