<?php

include 'connect.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id_user'];

// Verifikasi apakah id_user valid di tabel user
$query_check_user = "SELECT id_user FROM user WHERE id_user = '$id_user'";
$result_check_user = mysqli_query($conn, $query_check_user);

if (mysqli_num_rows($result_check_user) == 0) {
    die("Error: id_user from session not found in user table.");
}

// Cek apakah ada parameter id_vrf (pengajuan lama)
$id_vrf = isset($_GET['id_vrf']) ? $_GET['id_vrf'] : null;
$parent_vrf_id = null;

$data_lama = null;
$orang_list = [];
$kontak_undana_list = [];
$delegasi_list = [];
$interpreter_value = '';

// Jika id_vrf ada, ambil data pengajuan lama
if ($id_vrf) {
    $query_check_parent = "SELECT * FROM vrf WHERE id_vrf = '$id_vrf' AND id_user = '$id_user'";
    $result_check_parent = mysqli_query($conn, $query_check_parent);

    if (mysqli_num_rows($result_check_parent) > 0) {
        $data_lama = mysqli_fetch_assoc($result_check_parent);
        $parent_vrf_id = $data_lama['id_vrf']; // Tetapkan parent_vrf_id jika ditemukan

        // Ambil data orang yang ditemui
        $query_orang = "SELECT * FROM orang WHERE id_vrf = '$id_vrf'";
        $result_orang = mysqli_query($conn, $query_orang);
        $orang_list = mysqli_fetch_all($result_orang, MYSQLI_ASSOC);

        // Ambil data kontak undana
        $query_kontak_undana = "SELECT * FROM kontak_undana WHERE id_vrf = '$id_vrf'";
        $result_kontak_undana = mysqli_query($conn, $query_kontak_undana);
        $kontak_undana_list = mysqli_fetch_all($result_kontak_undana, MYSQLI_ASSOC);

        // Ambil data delegasi
        $query_delegasi = "SELECT * FROM delegasi WHERE id_vrf = '$id_vrf'";
        $result_delegasi = mysqli_query($conn, $query_delegasi);
        $delegasi_list = mysqli_fetch_all($result_delegasi, MYSQLI_ASSOC);

        // Ambil nilai interpreter
        $query_interpreter = "SELECT interpreter FROM vrf WHERE id_vrf = '$id_vrf'";
        $result_interpreter = mysqli_query($conn, $query_interpreter);
        $row_interpreter = mysqli_fetch_assoc($result_interpreter);
        $interpreter_value = $row_interpreter['interpreter'] ?? '';
    } else {
        die("Error: Invalid parent_vrf_id or not authorized to edit this submission.");
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/png">
    <link rel="apple-touch-icon" href="style/image/Logo_Undana.png">
    <link rel="icon" href="style/image/Logo_Undana.png" type="image/x-icon">

    <meta name="theme-color" content="#ffffff">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="phone\build\css\intlTelInput.css">
    <script src="phone\build\js\intlTelInputWithUtils.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.css">

    <link rel="stylesheet" href="user/fonts/icomoon/style.css" />

    <link rel="stylesheet" href="user/css/bootstrap.min.css" />

    <link rel="stylesheet" href="user/css/style.css" />
    <link rel="stylesheet" href="user/css/mystyle.css">
    <link rel="stylesheet" href="user/css/steps.css">

    <script src="user/js/jquery-3.3.1.min.js"></script>

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
    <!-- VRF Section -->
    <div class="container" style="margin-top: 20px">
        <h2 class="text-center mb-5" style="font-weight: bold;">NEW VISIT REQUEST FORM</h2>
        <div class="wizard-form">
            <iframe name="response_frame" id="response_frame" style="display:none;"></iframe>
            <form class="form-register" id="form-vrf" action="" method="POST" target="response_frame">
                <div id="vrf-form">
                    <?php

                    $waktu_kunjungan = isset($data_lama['waktu_kunjungan']) ? substr($data_lama['waktu_kunjungan'], 0, 5) : '';

                    ?>
                    <!-- Step 1 -->
                    <h2>1</h2>
                    <section>
                        <h3 style="font-family: 'Poppins';">Visit Details</h3>
                        <div class="divider2"></div>
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-12 col-md-6">
                                <!-- Date and Time of Proposed Visit -->
                                <div class="mb-3 row">
                                    <div class="col-12 col-md-6">
                                        <label for="visit-date" class="form-label">Date of Proposed Visit</label>
                                        <input type="date" class="form-control" id="visit-date" name="visit_date"
                                            value="<?php echo isset($data_lama['tgl_kunjungan']) ? $data_lama['tgl_kunjungan'] : ''; ?>"
                                            required />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="visit-time" class="form-label">Time of Proposed Visit</label>
                                        <input type="time" class="form-control" id="visit-time" name="visit_time"
                                            value="<?php echo $waktu_kunjungan; ?>" required>
                                    </div>
                                </div>

                                <!-- Duration of Visit -->
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration of Visit (Hour)</label>
                                    <input type="number" class="form-control" id="duration" name="visit_duration"
                                        value="<?php echo isset($data_lama['durasi_kunjungan']) ? $data_lama['durasi_kunjungan'] : ''; ?>"
                                        placeholder="Enter duration of visit" min="1" required />
                                </div>

                                <?php
                                // Memecah nama pemohon menjadi nama depan dan nama belakang
                                $full_name = isset($data_lama['nama_pemohon']) ? $data_lama['nama_pemohon'] : '';
                                $name_parts = explode(' ', $full_name);

                                // Jika hanya satu kata, maka dianggap sebagai nama depan
                                $first_name = isset($name_parts[0]) ? $name_parts[0] : '';
                                $last_name = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : ''; // Menggabungkan sisa nama belakang
                                ?>

                                <!-- Person Making the Visit Request -->
                                <div class="mb-3">
                                    <label class="form-label">Person Making the Visit Request</label>
                                    <div class="row">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="Title"
                                                value="<?php echo isset($data_lama['gelar_pemohon']) ? $data_lama['gelar_pemohon'] : '' ?>"
                                                name="request_person_title" required />
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="First Name"
                                                value="<?php echo isset($first_name) ? $first_name : ''; ?>"
                                                name="request_person_first_name" required />
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <input type="text" class="form-control" placeholder="Last Name"
                                                value="<?php echo isset($last_name) ? $last_name : ''; ?>"
                                                name="request_person_last_name" required />
                                        </div>
                                    </div>
                                </div>

                                <!-- Position -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="position" name="request_person_position"
                                        value="<?php echo isset($data_lama['posisi_pemohon']) ? $data_lama['posisi_pemohon'] : ''; ?>"
                                        placeholder="Enter position" required />
                                </div>

                                <!-- Institution/Organization -->
                                <div class="mb-3">
                                    <label for="institution" class="form-label">Institution/Organization</label>
                                    <input type="text" class="form-control" id="institution"
                                        name="request_person_institution" placeholder="Enter institution/organization"
                                        value="<?php echo isset($data_lama['institusi_pemohon']) ? $data_lama['institusi_pemohon'] : ''; ?>"
                                        required />
                                </div>

                                <!-- Institution Website -->
                                <div class="mb-3">
                                    <label for="website" class="form-label">Institution Website</label>
                                    <input type="url" class="form-control" id="website" name="request_person_website"
                                        placeholder="Enter institution website"
                                        value="<?php echo isset($data_lama['website_pemohon']) ? $data_lama['website_pemohon'] : ''; ?>"
                                        required />
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-12 col-md-6">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="request_person_email"
                                        value="<?php echo isset($data_lama['email_pemohon']) ? $data_lama['email_pemohon'] : ''; ?>"
                                        placeholder="Enter email" required />
                                </div>

                                <!-- Phone/Mobile Phone -->
                                <div class="mb-3 d-flex flex-column">
                                    <label for="phone" class="form-label">Phone/Mobile Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="request_person_phone"
                                        value="<?php echo isset($data_lama['telepon_pemohon']) ? $data_lama['telepon_pemohon'] : ''; ?>"
                                        required />
                                </div>

                                <!-- Facsimile -->
                                <div class="mb-3">
                                    <label for="fax" class="form-label">Facsimile</label>
                                    <input type="text" class="form-control" id="fax" name="request_person_fax"
                                        value="<?php echo isset($data_lama['faks_pemohon']) ? $data_lama['faks_pemohon'] : ''; ?>"
                                        placeholder="Enter fax number" required />
                                </div>

                                <!-- Overview of the Institution/Organization -->
                                <div class="mb-3">
                                    <label for="overview" class="form-label">Overview of the
                                        Institution/Organization</label>
                                    <textarea class="form-control" id="overview" name="institution_overview" rows="4"
                                        placeholder="Enter overview" required>
                                        <?php echo isset($data_lama['deskripsi_institusi']) ? $data_lama['deskripsi_institusi'] : ''; ?>
                                    </textarea>
                                </div>

                                <!-- Purpose of Visit -->
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose of Visit</label>
                                    <textarea class="form-control" id="purpose" name="visit_purpose" rows="4"
                                        placeholder="Enter purpose of visit" required>
                                        <?php echo isset($data_lama['tujuan_kunjungan']) ? $data_lama['tujuan_kunjungan'] : ''; ?>
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Step 2 -->
                    <h2>2</h2>
                    <section>
                        <h3 style="font-family: 'Poppins';">Additional Information</h3>
                        <div class="divider2"></div>

                        <!-- Person(s) You Would Like To Meet -->
                        <div class="mb-3" id="meet-persons">
                            <label for="meet-person" class="form-label">Person(s) You Would Like To Meet</label>
                            <?php
                            // Jika ada data orang yang ditemui
                            if (!empty($orang_list)) {
                                foreach ($orang_list as $index => $orang) {
                                    ?>
                                    <div class="input-group mb-3 meet-person-item">
                                        <input type="text" class="form-control" name="meet_person[]"
                                            placeholder="Enter person's name"
                                            value="<?php echo htmlspecialchars(trim($orang['nama_orang'])); ?>" required />
                                    </div>
                                    <?php
                                }
                            } else {
                                // Jika tidak ada data orang, tampilkan satu input kosong
                                ?>
                                <div class="input-group mb-3 meet-person-item">
                                    <input type="text" class="form-control" name="meet_person[]"
                                        placeholder="Enter person's name" required />
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-meet-person">Add Person</a>
                            <a href="#" class="btn-delete" id="remove-meet-person">Remove</a>
                        </div>

                        <!-- Specific Areas/Topics of Interest for Discussion -->
                        <div class="mb-3">
                            <label for="topics" class="form-label">Specific Areas/Topics of Interest for
                                Discussion</label>
                            <textarea class="form-control" id="topics" name="discussion_topics" rows="4"
                                placeholder="Enter topics of interest" required><?php echo isset($data_lama['bidang_pembahasan']) ? $data_lama['bidang_pembahasan'] : ''; ?>
                            </textarea>
                        </div>

                        <!-- Contact Person at Universitas Nusa Cendana (Optional) -->
                        <div class="mb-3" id="contact-persons">
                            <label for="contact-person" class="form-label">Contact Person at Universitas Nusa Cendana
                                (Optional)</label>
                            <?php
                            // Jika ada data kontak undana
                            if (!empty($kontak_undana_list)) {
                                foreach ($kontak_undana_list as $index => $kontak) {
                                    ?>
                                    <div class="row g-2 contact-person-item">
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="contact_person_title[]"
                                                placeholder="Title"
                                                value="<?php echo htmlspecialchars(trim($kontak['gelar_kontak'])); ?>" />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="contact_person_first_name[]"
                                                placeholder="First Name"
                                                value="<?php echo htmlspecialchars(trim($kontak['nama_depan_kontak'])); ?>" />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="contact_person_last_name[]"
                                                placeholder="Last Name"
                                                value="<?php echo htmlspecialchars(trim($kontak['nama_belakang_kontak'])); ?>" />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="contact_person_position[]"
                                                placeholder="Position"
                                                value="<?php echo htmlspecialchars(trim($kontak['posisi_kontak'])); ?>" />
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                // Jika tidak ada data, tampilkan satu input kosong
                                ?>
                                <div class="row g-2 contact-person-item">
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="contact_person_title[]"
                                            placeholder="Title" />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="contact_person_first_name[]"
                                            placeholder="First Name" />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="contact_person_last_name[]"
                                            placeholder="Last Name" />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="contact_person_position[]"
                                            placeholder="Position" />
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-contact-person">Add Contact</a>
                            <a href="#" class="btn-delete" id="remove-contact-person">Remove</a>
                        </div>

                        <!-- Names of Delegation/Visitors -->
                        <div class="mb-3" id="delegation">
                            <label for="delegation" class="form-label">Names of Delegation/Visitors</label>
                            <?php
                            // Jika ada data delegasi
                            if (!empty($delegasi_list)) {
                                foreach ($delegasi_list as $index => $delegasi) {
                                    ?>
                                    <div class="row g-2 delegation-item">
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="delegation_title[]"
                                                placeholder="Title"
                                                value="<?php echo htmlspecialchars(trim($delegasi['gelar'])); ?>" required />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="delegation_first_name[]"
                                                placeholder="First Name"
                                                value="<?php echo htmlspecialchars(trim($delegasi['nama_depan'])); ?>"
                                                required />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="delegation_last_name[]"
                                                placeholder="Last Name"
                                                value="<?php echo htmlspecialchars(trim($delegasi['nama_belakang'])); ?>"
                                                required />
                                        </div>
                                        <div class="col-12 col-md-3 pb-2">
                                            <input type="text" class="form-control" name="delegation_position[]"
                                                placeholder="Position"
                                                value="<?php echo htmlspecialchars(trim($delegasi['posisi'])); ?>" required />
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                // Jika tidak ada data delegasi, tampilkan satu input kosong
                                ?>
                                <div class="row g-2 delegation-item">
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="delegation_title[]"
                                            placeholder="Title" required />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="delegation_first_name[]"
                                            placeholder="First Name" required />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="delegation_last_name[]"
                                            placeholder="Last Name" required />
                                    </div>
                                    <div class="col-12 col-md-3 pb-2">
                                        <input type="text" class="form-control" name="delegation_position[]"
                                            placeholder="Position" required />
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="action-btn">
                            <a href="#" class="btn-add" id="add-delegation">Add Delegation</a>
                            <a href="#" class="btn-delete" id="remove-delegation">Remove</a>
                        </div>

                        <!-- Interpreter (Yes/No) -->
                        <div class="btn-interpreter mb-3">
                            <label class="form-label">Interpreter</label>
                            <label for="interpreter" class="form-label">
                                <input type="radio" name="interpreter" value="yes" <?php echo ($interpreter_value == 'Yes') ? 'checked' : ''; ?> required /> Yes
                            </label for="interpreter" class="form-label">
                            <label>
                                <input type="radio" name="interpreter" value="no" <?php echo ($interpreter_value == 'No') ? 'checked' : ''; ?> required /> No
                            </label>
                        </div>
                        <script>
                            // Generalized functions to add/remove inputs
                            function addInput(containerId, template, removeButtonId) {
                                const container = document.getElementById(containerId);
                                const newItem = document.createElement("div");
                                newItem.classList.add("mb-3");
                                newItem.innerHTML = template;
                                container.appendChild(newItem);

                                // Enable Remove button
                                const removeBtn = document.getElementById(removeButtonId);
                                removeBtn.style.opacity = "1";
                                removeBtn.style.pointerEvents = "auto";
                            }

                            function removeInput(containerId, removeButtonId) {
                                const container = document.getElementById(containerId);
                                if (container.children.length > 2) {
                                    container.removeChild(container.lastElementChild);
                                }

                                // Disable Remove button if only 1 input remains
                                const removeBtn = document.getElementById(removeButtonId);
                                if (container.children.length <= 2) {
                                    removeBtn.style.opacity = "0.5";
                                    removeBtn.style.pointerEvents = "none";
                                }
                            }

                            // Add/Remove Meet Person
                            document.getElementById("add-meet-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                addInput("meet-persons", '<input type="text" class="form-control" name="meet_person[]" placeholder="Enter person\'s name" required />', "remove-meet-person");
                            });

                            document.getElementById("remove-meet-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("meet-persons", "remove-meet-person");
                            });

                            // Add/Remove Contact Person
                            document.getElementById("add-contact-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                const template = `
                                <div class="row g-2">
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_title[]" placeholder="Title" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_first_name[]" placeholder="First Name" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_last_name[]" placeholder="Last Name" /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="contact_person_position[]" placeholder="Position" /></div>
                                </div>`;
                                addInput("contact-persons", template, "remove-contact-person");
                            });

                            document.getElementById("remove-contact-person").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("contact-persons", "remove-contact-person");
                            });

                            // Add/Remove Delegation
                            document.getElementById("add-delegation").addEventListener("click", function (e) {
                                e.preventDefault();
                                const template = `
                                <div class="row g-2">
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_title[]" placeholder="Title" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_first_name[]" placeholder="First Name" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_last_name[]" placeholder="Last Name" required /></div>
                                    <div class="col-12 col-md-3 pb-2"><input type="text" class="form-control" name="delegation_position[]" placeholder="Position" required /></div>
                                </div>`;
                                addInput("delegation", template, "remove-delegation");
                            });

                            document.getElementById("remove-delegation").addEventListener("click", function (e) {
                                e.preventDefault();
                                removeInput("delegation", "remove-delegation");
                            });

                            // Initial State: Set Remove buttons to disabled on load
                            document.addEventListener("DOMContentLoaded", function () {
                                document.getElementById("remove-meet-person").style.opacity = "0.5";
                                document.getElementById("remove-meet-person").style.pointerEvents = "none";

                                document.getElementById("remove-contact-person").style.opacity = "0.5";
                                document.getElementById("remove-contact-person").style.pointerEvents = "none";

                                document.getElementById("remove-delegation").style.opacity = "0.5";
                                document.getElementById("remove-delegation").style.pointerEvents = "none";
                            });

                        </script>

                    </section>

                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="border: none; padding: 10px 20px;"
                        id="btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End of VRF Section -->

    <footer class="bg-body-tertiary text-center text-lg-start">
        <div class="text-center p-3 mt-3" style="font-size: 0.6rem; background-color: #151c24; color: white;">
            Copyright © Computer Science UNDANA 2024
        </div>
    </footer>

    <!-- Success Modal -->
    <div class="modal fade" style="z-index: 9999;" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom text-gray" style="">
                    <h5 class="modal-title" id="successModalLabel">Submission Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Your new submission has been successfully recorded. Please wait for further confirmation. You can
                        check the progress and approval status of your submission on the <strong>Status</strong> page.
                    </p>
                </div>
                <div class="modal-footer">
                    <a href="status_page.php" class="btn btn-primary">Go to Status Page</a>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" style="display:none; position: fixed; top: 10px; right: 10px;
    background-color: #28a745; color: white; padding: 10px 20px;
    border-radius: 5px; z-index: 1000;">
        Form successfully submitted!
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="user/js/bootstrap.min.js"></script>
    <script src="user/js/jquery.sticky.js"></script>
    <script src="user/js/jquery.steps.js"></script>
    <script src="user/js/main2.js"></script>

</body>

</html>