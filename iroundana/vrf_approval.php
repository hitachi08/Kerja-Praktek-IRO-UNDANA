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

// Query data VRF
$query = "
    SELECT 
        vrf.id_vrf,
        vrf.tgl_kunjungan,
        DATE_FORMAT(vrf.waktu_kunjungan, '%H:%i') AS waktu_kunjungan,
        vrf.durasi_kunjungan,
        vrf.nama_pemohon,
        vrf.posisi_pemohon,
        vrf.institusi_pemohon,
        vrf.website_pemohon,
        vrf.email_pemohon,
        vrf.telepon_pemohon,
        vrf.faks_pemohon,
        vrf.deskripsi_institusi,
        vrf.tujuan_kunjungan,
        GROUP_CONCAT(DISTINCT orang.nama_orang SEPARATOR ', ') AS orang_ditemui,
        vrf.bidang_pembahasan,
        COALESCE(
            GROUP_CONCAT(
                DISTINCT NULLIF(
                    CONCAT(
                        IFNULL(kontak_undana.nama_depan_kontak, ''),
                        ' ',
                        IFNULL(kontak_undana.nama_belakang_kontak, ''),
                        ' ',
                        IFNULL(kontak_undana.gelar_kontak, ''),
                        ' ',
                        IFNULL(kontak_undana.posisi_kontak, '')
                    ),
                    '    '
                )
                SEPARATOR '<br>'
            ),
            'No Undana Contact'
        ) AS kontak_undana,
        GROUP_CONCAT(DISTINCT CONCAT(delegasi.gelar, ' ', delegasi.nama_depan, ' ', delegasi.nama_belakang, ' - ', delegasi.posisi) SEPARATOR '<br>') AS delegasi,
        vrf.interpreter,
        vrf.status
    FROM 
        vrf
    LEFT JOIN 
        orang ON vrf.id_vrf = orang.id_vrf
    LEFT JOIN 
        kontak_undana ON vrf.id_vrf = kontak_undana.id_vrf
    LEFT JOIN 
        delegasi ON vrf.id_vrf = delegasi.id_vrf
    GROUP BY 
        vrf.id_vrf;
";

$result = mysqli_query($conn, $query);

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

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <script src="bootstrap5\vendor\jquery\jquery.min.js"></script>
    <link rel="stylesheet" href="bootstrap5\vendor\datatables\dataTables.bootstrap4.min.css">

    <!-- Custom styles for this template-->
    <link href="bootstrap5/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/index.css">

    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #2e2e2ea7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Animasi kedap-kedip */
        @keyframes blink {

            0%,
            100% {
                background-color: #5d7ee06c;
                color: white;
            }

            50% {
                background-color: transparent;
                color: black;
            }
        }

        /* Class untuk highlight */
        .highlight-row {
            animation: blink 1s infinite;
        }
        
    </style>

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
                                    <img class="img-profile rounded-circle mb-2"
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

                <div class="table-controls flextable">
                    <div class="dataTables_length"></div>
                    <div class="dataTables_filter"></div>
                </div>

                <!-- container-fluid -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">VRF Approval</h1>
                    </div>

                    <!-- Filter Section -->
                    <form method="GET" action="" class="mb-3" id="filterForm">
                        <div class="row">

                            <div class="col-md-3">
                                <label for="start_date">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="<?= $_GET['start_date'] ?? '' ?>" onchange="filterTable()">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="<?= $_GET['end_date'] ?? '' ?>" onchange="filterTable()">
                            </div>

                            <div class="col-md-3">
                                <label for="status">Status:</label>
                                <select name="status" id="status" class="form-control" onchange="filterTable()">
                                    <option value="">All</option>
                                    <option value="Pending" <?= ($_GET['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>
                                        Pending</option>
                                    <option value="Approved" <?= ($_GET['status'] ?? '') == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="Reschedule" <?= ($_GET['status'] ?? '') == 'Reschedule' ? 'selected' : '' ?>>Reschedule</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="institution">Institution:</label>
                                <input type="text" name="institution" id="institution" class="form-control"
                                    placeholder="Search Institution" value="<?= $_GET['institution'] ?? '' ?>"
                                    onkeyup="filterTable()">
                            </div>

                            <div class="col-md-12 mt-3">
                                <!-- Tombol Clear Tanpa Reload -->
                                <button type="button" id="clearFilterBtn" class="btn btn-secondary">Clear</button>
                            </div>
                        </div>
                    </form>
                    <!-- /. Filter Section -->

                    <script>
                        document.getElementById("clearFilterBtn").addEventListener("click", function () {
                            document.getElementById("start_date").value = "";
                            document.getElementById("end_date").value = "";
                            document.getElementById("status").value = "";
                            document.getElementById("institution").value = "";

                            filterTable();
                        });

                        function filterTable() {
                            console.log("Filters updated or cleared!");
                        }
                    </script>

                    <!-- Table Content -->
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="vrfApprovalTable" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr class="column-nowrap">
                                        <th>ID VRF</th>
                                        <th>Visit Date</span></th>
                                        <th>Visit Time</span></th>
                                        <th>Duration (Hour)</th>
                                        <th>Applicant Name</th>
                                        <th>Applicant Position</th>
                                        <th>Institution</th>
                                        <th>Website</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Fax</th>
                                        <th>Institution Description</th>
                                        <th>Visit Purpose</th>
                                        <th>Person to Meet </th>
                                        <th>Discussion Field </th>
                                        <th>Undana Contact</th>
                                        <th>Delegation</th>
                                        <th>Interpreter</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr class="column-nowrap" data-id="<?= $row['id_vrf']; ?>"
                                            data-status="<?= $row['status']; ?>"
                                            data-institusi="<?= strtolower($row['institusi_pemohon']); ?>"
                                            data-tanggal="<?= $row['tgl_kunjungan']; ?>">
                                            <td><?= $row['id_vrf']; ?></td>
                                            <td><?= $row['tgl_kunjungan']; ?></td>
                                            <td><?= $row['waktu_kunjungan']; ?></td>
                                            <td><?= $row['durasi_kunjungan']; ?></td>
                                            <td><?= $row['nama_pemohon']; ?></td>
                                            <td><?= $row['posisi_pemohon']; ?></td>
                                            <td><?= $row['institusi_pemohon']; ?></td>
                                            <td><?= $row['website_pemohon']; ?></td>
                                            <td><?= $row['email_pemohon']; ?></td>
                                            <td><?= $row['telepon_pemohon']; ?></td>
                                            <td><?= $row['faks_pemohon']; ?></td>
                                            <td class="text-long"><?= $row['deskripsi_institusi']; ?></td>
                                            <td class="text-long"><?= $row['tujuan_kunjungan']; ?></td>
                                            <td>
                                                <?php
                                                $persons = explode(',', $row['orang_ditemui']);
                                                foreach ($persons as $person) {
                                                    echo htmlspecialchars(trim($person)) . '<br>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-long"><?= $row['bidang_pembahasan']; ?></td>
                                            <td>
                                                <?php
                                                if (empty(trim($row['kontak_undana'])) || $row['kontak_undana'] === 'No Undana Contact') {
                                                    echo 'No Undana Contact';
                                                } else {
                                                    $kontakUndanaArray = explode('<br>', $row['kontak_undana']);
                                                    foreach ($kontakUndanaArray as $kontak) {
                                                        echo htmlspecialchars(trim($kontak)) . '<br>';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $delegations = mysqli_query($conn, "SELECT * FROM delegasi WHERE id_vrf = '{$row['id_vrf']}'");
                                                while ($delegation = mysqli_fetch_assoc($delegations)) {
                                                    echo htmlspecialchars("{$delegation['gelar']} {$delegation['nama_depan']} {$delegation['nama_belakang']} ({$delegation['posisi']})") . '<br>';
                                                }
                                                ?>
                                            </td>
                                            <td><?= $row['interpreter']; ?></td>
                                            <td class="status-column">
                                                <form id="updateForm_<?php echo $row['id_vrf']; ?>" method="post">
                                                    <input type="hidden" name="id_vrf"
                                                        value="<?php echo $row['id_vrf']; ?>">
                                                    <select name="status" class="form-select status-select"
                                                        id="status_<?php echo $row['id_vrf']; ?>"
                                                        data-id="<?php echo $row['id_vrf']; ?>" <?php echo ($row['status'] == 'Approved' || $row['status'] == 'Reschedule') ? 'disabled' : ''; ?>>
                                                        <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Approved" <?php echo $row['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="Reschedule" <?php echo $row['status'] == 'Reschedule' ? 'selected' : ''; ?>>Reschedule</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary update-btn"
                                                    id="btn_<?php echo $row['id_vrf']; ?>"
                                                    onclick="updateStatus(<?php echo $row['id_vrf']; ?>)" <?php echo ($row['status'] == 'Approved' || $row['status'] == 'Reschedule') ? 'disabled' : ''; ?>>
                                                    Update
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /. Table Content -->
                    </div>
                    <?php
                    // Ambil highlight_id dari query string
                    $highlightId = isset($_GET['highlight_id']) ? $_GET['highlight_id'] : '';
                    ?>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Ambil ID VRF yang ingin di-highlight dari PHP
                            const highlightId = "<?php echo $highlightId; ?>";

                            if (highlightId) {
                                // Cari baris tabel dengan data-id yang cocok
                                const row = document.querySelector(`tr[data-id='${highlightId}']`);
                                const tableContainer = document.querySelector(".table-container"); // Container scrollable tabel

                                if (row && tableContainer) {
                                    // Hitung posisi baris relatif terhadap container
                                    const rowPosition = row.offsetTop - tableContainer.offsetTop;

                                    // Scroll pada container tabel ke posisi baris
                                    tableContainer.scrollTo({
                                        top: rowPosition,
                                        behavior: "smooth", // Efek smooth scroll
                                    });

                                    // Tambahkan animasi highlight (kedap-kedip)
                                    row.classList.add("highlight-row");
                                    setTimeout(() => row.classList.remove("highlight-row"), 10000); // Hilangkan highlight setelah 5 detik
                                }
                            }
                        });
                    </script>

                </div>
                <!-- /.container-fluid -->

                <div class="table-pagination">
                    <div class="dataTables_info"></div>
                    <div class="dataTables_paginate"></div>
                </div>

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

    <script>
        $(document).ready(function () {
            $('#vrfApprovalTable').DataTable({
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

    <!-- Modal Reschedule -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rescheduleForm">
                        <input type="hidden" id="rescheduleVrfId" name="id_vrf">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Reschedule</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rescheduleDate" class="form-label">Reschedule Date</label>
                            <input type="date" class="form-control" id="rescheduleDate" name="reschedule_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="rescheduleTime" class="form-label">Reschedule Time</label>
                            <input type="time" class="form-control" id="rescheduleTime" name="reschedule_time" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="rescheduleForm" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loadingOverlay" style="display:none;">
        <div class="spinner"></div>
        <p>Please wait...</p>
    </div>
    <script>
        document
            .getElementById("rescheduleForm")
            .addEventListener("submit", function (e) {
                e.preventDefault(); // Mencegah form dikirim secara default

                // Tampilkan loader
                document.getElementById("loadingOverlay").style.display = "flex";

                // Ambil data form
                const formData = new FormData(this);

                // Kirim data ke server menggunakan fetch
                fetch("send_reschedule_reason.php", {
                    method: "POST",
                    body: formData,
                })
                    .then((response) => response.json())
                    .then((data) => {
                        // Sembunyikan loader setelah respons diterima
                        document.getElementById("loadingOverlay").style.display = "none";

                        if (data.success) {
                            alert("Reschedule successful!");
                            // Anda bisa refresh halaman atau lakukan sesuatu yang lain
                            window.location.reload();
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        document.getElementById("loadingOverlay").style.display = "none";
                        alert("Something went wrong. Please try again.");
                    });
            });
    </script>

    <!-- Modal Reschedule Success -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Reschedule Berhasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Reschedule berhasil dilakukan. Email telah dikirim kepada pemohon.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail VRF -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailsModalLabel">VRF Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="container">
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>ID VRF:</strong></div>
                            <div class="col-sm-8" id="modalVrfId"></div>
                        </div>
                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Visit Date:</strong></div>
                            <div class="col-sm-8" id="modalVisitDate"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Visit Time:</strong></div>
                            <div class="col-sm-8" id="modalVisitTime"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Duration (Hour):</strong></div>
                            <div class="col-sm-8" id="modalDuration"></div>
                        </div>
                        <!-- Applicant Info -->
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Applicant Name:</strong></div>
                            <div class="col-sm-8" id="modalApplicantName"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Applicant Position:</strong></div>
                            <div class="col-sm-8" id="modalApplicantPosition"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Institution:</strong></div>
                            <div class="col-sm-8" id="modalInstitution"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Website:</strong></div>
                            <div class="col-sm-8"><a href="#" id="modalWebsite" target="_blank"></a></div>
                        </div>
                        <!-- Contact Info -->
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8" id="modalEmail"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Phone:</strong></div>
                            <div class="col-sm-8" id="modalPhone"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Fax:</strong></div>
                            <div class="col-sm-8" id="modalFax"></div>
                        </div>
                        <!-- Other Info -->
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Institution Description:</strong></div>
                            <div class="col-sm-8" id="modalDescription"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Visit Purpose:</strong></div>
                            <div class="col-sm-8" id="modalVisitPurpose"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>People Met:</strong></div>
                            <div class="col-sm-8" id="modalPeopleMet"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Discussion Topics:</strong></div>
                            <div class="col-sm-8" id="modalDiscussionTopics"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Undana Contacts:</strong></div>
                            <div class="col-sm-8" id="modalUndanaContacts"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Delegations:</strong></div>
                            <div class="col-sm-8" id="modalDelegations"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Interpreter:</strong></div>
                            <div class="col-sm-8" id="modalInterpreter"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Status:</strong></div>
                            <div class="col-sm-8" id="modalStatus"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-delay="5000">
            <div class="d-flex">
                <div class="toast-body">Status updated successfully.</div>
            </div>
        </div>
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
            aria-atomic="true" data-delay="5000">
            <div class="d-flex">
                <div class="toast-body">Failed to update status. Please try again.</div>
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

    <script src="js/index.js"></script>
    <script src="js/approval.js"></script>

    <!-- Bootstrap core JavaScript-->
    <script src="bootstrap5\vendor\datatables\jquery.dataTables.min.js"></script>
    <script src="bootstrap5\vendor\bootstrap\js\bootstrap.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="bootstrap5/js/sb-admin-2.min.js"></script>

</body>

</html>