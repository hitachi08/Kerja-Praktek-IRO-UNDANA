<?php

include 'connect.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = "SELECT * FROM vrf WHERE id_user = ? ORDER BY tanggal_submit DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user); // "i" menunjukkan tipe data integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Page - IRO UNDANA</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.css">

    <link rel="stylesheet" href="user/fonts/icomoon/style.css" />

    <link rel="stylesheet" href="user/css/bootstrap.min.css" />

    <link rel="stylesheet" href="user/css/style.css" />
    <link rel="stylesheet" href="user/css/mystyle.css">
    <link rel="stylesheet" href="user/css/steps.css">

    <script src="user/js/jquery-3.3.1.min.js"></script>
    <style>
        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
            font-weight: 500;
            text-transform: uppercase;
            color: #979797;
        }

        .badge {
            color: white !important;
            padding: 5px;
            border-radius: 10px;
            font-weight: normal !important;
        }
    </style>
    <style>
        ..site-navbar .site-navigation .site-menu>li>a {
            color: black !important;
            transition: all 0.3s ease-in-out;
        }

        .site-navbar-target.scrolled {
            color: white;
        }
    </style>

</head>

<body>
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <div class="site-navbar-wrap">
        <div class="site-navbar site-navbar-target js-sticky-header" style="background-color: #151c24;">
            <div class="container">
                <div class="row align-items-center">

                    <div class="nav-left col-4">
                        <img src="user/images/Logo_Undana.png" alt="Logo Undana" width="40rem" class="me-3" />
                        <div class="divider d-none d-lg-block"
                            style="border-left: 2px solid #805f03; height: 2.7rem; margin: 0px 1rem;"></div>
                        <div>
                            <div class="d-none d-lg-block" style="color: white;">
                                <span style="font-size: 1rem; font-weight: 500">International Relations</span><br>
                                <span style="font-size: 0.8rem;">Office UNDANA</span>
                            </div>
                            <div class="d-block d-lg-none">
                                <h1 class="my-0 site-logo"
                                    style="padding: 0.5rem 0px 0px 0.5rem; color: white; font-size: 1.2rem; font-weight: 600;">
                                    IROUNDANA
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-8">
                        <nav class="site-navigation text-right" role="navigation">
                            <div class="container">
                                <div class="d-inline-block d-lg-none ml-md-0 mr-auto py-3">
                                    <a href="#" class="site-menu-toggle js-menu-toggle text-white"><span
                                            class="icon-menu h3"></span></a>
                                </div>

                                <ul class="site-menu main-menu js-clone-nav d-none d-lg-block">
                                    <!-- Home -->
                                    <li class="<?php echo ($page == 'home') ? 'active' : ''; ?>">
                                        <a href="index.php?page=home" class="nav-link">Home</a>
                                    </li>
                                    <!-- VRF -->
                                    <li>
                                        <a href="index.php?page=home#vrf-section" class="nav-link">VRF</a>
                                    </li>
                                    <!-- Status -->
                                    <li class="<?php echo ($page == 'status') ? 'active' : ''; ?>">
                                        <a href="index.php?page=status" class="nav-link">Status</a>
                                    </li>
                                    <!-- Review -->
                                    <li class="<?php echo ($page == 'review') ? 'active' : ''; ?>">
                                        <a href="index.php?page=review" class="nav-link">Review</a>
                                    </li>
                                    <!-- Log out -->
                                    <li>
                                        <a href="#" class="nav-link" data-bs-toggle="modal"
                                            data-bs-target="#logoutModal">Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout_user.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container-fluid py-4">
            <div class="row" style="margin-top: 80px !important;">
                <!-- Surat Masuk Section -->
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title" style="font-family: 'Poppins' font-size: 16px;">Your Submission
                                Status</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 14px">
                                    <thead style="background-color: #f1f1f1;">
                                        <tr>
                                            <th>No.</th>
                                            <th>Submission Date & Time</th>
                                            <th>Applicant Data</th>
                                            <th>Status</th> <!-- Kolom Status Baru -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result) > 0) { // Jika data tersedia
                                            $counter = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $submission_date = date("d F Y", strtotime($row['tanggal_submit']));
                                                $submission_time = date("h:i A", strtotime($row['waktu_kunjungan']));
                                                $applicant_name = $row['nama_pemohon'];
                                                $applicant_institution = $row['institusi_pemohon'];
                                                $applicant_email = $row['email_pemohon'];
                                                $status = $row['status'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td>
                                                        <strong>Submission Date:</strong> <span
                                                            class="text-muted"><?php echo $submission_date; ?></span><br>
                                                        <strong>Time of Submission:</strong> <span
                                                            class="text-muted"><?php echo $submission_time; ?></span>
                                                    </td>
                                                    <td>
                                                        <strong>Name:</strong> <?php echo $applicant_name; ?><br>
                                                        <strong>Institution:</strong> <?php echo $applicant_institution; ?><br>
                                                        <strong>Email:</strong> <?php echo $applicant_email; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($status == 'Pending') {
                                                            echo '<span class="badge bg-warning text-dark">' . $status . '</span>';
                                                        } elseif ($status == 'Approved') {
                                                            echo '<span class="badge bg-success">' . $status . '</span>';
                                                        } elseif ($status == 'Reschedule') {
                                                            echo '<span class="badge bg-danger">' . $status . '</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button style="white-space: nowrap; font-size: 14px;"
                                                            class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#viewApplicationModal"
                                                            data-id="<?php echo $row['id_vrf']; ?>"
                                                            data-submission_date="<?php echo $submission_date; ?>"
                                                            data-submission_time="<?php echo $submission_time; ?>"
                                                            data-visit_date="<?php echo date("d F Y", strtotime($row['tgl_kunjungan'])); ?>"
                                                            data-visit_time="<?php echo date("h:i A", strtotime($row['waktu_kunjungan'])); ?>"
                                                            data-duration="<?php echo $row['durasi_kunjungan']; ?>"
                                                            data-applicant_name="<?php echo $row['nama_pemohon']; ?>"
                                                            data-applicant_institution="<?php echo $row['institusi_pemohon']; ?>"
                                                            data-applicant_email="<?php echo $row['email_pemohon']; ?>"
                                                            data-applicant_position="<?php echo $row['posisi_pemohon']; ?>"
                                                            data-applicant_title="<?php echo $row['gelar_pemohon']; ?>"
                                                            data-visit_purpose="<?php echo $row['tujuan_kunjungan']; ?>"
                                                            data-field_of_discussion="<?php echo $row['bidang_pembahasan']; ?>"
                                                            data-phone_number="<?php echo $row['telepon_pemohon']; ?>"
                                                            data-fax_number="<?php echo $row['faks_pemohon']; ?>"
                                                            data-website="<?php echo $row['website_pemohon']; ?>"
                                                            data-applicant_description="<?php echo $row['deskripsi_institusi']; ?>"
                                                            data-interpreter="<?php echo $row['interpreter']; ?>"
                                                            data-status="<?php echo $row['status']; ?>">
                                                            View Detail
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            // Jika tidak ada data dalam tabel
                                            echo '<tr><td colspan="5" class="text-center text-muted">No data available.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 mt-3" style="font-size: 0.6rem; background-color: #151c24; color: white;">
            <a href="https://international.undana.ac.id/" style="text-decoration: none; color: white;">Copyright</a> ©
            Computer Science UNDANA 2024
        </div>
    </footer>

    <div class="modal fade" id="viewApplicationModal" tabindex="-1" aria-labelledby="viewApplicationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="font-size: 14px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="font-size: 16px; font-weight: bold;" class="modal-title" id="viewApplicationModalLabel">
                        Submission Detail</h5>
                    <button style="border: none; background-color: transparent;" type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 20px;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6 d-flex flex-column">
                            <strong>Applicant Title:</strong> <span id="applicant_title"></span><br>
                            <strong>Applicant Name:</strong> <span id="applicant_name"></span><br>
                            <strong>Applicant Institution:</strong> <span id="applicant_institution"></span><br>
                            <strong>Applicant Position:</strong> <span id="applicant_position"></span><br>
                            <strong>Email:</strong> <span id="applicant_email"></span><br>
                            <strong>Phone Number:</strong> <span id="phone_number"></span><br>
                            <strong>Fax Number:</strong> <span id="fax_number"></span><br>
                        </div>
                        <div class="col-md-6 d-flex flex-column">
                            <strong>Submission Date:</strong> <span id="submission_date"></span><br>
                            <strong>Submission Time:</strong> <span id="submission_time"></span><br>
                            <strong>Visit Time:</strong> <span id="visit_time"></span><br>
                            <strong>Duration:</strong> <span id="duration"></span><br>
                            <strong>Visit Date:</strong> <span id="visit_date"></span><br>
                            <strong>Interpreter:</strong> <span id="interpreter"></span><br>
                            <strong>Institution Website:</strong> <span id="website"></span><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Purpose of Visit:</strong>
                            <p id="visit_purpose"></p>
                            <strong>Institution Description:</strong>
                            <p id="applicant_description"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Field of Discussion:</strong>
                            <p id="field_of_discussion"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const navbar = document.querySelector('.site-navbar-target');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 2) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    <script>
        var viewApplicationModal = document.getElementById('viewApplicationModal');
        viewApplicationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var submission_date = button.getAttribute('data-submission_date');
            var submission_time = button.getAttribute('data-submission_time');
            var visit_date = button.getAttribute('data-visit_date');
            var visit_time = button.getAttribute('data-visit_time');
            var duration = button.getAttribute('data-duration');
            var applicant_name = button.getAttribute('data-applicant_name');
            var applicant_institution = button.getAttribute('data-applicant_institution');
            var applicant_email = button.getAttribute('data-applicant_email');
            var applicant_position = button.getAttribute('data-applicant_position');
            var applicant_title = button.getAttribute('data-applicant_title');
            var visit_purpose = button.getAttribute('data-visit_purpose');
            var field_of_discussion = button.getAttribute('data-field_of_discussion');
            var phone_number = button.getAttribute('data-phone_number');
            var fax_number = button.getAttribute('data-fax_number');
            var website = button.getAttribute('data-website');
            var applicant_description = button.getAttribute('data-applicant_description');
            var interpreter = button.getAttribute('data-interpreter');
            var status = button.getAttribute('data-status');

            var modalTitle = viewApplicationModal.querySelector('.modal-title');
            modalTitle.textContent = 'Submission ' + applicant_name;

            document.getElementById('submission_date').textContent = submission_date;
            document.getElementById('submission_time').textContent = submission_time;
            document.getElementById('visit_date').textContent = visit_date;
            document.getElementById('visit_time').textContent = visit_time;
            document.getElementById('duration').textContent = duration + ' hour';
            document.getElementById('applicant_name').textContent = applicant_name;
            document.getElementById('applicant_institution').textContent = applicant_institution;
            document.getElementById('applicant_email').textContent = applicant_email;
            document.getElementById('applicant_position').textContent = applicant_position;
            document.getElementById('applicant_title').textContent = applicant_title;
            document.getElementById('visit_purpose').textContent = visit_purpose;
            document.getElementById('field_of_discussion').textContent = field_of_discussion;
            document.getElementById('phone_number').textContent = phone_number;
            document.getElementById('fax_number').textContent = fax_number;
            document.getElementById('website').textContent = website;
            document.getElementById('applicant_description').textContent = applicant_description;
            document.getElementById('interpreter').textContent = interpreter;
            document.getElementById('status').textContent = status;
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="user/js/bootstrap.min.js"></script>
    <script src="user/js/jquery.sticky.js"></script>
    <script src="user/js/main.js"></script>

    <script src="user/js/jquery.steps.js"></script>
    <script src="js/steps.js"></script>
</body>

</html>