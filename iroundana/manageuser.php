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

// Cek apakah sesi admin ada
if (!isset($_SESSION['admin_id'])) {
    // Jika tidak ada sesi admin, redirect ke halaman login admin
    header('Location: login.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Homepage Admin</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <!-- Custom fonts for this template-->
    <link href="bootstrap5/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <script src="bootstrap5\vendor\jquery\jquery.min.js"></script>
    <link rel="stylesheet" href="bootstrap5\vendor\datatables\dataTables.bootstrap4.min.css">

    <!-- Custom styles for this template-->
    <link href="bootstrap5/css/sb-admin-2.min.css" rel="stylesheet">
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
                                    <img class="img-profile rounded-circle mr-2" src="bootstrap5/img/undraw_profile_3.svg"
                                        alt="Admin Profile" style="width: 40px; height: 40px;">
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
                                    <img class="img-profile rounded-circle mb-2" src="bootstrap5/img/undraw_profile_3.svg"
                                        alt="Admin Profile" style="width: 60px; height: 60px;">
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

                <!-- container-fluid -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">User Management</h1>
                    </div>

                    <!-- User Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">List of Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Last Login</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT id_user, nama_user, email_user, status_user, created_at, last_login FROM user";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $row['id_user'] . "</td>";
                                                echo "<td>" . htmlspecialchars($row['nama_user']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['email_user']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['status_user']) . "</td>";
                                                echo "<td>" . $row['created_at'] . "</td>";
                                                echo "<td>" . ($row['last_login'] ? date('Y-m-d H:i:s', strtotime($row['last_login'])) : '-') . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No users found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End of user table -->

                </div>
                <!-- End of container-fluid -->

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

    <script>
        $(document).ready(function () {
            $('#userTable').DataTable({
                autoWidth: false,
                scrollY: "300px", // Scroll vertikal untuk isi tabel
                scrollX: true,    // Scroll horizontal jika tabel terlalu lebar
                paging: true,     // Aktifkan pagination
                searching: true,  // Aktifkan search
                info: true,       // Aktifkan informasi "Showing entries"
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:"
                },
                dom: '<"table-controls"lf>t<"table-pagination"ip>', // Tempatkan fitur di luar tabel
            });
        });
    </script>

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

    <script src="js/index.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="bootstrap5\vendor\datatables\jquery.dataTables.min.js"></script>
    <script src="bootstrap5\vendor\bootstrap\js\bootstrap.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="bootstrap5/js/sb-admin-2.min.js"></script>

</body>

</html>