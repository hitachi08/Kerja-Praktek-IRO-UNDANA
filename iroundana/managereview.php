<?php

require 'connect.php';
require 'check.php';

// Query untuk mengambil notifikasi terbaru
$query = "SELECT id_vrf, nama_pemohon, institusi_pemohon, tanggal_submit, notif FROM vrf ORDER BY tanggal_submit DESC";
$result = $conn->query($query);

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Hitung total unread notifications
$totalUnread = array_reduce($notifications, function ($carry, $notif) {
    return $carry + ($notif['notif'] === 'unread' ? 1 : 0);
}, 0);

$alertsCounter = $totalUnread;

// Query untuk mengambil semua data review
$query = "
    SELECT r.id_review, r.kategori, r.rating, r.ulasan, r.tanggal_review, u.nama_user, u.email_user
    FROM review r
    JOIN user u ON r.id_user = u.id_user
    ORDER BY r.tanggal_review DESC"; // Mengurutkan berdasarkan tanggal terbaru

$result = mysqli_query($conn, $query);


// Query untuk menghitung jumlah ulasan per rating
$ratingCounts = [];
$emotes = ['😄 Very Happy', '🙂 Happy', '😐 Neutral', '😠 Slightly Angry', '😡 Angry'];
$totalReviews = 0;

foreach ($emotes as $emote) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM review WHERE rating = ?");
    $stmt->bind_param("s", $emote);
    $stmt->execute();
    $resultRating = $stmt->get_result();
    $row = $resultRating->fetch_assoc();
    $ratingCounts[$emote] = $row['count'];
    $totalReviews += $row['count'];
}


// Query untuk menghitung rata-rata rating
$stmt = $conn->prepare("SELECT AVG(rating) as average FROM review");
$stmt->execute();
$resultAvg = $stmt->get_result();
$rowAvg = $resultAvg->fetch_assoc();
$averageRating = round($rowAvg['average'], 1);

// Tentukan emotikon berdasarkan rata-rata rating
if ($averageRating >= 4.5) {
    $ratingEmote = "😄 Very Happy";
} elseif ($averageRating >= 3.5) {
    $ratingEmote = "🙂 Happy";
} elseif ($averageRating >= 2.5) {
    $ratingEmote = "😐 Neutral";
} elseif ($averageRating >= 1.5) {
    $ratingEmote = "😠 Slightly Angry";
} else {
    $ratingEmote = "😡 Angry";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <!-- Custom fonts for this template-->
    <link href="bootstrap5/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="bootstrap5/css/sb-admin-2.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="style/index.css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="iro-sidebar navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin.php">
                <div class="sidebar-brand-icon">
                    <img src="style/image/Logo_Undana.png" alt="Logo Undana" class="img-fluid"
                        style="width: 40px; height: auto;">
                </div>
                <div class="sidebar-brand-text mx-3">IRO UNDANA</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <?php
            // Mendapatkan nama file dari URL
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>

            <!-- Nav Item - Dashboard -->
            <li class="iro-item nav-item <?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>"
                style="margin-top: 1vw">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Nav Item - User Management -->
            <li class="iro-item nav-item <?php echo ($current_page == 'manageuser.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="manageuser.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User Management</span>
                </a>
            </li>

            <!-- Nav Item - VRF Management -->
            <li
                class="iro-item nav-item <?php echo ($current_page == 'vrf_records.php' || $current_page == 'vrf_approval.php') ? 'active' : ''; ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#vrfManagementMenu"
                    aria-expanded="true" aria-controls="vrfManagementMenu">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>VRF Management</span>
                </a>
                <div id="vrfManagementMenu"
                    class="collapse <?php echo ($current_page == 'vrf_records.php' || $current_page == 'vrf_approval.php') ? 'show' : ''; ?>"
                    aria-labelledby="headingVRF" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">VRF Options:</h6>
                        <a class="collapse-item <?php echo ($current_page == 'vrf_records.php') ? 'active' : ''; ?>"
                            href="vrf_records.php">VRF Records</a>
                        <a class="collapse-item <?php echo ($current_page == 'vrf_approval.php') ? 'active' : ''; ?>"
                            href="vrf_approval.php">VRF Approval</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Review Management -->
            <li class=" iro-item nav-item <?php echo ($current_page == 'managereview.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="managereview.php">
                    <i class="fas fa-fw fa-comments"></i>
                    <span>Review Management</span>
                </a>
            </li>

            <!-- Nav Item - Submission Management -->
            <li class="nav-item <?php echo ($current_page == 'managehistory.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="managehistory.php">
                    <i class="fas fa-history"></i>
                    <span>Submission History</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter" id="alertsCounter">
                                    <?= $alertsCounter ?>
                                </span>

                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Notification Center</h6>
                                <?php
                                // Mengurutkan pemberitahuan berdasarkan status 'notif', yang 'unread' akan lebih dahulu
                                usort($notifications, function ($a, $b) {
                                    if ($a['notif'] === 'unread' && $b['notif'] === 'read') {
                                        return -1; // A di atas B
                                    } elseif ($a['notif'] === 'read' && $b['notif'] === 'unread') {
                                        return 1; // B di atas A
                                    } else {
                                        return 0; // Tidak ada perubahan
                                    }
                                });

                                // Limit notifications to 3
                                $notificationsToShow = array_slice($notifications, 0, 3);
                                if (!empty($notificationsToShow)):
                                    foreach ($notificationsToShow as $notif):
                                        // Cek apakah status pemberitahuan adalah 'read'
                                        $readClass = $notif['notif'] === 'read' ? 'read-notification' : '';
                                        ?>
                                        <a class="dropdown-item d-flex align-items-center notification-item <?= $readClass ?>"
                                            href="vrf_approval.php" data-id="<?= $notif['id_vrf'] ?>">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-primary">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500">
                                                    <?= date("F d, Y", strtotime($notif['tanggal_submit'])) ?>
                                                </div>
                                                <span
                                                    class="font-weight-bold"><?= htmlspecialchars($notif['nama_pemohon']) ?></span>
                                                <div><?= htmlspecialchars($notif['institusi_pemohon']) ?></div>
                                            </div>
                                        </a>
                                    <?php endforeach;
                                else: ?>
                                    <a class="dropdown-item text-center small text-gray-500">No new notifications</a>
                                <?php endif; ?>
                                <a class="dropdown-item text-center small text-gray-500 show-all-link" href="#"
                                    data-toggle="modal" data-target="#allNotificationsModal">
                                    Show All Notifications
                                </a>

                                <!-- Tombol Tandai Semua Sebagai Dibaca -->
                                <a class="dropdown-item text-center small text-gray-500" href="#"
                                    id="markAllAsRead">Mark All as Read</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <img class="img-profile rounded-circle mr-2"
                                        src="bootstrap5/img/undraw_profile_3.svg" alt="Admin Profile"
                                        style="width: 40px; height: 40px;">
                                    <div class="text-left">
                                        <span class="d-block text-gray-600 small font-weight-bold">
                                            <?= isset($_SESSION['admin_name']) ? strtoupper($_SESSION['admin_name']) : 'GUEST'; ?>
                                        </span>
                                        <span class="d-block text-gray-500 small">Administrator</span>
                                    </div>
                                </div>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-item text-center">
                                    <img class="img-profile rounded-circle mb-2 ps-0"
                                        src="bootstrap5/img/undraw_profile_3.svg" alt="Admin Profile"
                                        style="width: 60px; height: 60px;">
                                    <p id="adminUsernameDisplay" class="mb-0 font-weight-bold text-gray-800">
                                        <?= isset($_SESSION['admin_name']) ? strtoupper($_SESSION['admin_name']) : 'GUEST'; ?>
                                    </p>
                                    <small class="text-gray-500">Administrator</small>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#settingsModal">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid mt-4">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Review Management</h1>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <form id="filterForm">
                                <div class="d-flex align-items-center flex-wrap">
                                    <!-- Filter by Category -->
                                    <label for="filterCategory" class="mr-2 mb-0">Category:</label>
                                    <select id="filterCategory" name="category"
                                        class="form-control form-control-sm mr-3" style="width: auto;">
                                        <option value="">All</option>
                                        <option value="Visit">Visit</option>
                                        <option value="Website">Website</option>
                                    </select>
                                    <!-- Filter by Rating -->
                                    <label for="filterRating" class="mr-2 mb-0">Rating:</label>
                                    <select id="filterRating" name="rating" class="form-control form-control-sm mr-3"
                                        style="width: auto;">
                                        <option value="">All</option>
                                        <option value="😄 Very Happy">😄 Very Happy</option>
                                        <option value="🙂 Happy">🙂 Happy</option>
                                        <option value="😐 Neutral">😐 Neutral</option>
                                        <option value="😠 Slightly Angry">😠 Slightly Angry</option>
                                        <option value="😡 Angry">😡 Angry</option>
                                    </select>
                                    <!-- Filter by Date -->
                                    <label for="filterDate" class="mr-2 mb-0">Date:</label>
                                    <input type="date" id="filterDate" name="date"
                                        class="form-control form-control-sm mr-3" style="width: auto;">
                                    <button type="button" id="clearFilters" class="btn btn-secondary btn-sm">Clear
                                        All</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <p>Total Reviews: <strong><?php echo $totalReviews; ?></strong> | Average Rating:
                                <span><?php echo $ratingEmote; ?></span>
                            </p>
                            <canvas id="ratingChart" style="max-width: 80%; max-height: 85%;"></canvas>
                        </div>
                    </div>

                    <div class="row">
                        <div class="d-flex overflow-auto">
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <div class="card review-card me-3 mr-4 mb-4 col-md-4"
                                        style="min-width: 300px; cursor: pointer;" data-id="<?php echo $row['id_review']; ?>">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="icon">
                                                    <img class="img-profile rounded-circle mr-2"
                                                        src="bootstrap5/img/undraw_profile.svg" alt="Admin Profile"
                                                        style="width: 40px; height: 40px;">
                                                </div>
                                                <div class="ms-3">
                                                    <h5 class="card-title mb-2 text-gray-800" style="font-size: 1rem;">
                                                        <?php echo htmlspecialchars($row['nama_user']); ?>
                                                    </h5>
                                                    <h6 class="card-subtitle text-muted" style="font-size: 0.7rem;">
                                                        <?php echo htmlspecialchars($row['email_user']); ?>
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mb-2 mt-4">
                                                <?php
                                                $rating = htmlspecialchars($row['rating']);

                                                $color = "";
                                                $text = "";

                                                switch ($rating) {
                                                    case "😡 Angry":
                                                        $color = "red";
                                                        break;
                                                    case "😠 Slightly Angry":
                                                        $color = "orange";
                                                        break;
                                                    case "😐 Neutral":
                                                        $color = "gray";
                                                        break;
                                                    case "🙂 Happy":
                                                        $color = "yellow";
                                                        break;
                                                    case "😄 Very Happy":
                                                        $color = "green";
                                                        break;
                                                    default:
                                                        $color = "black";
                                                        $text = "Unknown";
                                                }
                                                ?>

                                                <span
                                                    style="font-size: 1rem; margin-right: 10px; color: <?php echo $color; ?>;">
                                                    <?php echo $rating; ?>         <?php echo $text; ?>
                                                </span>

                                                <small class="text-muted" style="font-size: 0.8rem;">
                                                    <?php echo htmlspecialchars(substr($row['tanggal_review'], 0, 10)); ?>
                                                </small>
                                            </div>
                                            <p class="text-muted mb-2" style="font-size: 0.8rem;">Category:
                                                <span
                                                    style="text-transform: uppercase;"><?php echo htmlspecialchars($row['kategori']); ?>
                                                </span>
                                            </p>
                                            <p class="card-text">
                                                <?php echo htmlspecialchars($row['ulasan']); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted">No reviews found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <script>
                        // Menangani klik pada card
                        document.querySelectorAll('.review-card').forEach(function (card) {
                            card.addEventListener('click', function () {
                                var reviewId = this.getAttribute('data-id'); // Mengambil ID review dari atribut data-id

                                // Mengambil data review dari server menggunakan AJAX
                                fetch('get_review_details.php?id_review=' + reviewId)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.error) {
                                            alert(data.error); // Menampilkan error jika review tidak ditemukan
                                        } else {
                                            // Mengisi modal dengan data review
                                            document.getElementById('modalUserName').textContent = data.user_name;
                                            document.getElementById('modalUserEmail').textContent = data.user_email;
                                            document.getElementById('modalRating').textContent = data.rating;
                                            document.getElementById('modalCategory').textContent = data.category;
                                            document.getElementById('modalDate').textContent = data.date;
                                            document.getElementById('modalReviewText').textContent = data.review;

                                            // Menampilkan modal
                                            $('#reviewModal').modal('show');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('There was an error loading the review details.');
                                    });
                            });
                        });
                    </script>
                </div>
                <!-- End Page Content -->

                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; International Relations Office UNDANA 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal for Review Details -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm modal-md modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Review Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>User:</strong> <span id="modalUserName"></span></p>
                    <p><strong>Email:</strong> <span id="modalUserEmail"></span></p>
                    <p><strong>Rating:</strong> <span id="modalRating"></span></p>
                    <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <p><strong>Review:</strong></p>
                    <p id="modalReviewText"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal All Notifications -->
    <div class="modal fade" id="allNotificationsModal" tabindex="-1" role="dialog"
        aria-labelledby="allNotificationsLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allNotificationsLabel">All Notifications</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <button type="button" class="btn-mark m-2" id="markAllAsReadModal">Mark All as Read</button>
                <div class="modal-body">
                    <div class="list-group" id="allNotificationsList">
                        <!-- Notifications will be loaded here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Settings -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Update Username</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateUsernameForm" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="currentUsername">Current Username</label>
                            <input type="text" id="currentUsername" class="form-control"
                                value="<?= $_SESSION['admin_name']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="newUsername">New Username</label>
                            <input type="text" name="newUsername" id="newUsername" class="form-control"
                                placeholder="Enter new username" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout_admin.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ratingCounts = <?php echo json_encode($ratingCounts); ?>;

        var ctx = document.getElementById('ratingChart').getContext('2d');
        var ratingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['😄 Very Happy', '🙂 Happy', '😐 Neutral', '😠 Slightly Angry', '😡 Angry'],
                datasets: [{
                    label: 'Number of Reviews',
                    data: [
                        ratingCounts['😄 Very Happy'] || 0,
                        ratingCounts['🙂 Happy'] || 0,
                        ratingCounts['😐 Neutral'] || 0,
                        ratingCounts['😠 Slightly Angry'] || 0,
                        ratingCounts['😡 Angry'] || 0
                    ],
                    backgroundColor: '#f6c23e',
                    borderRadius: 30,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 1.5,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: false // Hilangkan garis grid di sumbu X
                        },
                        border: {
                            display: false // Hilangkan garis sumbu X di awal
                        }
                    },
                    y: {
                        grid: {
                            display: false // Hilangkan garis grid di sumbu Y
                        },
                        border: {
                            display: false // Hilangkan garis sumbu Y di awal
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Menyembunyikan legenda jika diperlukan pada perangkat mobile
                    }
                }
            }
        });
    </script>



    <script src="bootstrap5/vendor/jquery/jquery.min.js"></script>
    <script src="bootstrap5/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap5/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="bootstrap5/js/sb-admin-2.min.js"></script>
    <script src="js/index.js"></script>
    <script src="js/review.js"></script>

</body>

</html>