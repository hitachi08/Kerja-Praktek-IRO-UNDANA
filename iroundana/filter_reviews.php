<?php
require 'connect.php';

// Ambil parameter filter dari request
$rating = isset($_GET['rating']) ? $_GET['rating'] : null; // Rating berupa emotikon
$category = isset($_GET['category']) ? $_GET['category'] : null; // Kategori review
$date = isset($_GET['date']) ? $_GET['date'] : null; // Tanggal review
$user = isset($_GET['user']) ? $_GET['user'] : null; // Nama pengguna

// Query awal untuk mendapatkan data review
$query = "
    SELECT r.id_review, r.kategori, r.rating, r.ulasan, r.tanggal_review, u.nama_user, u.email_user
    FROM review r
    JOIN user u ON r.id_user = u.id_user
    WHERE 1=1
";

// Tambahkan filter berdasarkan parameter
if (!empty($category)) {
    $query .= " AND r.kategori = '" . $conn->real_escape_string($category) . "'";
}
if (!empty($rating)) {
    $query .= " AND r.rating = '" . $conn->real_escape_string($rating) . "'";
}
if (!empty($date)) {
    $query .= " AND DATE(r.tanggal_review) = '" . $conn->real_escape_string($date) . "'";
}
if (!empty($user)) {
    $query .= " AND u.nama_user LIKE '%" . $conn->real_escape_string($user) . "%'";
}

// Tambahkan pengurutan
$query .= " ORDER BY r.tanggal_review DESC";

// Eksekusi query
$result = $conn->query($query);

// Hasilkan HTML untuk ulasan
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="card review-card me-3 mr-4 mb-4 col-md-4" style="min-width: 300px; cursor: pointer;" data-id="' . $row['id_review'] . '">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="icon">
                        <img class="img-profile rounded-circle mr-2"
                            src="bootstrap5/img/undraw_profile.svg" alt="Admin Profile"
                            style="width: 40px; height: 40px;">
                    </div>
                    <div class="ms-3">
                        <h5 class="card-title mb-2 text-gray-800" style="font-size: 1rem;">
                            ' . htmlspecialchars($row['nama_user']) . '
                        </h5>
                        <h6 class="card-subtitle text-muted" style="font-size: 0.7rem;">
                            ' . htmlspecialchars($row['email_user']) . '
                        </h6>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <span style="font-size: 1.2rem; margin-right: 10px;">
                        ' . htmlspecialchars($row['rating']) . '
                    </span>
                    <small class="text-muted" style="font-size: 0.8rem;">
                        ' . htmlspecialchars(substr($row['tanggal_review'], 0, 10)) . '
                    </small>
                </div>
                <p class="text-muted mb-2" style="font-size: 0.8rem;">
                    Category: ' . htmlspecialchars($row['kategori']) . '
                </p>
                <p class="card-text">
                    ' . htmlspecialchars($row['ulasan']) . '
                </p>
            </div>
        </div>';
    }
} else {
    echo '<p class="text-center text-muted">No reviews found.</p>';
}
?>