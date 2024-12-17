<?php
require 'connect.php';

if (isset($_GET['id_review'])) {
    $id_review = intval($_GET['id_review']);

    // Query untuk mendapatkan detail review berdasarkan ID
    $query = "
        SELECT r.kategori, r.rating, r.ulasan, r.tanggal_review, u.nama_user, u.email_user
        FROM review r
        JOIN user u ON r.id_user = u.id_user
        WHERE r.id_review = $id_review
    ";

    // Eksekusi query
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $review = $result->fetch_assoc();

        // Mengembalikan data review dalam format JSON
        echo json_encode([
            'user_name' => $review['nama_user'],
            'user_email' => $review['email_user'],
            'rating' => $review['rating'], // Rating emotikon
            'category' => $review['kategori'],
            'date' => $review['tanggal_review'],
            'review' => $review['ulasan']
        ]);
    } else {
        echo json_encode(['error' => 'Review not found']);
    }
}
?>